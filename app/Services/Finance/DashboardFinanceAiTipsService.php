<?php

namespace App\Services\Finance;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DashboardFinanceAiTipsService
{
    private const MODEL = 'gpt-4o-mini';

    /**
     * @param  'income_tips'|'savings_tips'  $variant
     */
    public function generate(
        int $userId,
        string $variant,
        string $startDate,
        string $endDate,
        string $timeframe,
        string $incomeCategoryFilter,
        string $expenseCategoryFilter,
        string $locale,
        string $tipsUiTitle,
        string $tipsUiScopeDescription,
    ): string {
        $apiKey = config('services.openai.api_key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('OpenAI is not configured.');
        }

        $payload = $this->buildDataPayload(
            $userId,
            $startDate,
            $endDate,
            $timeframe,
            $incomeCategoryFilter,
            $expenseCategoryFilter,
            $locale,
            $tipsUiTitle,
            $tipsUiScopeDescription,
        );

        $system = $variant === 'income_tips'
            ? 'You are a concise personal finance coach for Planance (EU/EUR). Focus on income stability, diversification, tax-aware ideas that are general education (not tax advice), and actionable habits. Be supportive and specific to the numbers given. Do not invent transactions. If data is empty, say so briefly and suggest recording income/expenses. Output plain text: short intro then 4–7 bullet lines starting with "- ". No HTML, no markdown headings.'
            : 'You are a concise personal finance coach for Planance (EU/EUR). Focus on spending patterns, realistic savings opportunities, category trade-offs, and emergency-fund style guidance where relevant. Be supportive and specific to the numbers given. Do not invent transactions. If data is empty, say so briefly. Output plain text: short intro then 4–7 bullet lines starting with "- ". No HTML, no markdown headings.';

        $userMessageHeader = $variant === 'income_tips'
            ? "Based on the following JSON for the user's selected dashboard period, write **Income tips** (not investment product recommendations)."
            : "Based on the following JSON for the user's selected dashboard period, write **Savings tips** (reducing waste, prioritizing goals; not shaming).";

        $userMessage =
            $userMessageHeader
            ."\n\nThe \"guided_context\" object states how the dashboard applies filters and how the UI describes this analysis to the user—use it alongside the numerical data.\n\n"
            .$payload;

        [$system, $userMessage] = $this->applyResponseLanguageDirectives($locale, $system, $userMessage);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
            'model' => self::MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0.5,
            'max_tokens' => 800,
        ]);

        if (! $response->successful()) {
            Log::warning('Dashboard finance AI tips: OpenAI HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Could not reach the AI service.');
        }

        $content = $response->json('choices.0.message.content');
        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Empty AI response.');
        }

        return trim($content);
    }

    private function buildDataPayload(
        int $userId,
        string $startDate,
        string $endDate,
        string $timeframe,
        string $incomeCategoryFilter,
        string $expenseCategoryFilter,
        string $locale,
        string $tipsUiTitle,
        string $tipsUiScopeDescription,
    ): string {
        app()->setLocale($locale);

        $incomes = $this->queryTransactions($userId, 'income', $startDate, $endDate, $incomeCategoryFilter);
        $expenses = $this->queryTransactions($userId, 'expense', $startDate, $endDate, $expenseCategoryFilter);

        $incomeTotal = (float) $incomes->sum('amount');
        $expenseTotal = (float) $expenses->sum('amount');

        $byIncomeCategory = $incomes->groupBy('category')->map(fn ($g) => round((float) $g->sum('amount'), 2))->sortDesc();
        $byExpenseCategory = $expenses->groupBy('category')->map(fn ($g) => round((float) $g->sum('amount'), 2))->sortDesc();

        $periodLabel = Carbon::parse($startDate)->isoFormat('LL').' → '.Carbon::parse($endDate)->isoFormat('LL');

        $describeFilter = static function (string $type, string $filter): string {
            if ($filter === 'all') {
                return 'all '.$type.' categories';
            }

            return $type.' category filter: '.$filter;
        };

        $primary = $this->localePrimaryTag($locale);

        $data = [
            'locale' => $locale,
            'expected_response_language' => $primary === 'lv' ? 'lv (Latvian—entire answer must be in Latvian)' : $primary,
            'currency' => 'EUR',
            'guided_context' => [
                'ui_card_title' => $tipsUiTitle,
                'scope_and_disclaimers_explained_for_user' => $tipsUiScopeDescription,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => $periodLabel,
                'timeframe' => $timeframe,
            ],
            'filters_explained' => [
                'incomes' => $describeFilter('income', $incomeCategoryFilter),
                'expenses' => $describeFilter('expense', $expenseCategoryFilter),
            ],
            'totals' => [
                'total_income' => round($incomeTotal, 2),
                'total_expenses' => round($expenseTotal, 2),
                'net' => round($incomeTotal - $expenseTotal, 2),
            ],
            'income_by_category' => $this->withLabels($byIncomeCategory, 'income'),
            'expense_by_category' => $this->withLabels($byExpenseCategory, 'expense'),
            'sample_income_transactions' => $this->summarizeTransactions($incomes),
            'sample_expense_transactions' => $this->summarizeTransactions($expenses),
        ];

        app()->setLocale(config('app.locale'));

        return json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Transaction>
     */
    private function queryTransactions(int $userId, string $type, string $startDate, string $endDate, string $categoryFilter)
    {
        $q = Transaction::query()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($categoryFilter !== 'all') {
            $q->where('category', $categoryFilter);
        }

        return $q->orderByDesc('date')->orderByDesc('id')->get();
    }

    private function withLabels($categoryTotals, string $expenseOrIncome): array
    {
        $prefix = $expenseOrIncome === 'income' ? 'messages.categories.income.' : 'messages.categories.expense.';
        $out = [];
        foreach ($categoryTotals as $slug => $amount) {
            $key = $prefix.$slug;
            $label = __($key);
            $out[] = [
                'category' => (string) $slug,
                'label' => $label !== $key ? $label : (string) $slug,
                'amount_eur' => (float) $amount,
            ];
        }

        return $out;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Transaction>  $rows
     * @return list<array{date:string, amount:float, category:string, description:string}>
     */
    private function summarizeTransactions($rows): array
    {
        return $rows->take(25)->map(function (Transaction $t) {
            return [
                'date' => (string) $t->date,
                'amount' => round((float) $t->amount, 2),
                'category' => (string) $t->category,
                'description' => mb_substr((string) ($t->description ?? ''), 0, 400),
            ];
        })->values()->all();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function applyResponseLanguageDirectives(string $locale, string $system, string $userMessage): array
    {
        if ($this->localePrimaryTag($locale) !== 'lv') {
            return [$system, $userMessage];
        }

        $system .= ' The user\'s app/panel language is Latvian (locale lv). Write the complete answer in natural, standard Latvian—intro and every bullet. Do not answer in English. You may keep € and numeric amounts as usual.';

        $userMessage .= "\n\nObligāti: visa atbilde tikai latviešu valodā (teksts un aizzīmes).";

        return [$system, $userMessage];
    }

    private function localePrimaryTag(string $locale): string
    {
        $normalized = strtolower(trim(str_replace('_', '-', $locale)));

        if ($normalized === '') {
            return 'en';
        }

        return strlen($normalized) >= 2 ? substr($normalized, 0, 2) : 'en';
    }
}
