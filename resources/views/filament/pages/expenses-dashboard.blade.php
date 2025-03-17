@php
    $startDateFormatted = \Carbon\Carbon::parse($this->startDate)->format('M d');
    $endDateFormatted = \Carbon\Carbon::parse($this->endDate)->format('M d, Y');
    $currentYear = \Carbon\Carbon::parse($this->startDate)->year;
    
    $timeframeLabels = [
        'week' => 'This Week',
        'month' => 'This Month',
        'quarter' => 'This Quarter',
        'year' => 'This Year',
        'custom' => 'Custom Range'
    ];
@endphp

<x-filament-panels::page>
    {{-- Load CSS & JS --}}
    <link rel="stylesheet" href="{{ asset('css/planance-expense-dashboard.css') }}">
    
    <div 
        class="expenses-dashboard dashboard-container"
        x-data="expensesDashboard({
            categoryBreakdown: {{ json_encode($this->getCategoryBreakdown()) }},
            monthlyTrend: {{ json_encode($this->getMonthlyTrend()) }},
            dailyTrend: {{ json_encode($this->getDailyTrend()) }},
            totalExpenses: {{ $this->getTotalExpenses() }},
            averageDailyExpense: {{ $this->getAverageExpensePerDay() }},
            unhealthyExpenses: {{ $this->getUnhealthyExpenses() }},
            recentTransactions: {{ json_encode($this->getRecentTransactions(5)) }},
            largestExpenses: {{ json_encode($this->getLargestExpenses()) }},
            timeframe: '{{ $this->timeframe }}',
            category: '{{ $this->category }}',
            startDate: '{{ $this->startDate }}',
            endDate: '{{ $this->endDate }}'
        })"
        x-init="$nextTick(() => { document.addEventListener('echart-loaded', initializeCharts); })"
    >
        {{-- Dashboard Header --}}
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Expenses Dashboard</h1>
                <p class="dashboard-subtitle">
                    {{ $startDateFormatted }} - {{ $endDateFormatted }}
                    @if($this->category !== 'all')
                        · Category: {{ ucwords(str_replace('_', ' ', $this->category)) }}
                    @endif
                </p>
            </div>
            
            <div class="dashboard-actions">
                <div class="timeframe-selector">
                    <button @click="updateTimeframe('week')" class="timeframe-btn" :class="{'active': timeframe === 'week'}">Week</button>
                    <button @click="updateTimeframe('month')" class="timeframe-btn" :class="{'active': timeframe === 'month'}">Month</button>
                    <button @click="updateTimeframe('quarter')" class="timeframe-btn" :class="{'active': timeframe === 'quarter'}">Quarter</button>
                    <button @click="updateTimeframe('year')" class="timeframe-btn" :class="{'active': timeframe === 'year'}">Year</button>
                </div>
                
                @foreach($this->getHeaderActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </div>
        
        {{-- Stats Grid --}}
        <div class="stats-grid">
            {{-- Total Expenses --}}
            <div class="stat-card stat-primary">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-title">Total Expenses</div>
                        <div class="stat-value" x-text="formatMoney(totalExpenses)"></div>
                        <div class="stat-trend">
                            {{ $timeframeLabels[$this->timeframe] }}
                        </div>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <rect width="20" height="14" x="2" y="5" rx="2" />
                            <line x1="2" x2="22" y1="10" y2="10" />
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- Average Daily --}}
            <div class="stat-card stat-success">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-title">Average Daily</div>
                        <div class="stat-value" x-text="formatMoney(averageDailyExpense)"></div>
                        <div class="stat-trend">
                            per day on average
                        </div>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- Categories --}}
            <div class="stat-card stat-warning">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-title">Categories</div>
                        <div class="stat-value">{{ count($this->getCategoryBreakdown()) }}</div>
                        <div class="stat-trend">
                            expense categories used
                        </div>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" x2="12" y1="8" y2="16" />
                            <line x1="8" x2="16" y1="12" y2="12" />
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- Unhealthy Expenses --}}
            <div class="stat-card stat-danger">
                <div class="stat-content">
                    <div class="stat-info">
                        <div class="stat-title">Unhealthy Expenses</div>
                        <div class="stat-value" x-text="formatMoney(unhealthyExpenses)"></div>
                        <div class="stat-trend">
                            <span x-text="(totalExpenses > 0 ? (unhealthyExpenses / totalExpenses * 100).toFixed(1) : 0) + '%'"></span> of total expenses
                        </div>
                    </div>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
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
        
        {{-- Charts Grid --}}
        <div class="charts-grid">
            {{-- Category Breakdown --}}
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Category Breakdown</h3>
                </div>
                
                @if(count($this->getCategoryBreakdown()) > 0)
                    <div class="chart-container" id="categoryPieChart"></div>
                    
                    <div class="category-list">
                        @foreach($this->getCategoryBreakdown()->take(5) as $index => $item)
                            <div class="category-item">
                                <div class="category-label">
                                    <div class="category-color" x-data="{ color: getCategoryColor({{ $index }}) }" :style="{ backgroundColor: color }"></div>
                                    <div class="category-name">{{ ucwords(str_replace('_', ' ', $item['category'])) }}</div>
                                </div>
                                <div class="category-value">
                                    <span class="category-value">€{{ number_format($item['total'], 2) }}</span>
                                    <span class="category-percentage">({{ number_format($item['percentage'], 1) }}%)</span>
                                </div>
                            </div>
                            
                            <div class="progress-container">
                                <div class="progress-bar" x-data="{ width: getProgressWidth({{ $item['total'] }}, {{ $this->getTotalExpenses() }}) }" 
                                     :style="{ width: width, backgroundColor: getCategoryColor({{ $index }}) }">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="8" x2="16" y1="12" y2="12" />
                            <line x1="12" x2="12" y1="8" y2="16" />
                        </svg>
                        <h3 class="empty-title">No Categories Found</h3>
                        <p class="empty-text">There are no expenses in this time period to categorize.</p>
                    </div>
                @endif
            </div>
            
            <div class="flex flex-col gap-6">
                {{-- Monthly Trend --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Monthly Trend ({{ $currentYear }})</h3>
                    </div>
                    
                    @if(count($this->getMonthlyTrend()) > 0)
                        <div class="chart-container" id="monthlyTrendChart"></div>
                    @else
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                            </svg>
                            <h3 class="empty-title">No Monthly Data</h3>
                            <p class="empty-text">There's no monthly expense data to display.</p>
                        </div>
                    @endif
                </div>
                
                {{-- Daily Trend --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Daily Expenses</h3>
                    </div>
                    
                    @if(count($this->getDailyTrend()) > 0)
                        <div class="chart-container" id="dailyTrendChart"></div>
                    @else
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <h3 class="empty-title">No Daily Data</h3>
                            <p class="empty-text">There's no daily expense data to display.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Transactions Grid --}}
        <div class="transactions-grid">
            {{-- Recent Transactions --}}
            <div class="trans-card">
                <div class="trans-header">
                    <h3 class="trans-title">Recent Transactions</h3>
                    @if(count($this->getRecentTransactions()) > 0)
                        <a href="{{ url('/app/transactions') }}" class="trans-link">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif
                </div>
                
                @if(count($this->getRecentTransactions()) > 0)
                    <div class="overflow-x-auto">
                        <table class="trans-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->getRecentTransactions(8) as $transaction)
                                    <tr>
                                        <td class="trans-date">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                        <td class="trans-desc">{{ $transaction->description }}</td>
                                        <td>
                                            <span x-data="{ category: '{{ $transaction->category }}' }" 
                                                  :class="getCategoryClass(category)"
                                                  class="category-badge">
                                                {{ ucwords(str_replace('_', ' ', $transaction->category)) }}
                                            </span>
                                        </td>
                                        <td class="trans-amount">€{{ number_format($transaction->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                            <path d="M17 8h1a4 4 0 1 1 0 8h-1" />
                            <path d="M12 8H5a3 3 0 0 0-3 3v1a3 3 0 0 0 3 3h7" />
                            <line x1="12" x2="12" y1="3" y2="21" />
                        </svg>
                        <h3 class="empty-title">No Transactions</h3>
                        <p class="empty-text">No expense transactions found for the selected period.</p>
                        <a href="{{ url('/app/transactions/create') }}" class="empty-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <line x1="12" x2="12" y1="5" y2="19" />
                                <line x1="5" x2="19" y1="12" y2="12" />
                            </svg>
                            Add Expense
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="flex flex-col gap-6">
                {{-- Largest Expenses --}}
                <div class="trans-card">
                    <div class="trans-header">
                        <h3 class="trans-title">Largest Expenses</h3>
                    </div>
                    
                    @if(count($this->getLargestExpenses()) > 0)
                        <div class="expense-list">
                            @foreach($this->getLargestExpenses() as $expense)
                                <div class="expense-item">
                                    <div class="expense-info">
                                        <div class="expense-desc">{{ $expense->description }}</div>
                                        <div class="expense-details">
                                            <span x-data="{ category: '{{ $expense->category }}' }" 
                                                  :class="getCategoryClass(category)"
                                                  class="category-badge">
                                                {{ ucwords(str_replace('_', ' ', $expense->category)) }}
                                            </span>
                                            <span>{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="expense-amount">€{{ number_format($expense->amount, 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="empty-icon">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M9 10h.01" />
                                <path d="M15 10h.01" />
                                <path d="M9.5 15a3.5 3.5 0 0 0 5 0" />
                            </svg>
                            <h3 class="empty-title">No Expenses</h3>
                            <p class="empty-text">No expenses found for this period.</p>
                        </div>
                    @endif
                </div>
                
                {{-- Savings Tips --}}
                <div class="trans-card">
                    <div class="trans-header">
                        <h3 class="trans-title">Savings Tips</h3>
                    </div>
                    
                    <div class="tips-list">
                        <div class="tip-item tip-item-primary">
                            <div class="tip-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <path d="M19.21 12.04l-1.53-.11-.3-1.5A5.004 5.004 0 0 0 12.16 6c-2.76 0-5 2.24-5 5s2.24 5 5 5h.34" />
                                    <path d="M17.96 9.8C19.13 8.27 20 7.94 20 6a4 4 0 0 0-4-4c-.7 0-1.34.25-1.85.66" />
                                    <path d="M8 10h5" />
                                    <path d="M8 14h2.5" />
                                    <path d="M13.54 17.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                                </svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title">Plan Your Meals</h4>
                                <p class="tip-desc">Planning meals in advance can reduce food expenses by up to 25%.</p>
                            </div>
                        </div>
                        
                        <div class="tip-item tip-item-blue">
                            <div class="tip-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title">Use Public Transport</h4>
                                <p class="tip-desc">Using public transportation instead of driving can save you money on fuel and parking.</p>
                            </div>
                        </div>
                        
                        <div class="tip-item tip-item-purple">
                            <div class="tip-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                            </div>
                            <div class="tip-content">
                                <h4 class="tip-title">24-Hour Rule</h4>
                                <p class="tip-desc">Wait 24 hours before making non-essential purchases to avoid impulse buying.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Load JavaScript --}}
    <script src="{{ asset('js/planance-expense-dashboard.js') }}"></script>
</x-filament-panels::page>