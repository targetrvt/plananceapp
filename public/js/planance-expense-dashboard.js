document.addEventListener('alpine:init', () => {
    Alpine.data('expensesDashboard', function(initialData) {
        return {
            // Data properties
            categoryBreakdown: initialData.categoryBreakdown || [],
            monthlyTrend: initialData.monthlyTrend || [],
            dailyTrend: initialData.dailyTrend || [],
            totalExpenses: initialData.totalExpenses || 0,
            averageDailyExpense: initialData.averageDailyExpense || 0,
            unhealthyExpenses: initialData.unhealthyExpenses || 0,
            recentTransactions: initialData.recentTransactions || [],
            largestExpenses: initialData.largestExpenses || [],
            timeframe: initialData.timeframe || 'month',
            category: initialData.category || 'all',
            startDate: initialData.startDate,
            endDate: initialData.endDate,
            darkMode: initialData.darkMode || document.documentElement.classList.contains('dark'),
            
            // Chart instances
            categoryChart: null,
            dailyChart: null,
            monthlyChart: null,
            chartsInitialized: false,
            
            // Colors for charts
            lightColors: [
                '#4F46E5', '#3B82F6', '#06B6D4', '#10B981', '#F59E0B', 
                '#8B5CF6', '#EC4899', '#EF4444', '#6366F1', '#0EA5E9',
                '#14B8A6', '#84CC16', '#D946EF', '#F43F5E', '#FACC15'
            ],
            darkColors: [
                '#818CF8', '#60A5FA', '#22D3EE', '#34D399', '#FBBF24', 
                '#A78BFA', '#F472B6', '#FB7185', '#A5B4FC', '#38BDF8',
                '#2DD4BF', '#A3E635', '#E879F9', '#FB7185', '#FDE047'
            ],
            
            // Initialize the component
            init() {
                // Safely load ECharts if needed
                this.safelyLoadECharts();
                
                // Handle window resize with debounce
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => this.resizeCharts(), 250);
                });
                
                // Listen for dark mode changes
                this.setupDarkModeDetection();
                
                // Watch for refreshes
                document.addEventListener('echart-loaded', () => this.initializeCharts());
                
                // Handle Livewire refresh events
                if (typeof Livewire !== 'undefined') {
                    document.addEventListener('livewire:update', () => {
                        setTimeout(() => this.refreshChartsAfterUpdate(), 100);
                    });
                }
            },
            
            // Safely load ECharts library if needed
            safelyLoadECharts() {
                try {
                    if (typeof echarts !== 'undefined') {
                        // ECharts already loaded, initialize charts
                        this.$nextTick(() => {
                            this.initializeCharts();
                            this.chartsInitialized = true;
                        });
                        return;
                    }
                    
                    // Create script element to load ECharts
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js';
                    script.integrity = 'sha512-EmNxF3E6bM0Xg1zvmkeYD3HDBeGxtsG92IxFt1myNZhXdCav9MzvuH/zNMBU1DmIPN6njrhX1VTbqdJxQ2wHDg==';
                    script.crossOrigin = 'anonymous';
                    script.referrerPolicy = 'no-referrer';
                    
                    script.onload = () => {
                        // Dispatch custom event when loaded
                        document.dispatchEvent(new CustomEvent('echart-loaded'));
                        this.chartsInitialized = true;
                    };
                    
                    script.onerror = (error) => {
                        console.error('Failed to load ECharts library:', error);
                    };
                    
                    document.head.appendChild(script);
                } catch (error) {
                    console.error('Error loading ECharts:', error);
                }
            },
            
            // Handle chart refreshes after Livewire updates
            refreshChartsAfterUpdate() {
                try {
                    if (typeof echarts === 'undefined') {
                        this.safelyLoadECharts();
                        return;
                    }
                    
                    if (this.chartsInitialized) {
                        this.resizeCharts();
                    } else {
                        this.initializeCharts();
                        this.chartsInitialized = true;
                    }
                } catch (error) {
                    console.error('Error refreshing charts:', error);
                }
            },
            
            // Setup dark mode detection
            setupDarkModeDetection() {
                try {
                    // Watch for class changes on the HTML element
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const isDarkMode = document.documentElement.classList.contains('dark');
                                if (this.darkMode !== isDarkMode) {
                                    this.darkMode = isDarkMode;
                                    this.refreshCharts();
                                }
                            }
                        });
                    });
                    
                    observer.observe(document.documentElement, { attributes: true });
                    
                    // Also listen for system preference changes
                    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                    mediaQuery.addEventListener('change', (e) => {
                        if (document.documentElement.classList.contains('dark') !== e.matches && 
                            document.documentElement.dataset.theme === 'auto') {
                            this.darkMode = e.matches;
                            this.refreshCharts();
                        }
                    });
                } catch (error) {
                    console.error('Error setting up dark mode detection:', error);
                }
            },
            
            // Initialize all charts
            initializeCharts() {
                try {
                    if (typeof echarts === 'undefined') {
                        console.warn('ECharts not loaded yet, will try again');
                        this.safelyLoadECharts();
                        return;
                    }
                    
                    this.$nextTick(() => {
                        // Initialize charts if DOM elements exist
                        if (document.getElementById('categoryPieChart')) {
                            this.initCategoryChart();
                        }
                        
                        if (document.getElementById('dailyTrendChart')) {
                            this.initDailyTrendChart();
                        }
                        
                        if (document.getElementById('monthlyTrendChart')) {
                            this.initMonthlyTrendChart();
                        }
                    });
                } catch (error) {
                    console.error('Error initializing charts:', error);
                }
            },
            
            // Refresh all charts (for theme changes, etc.)
            refreshCharts() {
                try {
                    // Safely dispose existing charts first
                    if (this.categoryChart) {
                        try { this.categoryChart.dispose(); } catch {}
                        this.categoryChart = null;
                    }
                    
                    if (this.dailyChart) {
                        try { this.dailyChart.dispose(); } catch {}
                        this.dailyChart = null;
                    }
                    
                    if (this.monthlyChart) {
                        try { this.monthlyChart.dispose(); } catch {}
                        this.monthlyChart = null;
                    }
                    
                    // Re-initialize the charts
                    this.$nextTick(() => this.initializeCharts());
                } catch (error) {
                    console.error('Error refreshing charts:', error);
                }
            },
            
            // Resize all charts
            resizeCharts() {
                try {
                    if (this.categoryChart && document.getElementById('categoryPieChart')) {
                        this.categoryChart.resize();
                    }
                    
                    if (this.dailyChart && document.getElementById('dailyTrendChart')) {
                        this.dailyChart.resize();
                    }
                    
                    if (this.monthlyChart && document.getElementById('monthlyTrendChart')) {
                        this.monthlyChart.resize();
                    }
                } catch (error) {
                    console.error('Error resizing charts:', error);
                }
            },
            
            // Get appropriate chart colors based on theme
            getChartColors() {
                return this.darkMode ? this.darkColors : this.lightColors;
            },
            
            // Initialize category pie chart
            initCategoryChart() {
                if (!this.categoryBreakdown || this.categoryBreakdown.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('categoryPieChart');
                if (!chartElement) return;
                
                // Safely dispose existing chart if it exists
                if (this.categoryChart) {
                    try { this.categoryChart.dispose(); } catch {}
                }
                
                const colors = this.getChartColors();
                
                // Prepare chart data
                const chartData = this.categoryBreakdown.map((item, index) => ({
                    name: this.formatCategoryName(item.category),
                    value: item.total,
                    percentage: item.percentage,
                    itemStyle: {
                        color: colors[index % colors.length]
                    }
                }));
                
                // Create chart options
                const options = {
                    tooltip: {
                        trigger: 'item',
                        formatter: params => {
                            return `<div style="font-weight:500;">${params.name}</div>` +
                                `<div>${this.formatMoney(params.value)} (${params.percent}%)</div>`;
                        }
                    },
                    legend: {
                        type: 'scroll',
                        orient: 'vertical',
                        right: 10,
                        top: 'center',
                        textStyle: {
                            fontSize: 12,
                            color: this.darkMode ? '#d1d5db' : '#4b5563'
                        },
                        formatter: name => {
                            // Truncate long category names
                            return name.length > 15 ? name.slice(0, 15) + '...' : name;
                        }
                    },
                    series: [{
                        name: 'Expense Categories',
                        type: 'pie',
                        radius: ['45%', '75%'],
                        avoidLabelOverlap: true,
                        itemStyle: {
                            borderRadius: 6,
                            borderColor: this.darkMode ? '#1f2937' : '#ffffff',
                            borderWidth: 2
                        },
                        label: {
                            show: false
                        },
                        emphasis: {
                            label: {
                                show: false
                            },
                            scale: true,
                            scaleSize: 10
                        },
                        labelLine: {
                            show: false
                        },
                        data: chartData
                    }]
                };
                
                try {
                    // Initialize chart with proper renderer
                    this.categoryChart = echarts.init(chartElement, null, { 
                        renderer: 'canvas',
                        width: 'auto',
                        height: 'auto'
                    });
                    this.categoryChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing category chart:', error);
                }
            },
            
            // Initialize daily trend chart
            initDailyTrendChart() {
                if (!this.dailyTrend || this.dailyTrend.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('dailyTrendChart');
                if (!chartElement) return;
                
                // Safely dispose existing chart if it exists
                if (this.dailyChart) {
                    try { this.dailyChart.dispose(); } catch {}
                }
                
                // Determine theme-specific colors
                const lineColor = this.darkMode ? '#10b981' : '#059669';
                const areaColorTop = this.darkMode ? 'rgba(16, 185, 129, 0.5)' : 'rgba(5, 150, 105, 0.5)';
                const areaColorBottom = this.darkMode ? 'rgba(16, 185, 129, 0.05)' : 'rgba(5, 150, 105, 0.05)';
                const axisLineColor = this.darkMode ? '#374151' : '#e5e7eb';
                const axisLabelColor = this.darkMode ? '#d1d5db' : '#6b7280';
                const splitLineColor = this.darkMode ? '#1f2937' : '#f3f4f6';
                
                // Create chart options
                const options = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: params => {
                            return `<div style="font-weight:500;">${params[0].name}</div>` +
                                `<div>Expenses: ${this.formatMoney(params[0].value)}</div>`;
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: this.dailyTrend.map(item => item.date),
                        axisLine: {
                            lineStyle: {
                                color: axisLineColor
                            }
                        },
                        axisLabel: {
                            color: axisLabelColor,
                            fontSize: 10,
                            rotate: this.dailyTrend.length > 15 ? 45 : 0
                        }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: {
                            show: false
                        },
                        axisLabel: {
                            color: axisLabelColor,
                            fontSize: 10,
                            formatter: value => {
                                return '€' + value;
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: splitLineColor,
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        name: 'Daily Expenses',
                        type: 'line',
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 6,
                        lineStyle: {
                            width: 3,
                            color: lineColor
                        },
                        itemStyle: {
                            color: lineColor
                        },
                        areaStyle: {
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 0,
                                y2: 1,
                                colorStops: [{
                                    offset: 0,
                                    color: areaColorTop
                                }, {
                                    offset: 1,
                                    color: areaColorBottom
                                }]
                            }
                        },
                        data: this.dailyTrend.map(item => item.total)
                    }]
                };
                
                try {
                    // Initialize chart with proper renderer
                    this.dailyChart = echarts.init(chartElement, null, { 
                        renderer: 'canvas',
                        width: 'auto',
                        height: 'auto'
                    });
                    this.dailyChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing daily chart:', error);
                }
            },
            
            // Initialize monthly trend chart
            initMonthlyTrendChart() {
                if (!this.monthlyTrend || this.monthlyTrend.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('monthlyTrendChart');
                if (!chartElement) return;
                
                // Safely dispose existing chart if it exists
                if (this.monthlyChart) {
                    try { this.monthlyChart.dispose(); } catch {}
                }
                
                // Determine theme-specific colors
                const barColor = this.darkMode ? '#60a5fa' : '#3b82f6';
                const barHoverColor = this.darkMode ? '#93c5fd' : '#2563eb';
                const axisLineColor = this.darkMode ? '#374151' : '#e5e7eb';
                const axisLabelColor = this.darkMode ? '#d1d5db' : '#6b7280';
                const splitLineColor = this.darkMode ? '#1f2937' : '#f3f4f6';
                
                // Create chart options
                const options = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: params => {
                            return `<div style="font-weight:500;">${params[0].name}</div>` +
                                `<div>Expenses: ${this.formatMoney(params[0].value)}</div>`;
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',
                        data: this.monthlyTrend.map(item => item.month),
                        axisLine: {
                            lineStyle: {
                                color: axisLineColor
                            }
                        },
                        axisLabel: {
                            color: axisLabelColor,
                            fontSize: 10
                        }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: {
                            show: false
                        },
                        axisLabel: {
                            color: axisLabelColor,
                            fontSize: 10,
                            formatter: value => {
                                if (value >= 1000) {
                                    return '€' + (value / 1000).toFixed(1) + 'k';
                                }
                                return '€' + value;
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: splitLineColor,
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        name: 'Monthly Expenses',
                        type: 'bar',
                        barWidth: '60%',
                        itemStyle: {
                            color: barColor,
                            borderRadius: [4, 4, 0, 0]
                        },
                        emphasis: {
                            itemStyle: {
                                color: barHoverColor
                            }
                        },
                        data: this.monthlyTrend.map(item => item.total)
                    }]
                };
                
                try {
                    // Initialize chart with proper renderer
                    this.monthlyChart = echarts.init(chartElement, null, { 
                        renderer: 'canvas',
                        width: 'auto',
                        height: 'auto'
                    });
                    this.monthlyChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing monthly chart:', error);
                }
            },
            
            // Format money values
            formatMoney(amount) {
                return new Intl.NumberFormat('de-DE', {
                    style: 'currency', 
                    currency: 'EUR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            },
            
            // Format category names for display
            formatCategoryName(category) {
                if (!category) return 'Uncategorized';
                
                return category
                    .replace(/_/g, ' ')
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },
            
            // Get color for a category
            getCategoryColor(index) {
                const colors = this.getChartColors();
                return colors[index % colors.length];
            },
            
            // Get CSS class for a category badge
            getCategoryClass(category) {
                const classes = {
                    food: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    transportation: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    housing: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    utilities: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                    health: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                    education: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
                    travel: 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-300',
                    unhealthy_habits: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    shopping: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                    entertainment: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                    other_expense: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                };
                
                return classes[category] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            },
            
            // Update timeframe and refresh data
            updateTimeframe(newTimeframe) {
                // Prevent unnecessary updates
                if (this.timeframe === newTimeframe) return;
            
                try {
                    this.timeframe = newTimeframe;
            
                    // Only try to call Livewire method if $wire is available
                    if (typeof this.$wire !== 'undefined' && this.$wire && typeof this.$wire.call === 'function') {
                        this.$wire.call('updateTimeframe', newTimeframe)
                            .then(() => {
                                // Use Alpine.js safe way to wait for DOM updates
                                if (typeof Alpine !== 'undefined' && Alpine.nextTick) {
                                    Alpine.nextTick(() => this.initializeCharts());
                                } else {
                                    // Fallback to basic timeout if Alpine is not available
                                    setTimeout(() => this.initializeCharts(), 0);
                                }
                            })
                            .catch(error => {
                                console.error('Error updating timeframe:', error);
                            });
                    }
                } catch (error) {
                    console.error('Error in updateTimeframe:', error);
                }
            }
        };
    });
});