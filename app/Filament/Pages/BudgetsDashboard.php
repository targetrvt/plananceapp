<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BudgetResource;
use App\Models\Budget;
use Filament\Actions\Action;
use Filament\Pages\Page;

class BudgetsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    protected static ?string $slug = 'budgets-dashboard';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.budgets-dashboard';

    public static function getNavigationLabel(): string
    {
        return __('budgets-dashboard.navigation.label');
    }

    public function getTitle(): string
    {
        return __('budgets-dashboard.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Overview';
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getBudgetSummaries()
    {
        return Budget::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Budget $budget) {
                $amount = (float) $budget->amount;
                $spent = $budget->spentAmount();
                $remaining = max(0, $amount - $spent);
                $usage = $budget->usagePercentage();

                return [
                    'id' => $budget->id,
                    'name' => $budget->name,
                    'amount' => $amount,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'usage' => $usage,
                    'start_date' => $budget->start_date,
                    'end_date' => $budget->end_date,
                    'is_over' => $usage >= 100,
                ];
            });
    }

    public function getTotalBudgetedAmount(): float
    {
        return (float) Budget::query()
            ->where('user_id', auth()->id())
            ->sum('amount');
    }

    public function getTotalSpentAmount(): float
    {
        return $this->getBudgetSummaries()->sum('spent');
    }

    public function getTotalRemainingAmount(): float
    {
        return max(0, $this->getTotalBudgetedAmount() - $this->getTotalSpentAmount());
    }

    public function getExceededBudgetsCount(): int
    {
        return $this->getBudgetSummaries()->where('is_over', true)->count();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_budget')
                ->label(__('budgets-dashboard.actions.create_budget.label'))
                ->url(BudgetResource::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->color('primary'),
            Action::make('manage_budgets')
                ->label(__('budgets-dashboard.actions.manage_budgets.label'))
                ->url(BudgetResource::getUrl('index'))
                ->icon('heroicon-o-rectangle-stack')
                ->color('gray'),
        ];
    }
}
