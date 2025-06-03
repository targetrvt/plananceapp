<x-filament-panels::page>
    @push('styles')
    <style>
        canvas { max-height: 300px; }
        @media (min-width: 1024px) { canvas { max-height: 280px; } }
        
        .chart-container {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .chart-container:hover {
            transform: translateY(-4px);
        }
        
        .subscription-card {
            transition: all 0.2s ease;
            border: 1px solid rgba(209, 213, 219, 0.5);
        }
        
        .subscription-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card .stat-icon {
            position: relative;
            z-index: 2;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 140px;
            height: 140px;
            border-radius: 70px;
            opacity: 0.1;
            z-index: 1;
        }
        
        .stat-card.primary::before {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-700));
        }
        
        .stat-card.secondary::before {
            background: linear-gradient(135deg, var(--blue-500), var(--indigo-600));
        }
        
        .stat-card.tertiary::before {
            background: linear-gradient(135deg, var(--amber-400), var(--amber-600));
        }
        
        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .days-indicator {
            display: inline-flex;
            align-items: center;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 500;
        }
        
        .progress-bar {
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
            background-color: rgba(229, 231, 235, 0.5);
        }
        
        .progress-value {
            height: 100%;
            border-radius: 2px;
        }
        
        .category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .category-label {
            display: flex;
            align-items: center;
        }
        
        .category-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .category-name {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .category-value {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .calendar-heatmap {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
        }
        
        .calendar-day {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 3px;
            cursor: pointer;
            transition: transform 0.1s ease;
        }
        
        .calendar-day:hover {
            transform: scale(1.2);
        }
        
        .tooltip {
            position: absolute;
            z-index: 100;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            max-width: 200px;
        }
        
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.6;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    @endpush

    <div class="space-y-6">
        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Monthly -->
            <x-filament::section class="stat-card primary">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Spending</h2>
                        <div class="mt-1 flex items-baseline">
                            <div class="relative">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    €{{ number_format($this->getTotalMonthlyAmount(), 2) }}
                                </p>
                                <div class="absolute -bottom-5 left-0 w-full h-1 bg-primary-200 dark:bg-primary-800 rounded-full">
                                    <div class="h-full bg-primary-500 dark:bg-primary-400 rounded-full" style="width: 70%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-icon bg-primary-100 dark:bg-primary-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Annual equivalent:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">€{{ number_format($this->getTotalAnnualAmount(), 2) }}</span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Subscription Count -->
            <x-filament::section class="stat-card secondary">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Subscriptions</h2>
                        <div class="mt-1 flex items-baseline">
                            <div class="relative">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $this->getSubscriptionCount() }}
                                </p>
                                <div class="absolute -bottom-5 left-0 w-full h-1 bg-blue-200 dark:bg-blue-800 rounded-full">
                                    <div class="h-full bg-blue-500 dark:bg-blue-400 rounded-full" style="width: 60%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-icon bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Average monthly cost:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            €{{ $this->getSubscriptionCount() ? number_format($this->getTotalMonthlyAmount() / $this->getSubscriptionCount(), 2) : '0.00' }}
                        </span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Upcoming Payments -->
            <x-filament::section class="stat-card tertiary">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Payments</h2>
                        <div class="mt-1 flex items-baseline">
                            <div class="relative">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $this->getUpcomingPayments()->count() }}
                                </p>
                                <div class="absolute -bottom-5 left-0 w-full h-1 bg-amber-200 dark:bg-amber-800 rounded-full">
                                    <div class="h-full bg-amber-500 dark:bg-amber-400 rounded-full" 
                                    style="width: {{ min(100, ($this->getUpcomingPayments()->count() / max(1, $this->getSubscriptionCount())) * 100) }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stat-icon bg-amber-100 dark:bg-amber-900/30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <span>Total upcoming amount:</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">
                            €{{ number_format($this->getUpcomingPayments()->sum('amount'), 2) }}
                        </span>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- Main Chart Sections - Professional Visualizations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Advanced Donut Chart for Category Breakdown -->
            <x-filament::section>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Subscription Categories</h2>
                
                @if(count($this->getCategoryBreakdown()) > 0)
                    <div class="chart-container aspect-square sm:aspect-auto min-h-60">
                        <canvas id="categoryDonutChart" class="w-full"></canvas>
                    </div>
                    
                    <!-- Enhanced Category List -->
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        @foreach($this->getCategoryBreakdown() as $category => $data)
                            <div class="category-item">
                                <div class="category-label truncate">
                                    <span class="category-color" style="background-color: {{ $this->getCategoryColors()[$category] ?? '#6b7280' }}"></span>
                                    <span class="category-name truncate">{{ ucfirst(str_replace('_', ' ', $category)) }}</span>
                                </div>
                                <div class="category-value">
                                    <span>€{{ number_format($data['total'], 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                    </div>
                @endif
            </x-filament::section>
            
            <!-- Enhanced Bar Chart for Subscription Costs -->
            <x-filament::section>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Top Subscriptions</h2>
                
                @if($this->getSubscriptionCount() > 0)
                    <div class="chart-container min-h-64">
                        <canvas id="subscriptionBarChart" class="w-full"></canvas>
                    </div>
                @else
                    <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                        <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                    </div>
                @endif
            </x-filament::section>
        </div>

        <!-- Enhanced Payment Timeline Chart -->
        <x-filament::section>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Payment Timeline</h2>
            
            @if($this->getSubscriptionCount() > 0)
                <div class="chart-container min-h-72">
                    <canvas id="paymentTimelineChart" class="w-full"></canvas>
                </div>
            @else
                <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                    <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                </div>
            @endif
        </x-filament::section>

        <!-- New: Payment Calendar Heatmap -->
        <x-filament::section>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Payment Calendar</h2>
            
            @if($this->getSubscriptionCount() > 0)
                <div class="relative">
                    <div id="paymentCalendar" class="calendar-heatmap min-h-48 w-full mb-2"></div>
                    <div id="calendarTooltip" class="tooltip"></div>
                    
                    <div class="flex justify-between items-center mt-6 mb-4">
                        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Subscription Density</div>
                        <div class="flex space-x-2 items-center">
                            <div class="flex space-x-1 items-center">
                                <div class="w-3 h-3 rounded" style="background-color: rgba(79, 70, 229, 0.1);"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Low</span>
                            </div>
                            <div class="flex space-x-1 items-center">
                                <div class="w-3 h-3 rounded" style="background-color: rgba(79, 70, 229, 0.3);"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Medium</span>
                            </div>
                            <div class="flex space-x-1 items-center">
                                <div class="w-3 h-3 rounded" style="background-color: rgba(79, 70, 229, 0.6);"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">High</span>
                            </div>
                            <div class="flex space-x-1 items-center">
                                <div class="w-3 h-3 rounded" style="background-color: rgba(79, 70, 229, 0.9);"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Very High</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                    <p class="text-gray-500 dark:text-gray-400">No active subscriptions found</p>
                </div>
            @endif
        </x-filament::section>

        <!-- Upcoming Payments Section - Enhanced Design -->
        <x-filament::section>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Payments</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Next 30 days
                </span>
            </div>
            
            @if($this->getUpcomingPayments()->count() > 0)
                <div class="space-y-4">
                    @foreach($this->getUpcomingPayments() as $subscription)
                        @php
                            $bgColor = match($subscription->category) {
                                'streaming' => 'bg-red-50 dark:bg-red-900/10',
                                'software' => 'bg-blue-50 dark:bg-blue-900/10',
                                'cloud' => 'bg-indigo-50 dark:bg-indigo-900/10',
                                'membership' => 'bg-emerald-50 dark:bg-emerald-900/10',
                                'utilities' => 'bg-amber-50 dark:bg-amber-900/10',
                                'phone' => 'bg-rose-50 dark:bg-rose-900/10',
                                'education' => 'bg-purple-50 dark:bg-purple-900/10',
                                'health' => 'bg-green-50 dark:bg-green-900/10',
                                'gaming' => 'bg-violet-50 dark:bg-violet-900/10',
                                'news' => 'bg-teal-50 dark:bg-teal-900/10',
                                default => 'bg-gray-50 dark:bg-gray-800',
                            };
                            
                            $borderColor = match($subscription->category) {
                                'streaming' => 'border-red-200 dark:border-red-800',
                                'software' => 'border-blue-200 dark:border-blue-800',
                                'cloud' => 'border-indigo-200 dark:border-indigo-800',
                                'membership' => 'border-emerald-200 dark:border-emerald-800',
                                'utilities' => 'border-amber-200 dark:border-amber-800',
                                'phone' => 'border-rose-200 dark:border-rose-800',
                                'education' => 'border-purple-200 dark:border-purple-800',
                                'health' => 'border-green-200 dark:border-green-800',
                                'gaming' => 'border-violet-200 dark:border-violet-800',
                                'news' => 'border-teal-200 dark:border-teal-800',
                                default => 'border-gray-200 dark:border-gray-700',
                            };
                            
                            $iconColor = match($subscription->category) {
                                'streaming' => 'text-red-600 dark:text-red-400',
                                'software' => 'text-blue-600 dark:text-blue-400',
                                'cloud' => 'text-indigo-600 dark:text-indigo-400',
                                'membership' => 'text-emerald-600 dark:text-emerald-400',
                                'utilities' => 'text-amber-600 dark:text-amber-400',
                                'phone' => 'text-rose-600 dark:text-rose-400',
                                'education' => 'text-purple-600 dark:text-purple-400',
                                'health' => 'text-green-600 dark:text-green-400',
                                'gaming' => 'text-violet-600 dark:text-violet-400',
                                'news' => 'text-teal-600 dark:text-teal-400',
                                default => 'text-gray-600 dark:text-gray-400',
                            };
                            
                            $isUrgent = $subscription->days_left <= 3;
                            $progressPercentage = min(100, max(0, 100 - (($subscription->days_left / 30) * 100)));
                        @endphp
                        
                        <div class="subscription-card rounded-lg p-4 {{ $bgColor }} border {{ $borderColor }}">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-full flex items-center justify-center bg-white dark:bg-gray-800 {{ $iconColor }}">
                                        @switch($subscription->category)
                                            @case('streaming')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                                </svg>
                                                @break
                                            @case('software')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                                @break
                                            @case('cloud')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z" />
                                                </svg>
                                                @break
                                            @case('membership')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                                </svg>
                                                @break
                                            @case('utilities')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" />
                                                </svg>
                                                @break
                                            @case('phone')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                </svg>
                                                @break
                                            @default
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                </svg>
                                        @endswitch
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-medium text-gray-900 dark:text-white">{{ $subscription->name }}</div>
                                        <div class="flex items-center mt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500 dark:text-gray-400 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($subscription->billing_date)->format('M d, Y') }}
                                            </span>
                                            
                                            @if($isUrgent)
                                                <span class="days-indicator ml-2 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 animate-pulse-slow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-0.5 inline" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ $subscription->days_left }} days left
                                                </span>
                                            @else
                                                <span class="days-indicator ml-2 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ $subscription->days_left }} days left
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                        €{{ number_format($subscription->amount, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 capitalize">
                                        {{ $subscription->billing_cycle }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress bar for days remaining -->
                            <div class="progress-bar mt-4">
                                <div class="progress-value {{ $isUrgent ? 'bg-red-500' : 'bg-blue-500' }}" style="width: {{ $progressPercentage }}%;"></div>
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="mt-4 flex justify-end space-x-2">
                                <a 
                                    href="{{ route('filament.app.resources.monthly-subscriptions.edit', $subscription) }}" 
                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit
                                </a>
                                <a 
                                    href="#" 
                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
                                    onclick="event.preventDefault(); window.location.href='{{ route('filament.app.resources.monthly-subscriptions.edit', $subscription) }}';"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Mark as Paid
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 bg-gray-100 dark:bg-gray-800 rounded-lg text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900 dark:text-white">No upcoming payments</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You're all set for the next 30 days!</p>
                    <div class="mt-6">
                        <a 
                            href="{{ route('filament.app.resources.monthly-subscriptions.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            Add Subscription
                        </a>
                    </div>
                </div>
            @endif
        </x-filament::section>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Donut Chart Initialization
            if (document.getElementById('categoryDonutChart')) {
                initCategoryDonutChart();
            }
            
            // Bar Chart Initialization
            if (document.getElementById('subscriptionBarChart')) {
                initSubscriptionBarChart();
            }
            
            // Timeline Chart Initialization
            if (document.getElementById('paymentTimelineChart')) {
                initPaymentTimelineChart();
            }
            
            // Calendar Heatmap Initialization
            if (document.getElementById('paymentCalendar')) {
                initPaymentCalendar();
            }
        });

        function initCategoryDonutChart() {
            Chart.register(ChartDataLabels);
            
            const ctx = document.getElementById('categoryDonutChart').getContext('2d');
            
            // Extract data from PHP
            const categoryData = @json(collect($this->getCategoryBreakdown()));
            const categoryColors = @json($this->getCategoryColors());
            
            const labels = Object.keys(categoryData).map(key => key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' '));
            const values = Object.values(categoryData).map(item => item.total);
            const colors = Object.keys(categoryData).map(key => categoryColors[key] || '#6b7280');
            const percentages = values.map(value => {
                const total = values.reduce((sum, val) => sum + val, 0);
                return ((value / total) * 100).toFixed(1) + '%';
            });
            
            // Custom interaction plugin to handle animations
            const customInteraction = {
                id: 'customInteraction',
                beforeDraw(chart) {
                    const activeElements = chart.getActiveElements();
                    if (activeElements.length > 0) {
                        const { ctx } = chart;
                        const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                        const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                        
                        const dataIndex = activeElements[0].index;
                        const value = chart.data.datasets[0].data[dataIndex];
                        const label = chart.data.labels[dataIndex];
                        const percentage = percentages[dataIndex];
                        
                        // Draw center text
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = 'bold 16px Arial';
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#fff' : '#111827';
                        ctx.fillText(label, centerX, centerY - 15);
                        
                        ctx.font = 'bold 20px Arial';
                        ctx.fillStyle = colors[dataIndex];
                        ctx.fillText(`€${value.toFixed(2)}`, centerX, centerY + 10);
                        
                        ctx.font = '14px Arial';
                        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280';
                        ctx.fillText(percentage, centerX, centerY + 30);
                        ctx.restore();
                    }
                }
            };
            
            // Create chart with custom options
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                        hoverOffset: 15,
                        hoverBorderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        },
                        datalabels: {
                            display: function(context) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const value = context.dataset.data[context.dataIndex];
                                const percentage = (value / total) * 100;
                                // Only show labels for segments with more than 5% of the total
                                return percentage > 5;
                            },
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: function(value, context) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = ((value / total) * 100).toFixed(0);
                                return percentage + '%';
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                },
                plugins: [customInteraction]
            });
            
            // Add center text if no segment is hovered
            customInteraction.beforeDraw = function(chart) {
                const activeElements = chart.getActiveElements();
                if (activeElements.length === 0) {
                    const { ctx } = chart;
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    
                    const total = values.reduce((sum, val) => sum + val, 0);
                    
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    ctx.font = '14px Arial';
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280';
                    ctx.fillText('Total Monthly', centerX, centerY - 15);
                    
                    ctx.font = 'bold 20px Arial';
                    ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827';
                    ctx.fillText(`€${total.toFixed(2)}`, centerX, centerY + 15);
                    
                    ctx.restore();
                }
            };
        }

        function initSubscriptionBarChart() {
            const ctx = document.getElementById('subscriptionBarChart').getContext('2d');
            
            // Get subscription data
            const subscriptions = @json($this->getSubscriptionsSortedByMonthlyCost());
            const categoryColors = @json($this->getCategoryColors());
            
            // Extract data for chart
            const labels = subscriptions.map(sub => sub.name);
            const values = subscriptions.map(sub => sub.monthly_cost);
            const categories = subscriptions.map(sub => sub.category);
            const colors = subscriptions.map(sub => categoryColors[sub.category] || '#6b7280');
            
            // Create gradients for bars
            const gradients = colors.map((color, index) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, color);
                gradient.addColorStop(1, adjustColor(color, -30));
                return gradient;
            });
            
            // Create chart with custom options
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Cost (€)',
                        data: values,
                        backgroundColor: gradients,
                        borderColor: colors,
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        hoverBorderWidth: 0,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            padding: 12,
                            cornerRadius: 8,
                            caretSize: 6,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `€${context.raw.toFixed(2)} per month`;
                                },
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    return `Category: ${categories[index].charAt(0).toUpperCase() + categories[index].slice(1).replace('_', ' ')}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: function(context) {
                                    return document.documentElement.classList.contains('dark') 
                                        ? 'rgba(255, 255, 255, 0.05)' 
                                        : 'rgba(0, 0, 0, 0.05)';
                                }
                            },
                            ticks: {
                                callback: function(value) { return '€' + value; },
                                color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280',
                                padding: 10
                            }
                        },
                        y: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280',
                                padding: 10,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        delay: function(context) {
                            return context.dataIndex * 100;
                        },
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        function initPaymentTimelineChart() {
            const ctx = document.getElementById('paymentTimelineChart').getContext('2d');
            
            // Generate monthly data
            const subscriptions = @json($this->getAllActiveSubscriptions());
            const monthlyTotal = {{ $this->getTotalMonthlyAmount() }};
            
            // Get current month and next 11 months
            const currentDate = new Date();
            const months = [];
            const monthLabels = [];
            
            for (let i = 0; i < 12; i++) {
                const date = new Date(currentDate.getFullYear(), currentDate.getMonth() + i, 1);
                months.push(date);
                monthLabels.push(date.toLocaleString('default', { month: 'short', year: 'numeric' }));
            }
            
            // Calculate payments for each month based on billing cycles
            const monthlyPayments = Array(12).fill(0);
            
            subscriptions.forEach(sub => {
                const amount = parseFloat(sub.amount);
                const billingDate = new Date(sub.billing_date);
                
                // Handle different billing cycles
                if (sub.billing_cycle === 'monthly') {
                    // Every month has a payment
                    for (let i = 0; i < 12; i++) {
                        monthlyPayments[i] += amount;
                    }
                } else if (sub.billing_cycle === 'quarterly') {
                    // Every 3 months
                    const monthDiff = (billingDate.getMonth() - currentDate.getMonth() + 12) % 3;
                    for (let i = 0; i < 12; i++) {
                        if ((i + monthDiff) % 3 === 0) {
                            monthlyPayments[i] += amount;
                        }
                    }
                } else if (sub.billing_cycle === 'biannual') {
                    // Every 6 months
                    const monthDiff = (billingDate.getMonth() - currentDate.getMonth() + 12) % 6;
                    for (let i = 0; i < 12; i++) {
                        if ((i + monthDiff) % 6 === 0) {
                            monthlyPayments[i] += amount;
                        }
                    }
                } else if (sub.billing_cycle === 'annual') {
                    // Once a year
                    const monthDiff = (billingDate.getMonth() - currentDate.getMonth() + 12) % 12;
                    monthlyPayments[monthDiff] += amount;
                }
            });
            
            // Create an array of average monthly values
            const averageData = Array(12).fill(monthlyTotal);
            
            // Create chart with advanced styling
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [
                        {
                            label: 'Monthly Payments',
                            data: monthlyPayments,
                            fill: {
                                target: 'origin',
                                above: function(context) {
                                    const chart = context.chart;
                                    const { ctx, chartArea } = chart;
                                    
                                    if (!chartArea) {
                                        return null;
                                    }
                                    
                                    // Create gradient
                                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                                    gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');
                                    
                                    return gradient;
                                }
                            },
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2
                        },
                        {
                            label: 'Average Monthly Cost',
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
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 8,
                            caretSize: 6,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: €${context.raw.toFixed(2)}`;
                                },
                                afterLabel: function(context) {
                                    if (context.datasetIndex === 0) {
                                        const diff = context.raw - monthlyTotal;
                                        const percentage = ((diff / monthlyTotal) * 100).toFixed(1);
                                        
                                        if (diff > 0) {
                                            return `${percentage}% above average`;
                                        } else if (diff < 0) {
                                            return `${Math.abs(percentage)}% below average`;
                                        } else {
                                            return 'Equal to average';
                                        }
                                    }
                                    return '';
                                }
                            }
                        },
                        legend: {
                            labels: {
                                padding: 20,
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: function(context) {
                                    return document.documentElement.classList.contains('dark') 
                                        ? 'rgba(255, 255, 255, 0.05)' 
                                        : 'rgba(0, 0, 0, 0.05)';
                                }
                            },
                            ticks: {
                                callback: function(value) { return '€' + value; },
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        function initPaymentCalendar() {
    const subscriptions = @json($this->getAllActiveSubscriptions());
    const calendarContainer = document.getElementById('paymentCalendar');
    const tooltip = document.getElementById('calendarTooltip');
    
    // Clear any existing calendar
    calendarContainer.innerHTML = '';
    
    // Get current date and create navigation controls
    const currentDate = new Date();
    let viewDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    
    // Create navigation controls
    const navContainer = document.createElement('div');
    navContainer.className = 'flex items-center justify-between mb-4';
    
    // Month display and navigation arrows
    const monthDisplay = document.createElement('h3');
    monthDisplay.className = 'text-lg font-medium text-gray-900 dark:text-white';
    updateMonthDisplay();
    
    const navButtons = document.createElement('div');
    navButtons.className = 'flex space-x-2';
    
    const prevButton = document.createElement('button');
    prevButton.className = 'p-2 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500';
    prevButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
    </svg>`;
    prevButton.addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() - 1);
        updateCalendar();
    });
    
    const nextButton = document.createElement('button');
    nextButton.className = 'p-2 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500';
    nextButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
    </svg>`;
    nextButton.addEventListener('click', () => {
        viewDate.setMonth(viewDate.getMonth() + 1);
        updateCalendar();
    });
    
    const todayButton = document.createElement('button');
    todayButton.className = 'px-3 py-1 text-sm rounded-md text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500';
    todayButton.textContent = 'Today';
    todayButton.addEventListener('click', () => {
        viewDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        updateCalendar();
    });
    
    navButtons.appendChild(prevButton);
    navButtons.appendChild(todayButton);
    navButtons.appendChild(nextButton);
    
    navContainer.appendChild(monthDisplay);
    navContainer.appendChild(navButtons);
    
    calendarContainer.appendChild(navContainer);
    
    // Create calendar grid container
    const calendarGrid = document.createElement('div');
    calendarGrid.className = 'calendar-grid';
    
    // Apply CSS grid styling
    calendarGrid.style.display = 'grid';
    calendarGrid.style.gridTemplateColumns = 'repeat(7, 1fr)';
    calendarGrid.style.gap = '2px';
    
    calendarContainer.appendChild(calendarGrid);
    
    // Calculate payment days and amounts
    function calculatePaymentData() {
        const paymentData = {};
        
        subscriptions.forEach(sub => {
            const billingDate = new Date(sub.billing_date);
            const amount = parseFloat(sub.amount);
            
            // Handle different billing cycles and calculate next payments
            let nextPaymentDate = new Date(billingDate);
            
            // If the first payment date is before the start date, 
            // advance to the next payment in the cycle
            const startDate = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
            startDate.setMonth(startDate.getMonth() - 1); // Look one month back to catch recurring payments
            
            while (nextPaymentDate < startDate) {
                if (sub.billing_cycle === 'monthly') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 1);
                } else if (sub.billing_cycle === 'quarterly') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 3);
                } else if (sub.billing_cycle === 'biannual') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 6);
                } else if (sub.billing_cycle === 'annual') {
                    nextPaymentDate.setFullYear(nextPaymentDate.getFullYear() + 1);
                }
            }
            
            // Add upcoming payments within the next 3 months
            let endDate = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
            endDate.setMonth(endDate.getMonth() + 3); // Show 3 months of data
            
            while (nextPaymentDate < endDate) {
                const dateKey = nextPaymentDate.toISOString().split('T')[0];
                
                if (!paymentData[dateKey]) {
                    paymentData[dateKey] = {
                        amount: 0,
                        subscriptions: []
                    };
                }
                
                paymentData[dateKey].amount += amount;
                paymentData[dateKey].subscriptions.push({
                    name: sub.name,
                    amount: amount,
                    category: sub.category
                });
                
                // Calculate next payment date based on billing cycle
                if (sub.billing_cycle === 'monthly') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 1);
                } else if (sub.billing_cycle === 'quarterly') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 3);
                } else if (sub.billing_cycle === 'biannual') {
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + 6);
                } else if (sub.billing_cycle === 'annual') {
                    nextPaymentDate.setFullYear(nextPaymentDate.getFullYear() + 1);
                }
            }
        });
        
        return paymentData;
    }
    
    // Update the month display
    function updateMonthDisplay() {
        monthDisplay.textContent = viewDate.toLocaleString('default', { 
            month: 'long', 
            year: 'numeric' 
        });
    }
    
    // Create weekday headers
    function addWeekdayHeaders() {
        const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        weekdays.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'text-center text-xs font-medium text-gray-500 dark:text-gray-400 py-2';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
    }
    
    // Build the calendar for the current view month
    function buildCalendarDays(paymentData) {
        // Find the max payment amount for color scaling
        const amounts = Object.values(paymentData).map(day => day.amount);
        const maxAmount = Math.max(...amounts, 1); // Avoid division by zero
        
        // Determine the first day to display (start of month)
        const firstDay = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
        const startingDayOfWeek = firstDay.getDay(); // 0 = Sunday, 1 = Monday, etc.
        
        // Add empty cells for days before the start of the month
        for (let i = 0; i < startingDayOfWeek; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day-empty';
            emptyDay.style.aspectRatio = '1';
            emptyDay.style.backgroundColor = 'rgba(229, 231, 235, 0.2)';
            emptyDay.style.borderRadius = '4px';
            calendarGrid.appendChild(emptyDay);
        }
        
        // Get number of days in the month
        const daysInMonth = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 0).getDate();
        
        // Create a day cell for each day in the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(viewDate.getFullYear(), viewDate.getMonth(), day);
            const dateKey = date.toISOString().split('T')[0];
            const dayData = paymentData[dateKey] || { amount: 0, subscriptions: [] };
            
            // Create day element
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day relative';
            dayElement.style.aspectRatio = '1';
            dayElement.style.padding = '2px';
            dayElement.style.overflow = 'hidden';
            dayElement.style.borderRadius = '4px';
            dayElement.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
            dayElement.style.cursor = 'pointer';
            
            // Determine if it's today
            const isToday = date.toDateString() === new Date().toDateString();
            
            // Create inner content container
            const innerContainer = document.createElement('div');
            innerContainer.className = 'w-full h-full rounded-sm flex flex-col p-1 relative';
            innerContainer.style.border = isToday ? '2px solid #ef4444' : '1px solid rgba(229, 231, 235, 0.5)';
            innerContainer.style.backgroundColor = dayData.amount > 0 ? 
                getCategoryColorGradient(dayData.subscriptions) : 
                'rgba(255, 255, 255, 0.5)';
            
            // In dark mode
            if (document.documentElement.classList.contains('dark')) {
                innerContainer.style.backgroundColor = dayData.amount > 0 ? 
                    getCategoryColorGradient(dayData.subscriptions, true) : 
                    'rgba(30, 41, 59, 0.5)';
                innerContainer.style.border = isToday ? '2px solid #ef4444' : '1px solid rgba(55, 65, 81, 0.5)';
            }
            
            // Add day number
            const dayNumber = document.createElement('div');
            dayNumber.className = 'text-xs font-medium';
            dayNumber.style.color = isToday ? '#ef4444' : (dayData.amount > 0 ? '#1f2937' : '#6b7280');
            if (document.documentElement.classList.contains('dark')) {
                dayNumber.style.color = isToday ? '#ef4444' : (dayData.amount > 0 ? '#f3f4f6' : '#9ca3af');
            }
            dayNumber.textContent = day;
            innerContainer.appendChild(dayNumber);
            
            // Add payment indicator if there are payments
            if (dayData.amount > 0) {
                // Add payment amount
                const paymentAmount = document.createElement('div');
                paymentAmount.className = 'text-xs font-semibold mt-auto text-center';
                paymentAmount.style.color = '#1f2937';
                if (document.documentElement.classList.contains('dark')) {
                    paymentAmount.style.color = '#f3f4f6';
                }
                paymentAmount.textContent = `€${dayData.amount.toFixed(0)}`;
                innerContainer.appendChild(paymentAmount);
                
                // Add small indicator dots for multiple subscriptions
                if (dayData.subscriptions.length > 1) {
                    const dotsContainer = document.createElement('div');
                    dotsContainer.className = 'flex justify-center space-x-0.5 mt-0.5';
                    
                    const maxDots = Math.min(dayData.subscriptions.length, 3);
                    for (let i = 0; i < maxDots; i++) {
                        const dot = document.createElement('div');
                        dot.className = 'w-1 h-1 rounded-full bg-gray-700 dark:bg-gray-300';
                        dotsContainer.appendChild(dot);
                    }
                    
                    innerContainer.appendChild(dotsContainer);
                }
            }
            
            // Add hover effect and tooltip data
            dayElement.addEventListener('mouseenter', function(e) {
                this.style.transform = 'scale(1.05)';
                this.style.zIndex = '10';
                this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
                
                const formattedDate = date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    month: 'short', 
                    day: 'numeric',
                    year: 'numeric'
                });
                
                // Build tooltip content
                let tooltipContent = `
                    <div class="font-medium text-sm mb-1 border-b border-gray-700 pb-1">${formattedDate}</div>
                `;
                
                if (dayData.subscriptions.length > 0) {
                    tooltipContent += `
                        <div class="font-medium text-green-400 text-sm">€${dayData.amount.toFixed(2)}</div>
                        <div class="text-xs mt-2 font-medium text-gray-300">Subscriptions:</div>
                        <div class="space-y-1 mt-1 max-h-40 overflow-y-auto">
                    `;
                    
                    // Sort subscriptions by amount (highest first)
                    const sortedSubs = [...dayData.subscriptions].sort((a, b) => b.amount - a.amount);
                    
                    sortedSubs.forEach(sub => {
                        tooltipContent += `
                            <div class="flex justify-between items-center text-xs">
                                <span class="truncate max-w-36">${sub.name}</span>
                                <span class="font-medium">€${sub.amount.toFixed(2)}</span>
                            </div>
                        `;
                    });
                    
                    tooltipContent += `</div>`;
                } else {
                    tooltipContent += `<div class="text-xs mt-1 text-gray-400">No payments scheduled</div>`;
                }
                
                tooltip.innerHTML = tooltipContent;
                tooltip.style.opacity = 1;
                
                // Position the tooltip
                const rect = this.getBoundingClientRect();
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                tooltip.style.left = `${rect.left + window.pageXOffset + rect.width / 2}px`;
                tooltip.style.top = `${rect.top + scrollTop - 5}px`;
                tooltip.style.transform = 'translate(-50%, -100%)';
                
                // Adjust if tooltip goes off-screen
                const tooltipRect = tooltip.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                
                if (tooltipRect.left < 10) {
                    tooltip.style.left = '10px';
                    tooltip.style.transform = 'translate(0, -100%)';
                } else if (tooltipRect.right > viewportWidth - 10) {
                    tooltip.style.left = `${viewportWidth - 10}px`;
                    tooltip.style.transform = 'translate(-100%, -100%)';
                }
            });
            
            dayElement.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.zIndex = '1';
                this.style.boxShadow = 'none';
                tooltip.style.opacity = 0;
            });
            
            // Add click event for detailed view
            dayElement.addEventListener('click', function() {
                if (dayData.subscriptions.length > 0) {
                    showDayDetailModal(date, dayData);
                }
            });
            
            // Store data attributes for potential JS interactions
            dayElement.dataset.date = dateKey;
            dayElement.dataset.amount = dayData.amount.toFixed(2);
            dayElement.dataset.count = dayData.subscriptions.length;
            
            dayElement.appendChild(innerContainer);
            calendarGrid.appendChild(dayElement);
        }
        
        // Create modal for day details
        function showDayDetailModal(date, dayData) {
            // Check if modal already exists, remove if it does
            const existingModal = document.getElementById('calendar-day-modal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Create modal backdrop
            const modalBackdrop = document.createElement('div');
            modalBackdrop.id = 'calendar-day-modal';
            modalBackdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
            modalBackdrop.style.backdropFilter = 'blur(3px)';
            
            // Create modal container
            const modalContainer = document.createElement('div');
            modalContainer.className = 'bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[80vh] flex flex-col';
            modalContainer.style.transform = 'scale(0.95)';
            modalContainer.style.transition = 'transform 0.2s ease';
            
            // Create modal header
            const modalHeader = document.createElement('div');
            modalHeader.className = 'px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center';
            
            const formattedDate = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                month: 'long', 
                day: 'numeric',
                year: 'numeric'
            });
            
            const modalTitle = document.createElement('h3');
            modalTitle.className = 'text-lg font-medium text-gray-900 dark:text-white';
            modalTitle.textContent = formattedDate;
            
            const closeButton = document.createElement('button');
            closeButton.className = 'text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:text-gray-500 transition-colors';
            closeButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>`;
            closeButton.addEventListener('click', () => {
                modalBackdrop.style.opacity = '0';
                modalContainer.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    modalBackdrop.remove();
                }, 200);
            });
            
            modalHeader.appendChild(modalTitle);
            modalHeader.appendChild(closeButton);
            
            // Create modal content
            const modalContent = document.createElement('div');
            modalContent.className = 'p-4 overflow-y-auto flex-grow';
            
            const totalAmount = document.createElement('div');
            totalAmount.className = 'text-2xl font-bold text-gray-900 dark:text-white mb-4';
            totalAmount.textContent = `€${dayData.amount.toFixed(2)}`;
            
            const subscriptionsTitle = document.createElement('h4');
            subscriptionsTitle.className = 'text-sm font-medium text-gray-500 dark:text-gray-400 mb-2';
            subscriptionsTitle.textContent = `${dayData.subscriptions.length} subscription${dayData.subscriptions.length !== 1 ? 's' : ''}`;
            
            const subscriptionsList = document.createElement('div');
            subscriptionsList.className = 'space-y-3';
            
            // Sort subscriptions by amount (highest first)
            const sortedSubs = [...dayData.subscriptions].sort((a, b) => b.amount - a.amount);
            
            sortedSubs.forEach(sub => {
                const categoryColors = {
                    'streaming': 'rgb(239, 68, 68)',
                    'software': 'rgb(59, 130, 246)',
                    'cloud': 'rgb(79, 70, 229)',
                    'membership': 'rgb(16, 185, 129)',
                    'utilities': 'rgb(245, 158, 11)',
                    'phone': 'rgb(244, 63, 94)',
                    'education': 'rgb(168, 85, 247)',
                    'health': 'rgb(34, 197, 94)',
                    'gaming': 'rgb(139, 92, 246)',
                    'news': 'rgb(20, 184, 166)',
                    'default': 'rgb(107, 114, 128)'
                };
                
                const categoryColor = categoryColors[sub.category] || categoryColors.default;
                
                const subItem = document.createElement('div');
                subItem.className = 'flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700';
                
                const iconContainer = document.createElement('div');
                iconContainer.className = 'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center';
                iconContainer.style.backgroundColor = `${categoryColor}25`;
                iconContainer.style.color = categoryColor;
                
                // Use appropriate icon based on category
                let iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>`;
                
                // Add different icons based on category
                switch(sub.category) {
                    case 'streaming':
                        iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>`;
                        break;
                    case 'software':
                        iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>`;
                        break;
                    // Add more cases for other categories
                }
                
                iconContainer.innerHTML = iconSvg;
                
                const subContent = document.createElement('div');
                subContent.className = 'ml-3 flex-grow';
                
                const subName = document.createElement('div');
                subName.className = 'text-sm font-medium text-gray-900 dark:text-white';
                subName.textContent = sub.name;
                
                const categoryName = document.createElement('div');
                categoryName.className = 'text-xs text-gray-500 dark:text-gray-400 capitalize';
                categoryName.textContent = sub.category.replace('_', ' ');
                
                subContent.appendChild(subName);
                subContent.appendChild(categoryName);
                
                const subAmount = document.createElement('div');
                subAmount.className = 'text-sm font-semibold text-gray-900 dark:text-white';
                subAmount.textContent = `€${sub.amount.toFixed(2)}`;
                
                subItem.appendChild(iconContainer);
                subItem.appendChild(subContent);
                subItem.appendChild(subAmount);
                
                subscriptionsList.appendChild(subItem);
            });
            
            modalContent.appendChild(totalAmount);
            modalContent.appendChild(subscriptionsTitle);
            modalContent.appendChild(subscriptionsList);
            
            // Create modal footer
            const modalFooter = document.createElement('div');
            modalFooter.className = 'px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end';
            
            const closeModalButton = document.createElement('button');
            closeModalButton.className = 'px-4 py-2 bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400';
            closeModalButton.textContent = 'Close';
            closeModalButton.addEventListener('click', () => {
                modalBackdrop.style.opacity = '0';
                modalContainer.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    modalBackdrop.remove();
                }, 200);
            });
            
            modalFooter.appendChild(closeModalButton);
            
            // Assemble modal
            modalContainer.appendChild(modalHeader);
            modalContainer.appendChild(modalContent);
            modalContainer.appendChild(modalFooter);
            modalBackdrop.appendChild(modalContainer);
            
            // Add to document
            document.body.appendChild(modalBackdrop);
            
            // Add animation effect
            setTimeout(() => {
                modalContainer.style.transform = 'scale(1)';
            }, 10);
            
            // Close modal when clicking outside
            modalBackdrop.addEventListener('click', (e) => {
                if (e.target === modalBackdrop) {
                    modalBackdrop.style.opacity = '0';
                    modalContainer.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        modalBackdrop.remove();
                    }, 200);
                }
            });
        }
    }
    
    // Get category color gradient
    function getCategoryColorGradient(subscriptions, isDarkMode = false) {
        // If no subscriptions, return default
        if (!subscriptions || subscriptions.length === 0) {
            return isDarkMode ? 'rgba(30, 41, 59, 0.5)' : 'rgba(255, 255, 255, 0.5)';
        }
        
        // Define category colors with 0.15 opacity for light theme and 0.25 for dark theme
        const categoryColors = {
            'streaming': isDarkMode ? 'rgba(239, 68, 68, 0.25)' : 'rgba(239, 68, 68, 0.15)',
            'software': isDarkMode ? 'rgba(59, 130, 246, 0.25)' : 'rgba(59, 130, 246, 0.15)',
            'cloud': isDarkMode ? 'rgba(79, 70, 229, 0.25)' : 'rgba(79, 70, 229, 0.15)',
            'membership': isDarkMode ? 'rgba(16, 185, 129, 0.25)' : 'rgba(16, 185, 129, 0.15)',
            'utilities': isDarkMode ? 'rgba(245, 158, 11, 0.25)' : 'rgba(245, 158, 11, 0.15)',
            'phone': isDarkMode ? 'rgba(244, 63, 94, 0.25)' : 'rgba(244, 63, 94, 0.15)',
            'education': isDarkMode ? 'rgba(168, 85, 247, 0.25)' : 'rgba(168, 85, 247, 0.15)',
            'health': isDarkMode ? 'rgba(34, 197, 94, 0.25)' : 'rgba(34, 197, 94, 0.15)',
            'gaming': isDarkMode ? 'rgba(139, 92, 246, 0.25)' : 'rgba(139, 92, 246, 0.15)',
            'news': isDarkMode ? 'rgba(20, 184, 166, 0.25)' : 'rgba(20, 184, 166, 0.15)',
            'default': isDarkMode ? 'rgba(107, 114, 128, 0.25)' : 'rgba(107, 114, 128, 0.15)'
        };
        
        // If only one subscription, return its color
        if (subscriptions.length === 1) {
            return categoryColors[subscriptions[0].category] || categoryColors.default;
        }
        
        // If multiple subscriptions, find dominant category by amount
        let totalAmount = 0;
        const categoryTotals = {};
        
        subscriptions.forEach(sub => {
            const category = sub.category || 'default';
            if (!categoryTotals[category]) {
                categoryTotals[category] = 0;
            }
            categoryTotals[category] += sub.amount;
            totalAmount += sub.amount;
        });
        
        // Find category with highest amount
        let dominantCategory = 'default';
        let highestAmount = 0;
        
        for (const [category, amount] of Object.entries(categoryTotals)) {
            if (amount > highestAmount) {
                highestAmount = amount;
                dominantCategory = category;
            }
        }
        
        return categoryColors[dominantCategory] || categoryColors.default;
    }
    
    // Update the calendar
    function updateCalendar() {
        // Clear current grid except for the navigation
        while (calendarGrid.firstChild) {
            calendarGrid.removeChild(calendarGrid.firstChild);
        }
        
        // Update month display
        updateMonthDisplay();
        
        // Add weekday headers
        addWeekdayHeaders();
        
        // Calculate payment data
        const paymentData = calculatePaymentData();
        
        // Build the calendar
        buildCalendarDays(paymentData);
    }
    
    // Initialize
    updateCalendar();
    
    // Enhance tooltip styling
    if (tooltip) {
        tooltip.className = 'tooltip fixed z-50 bg-gray-800 text-white px-4 py-2 rounded-lg shadow-lg pointer-events-none opacity-0 transition-opacity w-64';
        tooltip.style.backdropFilter = 'blur(8px)';
        tooltip.style.border = '1px solid rgba(75, 85, 99, 0.4)';
    }
    
    // Update calendar legend
    const legendContainer = document.querySelector('[style*="Subscription Density"]')?.closest('div')?.parentElement;
    
    if (legendContainer) {
        legendContainer.innerHTML = `
            <div class="flex justify-between items-center mt-6 mb-4">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Calendar Legend</div>
                <div class="flex space-x-4 items-center">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-1.5" style="background-color: rgba(239, 68, 68, 0.2);"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Streaming</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-1.5" style="background-color: rgba(59, 130, 246, 0.2);"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Software</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-1.5" style="background-color: rgba(79, 70, 229, 0.2);"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Cloud</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-1.5" style="background-color: rgba(16, 185, 129, 0.2);"></div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Other</span>
                    </div>
                </div>
            </div>
        `;
    }
}

// Also update the CSS styles for better appearance
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .tooltip {
            max-width: 280px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 640px) {
            .calendar-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }
        
        #calendarTooltip {
            z-index: 9999;
        }
        
        /* Animation for day cells */
        @keyframes dayAppear {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .calendar-day {
            animation: dayAppear 0.3s forwards;
            animation-delay: calc(var(--day-index, 0) * 0.03s);
        }
    `;
    document.head.appendChild(style);
});

        // Helper function to adjust color brightness
        function adjustColor(color, amount) {
            return '#' + color.replace(/^#/, '').replace(/../g, color => 
                ('0' + Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2)
            );
        }
    </script>
    @endpush
</x-filament-panels::page>