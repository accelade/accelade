@props([
    'type' => 'line',
    'series' => [],
    'categories' => [],
    'options' => [],
    'height' => '400px',
    'width' => '100%',
    'reactive' => true,
    'id' => null,
])

@php
    $chartId = $id ?? 'apex-chart-' . bin2hex(random_bytes(4));

    // Chart types that use flat series array (not [{name, data}] format)
    $flatSeriesTypes = ['pie', 'donut', 'radialBar', 'polarArea'];

    // Chart types that DON'T support shared tooltip at all
    $noSharedTooltipTypes = ['pie', 'donut', 'radialBar', 'polarArea', 'radar', 'treemap', 'heatmap'];

    // Default options - NO shared tooltip by default, we'll add it after merge if safe
    $defaultOptions = [
        'chart' => [
            'toolbar' => ['show' => true],
            'zoom' => ['enabled' => true],
            'animations' => [
                'enabled' => true,
                'easing' => 'easeinout',
                'speed' => 800,
            ],
        ],
        'dataLabels' => ['enabled' => false],
        'stroke' => ['curve' => 'smooth', 'width' => 2],
        'legend' => ['show' => true, 'position' => 'top'],
        'grid' => ['borderColor' => '#e7e7e7', 'strokeDashArray' => 0],
        'tooltip' => ['enabled' => true],
    ];

    // Process series for flat series types (pie, donut, radialBar, polarArea)
    // These types expect a flat array [44, 55, 13] not [{name, data}]
    $processedSeries = $series;
    if (in_array($type, $flatSeriesTypes) && !empty($series)) {
        // If series is in [{data: []}] format, extract the data
        if (isset($series[0]) && is_array($series[0]) && isset($series[0]['data'])) {
            $processedSeries = $series[0]['data'];
        }
    }

    // Merge all options
    $chartConfig = array_replace_recursive(
        $defaultOptions,
        $options,
        [
            'chart' => [
                'type' => $type,
                'height' => $height,
                'width' => $width,
            ],
            'series' => $processedSeries,
            'xaxis' => [
                'categories' => $categories,
            ],
        ]
    );

    // Fix tooltip.shared / tooltip.intersect conflict AFTER merge
    // ApexCharts throws error if both shared=true and intersect=true
    $hasIntersect = isset($chartConfig['tooltip']['intersect']) && $chartConfig['tooltip']['intersect'];
    $hasShared = isset($chartConfig['tooltip']['shared']) && $chartConfig['tooltip']['shared'];
    $supportsShared = !in_array($type, $noSharedTooltipTypes);

    if ($hasIntersect && $hasShared) {
        // Conflict: intersect takes priority, disable shared
        $chartConfig['tooltip']['shared'] = false;
    } elseif (!$supportsShared && $hasShared) {
        // Chart type doesn't support shared tooltip
        $chartConfig['tooltip']['shared'] = false;
    }
@endphp

<div
    {{ $attributes->merge(['class' => 'accelade-chart']) }}
    data-accelade
    data-accelade-chart
    data-chart-id="{{ $chartId }}"
    data-chart-library="apexcharts"
    data-chart-config="{{ json_encode($chartConfig) }}"
    data-chart-reactive="{{ $reactive ? 'true' : 'false' }}"
    style="width: {{ $width }}; height: {{ $height }};"
>
    <div id="{{ $chartId }}"></div>
    {{ $slot }}
</div>
