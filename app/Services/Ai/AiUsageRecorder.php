<?php

namespace App\Services\Ai;

use App\Models\AiUsageLog;
use Illuminate\Http\Client\Response as HttpResponse;
use OpenAI\Responses\Chat\CreateResponse;

class AiUsageRecorder
{
    public function recordFromOpenAiCreateResponse(
        ?int $userId,
        string $feature,
        CreateResponse $response,
    ): void {
        if ($userId === null) {
            return;
        }

        $usage = $response->usage;
        $model = $response->model;
        $prompt = $usage->promptTokens;
        $completion = $usage->completionTokens;

        $this->persist(
            $userId,
            $feature,
            $model,
            $prompt,
            $completion,
            $usage->totalTokens,
        );
    }

    public function recordFromHttpResponse(
        ?int $userId,
        string $feature,
        string $model,
        HttpResponse $response,
    ): void {
        if ($userId === null || ! $response->successful()) {
            return;
        }

        $json = $response->json();
        if (! is_array($json)) {
            return;
        }

        $usage = $json['usage'] ?? null;
        if (! is_array($usage)) {
            return;
        }

        $promptTokens = isset($usage['prompt_tokens']) ? (int) $usage['prompt_tokens'] : null;
        $completionTokens = isset($usage['completion_tokens']) ? (int) $usage['completion_tokens'] : null;
        $totalTokens = isset($usage['total_tokens']) ? (int) $usage['total_tokens'] : null;

        if ($totalTokens === null && ($promptTokens !== null || $completionTokens !== null)) {
            $totalTokens = ($promptTokens ?? 0) + ($completionTokens ?? 0);
        }

        $modelFromPayload = isset($json['model']) ? (string) $json['model'] : '';

        $this->persist(
            $userId,
            $feature,
            $modelFromPayload !== '' ? $modelFromPayload : $model,
            $promptTokens,
            $completionTokens,
            $totalTokens,
        );
    }

    /**
     * @param  '?int'  $totalTokens
     */
    private function persist(
        int $userId,
        string $feature,
        string $model,
        ?int $promptTokens,
        ?int $completionTokens,
        ?int $totalTokens,
    ): void {
        $completion = $completionTokens ?? 0;
        $costUsd = null;
        if ($promptTokens !== null || $completion > 0) {
            $costUsd = $this->estimateCostUsd($model, $promptTokens ?? 0, $completion);
        }

        AiUsageLog::create([
            'user_id' => $userId,
            'feature' => $feature,
            'model' => $model !== '' ? $model : null,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'estimated_cost_usd' => $costUsd,
        ]);
    }

    public function estimateCostUsd(string $model, int $promptTokens, int $completionTokens): ?float
    {
        /** @var array<string, array<string, float>> */
        $models = config('planance_ai_pricing.models', []);

        $row = is_array(($models[$model] ?? null) ?? null)
            ? $models[$model]
            : (is_array($models['default'] ?? null) ? $models['default'] : null);

        if (! is_array($row)) {
            return null;
        }

        $inputPer = (float) ($row['input_usd_per_1m'] ?? 0);
        $outputPer = (float) ($row['output_usd_per_1m'] ?? 0);

        return round(
            ($promptTokens / 1_000_000) * $inputPer + ($completionTokens / 1_000_000) * $outputPer,
            10,
        );
    }

    /** EUR estimate using {@see resolveEurPricingRow} for gpt-4o-mini tier and variants (dated models). */
    public function estimateCostEur(string $model, int $promptTokens, int $completionTokens): ?float
    {
        $row = $this->resolveEurPricingRow($model);
        if (! is_array($row)) {
            return null;
        }

        $inputPer = (float) ($row['input_eur_per_1m'] ?? 0);
        $outputPer = (float) ($row['output_eur_per_1m'] ?? 0);

        return round(
            ($promptTokens / 1_000_000) * $inputPer + ($completionTokens / 1_000_000) * $outputPer,
            10,
        );
    }

    /**
     * Display EUR for logs: recomputed from tokens + gpt-4o-mini EUR rates when possible,
     * else derived from stored USD estimate × FX (older rows missing token breakdown).
     */
    public function estimatedCostEurForLog(AiUsageLog $log): ?float
    {
        $model = (string) ($log->model ?? '');
        $prompt = $log->prompt_tokens;
        $completion = (int) ($log->completion_tokens ?? 0);

        if ($prompt !== null || $completion > 0) {
            $fromTokens = $this->estimateCostEur($model, $prompt ?? 0, $completion);
            if ($fromTokens !== null) {
                return $fromTokens;
            }
        }

        if ($log->estimated_cost_usd !== null) {
            return round((float) $log->estimated_cost_usd * (float) config('planance_ai_pricing.usd_to_eur', 0.93), 10);
        }

        return null;
    }

    /**
     * @return ?array<string, float>
     */
    private function resolveEurPricingRow(string $model): ?array
    {
        /** @var array<string, array<string, float>> */
        $models = config('planance_ai_pricing.models_eur', []);

        $key = strtolower(trim($model));

        if (isset($models[$key])) {
            $row = $models[$key];

            return is_array($row) ? $row : null;
        }

        if ($key !== '' && str_starts_with($key, 'gpt-4o-mini') && isset($models['gpt-4o-mini'])) {
            $row = $models['gpt-4o-mini'];

            return is_array($row) ? $row : null;
        }

        $default = $models['default'] ?? null;

        return is_array($default) ? $default : null;
    }
}
