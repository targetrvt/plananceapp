<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;
use Smalot\PdfParser\Parser;

class TransactionImportService
{
    private const MODEL = 'gpt-4o-mini';

    private const MAX_TRANSACTIONS = 500;

    private const MAX_PDF_CHARS = 14000;

    /** @var list<string> */
    private const INCOME_SLUGS = ['salary', 'investment', 'gift', 'refund', 'other_income'];

    /** @var list<string> */
    private const EXPENSE_SLUGS = ['food', 'shopping', 'entertainment', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel', 'unhealthy_habits', 'other_expense'];

    /** @var array<string, array<int, string>> */
    private const HEADER_SYNONYMS = [
        'date' => ['date', 'datums', 'datum', 'transaction date', 'value date', 'booking date'],
        'type' => ['type', 'tips', 'tips veida', 'tips/veida', 'veida', 'veids', 'direction', 'dr/cr'],
        'amount' => ['amount', 'summa', 'sum', 'value', 'total', 'transaction amount'],
        'debit' => ['debit', 'debets', 'izmaksāt', 'izmaksātā', 'out', 'withdrawal'],
        'credit' => ['credit', 'kredits', 'iemaksāt', 'incoming', 'income amount', 'payment in'],
        'description' => ['description', 'apraksts', 'details', 'memo', 'narrative', 'text'],
        'payee' => ['payee', 'merchant', 'counterparty', 'name', 'beneficiary'],
        'category' => ['category', 'kategorija', 'class'],
    ];

    public function importFromTemporaryPath(string $absolutePath, string $originalBasename, int $userId): TransactionImportResult
    {
        if (! is_readable($absolutePath)) {
            throw new RuntimeException('Import file could not be read.');
        }

        $extension = strtolower(pathinfo($originalBasename, PATHINFO_EXTENSION));

        $parsed = match ($extension) {
            'pdf' => $this->parsePdfWithAi($absolutePath),
            'csv', 'xlsx', 'xls', 'xlsm' => $this->parseSpreadsheet($absolutePath, $extension),
            default => throw new RuntimeException('Unsupported file type. Allowed: CSV, Excel (xlsx, xls), PDF.'),
        };

        if (count($parsed) === 0) {
            throw new RuntimeException('No transactions could be read from this file.');
        }

        if (count($parsed) > self::MAX_TRANSACTIONS) {
            $parsed = array_slice($parsed, 0, self::MAX_TRANSACTIONS);
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        DB::transaction(function () use (&$imported, &$skipped, &$errors, $parsed, $userId): void {
            foreach ($parsed as $i => $row) {
                if (! $row instanceof TransactionNormalizedRow) {
                    $skipped++;
                    $errors[] = __('transaction.import.messages.row_invalid_numbered', ['n' => $i + 1]);

                    continue;
                }

                try {
                    Transaction::create([
                        'user_id' => $userId,
                        'type' => $row->type,
                        'amount' => $row->amount,
                        'date' => $row->date,
                        'description' => $row->description,
                        'category' => $row->category,
                        'financial_goal_id' => null,
                        'receipt_image' => null,
                    ]);
                    $imported++;
                } catch (\Throwable $e) {
                    Log::warning('Transaction import row failed', [
                        'exception' => $e->getMessage(),
                        'row' => $i + 1,
                    ]);
                    $skipped++;
                    $errors[] = __('transaction.import.messages.row_save_failed', ['n' => $i + 1]);
                }
            }
        });

        return new TransactionImportResult($imported, $skipped, array_slice(array_unique($errors), 0, 30));
    }

    /**
     * @return array<int, TransactionNormalizedRow|null>
     */
    private function parseSpreadsheet(string $path, string $extension): array
    {
        try {
            if ($extension === 'csv') {
                $matrix = $this->csvToMatrix($path);
            } else {
                $spreadsheet = IOFactory::load($path);
                $matrix = $spreadsheet->getActiveSheet()->toArray(null, false, false, false);
            }
        } catch (\Throwable $e) {
            Log::warning('Spreadsheet parse failed', ['exception' => $e->getMessage()]);
            throw new RuntimeException('Could not read this spreadsheet. Check CSV/Excel formatting.');
        }

        return array_values(array_filter(
            $this->matrixToTransactions($matrix),
            fn ($r) => $r instanceof TransactionNormalizedRow
        ));
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function csvToMatrix(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return [];
        }
        // UTF-16 / UTF-8 BOM sniff
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
        $firstLineEnd = strpos($raw, "\n");
        $probe = $firstLineEnd !== false ? substr($raw, 0, $firstLineEnd) : $raw;
        $delimiter = match (true) {
            substr_count($probe, "\t") >= 2 => "\t",
            substr_count($probe, ';') > substr_count($probe, ',') => ';',
            default => ',',
        };

        $lines = preg_split('/\R/u', trim($raw)) ?: [];
        $matrix = [];
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }
            $matrix[] = str_getcsv($line, $delimiter);
        }

        return $matrix;
    }

    /**
     * @param  array<int, array<int, mixed>>  $matrix
     * @return array<int, TransactionNormalizedRow|null>
     */
    private function matrixToTransactions(array $matrix): array
    {
        $matrix = array_values(array_filter($matrix, fn ($r) => is_array($r) && array_filter($r, fn ($c) => $c !== '' && $c !== null)));

        if ($matrix === []) {
            return [];
        }

        $headerIdx = $this->guessHeaderRowIndex($matrix);
        $columnMap = $this->detectColumns($matrix[$headerIdx]);
        $out = [];

        for ($ri = $headerIdx + 1; $ri < count($matrix); $ri++) {
            $cells = array_map(static fn ($c) => is_string($c) ? trim($c) : $c, $matrix[$ri]);
            $rowAssoc = [];

            foreach ($columnMap as $field => $colIdx) {
                $rowAssoc[$field] = $cells[$colIdx] ?? '';
            }

            $out[] = $this->inferRowFromAssociations($rowAssoc);
        }

        return $out;
    }

    /**
     * @param  array<int, mixed>  $headerRow
     * @return array<string, int>
     */
    private function detectColumns(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $idx => $raw) {
            $key = $this->canonicalHeaderCell($raw);
            if ($key === '') {
                continue;
            }
            foreach (self::HEADER_SYNONYMS as $field => $synonyms) {
                if (in_array($key, $synonyms, true) && ! isset($map[$field])) {
                    $map[$field] = (int) $idx;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * @param  array<int, array<int, mixed>>  $matrix
     */
    private function guessHeaderRowIndex(array $matrix): int
    {
        $best = 0;
        $bestScore = -1;

        foreach (array_slice($matrix, 0, min(35, count($matrix))) as $idx => $row) {
            if (! is_array($row)) {
                continue;
            }
            $score = count($this->detectColumns(array_map(static fn ($c) => (string) $c, $row)));
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = (int) $idx;
            }
        }

        return $bestScore >= 2 ? $best : 0;
    }

    private function canonicalHeaderCell(mixed $raw): string
    {
        $s = mb_strtolower(trim((string) $raw));
        $s = preg_replace('/[^\p{L}0-9_\/\s\-]/ui', '', $s);

        return preg_replace('/\s+/u', ' ', $s);
    }

    /**
     * @param  array<string, mixed>  $assoc
     */
    private function inferRowFromAssociations(array $assoc): ?TransactionNormalizedRow
    {
        $dateRaw = isset($assoc['date']) ? (string) $assoc['date'] : '';
        $date = $dateRaw !== '' ? $this->parseDateFlexible($dateRaw) : null;
        $descriptionPieces = [];

        foreach (['description', 'payee'] as $f) {
            if (! empty($assoc[$f])) {
                $descriptionPieces[] = (string) $assoc[$f];
            }
        }
        $description = mb_substr(trim(implode(' — ', array_filter($descriptionPieces))), 0, 320);

        $typeHint = strtolower((string) ($assoc['type'] ?? ''));

        $debitAmt = isset($assoc['debit']) && ($assoc['debit'] !== '' && $assoc['debit'] !== null)
            ? abs((float) ($this->parseMoney($assoc['debit']) ?? 0))
            : null;

        $creditAmt = isset($assoc['credit']) && ($assoc['credit'] !== '' && $assoc['credit'] !== null)
            ? abs((float) ($this->parseMoney($assoc['credit']) ?? 0))
            : null;

        $signedAmt = isset($assoc['amount']) && ($assoc['amount'] !== '' && $assoc['amount'] !== null)
            ? $this->parseMoney($assoc['amount'])
            : null;

        $type = null;
        $amount = null;

        if (($debitAmt !== null && $debitAmt > 0) && ($creditAmt === null || $creditAmt <= 0)) {
            $type = 'expense';
            $amount = $debitAmt;
        } elseif (($creditAmt !== null && $creditAmt > 0) && ($debitAmt === null || $debitAmt <= 0)) {
            $type = 'income';
            $amount = $creditAmt;
        } elseif ($signedAmt !== null && round(abs($signedAmt), 2) > 0) {
            $amount = round(abs((float) $signedAmt), 2);
            if ($signedAmt < 0) {
                $type = 'expense';
            } elseif (str_contains($typeHint, 'income')
                || str_contains($typeHint, 'cr')
                || str_contains($typeHint, 'kred')
                || str_contains($typeHint, 'iena')) {
                $type = 'income';
            } elseif (str_contains($typeHint, 'expense')
                || str_contains($typeHint, 'debit')
                || str_contains($typeHint, 'deb')
                || str_contains($typeHint, 'izdev')) {
                $type = 'expense';
            } else {
                // Ambiguous spreadsheet rows default to outgoing for positive amounts
                $type = 'expense';
            }
            if (str_contains($typeHint, 'income')) {
                $type = 'income';
            }
            if (str_contains($typeHint, 'expense')) {
                $type = 'expense';
            }
        }

        if ($type === null || $amount === null || $amount <= 0 || $date === null) {
            return null;
        }

        $categorySlug = isset($assoc['category']) ? (string) $assoc['category'] : '';

        return new TransactionNormalizedRow(
            $type,
            $amount,
            $date,
            $description !== '' ? $description : null,
            $this->normalizeCategorySlug($categorySlug, $type)
        );
    }

    /**
     * @return array<int, TransactionNormalizedRow|null>
     */
    private function parsePdfWithAi(string $path): array
    {
        $apiKey = config('services.openai.api_key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException(__('transaction.import.messages.openai_missing'));
        }

        try {
            $parser = new Parser;
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            Log::warning('PDF text extraction failed', ['exception' => $e->getMessage()]);
            throw new RuntimeException('Could not extract text from this PDF.');
        }

        $snippet = mb_substr(trim($text), 0, self::MAX_PDF_CHARS);
        if ($snippet === '') {
            throw new RuntimeException(__('transaction.import.messages.pdf_empty_text'));
        }

        $decoded = $this->requestTransactionsJsonFromAi($snippet, 'extracted_pdf_text');

        return array_values(array_filter(
            array_map(fn ($item) => is_array($item) ? $this->decodedRowToNormalized($item) : null, $decoded),
            fn ($r) => $r instanceof TransactionNormalizedRow
        ));
    }

    /**
     * @return array<int, mixed>
     */
    private function requestTransactionsJsonFromAi(string $snippet, string $context): array
    {
        $apiKey = config('services.openai.api_key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException(__('transaction.import.messages.openai_missing'));
        }

        $incomeList = implode(', ', self::INCOME_SLUGS);
        $expenseList = implode(', ', self::EXPENSE_SLUGS);

        $system = <<<PROMPT
You extract financial transactions into strict JSON only. Each transaction must map to categories used by Planance.
Income categories exactly (slug): {$incomeList}.
Expense categories exactly (slug): {$expenseList}.
Never use slug "savings" (omit goal linking). Use amounts as positive decimals in EUR even if PDF shows separators.
"type" must be only "income" or "expense". Dates as YYYY-MM-DD.
Respond with JSON ONLY: {"transactions":[{"date":"","amount":0,"type":"expense","description":"","category":""}]} — no prose, no markdown.
PROMPT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => "Context: {$context}\nBank / statement text:\n\n".$snippet],
            ],
            'temperature' => 0.15,
            'max_tokens' => 8192,
        ]);

        if (! $response->successful()) {
            Log::warning('OpenAI import parse failed HTTP', ['body' => $response->body()]);

            throw new RuntimeException(__('transaction.import.messages.ai_failed'));
        }

        $content = $response->json('choices.0.message.content');

        return $this->parseAiTransactionsJsonPayload(is_string($content) ? $content : '');
    }

    /**
     * @return array<int, mixed>
     */
    private function parseAiTransactionsJsonPayload(string $content): array
    {
        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            preg_match('/\{[\s\S]*\}/', $content, $m);
            if (isset($m[0])) {
                $decoded = json_decode($m[0], true);
            }
        }

        if (! is_array($decoded)) {
            Log::warning('AI import JSON malformed', ['raw' => mb_substr($content, 0, 600)]);

            throw new RuntimeException(__('transaction.import.messages.ai_invalid_json'));
        }

        $list = isset($decoded['transactions']) && is_array($decoded['transactions'])
            ? $decoded['transactions']
            : (
                isset($decoded[0]) ? $decoded : []
            );

        if (! is_array($list)) {
            throw new RuntimeException(__('transaction.import.messages.ai_invalid_json'));
        }

        return array_slice($list, 0, self::MAX_TRANSACTIONS);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function decodedRowToNormalized(array $row): ?TransactionNormalizedRow
    {
        if (! isset($row['amount'], $row['type'], $row['date'])) {
            return null;
        }

        $type = strtolower((string) $row['type']);

        $resolvedAmount = null;
        if (is_numeric($row['amount'])) {
            $resolvedAmount = round((float) $row['amount'], 2);
        } elseif ($this->parseMoney($row['amount']) !== null) {
            $resolvedAmount = abs((float) $this->parseMoney($row['amount']));
        }

        try {
            $date = Carbon::parse((string) $row['date'])->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }

        if (! in_array($type, ['income', 'expense'], true) || $resolvedAmount === null || $resolvedAmount <= 0) {
            return null;
        }

        $amount = round(abs((float) $resolvedAmount), 2);

        $description = isset($row['description']) ? trim((string) $row['description']) : null;
        if ($description === '') {
            $description = null;
        }

        return new TransactionNormalizedRow(
            $type,
            $amount,
            $date,
            $description !== null ? mb_substr($description, 0, 480) : null,
            $this->normalizeCategorySlug(isset($row['category']) ? (string) $row['category'] : '', $type)
        );
    }

    private function parseDateFlexible(string $cell): ?string
    {
        if (is_numeric($cell)) {
            $n = (float) $cell;
            if ($n >= 28000 && $n <= 65000) {
                try {
                    return ExcelDate::excelToDateTimeObject($n)->format('Y-m-d');
                } catch (\Throwable) {
                    // Continue to Carbon fallback
                }
            }
        }

        try {
            return Carbon::parse($cell)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Parse money; preserves sign when the export provides it (e.g. leading minus or accounting parentheses).
     */
    private function parseMoney(mixed $cell): ?float
    {
        if ($cell === null || $cell === '') {
            return null;
        }

        if (is_numeric($cell)) {
            return round((float) $cell, 2);
        }

        $s = trim(str_replace(["\xc2\xa0", 'EUR', 'eur', '€'], '', (string) $cell));

        $negative = false;

        if (preg_match('/^\((.+)\)\s*$/u', $s, $wrapped)) {
            $negative = true;
            $s = trim((string) $wrapped[1]);
        } elseif (preg_match('/^[\x{002D}\x{2013}\x{2212}]+/u', $s)) {
            $negative = true;
            $s = trim(preg_replace('/^[\x{002D}\x{2013}\x{2212}]+/u', '', $s));
        }

        $s = preg_replace('/\s+/u', '', $s);

        // EU style 1.234,56 vs US 1,234.56
        $lastComma = mb_strrpos($s, ',');
        $lastDot = mb_strrpos($s, '.');

        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            $withoutThousands = preg_replace('/\./', '', $s);
            $numeric = str_replace(',', '.', (string) $withoutThousands);
        } else {
            $withoutThousands = preg_replace('/\,/', '', $s);
            $numeric = (string) $withoutThousands;
        }

        $n = round((float) $numeric, 2);

        if ($negative && $n !== 0.0) {
            return -abs($n);
        }

        return $n;
    }

    private function normalizeCategorySlug(string $rawSlug, string $type): string
    {
        $clean = strtolower(preg_replace('/[^a-z0-9_]/', '_', trim($rawSlug)));
        $clean = preg_replace('/_+/', '_', $clean) ?: '';

        $allowed = $type === 'income' ? self::INCOME_SLUGS : self::EXPENSE_SLUGS;

        if (in_array($clean, $allowed, true)) {
            return $clean;
        }

        foreach ($allowed as $slug) {
            if ($clean !== '' && (str_contains($clean, $slug) || str_contains($slug, $clean))) {
                return $slug;
            }
        }

        return $type === 'income' ? 'other_income' : 'other_expense';
    }
}
