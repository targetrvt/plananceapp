<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'date',
        'description',
        'category',
        'user_id',
        'financial_goal_id',
        'receipt_image',
    ];

    protected static function booted(): void
    {
        static::created(function (Transaction $transaction) {
            if ($transaction->category !== 'savings' || ! $transaction->financial_goal_id) {
                return;
            }

            if ($transaction->type === 'expense') {
                self::applySavingsToGoal(
                    (int) $transaction->financial_goal_id,
                    (int) $transaction->user_id,
                    (float) $transaction->amount
                );
            } elseif ($transaction->type === 'income') {
                self::applySavingsToGoal(
                    (int) $transaction->financial_goal_id,
                    (int) $transaction->user_id,
                    -1 * (float) $transaction->amount
                );
            }
        });

        static::deleting(function (Transaction $transaction) {
            if ($transaction->category !== 'savings' || ! $transaction->financial_goal_id) {
                return;
            }

            if ($transaction->type === 'expense') {
                self::applySavingsToGoal(
                    (int) $transaction->financial_goal_id,
                    (int) $transaction->user_id,
                    -1 * (float) $transaction->amount
                );
            } elseif ($transaction->type === 'income') {
                self::applySavingsToGoal(
                    (int) $transaction->financial_goal_id,
                    (int) $transaction->user_id,
                    (float) $transaction->amount
                );
            }
        });
    }

    protected static function applySavingsToGoal(int $goalId, int $userId, float $delta): void
    {
        $goal = FinancialGoal::query()
            ->whereKey($goalId)
            ->where('user_id', $userId)
            ->first();

        if (! $goal) {
            return;
        }

        $goal->current_amount = max(0, (float) $goal->current_amount + $delta);
        if ((float) $goal->target_amount > 0) {
            $goal->progress = round(
                min(999.99, ($goal->current_amount / (float) $goal->target_amount) * 100),
                2
            );
        }
        $goal->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function financialGoal(): BelongsTo
    {
        return $this->belongsTo(FinancialGoal::class);
    }
}
