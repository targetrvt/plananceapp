<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseTrendsWidget extends ChartWidget
{
    protected static ?string $heading = 'Expense Trends';
    protected static ?string $description = 'Your monthly expense overview for the past 6 months. Click on a data point to see details.';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '350px';

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
        
        // Calculate average for reference line
        $average = count($expenseData) > 0 ? array_sum($expenseData) / count($expenseData) : 0;
        
        return [
            'datasets' => [
                [
                    'label' => 'Monthly Expenses',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointHitRadius' => 12,
                    'pointHoverBackgroundColor' => 'rgb(79, 70, 229)',
                    'pointHoverBorderColor' => '#ffffff',
                    'pointHoverBorderWidth' => 3,
                ],
                [
                    'label' => 'Average',
                    'data' => array_fill(0, count($months), round($average, 2)),
                    'borderColor' => 'rgba(156, 163, 175, 0.5)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'fill' => false,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 0,
                ],
            ],
            'labels' => $months->toArray(),
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
            'interaction' => [
                'intersect' => true,
                'mode' => 'nearest',
            ],
            'animation' => [
                'duration' => 1000,
                'easing' => 'easeInOutQuart',
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                        'color' => 'rgba(107, 114, 128, 0.8)',
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.1)',
                        'borderDash' => [2, 2],
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { return "€" + value.toLocaleString("en-US", {minimumFractionDigits: 0, maximumFractionDigits: 0}); }',
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                        'color' => 'rgba(107, 114, 128, 0.8)',
                        'padding' => 10,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                        'color' => 'rgba(107, 114, 128, 0.9)',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'nearest',
                    'intersect' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => 'rgb(249, 250, 251)',
                    'bodyColor' => 'rgb(249, 250, 251)',
                    'borderColor' => 'rgba(156, 163, 175, 0.2)',
                    'borderWidth' => 1,
                    'padding' => 12,
                    'displayColors' => true,
                    'titleFont' => [
                        'size' => 14,
                        'weight' => '600',
                    ],
                    'bodyFont' => [
                        'size' => 13,
                        'weight' => '500',
                    ],
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            if (context.parsed.y !== null) {
                                label += "€" + context.parsed.y.toLocaleString("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                            return label;
                        }',
                        'title' => 'function(tooltipItems) {
                            return tooltipItems[0].label;
                        }',
                    ],
                ],
            ],
        ];
    }

    public function showMonthDetails(int $monthIndex): void
    {
        if ($monthIndex < 0 || $monthIndex > 5) {
            return;
        }

        $monthDate = Carbon::now()->subMonths(5 - $monthIndex);
        $startOfMonth = $monthDate->copy()->startOfMonth();
        $endOfMonth = $monthDate->copy()->endOfMonth();
        
        $total = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        $transactionCount = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->count();
        
        // Get top categories for this month
        $topCategories = Transaction::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => ucwords(str_replace('_', ' ', $item->category)),
                    'amount' => round($item->total, 2),
                ];
            });

        $bodyLines = [];
        $bodyLines[] = "Total Expenses: **€" . number_format($total, 2) . "**";
        $bodyLines[] = "Transactions: **" . $transactionCount . "**";
        
        if ($topCategories->isNotEmpty()) {
            $bodyLines[] = "";
            $bodyLines[] = "Top Categories:";
            foreach ($topCategories as $idx => $cat) {
                $bodyLines[] = ($idx + 1) . ". " . $cat['category'] . ": €" . number_format($cat['amount'], 2);
            }
        } else {
            $bodyLines[] = "";
            $bodyLines[] = "No expenses recorded for this month.";
        }
        
        Notification::make()
            ->title($monthDate->format('F Y') . ' Expense Details')
            ->body(implode("\n", $bodyLines))
            ->info()
            ->persistent()
            ->send();
    }

    public function getFooterHtml(): ?\Illuminate\Contracts\Support\Htmlable
    {
        $widgetId = $this->getId();
        
        return new \Illuminate\Support\HtmlString('
            <script>
                (function() {
                    let chartInstance = null;
                    let isSetup = false;
                    
                    function setupChartInteractions() {
                        if (isSetup) return;
                        
                        const widgetElement = document.querySelector("[wire\\\\:id=\\\"' . $widgetId . '\\\"]");
                        if (!widgetElement) {
                            setTimeout(setupChartInteractions, 200);
                            return;
                        }
                        
                        const canvas = widgetElement.querySelector("canvas");
                        if (!canvas) {
                            setTimeout(setupChartInteractions, 200);
                            return;
                        }
                        
                        // Try multiple ways to access the chart instance
                        chartInstance = canvas.chart || (canvas._chart || (window.Chart && Chart.getChart(canvas)));
                        
                        if (!chartInstance) {
                            setTimeout(setupChartInteractions, 300);
                            return;
                        }
                        
                        isSetup = true;
                        
                        // Update cursor on hover
                        canvas.addEventListener("mousemove", function(event) {
                            const points = chartInstance.getElementsAtEventForMode(event, "nearest", { intersect: true }, true);
                            canvas.style.cursor = points.length > 0 && points[0].datasetIndex === 0 ? "pointer" : "default";
                        });
                        
                        // Handle clicks
                        canvas.addEventListener("click", function(event) {
                            const points = chartInstance.getElementsAtEventForMode(event, "nearest", { intersect: true }, true);
                            if (points.length > 0 && points[0].datasetIndex === 0) {
                                const monthIndex = points[0].index;
                                // Try Livewire 3 first, then Livewire 2
                                if (window.Livewire) {
                                    if (window.Livewire.find) {
                                        // Livewire 2
                                        const component = window.Livewire.find("' . $widgetId . '");
                                        if (component) {
                                            component.call("showMonthDetails", monthIndex);
                                        }
                                    } else if (window.$wire) {
                                        // Livewire 3
                                        window.$wire.call("showMonthDetails", monthIndex);
                                    } else if (window.Livewire.all) {
                                        // Try to find component in Livewire 3
                                        const components = window.Livewire.all();
                                        for (let id in components) {
                                            if (components[id].__instance?.id === "' . $widgetId . '") {
                                                components[id].call("showMonthDetails", monthIndex);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                    
                    // Setup on multiple events
                    if (document.readyState === "loading") {
                        document.addEventListener("DOMContentLoaded", setupChartInteractions);
                    } else {
                        setupChartInteractions();
                    }
                    
                    document.addEventListener("livewire:init", setupChartInteractions);
                    document.addEventListener("livewire:load", setupChartInteractions);
                    
                    // Also try after a delay to catch late-loading charts
                    setTimeout(setupChartInteractions, 1000);
                })();
            </script>
        ');
    }
}