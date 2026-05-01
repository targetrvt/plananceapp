<?php

namespace App\Models;

use App\Services\Ai\AiUsageRecorder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    public const FEATURE_FINANCE_TIPS = 'finance_tips';

    public const FEATURE_TRANSACTION_IMPORT = 'transaction_import';

    public const FEATURE_RECEIPT_SCAN = 'receipt_scan';

    public const FEATURE_PLANANCE_SUPPORT = 'planance_support';

    protected $fillable = [
        'user_id',
        'feature',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'estimated_cost_usd',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost_usd' => 'decimal:10',
        ];
    }

    public function getCostEurAttribute(): ?float
    {
        return app(AiUsageRecorder::class)->estimatedCostEurForLog($this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
