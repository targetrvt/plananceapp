<?php

namespace App\Services;

use App\Models\FinancialGoal;
use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class RecordGoalContribution
{
    public static function record(FinancialGoal $goal, float $amount, string $date, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        if ($goal->user_id !== (int) auth()->id()) {
            abort(403);
        }

        return DB::transaction(function () use ($goal, $amount, $date, $description) {
            $transaction = Transaction::create([
                'user_id' => $goal->user_id,
                'type' => 'expense',
                'amount' => $amount,
                'date' => $date,
                'category' => 'savings',
                'description' => $description ?: __('financial-goals-dashboard.contribution.default_description', ['goal' => $goal->name]),
                'financial_goal_id' => $goal->id,
            ]);

            $userBalance = UserBalance::firstOrCreate(
                ['user_id' => $goal->user_id],
                ['balance' => 0, 'currency' => 'EUR']
            );
            $userBalance->balance -= $amount;
            $userBalance->save();

            return $transaction;
        });
    }

    public static function withdraw(FinancialGoal $goal, float $amount, string $date, ?string $description = null): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        if ($goal->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($amount > (float) $goal->current_amount) {
            throw new \InvalidArgumentException(__('financial-goals-dashboard.withdraw.exceeds_saved'));
        }

        return DB::transaction(function () use ($goal, $amount, $date, $description) {
            $transaction = Transaction::create([
                'user_id' => $goal->user_id,
                'type' => 'income',
                'amount' => $amount,
                'date' => $date,
                'category' => 'savings',
                'description' => $description ?: __('financial-goals-dashboard.withdraw.default_description', ['goal' => $goal->name]),
                'financial_goal_id' => $goal->id,
            ]);

            $userBalance = UserBalance::firstOrCreate(
                ['user_id' => $goal->user_id],
                ['balance' => 0, 'currency' => 'EUR']
            );
            $userBalance->balance += $amount;
            $userBalance->save();

            return $transaction;
        });
    }
}
