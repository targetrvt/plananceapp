document.addEventListener('alpine:init', () => {
    Alpine.data('incomeDashboard', function(initialData) {
        return {
            categoryBreakdown: initialData.categoryBreakdown || [],
            monthlyTrend: initialData.monthlyTrend || [],
            dailyTrend: initialData.dailyTrend || [],
            totalIncome: initialData.totalIncome || 0,
            averageDailyIncome: initialData.averageDailyIncome || 0,
            recentTransactions: initialData.recentTransactions || [],
            largestIncome: initialData.largestIncome || [],
            timeframe: initialData.timeframe || 'month',
            category: initialData.category || 'all',
            startDate: initialData.startDate,
            endDate: initialData.endDate,
            darkMode: initialData.darkMode || document.documentElement.classList.contains('dark'),

            categoryChart: null,
            dailyChart: null,
            monthlyChart: null,
            chartsInitialized: false,

            lightColors: [
                '#059669', '#0d9488', '#10b981', '#14b8a6', '#2dd4bf',
                '#5eead4', '#34d399', '#6ee7b7', '#0d9488', '#2dd4bf',
                '#047857', '#0f766e', '#115e59', '#134e4a', '#164e63'
            ],
            darkColors: [
                '#34d399', '#2dd4bf', '#10b981', '#14b8a6', '#5eead4',
                '#6ee7b7', '#2dd4bf', '#5eead4', '#34d399', '#2dd4bf',
                '#10b981', '#14b8a6', '#0d9488', '#0f766e', '#2dd4bf'
            ],

            init() {
                this.safelyLoadECharts();
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => this.resizeCharts(), 250);
                });
                this.setupDarkModeDetection();
                document.addEventListener('echart-loaded', () => this.initializeCharts());
            },

            safelyLoadECharts() {
                try {
                    if (typeof echarts !== 'undefined') {
                        this.$nextTick(() => {
                            this.initializeCharts();
                            this.chartsInitialized = true;
                        });
                        return;
                    }
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js';
                    script.integrity = 'sha512-EmNxF3E6bM0Xg1zvmkeYD3HDBeGxtsG92IxFt1myNZhXdCav9MzvuH/zNMBU1DmIPN6njrhX1VTbqdJxQ2wHDg==';
                    script.crossOrigin = 'anonymous';
                    script.referrerPolicy = 'no-referrer';
                    script.onload = () => {
                        document.dispatchEvent(new CustomEvent('echart-loaded'));
                        this.chartsInitialized = true;
                    };
                    script.onerror = (err) => console.error('Failed to load ECharts library:', err);
                    document.head.appendChild(script);
                } catch (error) {
                    console.error('Error loading ECharts:', error);
                }
            },

            setupDarkModeDetection() {
                try {
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
                } catch (error) {
                    console.error('Error setting up dark mode detection:', error);
                }
            },

            initializeCharts() {
                try {
                    if (typeof echarts === 'undefined') {
                        this.safelyLoadECharts();
                        return;
                    }
                    this.$nextTick(() => {
                        if (document.getElementById('categoryPieChart')) this.initCategoryChart();
                        if (document.getElementById('dailyTrendChart')) this.initDailyTrendChart();
                        if (document.getElementById('monthlyTrendChart')) this.initMonthlyTrendChart();
                    });
                } catch (error) {
                    console.error('Error initializing charts:', error);
                }
            },

            refreshCharts() {
                try {
                    if (this.categoryChart) { try { this.categoryChart.dispose(); } catch {} this.categoryChart = null; }
                    if (this.dailyChart) { try { this.dailyChart.dispose(); } catch {} this.dailyChart = null; }
                    if (this.monthlyChart) { try { this.monthlyChart.dispose(); } catch {} this.monthlyChart = null; }
                    this.$nextTick(() => this.initializeCharts());
                } catch (error) {
                    console.error('Error refreshing charts:', error);
                }
            },

            resizeCharts() {
                try {
                    if (this.categoryChart && document.getElementById('categoryPieChart')) this.categoryChart.resize();
                    if (this.dailyChart && document.getElementById('dailyTrendChart')) this.dailyChart.resize();
                    if (this.monthlyChart && document.getElementById('monthlyTrendChart')) this.monthlyChart.resize();
                } catch (error) {
                    console.error('Error resizing charts:', error);
                }
            },

            getChartColors() {
                return this.darkMode ? this.darkColors : this.lightColors;
            },

            initCategoryChart() {
                if (!this.categoryBreakdown || this.categoryBreakdown.length === 0) return;
                const chartElement = document.getElementById('categoryPieChart');
                if (!chartElement) return;
                if (this.categoryChart) { try { this.categoryChart.dispose(); } catch {} }
                const colors = this.getChartColors();
                const chartData = this.categoryBreakdown.map((item, index) => ({
                    name: this.formatCategoryName(item.category),
                    value: parseFloat(item.total) || 0,
                    percentage: parseFloat(item.percentage) || 0,
                    itemStyle: { color: colors[index % colors.length] }
                }));
                const options = {
                    tooltip: {
                        trigger: 'item',
                        formatter: params => `<div style="font-weight:500;">${params.name}</div><div>${this.formatMoney(params.value)} (${params.percent}%)</div>`
                    },
                    legend: {
                        type: 'scroll',
                        orient: 'vertical',
                        right: 10,
                        top: 'center',
                        textStyle: { fontSize: 12, color: this.darkMode ? '#d1d5db' : '#4b5563' },
                        formatter: name => name.length > 15 ? name.slice(0, 15) + '...' : name
                    },
                    series: [{
                        name: 'Income Categories',
                        type: 'pie',
                        radius: ['45%', '75%'],
                        avoidLabelOverlap: true,
                        itemStyle: { borderRadius: 6, borderColor: this.darkMode ? '#1f2937' : '#ffffff', borderWidth: 2 },
                        label: { show: false },
                        emphasis: { label: { show: false }, scale: true, scaleSize: 10 },
                        labelLine: { show: false },
                        data: chartData
                    }]
                };
                try {
                    this.categoryChart = echarts.init(chartElement, null, { renderer: 'canvas', width: 'auto', height: 'auto' });
                    this.categoryChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing category chart:', error);
                }
            },

            initDailyTrendChart() {
                if (!this.dailyTrend || this.dailyTrend.length === 0) return;
                const chartElement = document.getElementById('dailyTrendChart');
                if (!chartElement) return;
                if (this.dailyChart) { try { this.dailyChart.dispose(); } catch {} }
                const lineColor = this.darkMode ? '#10b981' : '#059669';
                const areaColorTop = this.darkMode ? 'rgba(16, 185, 129, 0.5)' : 'rgba(5, 150, 105, 0.5)';
                const areaColorBottom = this.darkMode ? 'rgba(16, 185, 129, 0.05)' : 'rgba(5, 150, 105, 0.05)';
                const axisLineColor = this.darkMode ? '#374151' : '#e5e7eb';
                const axisLabelColor = this.darkMode ? '#d1d5db' : '#6b7280';
                const splitLineColor = this.darkMode ? '#1f2937' : '#f3f4f6';
                const dailyData = this.dailyTrend.map(item => parseFloat(item.total) || 0);
                const dailyLabels = this.dailyTrend.map(item => item.date);
                const options = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: params => `<div style="font-weight:500;">${params[0].name}</div><div>Income: ${this.formatMoney(params[0].value)}</div>`
                    },
                    grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: dailyLabels,
                        axisLine: { lineStyle: { color: axisLineColor } },
                        axisLabel: { color: axisLabelColor, fontSize: 10, rotate: dailyLabels.length > 15 ? 45 : 0 }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisLabel: { color: axisLabelColor, fontSize: 10, formatter: value => '€' + value },
                        splitLine: { lineStyle: { color: splitLineColor, type: 'dashed' } }
                    },
                    series: [{
                        name: 'Daily Income',
                        type: 'line',
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 6,
                        lineStyle: { width: 3, color: lineColor },
                        itemStyle: { color: lineColor },
                        areaStyle: {
                            color: {
                                type: 'linear',
                                x: 0, y: 0, x2: 0, y2: 1,
                                colorStops: [{ offset: 0, color: areaColorTop }, { offset: 1, color: areaColorBottom }]
                            }
                        },
                        data: dailyData
                    }]
                };
                try {
                    this.dailyChart = echarts.init(chartElement, null, { renderer: 'canvas', width: 'auto', height: 'auto' });
                    this.dailyChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing daily chart:', error);
                }
            },

            initMonthlyTrendChart() {
                if (!this.monthlyTrend || this.monthlyTrend.length === 0) return;
                const chartElement = document.getElementById('monthlyTrendChart');
                if (!chartElement) return;
                if (this.monthlyChart) { try { this.monthlyChart.dispose(); } catch {} }
                const barColor = this.darkMode ? '#34d399' : '#059669';
                const barHoverColor = this.darkMode ? '#6ee7b7' : '#047857';
                const axisLineColor = this.darkMode ? '#374151' : '#e5e7eb';
                const axisLabelColor = this.darkMode ? '#d1d5db' : '#6b7280';
                const splitLineColor = this.darkMode ? '#1f2937' : '#f3f4f6';
                const monthlyData = this.monthlyTrend.map(item => parseFloat(item.total) || 0);
                const monthlyLabels = this.monthlyTrend.map(item => item.month);
                const options = {
                    tooltip: {
                        trigger: 'axis',
                        formatter: params => `<div style="font-weight:500;">${params[0].name}</div><div>Income: ${this.formatMoney(params[0].value)}</div>`
                    },
                    grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
                    xAxis: {
                        type: 'category',
                        data: monthlyLabels,
                        axisLine: { lineStyle: { color: axisLineColor } },
                        axisLabel: { color: axisLabelColor, fontSize: 10 }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisLabel: {
                            color: axisLabelColor,
                            fontSize: 10,
                            formatter: value => value >= 1000 ? '€' + (value / 1000).toFixed(1) + 'k' : '€' + value
                        },
                        splitLine: { lineStyle: { color: splitLineColor, type: 'dashed' } }
                    },
                    series: [{
                        name: 'Monthly Income',
                        type: 'bar',
                        barWidth: '60%',
                        itemStyle: { color: barColor, borderRadius: [4, 4, 0, 0] },
                        emphasis: { itemStyle: { color: barHoverColor } },
                        data: monthlyData
                    }]
                };
                try {
                    this.monthlyChart = echarts.init(chartElement, null, { renderer: 'canvas', width: 'auto', height: 'auto' });
                    this.monthlyChart.setOption(options, true);
                } catch (error) {
                    console.error('Error initializing monthly chart:', error);
                }
            },

            formatMoney(amount) {
                const value = parseFloat(amount) || 0;
                return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
            },

            formatCategoryName(category) {
                if (!category) return 'Uncategorized';
                return category.replace(/_/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            },

            getCategoryColor(index) {
                const colors = this.getChartColors();
                return colors[index % colors.length];
            },

            getCategoryClass(category) {
                const classes = {
                    salary: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                    investment: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    gift: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                    refund: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                    other_income: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                };
                return classes[category] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            }
        };
    });
});
