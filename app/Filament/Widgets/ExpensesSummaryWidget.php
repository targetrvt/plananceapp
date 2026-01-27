<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ExpensesSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected function getStats(): array
    {
        // Total expenses this month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyExpenses = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // Yesterday's expenses
        $yesterday = Carbon::yesterday();
        $yesterdayExpenses = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereDate('date', $yesterday)
            ->sum('amount');
        
        // Get previous month's expenses for comparison
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $previousMonthExpenses = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount');
        
        // Calculate monthly comparison percentage
        $monthlyComparison = 0;
        if ($previousMonthExpenses > 0) {
            $monthlyComparison = (($monthlyExpenses - $previousMonthExpenses) / $previousMonthExpenses) * 100;
        }
        
        // Get largest expense category this month - fixing the SQL order by issue
        $topCategoryResults = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category')
            ->get();
            
        // Sort manually and get the first one
        $topCategory = $topCategoryResults->sortByDesc('total')->first();
            
        return [
            Stat::make(__('widgets.expenses_summary.monthly_expenses.label'), "€" . number_format($monthlyExpenses, 2))
                ->description($monthlyComparison >= 0 
                    ? __('widgets.expenses_summary.monthly_expenses.increase', ['percentage' => number_format(abs($monthlyComparison), 1)])
                    : __('widgets.expenses_summary.monthly_expenses.decrease', ['percentage' => number_format(abs($monthlyComparison), 1)]))
                ->descriptionIcon($monthlyComparison >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyComparison >= 0 ? 'danger' : 'success')
                ->chart([
                    $previousMonthExpenses / 100, 
                    $monthlyExpenses / 100
                ]),
            
            Stat::make(__('widgets.expenses_summary.yesterday_expenses.label'), "€" . number_format($yesterdayExpenses, 2))
                ->description(__('widgets.expenses_summary.yesterday_expenses.description', ['date' => $yesterday->format('D, M j')]))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make(__('widgets.expenses_summary.top_category.label'), $topCategory 
                ? __('messages.categories.expense.' . $topCategory->category)
                : __('widgets.expenses_summary.top_category.no_expenses'))
                ->description($topCategory 
                    ? "€" . number_format($topCategory->total, 2)
                    : __('widgets.expenses_summary.top_category.description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}