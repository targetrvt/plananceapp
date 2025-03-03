@php
    $startDateFormatted = \Carbon\Carbon::parse($startDate)->format('M d');
    $endDateFormatted = \Carbon\Carbon::parse($endDate)->format('M d, Y');
    $currentYear = \Carbon\Carbon::parse($startDate)->year;
@endphp

<x-filament-panels::page>
    <div 
        x-data="{
            categoryBreakdown: {{ json_encode($this->getCategoryBreakdown()) }},
            monthlyTrend: {{ json_encode($this->getMonthlyTrend()) }},
            dailyTrend: {{ json_encode($this->getDailyTrend()) }},
            totalExpenses: {{ $this->getTotalExpenses() }},
            averageDailyExpense: {{ $this->getAverageExpensePerDay() }},
            unhealthyExpenses: {{ $this->getUnhealthyExpenses() }},
            recentTransactions: {{ json_encode($this->getRecentTransactions(5)) }},
            initCharts() {
                this.renderCategoryPieChart();
                this.renderMonthlyTrendChart();
                this.renderDailyTrendChart();
            },
            renderCategoryPieChart() {
                if (this.categoryBreakdown.length === 0) return;
                
                const ctx = document.getElementById('categoryPieChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: this.categoryBreakdown.map(item => this.formatCategoryName(item.category)),
                        datasets: [{
                            data: this.categoryBreakdown.map(item => item.total),
                            backgroundColor: [
                                '#4f46e5', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b',
                                '#8b5cf6', '#ec4899', '#ef4444', '#f97316', '#6366f1'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const percentage = context.dataset.data.reduce((a, b) => a + b, 0) > 0 
                                            ? Math.round((value / context.dataset.data.reduce((a, b) => a + b, 0)) * 100) 
                                            : 0;
                                        return `${label}: €${value.toFixed(2)} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '65%',
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            },
            renderMonthlyTrendChart() {
                if (this.monthlyTrend.length === 0) return;
                
                const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.monthlyTrend.map(item => item.month),
                        datasets: [{
                            label: 'Monthly Expenses',
                            data: this.monthlyTrend.map(item => item.total),
                            backgroundColor: '#4f46e5',
                            borderColor: '#4338ca',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '€' + value;
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Expenses: €${context.raw.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            renderDailyTrendChart() {
                if (this.dailyTrend.length === 0) return;
                
                const ctx = document.getElementById('dailyTrendChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.dailyTrend.map(item => item.date),
                        datasets: [{
                            label: 'Daily Expenses',
                            data: this.dailyTrend.map(item => item.total),
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            borderColor: '#4f46e5',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#4f46e5',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#4f46e5',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '€' + value;
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Expenses: €${context.raw.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            formatCategoryName(category) {
                if (!category) return 'Uncategorized';
                
                return category
                    .replace(/_/g, ' ')
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },
            formatMoney(amount) {
                return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(amount);
            }
        }" 
        x-init="initCharts()"
    >
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-filament::section>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</h3>
                        <p class="text-2xl font-bold" x-text="formatMoney(totalExpenses)"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $startDateFormatted }} - {{ $endDateFormatted }}
                        </p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                        <x-heroicon-o-banknotes class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Daily</h3>
                        <p class="text-2xl font-bold" x-text="formatMoney(averageDailyExpense)"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            per day on average
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <x-heroicon-o-calendar class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Categories</h3>
                        <p class="text-2xl font-bold">{{ count($this->getCategoryBreakdown()) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            expense categories used
                        </p>
                    </div>
                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900 rounded-full">
                        <x-heroicon-o-tag class="h-6 w-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </x-filament::section>
            
            <x-filament::section>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Unhealthy Expenses</h3>
                        <p class="text-2xl font-bold" x-text="formatMoney(unhealthyExpenses)"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span x-text="(totalExpenses > 0 ? (unhealthyExpenses / totalExpenses * 100).toFixed(1) : 0) + '%'"></span> of total expenses
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </x-filament::section>
        </div>
        
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <x-filament::section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Category Breakdown</h2>
                </div>
                <div class="h-80">
                    <canvas id="categoryPieChart"></canvas>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Top Categories</h3>
                    <div class="space-y-3">
                        <template x-for="(category, index) in categoryBreakdown.slice(0, 5)" :key="index">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-2" :style="`background-color: ${['#4f46e5', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b'][index]};`"></div>
                                    <span class="text-sm" x-text="formatCategoryName(category.category)"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium" x-text="formatMoney(category.total)"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-2" x-text="`${category.percentage}%`"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </x-filament::section>
            
            <div class="space-y-6">
                <x-filament::section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Monthly Trend ({{ $currentYear }})</h2>
                    </div>
                    <div class="h-72">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </x-filament::section>
                
                <x-filament::section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Daily Expenses</h2>
                    </div>
                    <div class="h-60">
                        <canvas id="dailyTrendChart"></canvas>
                    </div>
                </x-filament::section>
            </div>
        </div>
        
        <!-- Transactions & Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <x-filament::section class="lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Recent Transactions</h2>
                    @if(count($this->getRecentTransactions()) > 0)
                        <a href="{{ url('/app/transactions') }}" 
                           class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400">
                            View All
                        </a>
                    @endif
                </div>
                
                @if(count($this->getRecentTransactions()) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr>
                                    <th class="text-left pb-3 font-medium text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="text-left pb-3 font-medium text-gray-500 dark:text-gray-400">Description</th>
                                    <th class="text-left pb-3 font-medium text-gray-500 dark:text-gray-400">Category</th>
                                    <th class="text-right pb-3 font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($this->getRecentTransactions() as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                        <td class="py-3">{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                        <td class="py-3">{{ $transaction->description }}</td>
                                        <td class="py-3">
                                            <span @class([
                                                'px-2 py-1 text-xs rounded-full',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => in_array($transaction->category, ['food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel']),
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $transaction->category === 'unhealthy_habits',
                                                'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' => in_array($transaction->category, ['shopping', 'entertainment', 'other_expense']),
                                                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' => !in_array($transaction->category, ['food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel', 'unhealthy_habits', 'shopping', 'entertainment', 'other_expense']),
                                            ])>
                                                {{ str_replace('_', ' ', ucwords($transaction->category)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right font-medium">€{{ number_format($transaction->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6">
                        <x-heroicon-o-banknotes class="h-12 w-12 mx-auto text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No transactions</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No expense transactions found for the selected period.</p>
                        <div class="mt-6">
                            <a href="{{ url('/app/transactions/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <x-heroicon-m-plus class="h-5 w-5 mr-2" />
                                Add Expense
                            </a>
                        </div>
                    </div>
                @endif
            </x-filament::section>
            
            <x-filament::section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Largest Expenses</h2>
                </div>
                
                @if(count($this->getLargestExpenses()) > 0)
                    <div class="space-y-4">
                        @foreach($this->getLargestExpenses() as $expense)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium">{{ $expense->description }}</h3>
                                    <div class="flex items-center mt-1">
                                        <span @class([
                                            'px-2 py-0.5 text-xs rounded-full',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => in_array($expense->category, ['food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel']),
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $expense->category === 'unhealthy_habits',
                                            'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' => in_array($expense->category, ['shopping', 'entertainment', 'other_expense']),
                                            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' => !in_array($expense->category, ['food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel', 'unhealthy_habits', 'shopping', 'entertainment', 'other_expense']),
                                        ])>
                                            {{ str_replace('_', ' ', ucwords($expense->category)) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <span class="text-base font-medium">€{{ number_format($expense->amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No expenses found for this period.</p>
                    </div>
                @endif
                
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Savings Tips</h3>
                    <div class="space-y-3">
                        <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3">
                            <div class="flex items-start">
                                <x-heroicon-o-light-bulb class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Plan Your Meals</h4>
                                    <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">Planning meals in advance can reduce food expenses by up to 25%.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-3">
                            <div class="flex items-start">
                                <x-heroicon-o-light-bulb class="h-5 w-5 text-indigo-600 dark:text-indigo-400 mt-0.5 mr-2" />
                                <div>
                                    <h4 class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Use Public Transport</h4>
                                    <p class="text-xs text-indigo-700 dark:text-indigo-400 mt-1">Using public transportation instead of driving can save you money on fuel and parking.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-lg p-3">
                            <div class="flex items-start">
                                <x-heroicon-o-light-bulb class="h-5 w-5 text-emerald-600 dark:text-emerald-400 mt-0.5 mr-2" />
                                <div>
                                    <h4 class="text-sm font-medium text-emerald-800 dark:text-emerald-300">24-Hour Rule</h4>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">Wait 24 hours before making non-essential purchases to avoid impulse buying.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
</x-filament-panels::page>