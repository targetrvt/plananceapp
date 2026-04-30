<x-filament-panels::page>
    @php
        $budgetSummaries = $this->getBudgetSummaries();
        $totalBudgeted = $this->getTotalBudgetedAmount();
        $totalSpent = $this->getTotalSpentAmount();
        $totalRemaining = $this->getTotalRemainingAmount();
        $exceededCount = $this->getExceededBudgetsCount();
    @endphp

    @push('styles')
        <style>
            .budget-stat-card {
                position: relative;
                overflow: hidden;
                border-radius: 1rem;
                border: 1px solid rgba(229, 231, 235, 0.8);
                background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9));
            }

            .dark .budget-stat-card {
                border-color: rgba(71, 85, 105, 0.6);
                background: linear-gradient(145deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9));
            }

            .budget-stat-card::after {
                content: "";
                position: absolute;
                right: -32px;
                top: -32px;
                width: 120px;
                height: 120px;
                border-radius: 9999px;
                background: rgba(99, 102, 241, 0.08);
            }

            .budget-item-card {
                border-radius: 1rem;
                border: 1px solid rgba(229, 231, 235, 0.9);
                background: rgba(255, 255, 255, 0.9);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                position: relative;
                overflow: hidden;
            }

            .budget-item-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px -8px rgba(15, 23, 42, 0.2);
                border-color: rgba(99, 102, 241, 0.4);
            }

            .dark .budget-item-card {
                border-color: rgba(71, 85, 105, 0.55);
                background: rgba(30, 41, 59, 0.65);
            }

            .budget-item-card::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                border-radius: 9999px;
                background: rgba(16, 185, 129, 0.9);
            }

            .budget-item-card.is-warning::before {
                background: rgba(245, 158, 11, 0.95);
            }

            .budget-item-card.is-over::before {
                background: rgba(244, 63, 94, 0.95);
            }

            .budget-mini-stat {
                border: 1px solid rgba(229, 231, 235, 0.8);
                border-radius: 0.7rem;
                padding: 0.55rem 0.7rem;
                background: rgba(248, 250, 252, 0.85);
                min-height: 58px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 0.28rem;
            }

            .dark .budget-mini-stat {
                border-color: rgba(71, 85, 105, 0.5);
                background: rgba(15, 23, 42, 0.45);
            }

            .budget-mini-stat-label {
                font-size: 0.67rem;
                line-height: 1;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin: 0;
            }

            .budget-mini-stat-value {
                font-size: 0.92rem;
                line-height: 1.15;
                font-weight: 700;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
            }

            .budget-metrics-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.5rem;
                width: 100%;
            }

            @media (min-width: 1024px) {
                .budget-metrics-grid {
                    grid-template-columns: 1fr 1fr 1fr 0.5fr;
                    min-width: 0;
                    width: 100%;
                }
            }

            .budget-track {
                height: 0.5rem;
                border-radius: 9999px;
                background: linear-gradient(to right, rgba(226, 232, 240, 0.9), rgba(203, 213, 225, 0.9));
                overflow: hidden;
            }

            .dark .budget-track {
                background: linear-gradient(to right, rgba(51, 65, 85, 0.9), rgba(71, 85, 105, 0.9));
            }

            .budget-progress-fill {
                display: block;
                height: 100%;
                border-radius: 9999px;
                transition: width 0.25s ease;
            }

            .budget-status-badge {
                border-width: 1px;
                border-style: solid;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
                letter-spacing: 0.01em;
            }
        </style>
    @endpush

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <x-filament::section class="budget-stat-card">
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.stats.total_budgeted') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">EUR {{ number_format($totalBudgeted, 2) }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center">
                        <x-heroicon-o-wallet class="h-7 w-7 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="budget-stat-card">
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.stats.total_spent') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">EUR {{ number_format($totalSpent, 2) }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                        <x-heroicon-o-chart-bar-square class="h-7 w-7 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="budget-stat-card">
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.stats.total_remaining') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">EUR {{ number_format($totalRemaining, 2) }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                        <x-heroicon-o-arrow-trending-up class="h-7 w-7 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section class="budget-stat-card">
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.stats.exceeded_budgets') }}</div>
                        <div class="mt-1 text-2xl font-semibold {{ $exceededCount > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                            {{ $exceededCount }}
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-xl {{ $exceededCount > 0 ? 'bg-rose-100 dark:bg-rose-900/40' : 'bg-emerald-100 dark:bg-emerald-900/40' }} flex items-center justify-center">
                        <x-heroicon-o-exclamation-triangle class="h-7 w-7 {{ $exceededCount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}" />
                    </div>
                </div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('budgets-dashboard.sections.budgets_overview') }}</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.sections.active_count', ['count' => $budgetSummaries->count()]) }}</span>
            </div>

            @if($budgetSummaries->isNotEmpty())
                <div class="space-y-4">
                    @foreach($budgetSummaries as $budget)
                        @php
                            $usage = round($budget['usage'], 1);
                            $progressWidth = $usage > 0 ? max(2, min(100, $usage)) : 0;
                            $progressColor = $usage >= 100
                                ? '#f43f5e'
                                : ($usage >= 90 ? '#f59e0b' : '#10b981');
                            $badgeColor = $usage >= 100
                                ? '#e11d48'
                                : ($usage >= 90 ? '#f59e0b' : '#059669');
                            $cardStateClass = $budget['is_over'] ? 'is-over' : ($usage >= 90 ? 'is-warning' : '');
                            $statusLabel = $budget['is_over']
                                ? __('budgets-dashboard.status.over_limit')
                                : ($usage >= 90
                                    ? __('budgets-dashboard.status.warning')
                                    : __('budgets-dashboard.status.ok'));
                        @endphp

                        <div class="budget-item-card {{ $cardStateClass }} p-4 pl-5">
                            <div class="flex flex-col gap-3 lg:grid lg:grid-cols-[minmax(320px,1.35fr)_minmax(0,1fr)] lg:items-start lg:gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="text-base font-semibold text-gray-900 dark:text-white leading-tight break-words">{{ $budget['name'] }}</div>
                                        <span class="budget-status-badge inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold shrink-0 text-white" style="background-color: {{ $badgeColor }}; border-color: {{ $badgeColor }};">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1.5">
                                        <x-heroicon-o-calendar-days class="h-3.5 w-3.5" />
                                        {{ \Carbon\Carbon::parse($budget['start_date'])->format('M d, Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($budget['end_date'])->format('M d, Y') }}
                                    </div>
                                </div>

                                <div class="budget-metrics-grid">
                                    <div class="budget-mini-stat">
                                        <div class="budget-mini-stat-label text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.table.budget') }}</div>
                                        <div class="budget-mini-stat-value text-gray-900 dark:text-white">EUR {{ number_format($budget['amount'], 2) }}</div>
                                    </div>
                                    <div class="budget-mini-stat">
                                        <div class="budget-mini-stat-label text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.table.spent') }}</div>
                                        <div class="budget-mini-stat-value text-gray-900 dark:text-white">EUR {{ number_format($budget['spent'], 2) }}</div>
                                    </div>
                                    <div class="budget-mini-stat">
                                        <div class="budget-mini-stat-label text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.table.remaining') }}</div>
                                        <div class="budget-mini-stat-value text-gray-900 dark:text-white">EUR {{ number_format($budget['remaining'], 2) }}</div>
                                    </div>
                                    <div class="budget-mini-stat">
                                        <div class="budget-mini-stat-label text-gray-500 dark:text-gray-400">{{ __('budgets-dashboard.table.usage') }}</div>
                                        <div class="budget-mini-stat-value {{ $budget['is_over'] ? 'text-danger-600 dark:text-danger-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $usage }}%
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span class="uppercase tracking-wide">{{ __('budgets-dashboard.table.usage') }}</span>
                                    <span class="font-medium">{{ $usage }}%</span>
                                </div>
                                <div class="budget-track w-full">
                                    <span class="budget-progress-fill" style="width: {{ $progressWidth }}%; background-color: {{ $progressColor }};"></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center">
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('budgets-dashboard.empty.title') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('budgets-dashboard.empty.description') }}</p>
                    <a
                        href="{{ route('filament.app.resources.budgets.create') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500"
                    >
                        {{ __('budgets-dashboard.actions.create_budget.label') }}
                    </a>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
