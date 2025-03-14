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
                
                // Color palette - more professional, distinct colors
                const colorPalette = [
                    '#4338ca', '#3b82f6', '#0891b2', '#059669', '#84cc16', 
                    '#8b5cf6', '#a855f7', '#ec4899', '#f43f5e', '#f97316'
                ];
                
                // Create a more sophisticated doughnut chart
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: this.categoryBreakdown.map(item => this.formatCategoryName(item.category)),
                        datasets: [{
                            data: this.categoryBreakdown.map(item => item.total),
                            backgroundColor: colorPalette,
                            borderWidth: 1,
                            borderColor: '#ffffff',
                            hoverBorderWidth: 3,
                            hoverBorderColor: '#ffffff',
                            hoverOffset: 10,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: 'bold'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255,255,255,0.9)',
                                titleColor: '#111827',
                                bodyColor: '#374151',
                                borderColor: '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: €${value.toFixed(2)} (${percentage}%)`;
                                    },
                                    labelTextColor: function() {
                                        return '#374151'; // Dark gray text for better readability
                                    }
                                }
                            },
                            datalabels: {
                                color: '#ffffff',
                                font: {
                                    weight: 'bold',
                                    size: 11
                                },
                                formatter: (value, ctx) => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return percentage > 5 ? `${percentage}%` : ''; // Only show percentage for significant slices
                                }
                            }
                        },
                        cutout: '70%', // Larger hole for a more modern look
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
                
                // Add a center text element to display total expenses
                this.addCenterText('categoryPieChart', this.formatMoney(this.totalExpenses), 'Total Expenses');
            },
            
            // Add this new helper function to render the center text
            addCenterText(chartId, mainText, subText) {
                const chart = document.getElementById(chartId);
                
                // Remove any existing center text elements
                const existingText = chart.parentNode.querySelector('.chart-center-text');
                if (existingText) {
                    existingText.remove();
                }
                
                // Create the center text container
                const centerText = document.createElement('div');
                centerText.className = 'chart-center-text';
                centerText.style.position = 'absolute';
                centerText.style.top = '50%';
                centerText.style.left = '50%';
                centerText.style.transform = 'translate(-50%, -50%)';
                centerText.style.textAlign = 'center';
                
                // Create main text
                const mainTextEl = document.createElement('div');
                mainTextEl.textContent = mainText;
                mainTextEl.style.fontSize = '1.1rem';
                mainTextEl.style.fontWeight = 'bold';
                mainTextEl.style.color = '#1f2937';
                mainTextEl.className = 'dark:text-white';
                
                // Create sub text
                const subTextEl = document.createElement('div');
                subTextEl.textContent = subText;
                subTextEl.style.fontSize = '0.75rem';
                subTextEl.style.color = '#6b7280';
                subTextEl.className = 'dark:text-gray-300';
                
                // Append texts to container
                centerText.appendChild(mainTextEl);
                centerText.appendChild(subTextEl);
                
                // Add the center text to the chart's parent container
                chart.parentNode.style.position = 'relative';
                chart.parentNode.appendChild(centerText);
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
            <!-- REPLACE THIS SECTION WITH THE NEW CHART IMPLEMENTATION -->
            <x-filament::section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Category Breakdown</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $startDateFormatted }} - {{ $endDateFormatted }}</span>
                    </div>
                </div>

                <!-- Chart Container with Position Relative for Center Text -->
                <div class="relative h-80">
                    <canvas id="categoryPieChart"></canvas>
                </div>
                
                <!-- CSS for Chart Area -->
                <style>
                    .chart-center-text {
                        pointer-events: none;
                        z-index: 10;
                    }
                    
                    /* Dark mode styles */
                    .dark .chart-center-text div:first-child {
                        color: white;
                    }
                    
                    .dark .chart-center-text div:last-child {
                        color: #9ca3af;
                    }
                </style>
                
                <!-- Top Categories with improved visualization -->
                <div id="topCategoriesContainer" class="mt-6">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Top Expense Categories</h3>
                    <div class="space-y-3">
                        <template x-for="(category, index) in categoryBreakdown.slice(0, 5)" :key="index">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium" x-text="formatCategoryName(category.category)"></span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        <span x-text="formatMoney(category.total)"></span>
                                        <span x-text="`(${category.percentage}%)`"></span>
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full" 
                                         :style="`width: ${category.percentage}%; background-color: ${['#4338ca', '#3b82f6', '#0891b2', '#059669', '#84cc16', '#8b5cf6', '#a855f7', '#ec4899', '#f43f5e', '#f97316'][index % 10]};`">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Add quick insights section -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Insights</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start space-x-2">
                            <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900 rounded-full">
                                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Largest Category</p>
                                <p class="text-sm" x-text="categoryBreakdown.length > 0 ? formatCategoryName(categoryBreakdown[0].category) + ' (' + formatMoney(categoryBreakdown[0].total) + ')' : 'No data'"></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-2">
                            <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900 rounded-full">
                                <x-heroicon-o-arrow-trending-down class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Average Per Category</p>
                                <p class="text-sm" x-text="categoryBreakdown.length > 0 ? formatMoney(totalExpenses / categoryBreakdown.length) : 'No data'"></p>
                            </div>
                        </div>
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
        
        <!-- Add the Chart.js libraries and plugins -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

        <script>
            document.addEventListener('alpine:init', () => {
                // Register Chart.js plugins
                Chart.register(ChartDataLabels);
            });
        </script>
    </div>
</x-filament-panels::page>