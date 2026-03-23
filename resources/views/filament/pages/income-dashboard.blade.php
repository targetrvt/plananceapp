@php
    $startDateFormatted = \Carbon\Carbon::parse($this->startDate)->format('M d');
    $endDateFormatted = \Carbon\Carbon::parse($this->endDate)->format('M d, Y');
    $currentYear = \Carbon\Carbon::parse($this->startDate)->year;

    $timeframeLabels = [
        'week' => __('messages.dashboard.income.period_labels.week'),
        'month' => __('messages.dashboard.income.period_labels.month'),
        'quarter' => __('messages.dashboard.income.period_labels.quarter'),
        'year' => __('messages.dashboard.income.period_labels.year'),
        'custom' => __('messages.dashboard.income.period_labels.custom')
    ];

    $categoryBreakdown = $this->getCategoryBreakdown();
    $monthlyTrend = $this->getMonthlyTrend();
    $dailyTrend = $this->getDailyTrend();
    $totalIncome = $this->getTotalIncome();
    $averageDailyIncome = $this->getAverageIncomePerDay();
    $recentTransactions = $this->getRecentTransactions(5);
    $largestIncome = $this->getLargestIncome();
@endphp

<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/planance-income-dashboard.css') }}">

    <div
        class="income-dashboard dashboard-container"
        x-data="incomeDashboard({
            categoryBreakdown: {{ json_encode($categoryBreakdown) }},
            monthlyTrend: {{ json_encode($monthlyTrend) }},
            dailyTrend: {{ json_encode($dailyTrend) }},
            totalIncome: {{ $totalIncome }},
            averageDailyIncome: {{ $averageDailyIncome }},
            recentTransactions: {{ json_encode($recentTransactions) }},
            largestIncome: {{ json_encode($largestIncome) }},
            timeframe: '{{ $this->timeframe }}',
            category: '{{ $this->category }}',
            startDate: '{{ $this->startDate }}',
            endDate: '{{ $this->endDate }}',
            darkMode: document.documentElement.classList.contains('dark')
        })"
        x-init="$nextTick(() => {
            document.addEventListener('echart-loaded', initializeCharts);
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                darkMode = e.matches;
                if (typeof refreshCharts === 'function') refreshCharts();
            });
        })"
        wire:key="income-dashboard-{{ $this->timeframe }}-{{ $this->category }}-{{ $this->startDate }}-{{ $this->endDate }}"
    >
    <div class="dashboard-header flex justify-between items-center flex-wrap gap-4 mb-6">
        <div>
            <p class="dashboard-tagline text-sm">{{ __('messages.dashboard.income.tagline') }}</p>
            <div class="dashboard-period flex items-center gap-2 mt-1">
                @if($this->timeframe === 'month' || $this->timeframe === 'quarter' || $this->timeframe === 'year')
                    <button type="button" wire:click="previousPeriod" class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                        <x-heroicon-o-chevron-left class="w-5 h-5" />
                    </button>
                    <span class="dashboard-subtitle text-sm text-gray-600 dark:text-gray-300 min-w-[120px] text-center">{{ $this->getPeriodLabel() }}</span>
                    <button type="button" wire:click="nextPeriod" class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                        <x-heroicon-o-chevron-right class="w-5 h-5" />
                    </button>
                    <button type="button" wire:click="resetToCurrentPeriod" class="text-xs text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 ml-2 transition-colors" title="Reset to current period">
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                    </button>
                @else
                    <span class="dashboard-subtitle text-sm text-gray-600 dark:text-gray-300">
                        {{ $startDateFormatted }} - {{ $endDateFormatted }}
                        @if($this->category !== 'all')
                            · {{ __('messages.dashboard.income.filter.category') }}: {{ __('messages.categories.income.' . $this->category) }}
                        @endif
                    </span>
                @endif
            </div>
        </div>
        <div class="dashboard-actions flex items-center gap-2 flex-wrap">
            @if($this->timeframe !== 'custom')
                <div class="timeframe-selector flex rounded-md overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                    <button wire:click="updateTimeframe('week')" class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'week' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ $timeframeLabels['week'] }}</button>
                    <button wire:click="updateTimeframe('month')" class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'month' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ $timeframeLabels['month'] }}</button>
                    <button wire:click="updateTimeframe('quarter')" class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'quarter' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ $timeframeLabels['quarter'] }}</button>
                    <button wire:click="updateTimeframe('year')" class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'year' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">{{ $timeframeLabels['year'] }}</button>
                </div>
            @endif
        </div>
    </div>

    <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="stat-card stat-primary bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="stat-content flex justify-between items-start">
                <div class="stat-info">
                    <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.income.stats.total_income') }}</div>
                    <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($totalIncome, 2) }}</div>
                    <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $timeframeLabels[$this->timeframe] }}</div>
                </div>
                <div class="stat-icon bg-primary-100 dark:bg-primary-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-primary-600 dark:text-primary-400">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card stat-success bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="stat-content flex justify-between items-start">
                <div class="stat-info">
                    <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.income.stats.average_daily') }}</div>
                    <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($averageDailyIncome, 2) }}</div>
                    <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.dashboard.income.stats.per_day_average') }}</div>
                </div>
                <div class="stat-icon bg-emerald-100 dark:bg-emerald-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-emerald-600 dark:text-emerald-400">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="stat-card stat-warning bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="stat-content flex justify-between items-start">
                <div class="stat-info">
                    <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.income.stats.categories') }}</div>
                    <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">{{ count($categoryBreakdown) }}</div>
                    <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.dashboard.income.stats.income_categories_used') }}</div>
                </div>
                <div class="stat-icon bg-amber-100 dark:bg-amber-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-amber-600 dark:text-amber-400">
                        <circle cx="12" cy="12" r="10" /><line x1="12" x2="12" y1="8" y2="16" /><line x1="8" x2="16" y1="12" y2="12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="charts-grid grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="chart-header mb-4">
                <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.charts.category_breakdown') }}</h3>
            </div>
            @if(count($categoryBreakdown) > 0)
                <div class="chart-container h-64" id="categoryPieChart"></div>
                <div class="category-list mt-4 space-y-3">
                    @foreach($categoryBreakdown->take(5) as $index => $item)
                        <div class="category-item">
                            <div class="category-label flex items-center justify-between mb-1">
                                <div class="flex items-center">
                                    <div class="category-color w-3 h-3 rounded-full mr-2" x-data="{ color: getCategoryColor({{ $index }}) }" :style="{ backgroundColor: color }"></div>
                                    <div class="category-name text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.categories.income.' . $item['category']) }}</div>
                                </div>
                                <div class="category-value text-sm text-gray-600 dark:text-gray-400">
                                    <span>€{{ number_format($item['total'], 2) }}</span>
                                    <span class="text-xs ml-1">({{ number_format($item['percentage'], 1) }}%)</span>
                                </div>
                            </div>
                            <div class="progress-container bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="progress-bar h-full rounded-full" :style="{ width: '{{ $item['percentage'] }}%', backgroundColor: getCategoryColor({{ $index }}) }"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4"><circle cx="12" cy="12" r="10" /><line x1="8" x2="16" y1="12" y2="12" /><line x1="12" x2="12" y1="8" y2="16" /></svg>
                    <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.charts.no_categories') }}</h3>
                    <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.charts.no_categories_desc') }}</p>
                </div>
            @endif
        </div>
        <div class="charts-col lg:col-span-2 flex flex-col gap-6">
            <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="chart-header mb-4">
                    <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.charts.monthly_trend') }} ({{ $currentYear }})</h3>
                </div>
                @if(count($monthlyTrend) > 0)
                    <div class="chart-container h-64" id="monthlyTrendChart"></div>
                @else
                    <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12" /></svg>
                        <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.charts.no_monthly_data') }}</h3>
                        <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.charts.no_monthly_desc') }}</p>
                    </div>
                @endif
            </div>
            <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="chart-header mb-4">
                    <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.charts.daily_income') }}</h3>
                </div>
                @if(count($dailyTrend) > 0)
                    <div class="chart-container h-64" id="dailyTrendChart"></div>
                @else
                    <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" x2="16" y1="2" y2="6" /><line x1="8" x2="8" y1="2" y2="6" /><line x1="3" x2="21" y1="10" y2="10" /></svg>
                        <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.charts.no_daily_data') }}</h3>
                        <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.charts.no_daily_desc') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="transactions-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="trans-card lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="trans-header flex justify-between items-center mb-4">
                <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.transactions.recent') }}</h3>
                @if(count($recentTransactions) > 0)
                    <a href="{{ url('/app/transactions') }}" class="trans-link flex items-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                        {{ __('messages.dashboard.income.transactions.view_all') }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 ml-1"><path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" /></svg>
                    </a>
                @endif
            </div>
            @if(count($recentTransactions) > 0)
                <div class="overflow-x-auto">
                    <table class="trans-table w-full text-left">
                        <thead>
                            <tr>
                                <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.transactions.date') }}</th>
                                <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.transactions.description') }}</th>
                                <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.transactions.category') }}</th>
                                <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 text-right">{{ __('messages.dashboard.income.transactions.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentTransactions->take(8) as $transaction)
                                <tr>
                                    <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                    <td class="py-3 text-sm text-gray-900 dark:text-white">{{ $transaction->description ?? '-' }}</td>
                                    <td class="py-3">
                                        <span :class="getCategoryClass('{{ $transaction->category }}')" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">{{ __('messages.categories.income.' . $transaction->category) }}</span>
                                    </td>
                                    <td class="py-3 text-sm font-medium text-gray-900 dark:text-white text-right">€{{ number_format($transaction->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>
                    <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.transactions.no_transactions') }}</h3>
                    <p class="empty-text text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('messages.dashboard.income.transactions.no_transactions_desc') }}</p>
                    <a href="{{ url('/app/transactions/create') }}" class="empty-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">{{ __('messages.dashboard.income.transactions.add_income') }}</a>
                </div>
            @endif
        </div>
        <div class="flex flex-col gap-6">
            <div class="trans-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="trans-header mb-4">
                    <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.transactions.largest_income') }}</h3>
                </div>
                @if(count($largestIncome) > 0)
                    <div class="expense-list divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($largestIncome as $item)
                            <div class="expense-item py-3">
                                <div class="expense-info flex justify-between mb-1">
                                    <div class="expense-desc text-sm font-medium text-gray-900 dark:text-white truncate max-w-[70%]">{{ $item->description ?? '-' }}</div>
                                    <div class="expense-amount text-sm font-medium text-gray-900 dark:text-white">€{{ number_format($item->amount, 2) }}</div>
                                </div>
                                <div class="expense-details flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span :class="getCategoryClass('{{ $item->category }}')" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">{{ __('messages.categories.income.' . $item->category) }}</span>
                                    <span>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state flex flex-col items-center justify-center py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon w-10 h-10 text-gray-400 dark:text-gray-600 mb-3"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>
                        <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.transactions.no_income') }}</h3>
                        <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.income.transactions.no_income_desc') }}</p>
                    </div>
                @endif
            </div>
            <div class="trans-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="trans-header mb-4">
                    <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.income.tips.title') }}</h3>
                </div>
                <div class="tips-list space-y-4">
                    <div class="tip-item bg-primary-50 dark:bg-primary-900/30 rounded-lg p-4">
                        <div class="flex">
                            <div class="tip-icon bg-primary-100 dark:bg-primary-800 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-primary-600 dark:text-primary-400"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.tips.track_sources.title') }}</h4>
                                <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.income.tips.track_sources.description') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="tip-item bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                        <div class="flex">
                            <div class="tip-icon bg-blue-100 dark:bg-blue-800 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-blue-600 dark:text-blue-400"><rect x="3" y="11" width="18" height="11" rx="2" ry="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.tips.side_income.title') }}</h4>
                                <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.income.tips.side_income.description') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="tip-item bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                        <div class="flex">
                            <div class="tip-icon bg-purple-100 dark:bg-purple-800 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-purple-600 dark:text-purple-400"><circle cx="12" cy="12" r="10" /><polyline points="12 6 12 12 16 14" /></svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.income.tips.recurring.title') }}</h4>
                                <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.income.tips.recurring.description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="{{ asset('js/planance-income-dashboard.js') }}"></script>
</x-filament-panels::page>
