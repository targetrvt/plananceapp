@php
    $startDateFormatted = \Carbon\Carbon::parse($this->startDate)->format('M d');
    $endDateFormatted = \Carbon\Carbon::parse($this->endDate)->format('M d, Y');
    $currentYear = \Carbon\Carbon::parse($this->startDate)->year;
    
    $timeframeLabels = [
        'week' => __('messages.dashboard.expenses.period_labels.week'),
        'month' => __('messages.dashboard.expenses.period_labels.month'),
        'quarter' => __('messages.dashboard.expenses.period_labels.quarter'),
        'year' => __('messages.dashboard.expenses.period_labels.year'),
        'custom' => __('messages.dashboard.expenses.period_labels.custom')
    ];
    
    $categoryBreakdown = $this->getCategoryBreakdown();
    $monthlyTrend = $this->getMonthlyTrend();
    $dailyTrend = $this->getDailyTrend();
    $totalExpenses = $this->getTotalExpenses();
    $averageDailyExpense = $this->getAverageExpensePerDay();
    $unhealthyExpenses = $this->getUnhealthyExpenses();
    $recentTransactions = $this->getRecentTransactions(5);
    $largestExpenses = $this->getLargestExpenses();
@endphp

<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/planance-expense-dashboard.css') }}">
    
    <div 
        class="expenses-dashboard dashboard-container"
        x-data="expensesDashboard({
            categoryBreakdown: {{ json_encode($categoryBreakdown) }},
            monthlyTrend: {{ json_encode($monthlyTrend) }},
            dailyTrend: {{ json_encode($dailyTrend) }},
            totalExpenses: {{ $totalExpenses }},
            averageDailyExpense: {{ $averageDailyExpense }},
            unhealthyExpenses: {{ $unhealthyExpenses }},
            recentTransactions: {{ json_encode($recentTransactions) }},
            largestExpenses: {{ json_encode($largestExpenses) }},
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
                if (typeof refreshCharts === 'function') {
                    refreshCharts();
                }
            });
        })"
        wire:key="dashboard-{{ $this->timeframe }}-{{ $this->category }}-{{ $this->startDate }}-{{ $this->endDate }}"
    >
    <div class="dashboard-header flex justify-between items-center flex-wrap gap-4 mb-6">
        <div>
            <div class="dashboard-period flex items-center gap-2 mt-1">
                @if($this->timeframe === 'month' || $this->timeframe === 'quarter' || $this->timeframe === 'year')
                    <button 
                        type="button" 
                        wire:click="previousPeriod" 
                        class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
                    >
                        <x-heroicon-o-chevron-left class="w-5 h-5" />
                    </button>
                    
                    <span class="dashboard-subtitle text-sm text-gray-600 dark:text-gray-300 min-w-[120px] text-center">
                        {{ $this->getPeriodLabel() }}
                    </span>
                    
                    <button 
                        type="button" 
                        wire:click="nextPeriod" 
                        class="text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 transition-colors"
                    >
                        <x-heroicon-o-chevron-right class="w-5 h-5" />
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="resetToCurrentPeriod" 
                        class="text-xs text-gray-500 hover:text-primary-500 dark:text-gray-400 dark:hover:text-primary-400 ml-2 transition-colors"
                        title="Reset to current period"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                    </button>
                @else
                    <span class="dashboard-subtitle text-sm text-gray-600 dark:text-gray-300">
                        {{ $startDateFormatted }} - {{ $endDateFormatted }}
                        @if($this->category !== 'all')
                            · {{ __('messages.dashboard.expenses.filter.category') }}: {{ ucwords(str_replace('_', ' ', $this->category)) }}
                        @endif
                    </span>
                @endif
            </div>
        </div>
        
        <div class="dashboard-actions flex items-center gap-2 flex-wrap">
            @if($this->timeframe !== 'custom')
                <div class="timeframe-selector flex rounded-md overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                    <button 
                        wire:click="updateTimeframe('week')" 
                        class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'week' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ __('messages.dashboard.expenses.period_labels.week') }}
                    </button>
                    <button 
                        wire:click="updateTimeframe('month')" 
                        class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'month' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ __('messages.dashboard.expenses.period_labels.month') }}
                    </button>
                    <button 
                        wire:click="updateTimeframe('quarter')" 
                        class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'quarter' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ __('messages.dashboard.expenses.period_labels.quarter') }}
                    </button>
                    <button 
                        wire:click="updateTimeframe('year')" 
                        class="timeframe-btn px-3 py-1.5 text-sm dark:text-gray-300 transition-colors {{ $this->timeframe === 'year' ? 'text-white bg-primary-600 dark:bg-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        {{ __('messages.dashboard.expenses.period_labels.year') }}
                    </button>
                </div>
            @endif
        </div>
    </div>
        
        <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card stat-primary bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="stat-content flex justify-between items-start">
                    <div class="stat-info">
                        <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.expenses.stats.total_expenses') }}</div>
                        <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($totalExpenses, 2) }}</div>
                        <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $timeframeLabels[$this->timeframe] }}
                        </div>
                    </div>
                    <div class="stat-icon bg-primary-100 dark:bg-primary-900/50 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-primary-600 dark:text-primary-400">
                            <rect width="20" height="14" x="2" y="5" rx="2" />
                            <line x1="2" x2="22" y1="10" y2="10" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stat-card stat-success bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="stat-content flex justify-between items-start">
                    <div class="stat-info">
                        <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.expenses.stats.average_daily') }}</div>
                        <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($averageDailyExpense, 2) }}</div>
                        <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('messages.dashboard.expenses.stats.per_day_average') }}
                        </div>
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
                        <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.expenses.stats.categories') }}</div>
                        <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">{{ count($categoryBreakdown) }}</div>
                        <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('messages.dashboard.expenses.stats.expense_categories_used') }}
                        </div>
                    </div>
                    <div class="stat-icon bg-amber-100 dark:bg-amber-900/50 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-amber-600 dark:text-amber-400">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" x2="12" y1="8" y2="16" />
                            <line x1="8" x2="16" y1="12" y2="12" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stat-card stat-danger bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="stat-content flex justify-between items-start">
                    <div class="stat-info">
                        <div class="stat-title text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('messages.dashboard.expenses.stats.unhealthy_expenses') }}</div>
                        <div class="stat-value text-2xl font-bold text-gray-900 dark:text-white">€{{ number_format($unhealthyExpenses, 2) }}</div>
                        <div class="stat-trend text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $totalExpenses > 0 ? number_format(($unhealthyExpenses / $totalExpenses) * 100, 1) : 0 }}% {{ __('messages.dashboard.expenses.stats.percentage_of_total') }}
                        </div>
                    </div>
                    <div class="stat-icon bg-rose-100 dark:bg-rose-900/50 p-3 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 text-rose-600 dark:text-rose-400">
                            <path d="M19.07 4.93A10 10 0 0 0 6.99 3.5" />
                            <path d="M4 6h.01" />
                            <path d="M2.5 10a2.5 2.5 0 0 1 5 0c0 2.5-5 2.5-5 5a2.5 2.5 0 0 0 5 0" />
                            <path d="M12.5 8a2.5 2.5 0 0 1 5 0c0 2.5-5 2.5-5 5a2.5 2.5 0 0 0 5 0" />
                            <path d="M17 17.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="charts-grid grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="chart-header mb-4">
                    <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.charts.category_breakdown') }}</h3>
                </div>
                
                @if(count($categoryBreakdown) > 0)
                    <div class="chart-container h-64" id="categoryPieChart"></div>
                    
                    <div class="category-list mt-4 space-y-3">
                        @foreach($categoryBreakdown->take(5) as $index => $item)
                            <div class="category-item">
                                <div class="category-label flex items-center justify-between mb-1">
                                    <div class="flex items-center">
                                        <div class="category-color w-3 h-3 rounded-full mr-2" x-data="{ color: getCategoryColor({{ $index }}) }" :style="{ backgroundColor: color }"></div>
                                        <div class="category-name text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.categories.expense.' . $item['category']) }}</div>
                                    </div>
                                    <div class="category-value text-sm text-gray-600 dark:text-gray-400">
                                        <span>€{{ number_format($item['total'], 2) }}</span>
                                        <span class="text-xs ml-1">({{ number_format($item['percentage'], 1) }}%)</span>
                                    </div>
                                </div>
                                
                                <div class="progress-container bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="progress-bar h-full rounded-full" x-data="{}" 
                                         :style="{ width: '{{ $item['percentage'] }}%', backgroundColor: getCategoryColor({{ $index }}) }">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="8" x2="16" y1="12" y2="12" />
                            <line x1="12" x2="12" y1="8" y2="16" />
                        </svg>
                        <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.charts.no_categories') }}</h3>
                        <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.charts.no_categories_desc') }}</p>
                    </div>
                @endif
            </div>
            
            <div class="charts-col lg:col-span-2 flex flex-col gap-6">
                <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="chart-header mb-4">
                        <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.charts.monthly_trend') }} ({{ $currentYear }})</h3>
                    </div>
                    
                    @if(count($monthlyTrend) > 0)
                        <div class="chart-container h-64" id="monthlyTrendChart"></div>
                    @else
                        <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                            <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.charts.no_monthly_data') }}</h3>
                            <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.charts.no_monthly_desc') }}</p>
                        </div>
                    @endif
                </div>
                
                <div class="chart-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="chart-header mb-4">
                        <h3 class="chart-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.charts.daily_expenses') }}</h3>
                    </div>
                    
                    @if(count($dailyTrend) > 0)
                        <div class="chart-container h-64" id="dailyTrendChart"></div>
                    @else
                        <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.charts.no_daily_data') }}</h3>
                            <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.charts.no_daily_desc') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="transactions-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="trans-card lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="trans-header flex justify-between items-center mb-4">
                    <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.transactions.recent') }}</h3>
                    @if(count($recentTransactions) > 0)
                        <a href="{{ url('/app/transactions') }}" class="trans-link flex items-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                            {{ __('messages.dashboard.expenses.transactions.view_all') }}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 ml-1">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>
                
                @if(count($recentTransactions) > 0)
                    <div class="overflow-x-auto">
                        <table class="trans-table w-full text-left">
                            <thead>
                                <tr>
                                    <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.transactions.date') }}</th>
                                    <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.transactions.description') }}</th>
                                    <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.transactions.category') }}</th>
                                    <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 text-right">{{ __('messages.dashboard.expenses.transactions.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentTransactions->take(8) as $transaction)
                                    <tr>
                                        <td class="py-3 text-sm text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                        <td class="py-3 text-sm text-gray-900 dark:text-white">{{ $transaction->description }}</td>
                                        <td class="py-3">
                                            <span x-data="{ category: '{{ $transaction->category }}' }" 
                                                  :class="getCategoryClass(category)"
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ __('messages.categories.expense.' . $transaction->category) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-sm font-medium text-gray-900 dark:text-white text-right">€{{ number_format($transaction->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state flex flex-col items-center justify-center py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon w-12 h-12 text-gray-400 dark:text-gray-600 mb-4">
                            <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
                            <path d="M12 8H5a3 3 0 0 0-3 3v1a3 3 0 0 0 3 3h7" />
                            <line x1="12" x2="12" y1="3" y2="21" />
                        </svg>
                        <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.transactions.no_transactions') }}</h3>
                        <p class="empty-text text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('messages.dashboard.expenses.transactions.no_transactions_desc') }}</p>
                        <a href="{{ url('/app/transactions/create') }}" class="empty-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-2">
                                <line x1="12" x2="12" y1="5" y2="19" />
                                <line x1="5" x2="19" y1="12" y2="12" />
                            </svg>
                            {{ __('messages.dashboard.expenses.transactions.add_expense') }}
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="flex flex-col gap-6">
                <div class="trans-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="trans-header mb-4">
                        <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.transactions.largest_expenses') }}</h3>
                    </div>
                    
                    @if(count($largestExpenses) > 0)
                        <div class="expense-list divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($largestExpenses as $expense)
                                <div class="expense-item py-3">
                                    <div class="expense-info flex justify-between mb-1">
                                        <div class="expense-desc text-sm font-medium text-gray-900 dark:text-white truncate max-w-[70%]">{{ $expense->description }}</div>
                                        <div class="expense-amount text-sm font-medium text-gray-900 dark:text-white">€{{ number_format($expense->amount, 2) }}</div>
                                    </div>
                                    <div class="expense-details flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span x-data="{ category: '{{ $expense->category }}' }" 
                                              :class="getCategoryClass(category)"
                                              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                            {{ __('messages.categories.expense.' . $expense->category) }}
                                        </span>
                                        <span>{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state flex flex-col items-center justify-center py-8 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon w-10 h-10 text-gray-400 dark:text-gray-600 mb-3">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M9 10h.01" />
                                <path d="M15 10h.01" />
                                <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                            </svg>
                            <h3 class="empty-title text-base font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.transactions.no_expenses') }}</h3>
                            <p class="empty-text text-sm text-gray-500 dark:text-gray-400">{{ __('messages.dashboard.expenses.transactions.no_expenses_desc') }}</p>
                        </div>
                    @endif
                </div>
                
                <div class="trans-card bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="trans-header mb-4">
                        <h3 class="trans-title text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.dashboard.expenses.tips.title') }}</h3>
                    </div>
                    
                    <div class="tips-list space-y-4">
                        <div class="tip-item bg-primary-50 dark:bg-primary-900/30 rounded-lg p-4">
                            <div class="flex">
                                <div class="tip-icon bg-primary-100 dark:bg-primary-800 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary-600 dark:text-primary-400">
                                        <path d="M19.21 12.04l-1.53-.11-.3-1.5A5.004 5.004 0 0 0 12.16 6c-2.76 0-5 2.24-5 5s2.24 5 5 5h.34" />
                                        <path d="M17.96 9.8C19.13 8.27 20 7.94 20 6a4 4 0 0 0-4-4c-.7 0-1.34.25-1.85.66" />
                                        <path d="M8 10h5" />
                                        <path d="M8 14h2.5" />
                                        <path d="M13.54 17.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.tips.meal_planning.title') }}</h4>
                                    <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.expenses.tips.meal_planning.description') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tip-item bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
                            <div class="flex">
                                <div class="tip-icon bg-blue-100 dark:bg-blue-800 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-blue-600 dark:text-blue-400">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.tips.public_transport.title') }}</h4>
                                    <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.expenses.tips.public_transport.description') }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tip-item bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4">
                            <div class="flex">
                                <div class="tip-icon bg-purple-100 dark:bg-purple-800 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-purple-600 dark:text-purple-400">
                                        <circle cx="12" cy="12" r="10" />
                                        <polyline points="12 6 12 12 16 14" />
                                    </svg>
                                </div>
                                <div class="tip-content">
                                    <h4 class="tip-title text-sm font-medium text-gray-900 dark:text-white mb-1">{{ __('messages.dashboard.expenses.tips.hour_rule.title') }}</h4>
                                    <p class="tip-desc text-xs text-gray-600 dark:text-gray-400">{{ __('messages.dashboard.expenses.tips.hour_rule.description') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/planance-expense-dashboard.js') }}"></script>
</x-filament-panels::page>