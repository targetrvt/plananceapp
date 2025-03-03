<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Expense Trends';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get the last 6 months of data
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('M Y'));
        }
        
        // Get expense data by month
        $expenseData = [];
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::now()->subMonths($i)->endOfMonth();
            
            $total = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
                
            $expenseData[] = round($total, 2);
        }
        
        // Get category breakdown for the current month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $categoryDataResults = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
            
        // Sort manually and limit to 6
        $categoryData = $categoryDataResults->sortByDesc('total')->take(6);
            
        $categoryLabels = $categoryData->pluck('category')->map(function ($category) {
            return str_replace('_', ' ', ucfirst($category));
        })->toArray();
        
        $categoryValues = $categoryData->pluck('total')->toArray();
        
        return [
            'datasets' => [
                [
                    'label' => 'Monthly Expenses',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                    'borderColor' => '#4f46e5',
                    'borderWidth' => 2,
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Current Month Categories',
                    'data' => $categoryValues,
                    'backgroundColor' => [
                        '#4f46e5', '#3b82f6', '#06b6d4', '#10b981', 
                        '#f59e0b', '#8b5cf6'
                    ],
                    'hidden' => true, // Hidden by default
                ],
            ],
            'labels' => $months->toArray(),
            // Add a second set of labels for the category data
            'categoryLabels' => $categoryLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => '(value) => "€" + value',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "(context) => {
                            let label = context.dataset.label || '';
                            let value = context.raw || 0;
                            
                        }",
                    ],
                ],
            ],
        ];
    }
}