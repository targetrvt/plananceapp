<?php

namespace App\Filament\GlobalSearch;

use App\Filament\Pages\BudgetsDashboard;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ExpensesDashboard;
use App\Filament\Pages\FinancialGoalsDashboard;
use App\Filament\Pages\IncomeDashboard;
use App\Filament\Pages\MonthlySubscriptionsDashboard;
use App\Filament\Pages\PricingPlansPage;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AppGlobalSearchProvider implements GlobalSearchProvider
{
    public function __construct(
        protected DefaultGlobalSearchProvider $defaultProvider
    ) {}

    public function getResults(string $query): ?GlobalSearchResults
    {
        $query = trim($query);

        if ($query === '') {
            return null;
        }

        $default = $this->defaultProvider->getResults($query);
        $pageHits = $this->matchingPages($query);

        if ($pageHits->isEmpty()) {
            return $default;
        }

        $merged = GlobalSearchResults::make();

        foreach ($default->getCategories() as $name => $groupResults) {
            $merged->category($name, $groupResults);
        }

        $merged->category(__('filament.global_search.pages'), $pageHits->all());

        return $merged;
    }

    /**
     * @return Collection<int, GlobalSearchResult>
     */
    protected function matchingPages(string $search): Collection
    {
        $words = array_values(array_filter(
            preg_split('/\s+/', Str::lower($search)) ?: [],
            fn (string $w): bool => $w !== ''
        ));

        if ($words === []) {
            return Collection::make();
        }

        $definitions = [
            [
                'class' => Dashboard::class,
                'strings' => [
                    Str::lower((string) Dashboard::getNavigationLabel()),
                    Str::lower((string) __('dashboard.title')),
                    'home',
                    'dashboard',
                ],
            ],
            [
                'class' => IncomeDashboard::class,
                'strings' => [
                    Str::lower((string) IncomeDashboard::getNavigationLabel()),
                    Str::lower((string) __('income-dashboard.title')),
                    'income',
                    'revenue',
                    'salary',
                ],
            ],
            [
                'class' => ExpensesDashboard::class,
                'strings' => [
                    Str::lower((string) ExpensesDashboard::getNavigationLabel()),
                    Str::lower((string) __('expenses-dashboard.title')),
                    'expense',
                    'spending',
                ],
            ],
            [
                'class' => BudgetsDashboard::class,
                'strings' => [
                    Str::lower((string) BudgetsDashboard::getNavigationLabel()),
                    Str::lower((string) __('budgets-dashboard.title')),
                    'budget',
                ],
            ],
            [
                'class' => FinancialGoalsDashboard::class,
                'strings' => [
                    Str::lower((string) FinancialGoalsDashboard::getNavigationLabel()),
                    Str::lower((string) __('financial-goals-dashboard.title')),
                    'goal',
                    'savings',
                ],
            ],
            [
                'class' => MonthlySubscriptionsDashboard::class,
                'strings' => [
                    Str::lower((string) MonthlySubscriptionsDashboard::getNavigationLabel()),
                    Str::lower((string) __('monthly-subscriptions-dashboard.title')),
                    'subscription',
                    'recurring',
                ],
            ],
            [
                'class' => PricingPlansPage::class,
                'strings' => [
                    Str::lower((string) PricingPlansPage::getNavigationLabel()),
                    Str::lower((string) __('messages.navigation.pricing')),
                    'pricing',
                    'plan',
                    'billing',
                    'stripe',
                ],
            ],
        ];

        $out = Collection::make();

        foreach ($definitions as $def) {
            if (! $this->stringsMatchAllWords($words, $def['strings'])) {
                continue;
            }

            /** @var class-string<\Filament\Pages\Page> $class */
            $class = $def['class'];

            if (! $class::canAccess()) {
                continue;
            }

            $out->push(new GlobalSearchResult(
                title: $class::getNavigationLabel(),
                url: $class::getUrl(),
            ));
        }

        return $out;
    }

    /**
     * @param  array<int, string>  $words
     * @param  array<int, string>  $strings
     */
    protected function stringsMatchAllWords(array $words, array $strings): bool
    {
        foreach ($words as $word) {
            $matched = false;

            foreach ($strings as $haystack) {
                if ($haystack !== '' && str_contains($haystack, $word)) {
                    $matched = true;

                    break;
                }
            }

            if (! $matched) {
                return false;
            }
        }

        return true;
    }
}
