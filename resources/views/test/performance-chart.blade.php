<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <div id="performance-chart">
        <h1>PerformanceChart</h1>
        <h2>{{ $title }}</h2>
        <div class="chart-container">
            <canvas id="chart"></canvas>
        </div>
        
        @if(isset($chartData) && is_array($chartData) && !empty($chartData['labels']) && !empty($chartData['datasets']))
            @if(isset($chartData['labels']) && is_array($chartData['labels']))
                <div class="chart-labels">
                    @foreach($chartData['labels'] as $label)
                        <span class="label">{{ $label }}</span>
                    @endforeach
                </div>
            @endif
            
            @if(isset($chartData['datasets']) && is_array($chartData['datasets']))
                @foreach($chartData['datasets'] as $dataset)
                    @if(isset($dataset['data']) && is_array($dataset['data']))
                        @foreach($dataset['data'] as $value)
                            <span class="data-value">{{ $value }}</span>
                        @endforeach
                    @endif
                @endforeach
            @endif
        @else
            <div class="no-data">No data available</div>
        @endif
        
                       @if(isset($chartType))
                   <div class="chart-type">{{ $chartType }}</div>
               @endif
               
               @if(isset($timeRange))
                   <div class="time-range">{{ $timeRange }}</div>
               @endif
               
               @if(isset($showLegend) && $showLegend)
                   <div class="chart-legend">
                       @if(isset($chartData['datasets']))
                           @foreach($chartData['datasets'] as $dataset)
                               @if(isset($dataset['label']))
                                   <div class="legend-item">{{ $dataset['label'] }}</div>
                               @endif
                           @endforeach
                       @endif
                   </div>
               @endif
               
               @if(isset($theme))
                   <div class="theme">{{ $theme }}</div>
               @endif
               
               @if(isset($loading) && $loading)
                   <div class="loading">Loading</div>
               @endif
               
               @if(isset($options))
                   <div class="chart-options">
                       @if(isset($options['responsive']))
                           <span class="responsive">{{ $options['responsive'] ? 'responsive' : 'not-responsive' }}</span>
                       @endif
                       @if(isset($options['maintainAspectRatio']))
                           <span class="maintainAspectRatio">{{ $options['maintainAspectRatio'] ? 'maintainAspectRatio' : 'no-maintainAspectRatio' }}</span>
                       @endif
                   </div>
               @endif
    </div>
</body>
</html> 