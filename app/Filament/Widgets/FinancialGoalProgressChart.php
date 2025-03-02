<?php

namespace App\Filament\Widgets;

use App\Models\FinancialGoal;
use Filament\Widgets\ChartWidget;

class FinancialGoalProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Goal Progress';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $goals = FinancialGoal::where('user_id', auth()->id())->get();

        $progressData = [];
        $labels = [];
        $colors = [];

        foreach ($goals as $goal) {
            $percentage = $goal->target_amount > 0 
                ? ($goal->current_amount / $goal->target_amount) * 100
                : 0;

            // Completed portion
            $progressData[] = $percentage;
            $labels[] = $goal->name . ' (Completed)';
            $colors[] = '#4bc0c0'; // Green for completed
            
            // Remaining portion
            $progressData[] = 100 - $percentage;
            $labels[] = $goal->name . ' (Remaining)';
            $colors[] = '#ff6384'; // Red for remaining
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Progress (%)',
                    'data' => $progressData,
                    'backgroundColor' => $colors,
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => function($context) {
                            $label = $this->data['labels'][$context->dataIndex];
                            $value = number_format($context->parsed, 2) . '%';
                            return "$label: $value";
                        }
                    ]
                ],
                'legend' => [
                    'position' => 'bottom',
                ]
            ]
        ];
    }
}