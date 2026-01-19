@props([
    'type' => 'line',
    'labels' => [],
    'datasets' => [],
    'options' => [],
    'height' => '400px',
    'width' => '100%',
    'reactive' => true,
    'id' => null,
])

@php
    $chartId = $id ?? 'chart-' . bin2hex(random_bytes(4));

    // Merge default options
    $defaultOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top',
            ],
        ],
    ];

    $mergedOptions = array_replace_recursive($defaultOptions, $options);

    $chartConfig = [
        'type' => $type,
        'data' => [
            'labels' => $labels,
            'datasets' => $datasets,
        ],
        'options' => $mergedOptions,
    ];
@endphp

<div
    {{ $attributes->merge(['class' => 'accelade-chart']) }}
    data-accelade
    data-accelade-chart
    data-chart-id="{{ $chartId }}"
    data-chart-library="chartjs"
    data-chart-config="{{ json_encode($chartConfig) }}"
    data-chart-reactive="{{ $reactive ? 'true' : 'false' }}"
    style="width: {{ $width }}; height: {{ $height }};"
>
    <canvas id="{{ $chartId }}"></canvas>
    {{ $slot }}
</div>
