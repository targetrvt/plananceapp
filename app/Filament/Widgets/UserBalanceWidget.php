<?php

namespace App\Filament\Widgets;

use App\Models\UserBalance;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserBalanceWidget extends BaseWidget
{
    protected static ?int $sort = -2; // To make it appear at the top
    
    protected function getStats(): array
    {
        // Get user balance or create one if it doesn't exist
        $userBalance = UserBalance::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0, 'currency' => 'EUR']
        );
        
        // Calculate income
        $income = Transaction::where('user_id', auth()->id())
            ->where('type', 'income')
            ->sum('amount');
            
        // Calculate expenses
        $expenses = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->sum('amount');
        
        return [
            Stat::make(__('widgets.user_balance.current_balance.label'), number_format($userBalance->balance, 2) . ' €')
                ->description(__('widgets.user_balance.current_balance.description'))
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('primary'),
                
            Stat::make(__('widgets.user_balance.total_income.label'), number_format($income, 2) . ' €')
                ->description(__('widgets.user_balance.total_income.description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make(__('widgets.user_balance.total_expenses.label'), number_format($expenses, 2) . ' €')
                ->description(__('widgets.user_balance.total_expenses.description'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}