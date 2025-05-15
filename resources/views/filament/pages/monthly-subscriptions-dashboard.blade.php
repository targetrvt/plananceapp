<x-filament-panels::page>
    <!-- Simplified styles -->
    <style>
        canvas { max-height: 300px; }
        @media (min-width: 1024px) { canvas { max-height: 250px; } }
        
        .filter-pill {
            transition: all 0.2s ease;
        }
        .filter-pill:hover { transform: translateY(-1px); }
        .filter-pill.active {
            background-color: rgba(79, 70, 229, 0.1);
            border-color: rgba(79, 70, 229, 0.5);
            color: rgb(79, 70, 229);
        }
        .category-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            margin-right: 4px;
        }
    </style>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Helper Functions -->
    <script>
        // Text capitalization helper
        function ucfirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Category emojis map
        const categoryEmojis = {
            'streaming': '🎬', 'software': '💻', 'cloud': '☁️', 'membership': '👥',
            'utilities': '⚡', 'phone': '📱', 'education': '🎓', 'health': '❤️',
            'gaming': '🎮', 'news': '📰', 'other': '🔍'
        };
        
        // Category icons SVG map
        const categoryIcons = {
            'streaming': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>',
            'software': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>',
            'cloud': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"></path></svg>',
            'membership': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>',
            'utilities': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>',
            'phone': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>',
            'education': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path></svg>',
            'health': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>',
            'gaming': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"></path></svg>',
            'news': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path></svg>',
            'other': '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>'
        };
        
        // Setup Alpine store for filtering
        document.addEventListener('alpine:init', () => {
            Alpine.store('filters', {
                categoryFilter: 'all',
                billingCycleFilter: 'all',
                timeRangeFilter: 'all',
            });
        });
    </script>

    <div 
        x-data="{ 
            toggleCategoryFilter(category) {
                Alpine.store('filters').categoryFilter = 
                    Alpine.store('filters').categoryFilter === category ? 'all' : category;
                this.refreshCharts();
            },
            toggleBillingCycleFilter(cycle) {
                Alpine.store('filters').billingCycleFilter = 
                    Alpine.store('filters').billingCycleFilter === cycle ? 'all' : cycle;
                this.refreshCharts();
            },
            refreshCharts() {
                window.dispatchEvent(new CustomEvent('refresh-subscription-charts', {
                    detail: {
                        categoryFilter: Alpine.store('filters').categoryFilter,
                        billingCycleFilter: Alpine.store('filters').billingCycleFilter,
                        timeRangeFilter: Alpine.store('filters').timeRangeFilter
                    }
                }));
            },
            getCategoryIcon(category) {
                return categoryIcons[category] || categoryIcons['other'];
            }
        }"
        class="space-y-6"
    >
        <!-- Stats Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total Monthly -->
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Spending</h2>
                        <div class="mt-1 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                €{{ number_format($this->getTotalMonthlyAmount(), 2) }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-primary-500 to-primary-600 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Annual equivalent:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">€{{ number_format($this->getTotalAnnualAmount(), 2) }}</span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Subscription Count -->
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Subscriptions</h2>
                        <div class="mt-1 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $this->getSubscriptionCount() }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Average cost per subscription:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            €{{ $this->getSubscriptionCount() ? number_format($this->getTotalMonthlyAmount() / $this->getSubscriptionCount(), 2) : '0.00' }}
                        </span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Upcoming Payments -->
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Payments (30 days)</h2>
                        <div class="mt-1 flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $this->getUpcomingPayments()->count() }}
                            </p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Total upcoming amount:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            €{{ number_format($this->getUpcomingPayments()->sum('amount'), 2) }}
                        </span>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Filters Section -->
        <x-filament::section>
            <div class="flex flex-col space-y-4">
                <!-- Filter title -->
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Subscriptions</h3>
                    <button
                        @click="Alpine.store('filters').categoryFilter = 'all'; Alpine.store('filters').billingCycleFilter = 'all'; refreshCharts()"
                        class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                    >
                        Reset Filters
                    </button>
                </div>
                
                <!-- Category Filters -->
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Category</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($this->getCategoryColors() as $category => $color)
                            <button 
                                @click="toggleCategoryFilter('{{ $category }}')"
                                class="filter-pill px-2.5 py-1 text-xs font-medium border rounded-full transition-colors"
                                :class="{'active': Alpine.store('filters').categoryFilter === '{{ $category }}'}"
                            >
                                <span class="category-icon" style="color: {{ $color }}">
                                    <span x-html="getCategoryIcon('{{ $category }}')"></span>
                                </span>
                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <!-- Billing Cycle Filters -->
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Billing Cycle</p>
                    <div class="flex flex-wrap gap-2">
                        <button 
                            @click="toggleBillingCycleFilter('monthly')"
                            class="filter-pill px-2.5 py-1 text-xs font-medium border rounded-full transition-colors"
                            :class="{'active': Alpine.store('filters').billingCycleFilter === 'monthly'}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            Monthly
                        </button>
                        <button 
                            @click="toggleBillingCycleFilter('quarterly')"
                            class="filter-pill px-2.5 py-1 text-xs font-medium border rounded-full transition-colors"
                            :class="{'active': Alpine.store('filters').billingCycleFilter === 'quarterly'}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Quarterly
                        </button>
                        <button 
                            @click="toggleBillingCycleFilter('annual')"
                            class="filter-pill px-2.5 py-1 text-xs font-medium border rounded-full transition-colors"
                            :class="{'active': Alpine.store('filters').billingCycleFilter === 'annual'}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Annual
                        </button>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Donut Chart for Category Breakdown -->
            <x-filament::section>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Subscription Categories</h2>
                
                @if(count($this->getCategoryBreakdown()) > 0)
                    <div class="min-h-60">
                        <canvas id="categoryDonutChart" class="w-full"></canvas>
                    </div>
                    
                    <script>
                        // Create the chart when page loads
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('categoryDonutChart').getContext('2d');
                            let categoryChart;
                            
                            function initializeChart(categoryFilter = 'all', billingCycleFilter = 'all') {
                                // Get all category data
                                const allCategories = @json(collect($this->getCategoryBreakdown()));
                                
                                // Filter data if needed
                                let filteredCategories = allCategories;
                                
                                // Apply category filter if set
                                if (categoryFilter !== 'all') {
                                    filteredCategories = Object.fromEntries(
                                        Object.entries(allCategories).filter(([key, _]) => key === categoryFilter)
                                    );
                                }
                                
                                // Get colors, labels and values
                                const categoryColors = Object.values(filteredCategories).map(item => item.color);
                                const categoryLabels = Object.keys(filteredCategories).map(category => {
                                    const emoji = categoryEmojis[category] || categoryEmojis['other'];
                                    return emoji + ' ' + ucfirst(category.replace('_', ' '));
                                });
                                
                                const categoryValues = Object.values(filteredCategories).map(item => item.total);
                                
                                // Get category icons for chart segments
                                const categoryIconsArray = Object.keys(filteredCategories).map(category => 
                                    categoryEmojis[category] || categoryEmojis['other']
                                );
                                
                                // If there's an existing chart, destroy it
                                if (categoryChart) {
                                    categoryChart.destroy();
                                }
                                
                                // Create new chart
                                categoryChart = new Chart(ctx, {
                                    type: 'doughnut',
                                    data: {
                                        labels: categoryLabels,
                                        datasets: [{
                                            data: categoryValues,
                                            backgroundColor: categoryColors,
                                            borderWidth: 2,
                                            borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                                            hoverOffset: 10
                                        }]
                                    },
                                    options: {
                                        plugins: {
                                            categoryIcons: {
                                                icons: categoryIconsArray
                                            },
                                            legend: {
                                                position: 'right',
                                                labels: {
                                                    padding: 15,
                                                    boxWidth: 10,
                                                    font: { size: 11 },
                                                    color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#4b5563'
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.raw;
                                                        const percent = Math.round((value / categoryValues.reduce((a, b) => a + b, 0)) * 100);
                                                        return `€${value.toFixed(2)} (${percent}%)`;
                                                    }
                                                }
                                            }
                                        },
                                        cutout: '60%',
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        aspectRatio: 1.5
                                    }
                                });
                            }
                            
                            // Initial chart creation
                            initializeChart();
                            
                            // Listen for filter change events
                            window.addEventListener('refresh-subscription-charts', function(e) {
                                initializeChart(e.detail.categoryFilter, e.detail.billingCycleFilter);
                            });
                        });
                    </script>
                @else
                    <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                    </div>
                @endif
            </x-filament::section>
            
            <!-- Bar Chart for Subscription Costs -->
            <x-filament::section>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Subscription Costs</h2>
                
                @if($this->getSubscriptionCount() > 0)
                    <div class="min-h-60">
                        <canvas id="subscriptionBarChart" class="w-full"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('subscriptionBarChart').getContext('2d');
                            let barChart;
                            
                            function initializeChart(categoryFilter = 'all', billingCycleFilter = 'all') {
                                // Get all subscriptions data
                                const allSubscriptions = @json($this->getAllActiveSubscriptions());
                                const categoryColors = @json($this->getCategoryColors());
                                
                                // Filter subscriptions based on selected filters
                                let filteredSubscriptions = [...allSubscriptions];
                                
                                if (categoryFilter !== 'all') {
                                    filteredSubscriptions = filteredSubscriptions.filter(sub => sub.category === categoryFilter);
                                }
                                
                                if (billingCycleFilter !== 'all') {
                                    filteredSubscriptions = filteredSubscriptions.filter(sub => sub.billing_cycle === billingCycleFilter);
                                }
                                
                                // Calculate monthly costs
                                const subscriptionsWithMonthlyCost = filteredSubscriptions.map(sub => {
                                    const divisor = {
                                        'monthly': 1,
                                        'quarterly': 3,
                                        'biannual': 6,
                                        'annual': 12
                                    }[sub.billing_cycle] || 1;
                                    
                                    return {
                                        name: sub.name,
                                        category: sub.category,
                                        monthly_cost: parseFloat(sub.amount) / divisor
                                    };
                                });
                                
                                // Sort by monthly cost and limit to top 10
                                const sortedSubscriptions = subscriptionsWithMonthlyCost
                                    .sort((a, b) => b.monthly_cost - a.monthly_cost)
                                    .slice(0, 10);
                                
                                // Prepare data for the chart
                                const barColors = sortedSubscriptions.map(sub => categoryColors[sub.category] || '#6b7280');
                                const labels = sortedSubscriptions.map(sub => sub.name);
                                
                                // If there's an existing chart, destroy it
                                if (barChart) {
                                    barChart.destroy();
                                }
                                
                                // Create new chart
                                barChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Monthly Cost (€)',
                                            data: sortedSubscriptions.map(sub => sub.monthly_cost),
                                            backgroundColor: barColors,
                                            borderColor: barColors,
                                            borderWidth: 1,
                                            borderRadius: 4,
                                            maxBarThickness: 40
                                        }]
                                    },
                                    options: {
                                        indexAxis: 'y',
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return `€${context.raw.toFixed(2)}`;
                                                    },
                                                    beforeLabel: function(context) {
                                                        const subscription = sortedSubscriptions[context.dataIndex];
                                                        return `Category: ${subscription.category.charAt(0).toUpperCase() + subscription.category.slice(1).replace('_', ' ')}`;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: function(value) { return '€' + value; },
                                                    color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#4b5563'
                                                },
                                                grid: {
                                                    display: true,
                                                    color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(209, 213, 219, 0.2)'
                                                }
                                            },
                                            y: {
                                                ticks: {
                                                    color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#4b5563',
                                                    callback: function(value, index) {
                                                        if (index < sortedSubscriptions.length) {
                                                            const category = sortedSubscriptions[index].category;
                                                            return categoryEmojis[category] + ' ' + labels[index];
                                                        }
                                                        return value;
                                                    }
                                                },
                                                grid: { display: false }
                                            }
                                        },
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        aspectRatio: 1.5
                                    }
                                });
                            }
                            
                            // Initialize chart
                            initializeChart();
                            
                            // Listen for filter changes
                            window.addEventListener('refresh-subscription-charts', function(e) {
                                initializeChart(e.detail.categoryFilter, e.detail.billingCycleFilter);
                            });
                        });
                    </script>
                @else
                    <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Monthly Cost Timeline Chart -->
            <x-filament::section class="lg:col-span-2">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Monthly Payment Timeline</h2>
                
                @if($this->getSubscriptionCount() > 0)
                    <div class="min-h-60">
                        <canvas id="monthlyTimelineChart" class="w-full"></canvas>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('monthlyTimelineChart').getContext('2d');
                            let timelineChart;
                            
                            function initializeChart(categoryFilter = 'all', billingCycleFilter = 'all') {
                                // Get the next 12 months
                                const months = [];
                                const today = new Date('2025-06-14');
                                for (let i = 0; i < 12; i++) {
                                    const month = new Date(today.getFullYear(), today.getMonth() + i, 1);
                                    months.push(month.toLocaleString('default', { month: 'short', year: 'numeric' }));
                                }
                                
                                // Get all subscriptions
                                const allSubscriptions = @json($this->getAllActiveSubscriptions());
                                
                                // Ensure days_left is an integer
                                allSubscriptions.forEach(sub => {
                                    if (sub.days_left) {
                                        sub.days_left = parseInt(sub.days_left);
                                    }
                                });
                                
                                // Filter subscriptions based on selected filters
                                let filteredSubscriptions = [...allSubscriptions];
                                
                                if (categoryFilter !== 'all') {
                                    filteredSubscriptions = filteredSubscriptions.filter(sub => sub.category === categoryFilter);
                                }
                                
                                if (billingCycleFilter !== 'all') {
                                    filteredSubscriptions = filteredSubscriptions.filter(sub => sub.billing_cycle === billingCycleFilter);
                                }
                                
                                // Initialize monthly costs array
                                const monthlyCosts = Array(12).fill(0);
                                
                                // Calculate payments
                                filteredSubscriptions.forEach(sub => {
                                    const amount = parseFloat(sub.amount);
                                    let billingDate;
                                    
                                    if (typeof sub.billing_date === 'string') {
                                        billingDate = new Date(sub.billing_date);
                                    } else {
                                        try {
                                            billingDate = new Date(sub.billing_date);
                                        } catch (e) {
                                            billingDate = new Date();
                                        }
                                    }
                                    
                                    // Payment frequencies in months
                                    const frequency = {
                                        'monthly': 1,
                                        'quarterly': 3,
                                        'biannual': 6,
                                        'annual': 12
                                    }[sub.billing_cycle] || 1;
                                    
                                    // Current month and next billing month
                                    const currentMonth = today.getMonth();
                                    const billingMonth = billingDate.getMonth();
                                    
                                    // For each of the next 12 months
                                    for (let i = 0; i < 12; i++) {
                                        const monthIndex = (currentMonth + i) % 12;
                                        
                                        if (sub.billing_cycle === 'monthly') {
                                            monthlyCosts[i] += amount;
                                        } else {
                                            // Calculate months since the last billing date
                                            const monthsSinceLastBilling = (monthIndex - billingMonth + 12) % 12;
                                            
                                            // If this is a payment month
                                            if (monthsSinceLastBilling % frequency === 0) {
                                                monthlyCosts[i] += amount;
                                            }
                                        }
                                    }
                                });
                                
                                // Calculate average monthly spending
                                let averageMonthly;
                                
                                if (categoryFilter !== 'all' || billingCycleFilter !== 'all') {
                                    averageMonthly = monthlyCosts.reduce((sum, cost) => sum + cost, 0) / 12;
                                } else {
                                    averageMonthly = {{ $this->getTotalMonthlyAmount() }};
                                }
                                
                                const averageData = Array(12).fill(averageMonthly);
                                
                                // If there's an existing chart, destroy it
                                if (timelineChart) {
                                    timelineChart.destroy();
                                }
                                
                                // Create new chart
                                timelineChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: months,
                                        datasets: [
                                            {
                                                label: 'Projected Payments',
                                                data: monthlyCosts,
                                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                                borderColor: 'rgba(79, 70, 229, 1)',
                                                borderWidth: 2,
                                                fill: true,
                                                tension: 0.2,
                                                pointBackgroundColor: 'rgba(79, 70, 229, 1)'
                                            },
                                            {
                                                label: 'Average Monthly',
                                                data: averageData,
                                                borderColor: 'rgba(239, 68, 68, 0.7)',
                                                borderWidth: 2,
                                                borderDash: [5, 5],
                                                fill: false,
                                                tension: 0,
                                                pointRadius: 0
                                            }
                                        ]
                                    },
                                    options: {
                                        plugins: {
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return `${context.dataset.label}: €${context.raw.toFixed(2)}`;
                                                    }
                                                }
                                            },
                                            legend: {
                                                labels: {
                                                    font: { size: 11 }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: function(value) { return '€' + value; },
                                                    color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#4b5563'
                                                },
                                                grid: {
                                                    color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.2)' : 'rgba(209, 213, 219, 0.2)'
                                                }
                                            },
                                            x: {
                                                ticks: {
                                                    color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#4b5563',
                                                    maxRotation: 45,
                                                    minRotation: 45
                                                },
                                                grid: { display: false }
                                            }
                                        },
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        aspectRatio: 2
                                    }
                                });
                            }
                            
                            // Initialize chart
                            initializeChart();
                            
                            // Listen for filter changes
                            window.addEventListener('refresh-subscription-charts', function(e) {
                                initializeChart(e.detail.categoryFilter, e.detail.billingCycleFilter);
                            });
                        });
                    </script>
                @else
                    <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                    </div>
                @endif
            </x-filament::section>

            <!-- Upcoming Payments List -->
            <x-filament::section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Payments</h2>
                    
                    @if(count($this->getUpcomingPayments()) > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            Next 30 days
                        </span>
                    @endif
                </div>
                
                @if(count($this->getUpcomingPayments()) > 0)
                    <div class="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                        @foreach($this->getUpcomingPayments() as $subscription)
                            <div 
                                x-data="{ visible: true }" 
                                x-show="Alpine.store('filters').categoryFilter === 'all' || Alpine.store('filters').categoryFilter === '{{ $subscription->category }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm transition-all hover:shadow-md hover:border-primary-300 dark:hover:border-primary-700"
                            >
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 flex items-center justify-center rounded-full mr-3 
                                        bg-{{ match($subscription->category) {
                                            'streaming' => 'red',
                                            'software' => 'blue',
                                            'cloud' => 'indigo',
                                            'membership' => 'emerald',
                                            'utilities' => 'amber',
                                            'phone' => 'rose',
                                            'education' => 'purple',
                                            'health' => 'green',
                                            'gaming' => 'violet',
                                            'news' => 'teal',
                                            default => 'gray',
                                        } }}-100 
                                        dark:bg-{{ match($subscription->category) {
                                            'streaming' => 'red',
                                            'software' => 'blue',
                                            'cloud' => 'indigo',
                                            'membership' => 'emerald',
                                            'utilities' => 'amber',
                                            'phone' => 'rose',
                                            'education' => 'purple',
                                            'health' => 'green',
                                            'gaming' => 'violet',
                                            'news' => 'teal',
                                            default => 'gray',
                                        } }}-900/50">
                                            <span x-html="getCategoryIcon('{{ $subscription->category }}')"></span>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->name }}</h3>
                                            <div class="flex items-center mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $subscription->billing_date->format('M d, Y') }}
                                                </p>
                                                
                                                @if($subscription->days_left < 7)
                                                    <span class="ml-2 px-1.5 py-0.5 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 text-xs rounded-full">
                                                        {{ $subscription->days_left }} days left
                                                    </span>
                                                @else
                                                    <span class="ml-2 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 text-xs rounded-full">
                                                        {{ $subscription->days_left }} days left
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        €{{ number_format($subscription->amount, 2) }}
                                    </div>
                                </div>
                                
                                <!-- Progress bar for days remaining -->
                                @php
                                    $percentage = min(100, max(0, ($subscription->days_left / 30) * 100));
                                @endphp
                                
                                <div class="mt-2 relative pt-1">
                                    <div class="overflow-hidden h-1.5 text-xs flex rounded bg-gray-200 dark:bg-gray-700">
                                        <div class="rounded h-full bg-{{ $subscription->days_left < 7 ? 'red' : 'blue' }}-500" style="width: {{ 100 - $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No upcoming payments in the next 30 days</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>