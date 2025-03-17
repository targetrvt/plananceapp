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
            
            // Chart instances
            categoryChart: null,
            dailyChart: null,
            monthlyChart: null,
            
            // Chart colors
            chartColors: [
                '#4F46E5', '#3B82F6', '#06B6D4', '#10B981', '#F59E0B', 
                '#8B5CF6', '#EC4899', '#EF4444', '#6366F1', '#0EA5E9',
                '#14B8A6', '#84CC16', '#D946EF', '#F43F5E', '#FACC15'
            ],
            
            init() {
                // Initialize charts once DOM is fully loaded
                this.$nextTick(() => {
                    this.initializeCharts();
                });
                
                // Handle window resize to make charts responsive
                window.addEventListener('resize', () => {
                    this.resizeCharts();
                });
                
                // Setup Livewire event listeners
                this.$watch('timeframe', (value) => {
                    this.$wire.set('timeframe', value);
                });
            },
            
            initializeCharts() {
                // Only initialize charts if the DOM elements exist
                if (document.getElementById('categoryPieChart')) {
                    this.initCategoryChart();
                }
                
                if (document.getElementById('dailyTrendChart')) {
                    this.initDailyTrendChart();
                }
                
                if (document.getElementById('monthlyTrendChart')) {
                    this.initMonthlyTrendChart();
                }
            },
            
            resizeCharts() {
                if (this.categoryChart) {
                    this.categoryChart.resize();
                }
                
                if (this.dailyChart) {
                    this.dailyChart.resize();
                }
                
                if (this.monthlyChart) {
                    this.monthlyChart.resize();
                }
            },
            
            initCategoryChart() {
                if (this.categoryBreakdown.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('categoryPieChart');
                
                // Destroy existing chart if it exists
                if (this.categoryChart) {
                    this.categoryChart.dispose();
                }
                
                // Prepare chart data
                const chartData = this.categoryBreakdown.map((item, index) => ({
                    name: this.formatCategoryName(item.category),
                    value: item.total,
                    percentage: item.percentage,
                    itemStyle: {
                        color: this.chartColors[index % this.chartColors.length]
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
                            fontSize: 12
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
                            borderColor: '#fff',
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
                
                // Initialize chart
                this.categoryChart = echarts.init(chartElement);
                this.categoryChart.setOption(options);
            },
            
            initDailyTrendChart() {
                if (this.dailyTrend.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('dailyTrendChart');
                
                // Destroy existing chart if it exists
                if (this.dailyChart) {
                    this.dailyChart.dispose();
                }
                
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
                                color: '#E5E7EB'
                            }
                        },
                        axisLabel: {
                            color: '#6B7280',
                            fontSize: 10
                        }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: {
                            show: false
                        },
                        axisLabel: {
                            color: '#6B7280',
                            fontSize: 10,
                            formatter: value => {
                                return '€' + value;
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: '#E5E7EB',
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
                            color: '#4F46E5'
                        },
                        itemStyle: {
                            color: '#4F46E5'
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
                                    color: 'rgba(79, 70, 229, 0.4)'
                                }, {
                                    offset: 1,
                                    color: 'rgba(79, 70, 229, 0.1)'
                                }]
                            }
                        },
                        data: this.dailyTrend.map(item => item.total)
                    }]
                };
                
                // Initialize chart
                this.dailyChart = echarts.init(chartElement);
                this.dailyChart.setOption(options);
            },
            
            initMonthlyTrendChart() {
                if (this.monthlyTrend.length === 0) {
                    return;
                }
                
                const chartElement = document.getElementById('monthlyTrendChart');
                
                // Destroy existing chart if it exists
                if (this.monthlyChart) {
                    this.monthlyChart.dispose();
                }
                
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
                                color: '#E5E7EB'
                            }
                        },
                        axisLabel: {
                            color: '#6B7280',
                            fontSize: 10
                        }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: {
                            show: false
                        },
                        axisLabel: {
                            color: '#6B7280',
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
                                color: '#E5E7EB',
                                type: 'dashed'
                            }
                        }
                    },
                    series: [{
                        name: 'Monthly Expenses',
                        type: 'bar',
                        barWidth: '60%',
                        itemStyle: {
                            color: '#4F46E5',
                            borderRadius: [4, 4, 0, 0]
                        },
                        emphasis: {
                            itemStyle: {
                                color: '#4338CA'
                            }
                        },
                        data: this.monthlyTrend.map(item => item.total)
                    }]
                };
                
                // Initialize chart
                this.monthlyChart = echarts.init(chartElement);
                this.monthlyChart.setOption(options);
            },
            
            // Helper methods
            formatMoney(amount) {
                return new Intl.NumberFormat('de-DE', {
                    style: 'currency', 
                    currency: 'EUR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            },
            
            formatCategoryName(category) {
                if (!category) return 'Uncategorized';
                
                return category
                    .replace(/_/g, ' ')
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            },
            
            getCategoryColor(index) {
                return this.chartColors[index % this.chartColors.length];
            },
            
            getCategoryClass(category) {
                const essentialCategories = ['food', 'transportation', 'housing', 'utilities', 'health', 'education', 'travel'];
                const lifestyleCategories = ['shopping', 'entertainment', 'other_expense'];
                
                if (essentialCategories.includes(category)) {
                    return 'badge-essential';
                } else if (category === 'unhealthy_habits') {
                    return 'badge-unhealthy';
                } else if (lifestyleCategories.includes(category)) {
                    return 'badge-lifestyle';
                } else {
                    return 'badge-other';
                }
            },
            
            getProgressWidth(value, total) {
                if (!total || total === 0) return '0%';
                const percentage = (value / total) * 100;
                return `${Math.min(100, percentage)}%`;
            },
            
            updateTimeframe(newTimeframe) {
                if (this.timeframe !== newTimeframe) {
                    this.timeframe = newTimeframe;
                    this.$wire.call('updateTimeframe', newTimeframe)
                        .then(() => {
                            // After the server returns new data, re-initialize charts
                            this.$nextTick(() => {
                                this.initializeCharts();
                            });
                        });
                }
            }
        };
    });
});

// Load ECharts library
document.addEventListener('DOMContentLoaded', function() {
    if (typeof echarts === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js';
        script.integrity = 'sha512-EmNxF3E6bM0Xg1zvmkeYD3HDBeGxtsG92IxFt1myNZhXdCav9MzvuH/zNMBU1DmIPN6njrhX1VTbqdJxQ2wHDg==';
        script.crossOrigin = 'anonymous';
        script.referrerPolicy = 'no-referrer';
        
        script.onload = function() {
            // Dispatch custom event when ECharts is loaded
            document.dispatchEvent(new CustomEvent('echart-loaded'));
        };
        
        document.head.appendChild(script);
    }
});