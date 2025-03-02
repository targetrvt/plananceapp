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
            Stat::make('Current Balance', number_format($userBalance->balance, 2) . ' €')
                ->description('Your available funds')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('primary'),
                
            Stat::make('Total Income', number_format($income, 2) . ' €')
                ->description('All time income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Total Expenses', number_format($expenses, 2) . ' €')
                ->description('All time expenses')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}