<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Chart Test</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Debug: Show raw $_GET and request()->all() at the very top -->
    <div id="raw-get-debug">
        <pre>_GET: {{ print_r($_GET, true) }}</pre>
        <pre>request()->all(): {{ print_r(request()->all(), true) }}</pre>
    </div>
    <div id="app">
        <div class="container">
            <h1>PerformanceChart Test</h1>
            <h2>Performance Trends</h2>
            
            <!-- Chart Type Selector -->
            <div class="chart-controls">
                <label for="chartType">Chart Type:</label>
                <select id="chartType" v-model="chartType" @change="updateChartType">
                    <option value="line">Line Chart</option>
                    <option value="bar">Bar Chart</option>
                    <option value="radar">Radar Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                </select>
            </div>

            <!-- Time Range Selector -->
            <div class="time-controls">
                <label for="timeRange">Time Range:</label>
                <select id="timeRange" v-model="timeRange" @change="updateTimeRange">
                    <option value="7d">Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 90 Days</option>
                    <option value="1y">Last Year</option>
                </select>
            </div>

            <!-- Chart Container -->
            <div class="chart-container" style="position: relative; height:400px; width:100%">
                <canvas id="performanceChart"></canvas>
            </div>

            <!-- Chart Legend -->
            <div class="chart-legend" v-if="showLegend && chartData && chartData.datasets">
                <h3>Legend:</h3>
                <div v-for="dataset in chartData.datasets" :key="dataset.label" class="legend-item">
                    <span class="legend-color" :style="{ backgroundColor: dataset.borderColor }"></span>
                    <span class="legend-label">@{{ dataset.label }}</span>
                </div>
            </div>

            <!-- Empty Data Message -->
            <div v-if="!chartData || !chartData.datasets || chartData.datasets.length === 0" class="empty-data">
                <p>No data available</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading">
                <p>Loading chart data...</p>
            </div>

            <!-- Error State -->
            <div v-if="error" class="error">
                <p>Error: @{{ error }}</p>
            </div>

            <!-- Visible data for test assertions -->
            <div id="test-data">
                <!-- Debug: Show parsed chartData variable -->
                <div id="parsed-chartdata-debug">
                    @php
                        $chartDataParam = request('chartData');
                        $chartData = null;
                        if ($chartDataParam) {
                            $chartData = json_decode($chartDataParam, true);
                        }
                        echo '<pre>chartData: ' . print_r($chartData, true) . '</pre>';
                    @endphp
                </div>
                <!-- Chart data values for testing -->
                <span id="chart-data-values">
                    @php
                        if ($chartData && isset($chartData['datasets'])) {
                            foreach ($chartData['datasets'] as $dataset) {
                                if (isset($dataset['data']) && is_array($dataset['data'])) {
                                    foreach ($dataset['data'] as $value) {
                                        echo '<span class="data-value">' . $value . '</span>';
                                    }
                                }
                            }
                        }
                    @endphp
                </span>
                <!-- Legend labels for testing -->
                <span id="legend-labels">
                    @php
                        if (isset($chartData) && isset($chartData['datasets'])) {
                            foreach ($chartData['datasets'] as $dataset) {
                                if (isset($dataset['label'])) {
                                    echo '<span class="legend-label">' . $dataset['label'] . '</span>';
                                }
                            }
                        }
                    @endphp
                </span>
                <!-- Theme for testing -->
                <span id="theme-value">{{ request('theme', 'light') }}</span>
                <!-- Time range for testing -->
                <span id="time-range-value">{{ request('timeRange', '30d') }}</span>
                <!-- Chart options for testing -->
                <span id="chart-options">
                    @php
                        $optionsParam = request('options');
                        if ($optionsParam) {
                            $options = json_decode($optionsParam, true);
                            if ($options) {
                                foreach ($options as $key => $value) {
                                    echo '<span class="option-' . $key . '">' . $key . '</span>';
                                }
                            }
                        }
                    @endphp
                </span>
                <!-- Debug: Show raw request data -->
                <div id="debug-data">
                    <h4>Debug Data:</h4>
                    <p>Raw chartData: {{ request('chartData') }}</p>
                    <p>Theme: {{ request('theme') }}</p>
                    <p>TimeRange: {{ request('timeRange') }}</p>
                    <p>ShowLegend: {{ request('showLegend') }}</p>
                    <p>ChartData type: {{ gettype($chartData ?? null) }}</p>
                    @if(isset($chartData))
                        <p>Has datasets: {{ isset($chartData['datasets']) ? 'yes' : 'no' }}</p>
                        @if(isset($chartData['datasets']))
                            <p>Datasets count: {{ count($chartData['datasets']) }}</p>
                            @foreach($chartData['datasets'] as $index => $dataset)
                                <p>Dataset {{ $index }}: @json($dataset)</p>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .chart-controls, .time-controls {
            margin-bottom: 20px;
        }
        
        .chart-controls label, .time-controls label {
            margin-right: 10px;
            font-weight: bold;
        }
        
        .chart-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .chart-legend {
            margin-top: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border-radius: 3px;
        }
        
        .loading, .error, .empty-data {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
        }
        
        .loading {
            background-color: #f0f8ff;
        }
        
        .error {
            background-color: #ffe6e6;
            color: #d32f2f;
        }
        
        .empty-data {
            background-color: #f5f5f5;
            color: #666;
        }
        
        #test-data {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        #test-data span {
            margin-right: 10px;
        }
        
        #debug-data {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
        }
    </style>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    chart: null,
                    chartType: '{{ request("chartType", "line") }}',
                    timeRange: '{{ request("timeRange", "30d") }}',
                    loading: {{ request('loading', false) ? 'true' : 'false' }},
                    error: null,
                    chartData: this.parseChartData(),
                    options: this.parseOptions(),
                    showLegend: {{ request('showLegend', false) ? 'true' : 'false' }},
                    theme: '{{ request("theme", "light") }}'
                }
            },
            mounted() {
                this.initChart();
                this.loadChartData();
            },
            methods: {
                parseChartData() {
                    const chartDataParam = '{{ request("chartData") }}';
                    if (chartDataParam) {
                        try {
                            return JSON.parse(chartDataParam);
                        } catch (e) {
                            console.error('Failed to parse chart data:', e);
                        }
                    }
                    return {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Performance Score',
                            data: [65, 59, 80, 81, 56, 55],
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }]
                    };
                },
                parseOptions() {
                    const optionsParam = '{{ request("options") }}';
                    if (optionsParam) {
                        try {
                            return JSON.parse(optionsParam);
                        } catch (e) {
                            console.error('Failed to parse options:', e);
                        }
                    }
                    return {};
                },
                initChart() {
                    const ctx = document.getElementById('performanceChart').getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: this.chartType,
                        data: this.chartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: this.showLegend
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                },
                
                updateChartType() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    this.initChart();
                },
                
                updateTimeRange() {
                    this.loadChartData();
                },
                
                async loadChartData() {
                    this.loading = true;
                    this.error = null;
                    
                    try {
                        // Simulate API call
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        
                        // Mock data based on time range
                        const mockData = this.generateMockData();
                        this.chartData = mockData;
                        
                        if (this.chart) {
                            this.chart.data = this.chartData;
                            this.chart.update();
                        }
                    } catch (err) {
                        this.error = 'Failed to load chart data';
                        console.error(err);
                    } finally {
                        this.loading = false;
                    }
                },
                
                generateMockData() {
                    const labels = [];
                    const data = [];
                    const count = this.timeRange === '7d' ? 7 : this.timeRange === '30d' ? 30 : this.timeRange === '90d' ? 90 : 365;
                    
                    for (let i = 0; i < count; i++) {
                        labels.push(`Day ${i + 1}`);
                        data.push(Math.floor(Math.random() * 40) + 60); // Random score between 60-100
                    }
                    
                    return {
                        labels: labels,
                        datasets: [{
                            label: 'Performance Score',
                            data: data,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1
                        }]
                    };
                }
            }
        }).mount('#app');
    </script>
</body>
</html> 