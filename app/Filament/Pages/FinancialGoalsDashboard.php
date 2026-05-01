<?php

namespace App\Filament\Pages;

use App\Filament\Resources\FinancialGoalResource;
use App\Models\FinancialGoal;
use App\Services\RecordGoalContribution;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class FinancialGoalsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?string $slug = 'financial-goals-dashboard';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.financial-goals-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('financial-goals-dashboard.navigation.label');
    }

    public function getTitle(): string
    {
        return __('financial-goals-dashboard.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Overview';
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getGoalSummaries()
    {
        return FinancialGoal::query()
            ->where('user_id', auth()->id())
            ->orderBy('target_date')
            ->get()
            ->map(function (FinancialGoal $goal) {
                $target = (float) $goal->target_amount;
                $current = (float) $goal->current_amount;
                $progressPct = $target > 0 ? ($current / $target) * 100 : 0.0;
                $remaining = max(0, $target - $current);
                $isComplete = $current >= $target;

                $targetDateStr = $goal->target_date?->toDateString();

                $isOverdue = false;
                $daysRemaining = 0;
                if ($targetDateStr) {
                    $isOverdue = ! $isComplete
                        && Carbon::parse($targetDateStr)->lt(Carbon::today());

                    $targetDay = Carbon::parse($targetDateStr)->startOfDay();
                    $today = Carbon::today()->startOfDay();
                    if ($targetDay->greaterThan($today)) {
                        $daysRemaining = (int) $today->diffInDays($targetDay);
                    } elseif ($targetDay->equalTo($today)) {
                        $daysRemaining = 0;
                    } else {
                        $daysRemaining = -((int) $targetDay->diffInDays($today));
                    }
                }

                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'target_amount' => $target,
                    'current_amount' => $current,
                    'remaining' => $remaining,
                    'progress' => round(min($progressPct, 999), 1),
                    'target_date' => $targetDateStr,
                    'days_remaining' => $daysRemaining,
                    'is_complete' => $isComplete,
                    'is_overdue' => $isOverdue,
                ];
            });
    }

    public function getTotalTargetAmount(): float
    {
        return (float) FinancialGoal::query()
            ->where('user_id', auth()->id())
            ->sum('target_amount');
    }

    public function getTotalSavedAmount(): float
    {
        return (float) FinancialGoal::query()
            ->where('user_id', auth()->id())
            ->sum('current_amount');
    }

    public function getTotalRemainingToTargets(): float
    {
        return (float) $this->getGoalSummaries()->sum('remaining');
    }

    public function getOverdueGoalsCount(): int
    {
        return $this->getGoalSummaries()->where('is_overdue', true)->count();
    }

    public function getOverallSavedProgressPercent(): float
    {
        $totalTarget = $this->getTotalTargetAmount();
        if ($totalTarget <= 0) {
            return 0.0;
        }

        return round(min(999, ($this->getTotalSavedAmount() / $totalTarget) * 100), 1);
    }

    protected function quickAddToGoalAction(): Action
    {
        return Action::make('quickAddToGoal')
            ->label(__('financial-goals-dashboard.quick_add.trigger_label'))
            ->modalHeading(__('financial-goals-dashboard.quick_add.modal_heading'))
            ->modalDescription(__('financial-goals-dashboard.quick_add.modal_description'))
            ->modalSubmitActionLabel(__('financial-goals-dashboard.quick_add.submit'))
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->form([
                Hidden::make('goal_id'),
                Placeholder::make('goal_display')
                    ->label(__('financial-goals-dashboard.quick_add.modal_goal_label'))
                    ->content(function (Get $get): string {
                        $id = $get('goal_id');
                        if (! $id) {
                            return '';
                        }

                        return (string) FinancialGoal::query()
                            ->whereKey($id)
                            ->where('user_id', auth()->id())
                            ->value('name');
                    }),
                TextInput::make('amount')
                    ->label(__('financial-goals-dashboard.quick_add.amount_label'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->prefix('EUR'),
                DatePicker::make('date')
                    ->label(__('financial-goals-dashboard.quick_add.date_label'))
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->label(__('financial-goals-dashboard.quick_add.note_label'))
                    ->rows(2)
                    ->maxLength(500),
            ])
            ->mountUsing(function ($form, array $arguments = []): void {
                if ($form === null) {
                    return;
                }

                $form->fill([
                    'goal_id' => $arguments['goalId'] ?? null,
                    'amount' => null,
                    'date' => now()->toDateString(),
                    'description' => '',
                ]);
            })
            ->action(function (array $data, array $arguments): void {
                $goalId = (int) ($data['goal_id'] ?? $arguments['goalId'] ?? 0);
                $goal = FinancialGoal::query()
                    ->whereKey($goalId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                RecordGoalContribution::record(
                    $goal,
                    (float) $data['amount'],
                    $data['date'],
                    filled($data['description'] ?? null) ? (string) $data['description'] : null
                );

                Notification::make()
                    ->title(__('financial-goals-dashboard.quick_add.notification_title'))
                    ->success()
                    ->send();

                $this->redirect(static::getUrl());
            });
    }

    protected function quickWithdrawFromGoalAction(): Action
    {
        return Action::make('quickWithdrawFromGoal')
            ->label(__('financial-goals-dashboard.withdraw.trigger_label'))
            ->modalHeading(__('financial-goals-dashboard.withdraw.modal_heading'))
            ->modalDescription(__('financial-goals-dashboard.withdraw.modal_description'))
            ->modalSubmitActionLabel(__('financial-goals-dashboard.withdraw.submit'))
            ->icon('heroicon-o-minus-circle')
            ->color('warning')
            ->form([
                Hidden::make('goal_id'),
                Placeholder::make('goal_display')
                    ->label(__('financial-goals-dashboard.quick_add.modal_goal_label'))
                    ->content(function (Get $get): string {
                        $id = $get('goal_id');
                        if (! $id) {
                            return '';
                        }

                        return (string) FinancialGoal::query()
                            ->whereKey($id)
                            ->where('user_id', auth()->id())
                            ->value('name');
                    }),
                TextInput::make('amount')
                    ->label(__('financial-goals-dashboard.withdraw.amount_label'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->prefix('EUR')
                    ->maxValue(function (Get $get): float {
                        $id = $get('goal_id');
                        if (! $id) {
                            return 0.0;
                        }

                        return (float) FinancialGoal::query()
                            ->whereKey($id)
                            ->where('user_id', auth()->id())
                            ->value('current_amount');
                    }),
                DatePicker::make('date')
                    ->label(__('financial-goals-dashboard.quick_add.date_label'))
                    ->required()
                    ->default(now()),
                Textarea::make('description')
                    ->label(__('financial-goals-dashboard.quick_add.note_label'))
                    ->rows(2)
                    ->maxLength(500),
            ])
            ->mountUsing(function ($form, array $arguments = []): void {
                if ($form === null) {
                    return;
                }

                $form->fill([
                    'goal_id' => $arguments['goalId'] ?? null,
                    'amount' => null,
                    'date' => now()->toDateString(),
                    'description' => '',
                ]);
            })
            ->action(function (array $data, array $arguments): void {
                $goalId = (int) ($data['goal_id'] ?? $arguments['goalId'] ?? 0);
                $goal = FinancialGoal::query()
                    ->whereKey($goalId)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

                try {
                    RecordGoalContribution::withdraw(
                        $goal,
                        (float) $data['amount'],
                        $data['date'],
                        filled($data['description'] ?? null) ? (string) $data['description'] : null
                    );
                } catch (\InvalidArgumentException $e) {
                    Notification::make()
                        ->title($e->getMessage())
                        ->danger()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title(__('financial-goals-dashboard.withdraw.notification_title'))
                    ->success()
                    ->send();

                $this->redirect(static::getUrl());
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_goal')
                ->label(__('financial-goals-dashboard.actions.create_goal.label'))
                ->url(FinancialGoalResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
            Action::make('manage_goals')
                ->label(__('financial-goals-dashboard.actions.manage_goals.label'))
                ->url(FinancialGoalResource::getUrl('index'))
                ->icon('heroicon-o-rectangle-stack')
                ->color('gray'),
        ];
    }
}
