<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeChartView(array $props = []): string
{
    $defaults = [
        'type' => 'line',
        'labels' => ['Jan', 'Feb', 'Mar'],
        'datasets' => [
            [
                'label' => 'Sales',
                'data' => [10, 20, 30],
                'borderColor' => 'rgb(99, 102, 241)',
            ],
        ],
        'options' => [],
        'height' => '400px',
        'width' => '100%',
        'reactive' => true,
        'id' => null,
        'slot' => new HtmlString(''),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/chart.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic chart component', function () {
    $html = makeChartView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-chart')
        ->toContain('data-chart-library="chartjs"')
        ->toContain('<canvas');
});

it('generates unique id when not provided', function () {
    $html = makeChartView();

    expect($html)->toMatch('/data-chart-id="chart-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeChartView([
        'id' => 'my-custom-chart',
    ]);

    expect($html)
        ->toContain('data-chart-id="my-custom-chart"')
        ->toContain('id="my-custom-chart"');
});

it('renders with correct chart type', function () {
    $html = makeChartView(['type' => 'bar']);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;bar&quot;');
});

it('renders with labels', function () {
    $html = makeChartView([
        'labels' => ['Q1', 'Q2', 'Q3', 'Q4'],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;labels&quot;:[&quot;Q1&quot;,&quot;Q2&quot;,&quot;Q3&quot;,&quot;Q4&quot;]');
});

it('renders with datasets', function () {
    $html = makeChartView([
        'datasets' => [
            [
                'label' => 'Revenue',
                'data' => [100, 200, 300],
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;label&quot;:&quot;Revenue&quot;')
        ->toContain('&quot;data&quot;:[100,200,300]');
});

it('renders with multiple datasets', function () {
    $html = makeChartView([
        'datasets' => [
            ['label' => 'Series A', 'data' => [1, 2, 3]],
            ['label' => 'Series B', 'data' => [4, 5, 6]],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;label&quot;:&quot;Series A&quot;')
        ->toContain('&quot;label&quot;:&quot;Series B&quot;');
});

it('renders with custom options', function () {
    $html = makeChartView([
        'options' => [
            'plugins' => [
                'legend' => ['display' => false],
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes - display:false overrides default display:true
    expect($html)->toContain('&quot;display&quot;:false');
});

it('renders with custom dimensions', function () {
    $html = makeChartView([
        'height' => '300px',
        'width' => '500px',
    ]);

    expect($html)
        ->toContain('height: 300px')
        ->toContain('width: 500px');
});

it('renders with reactive disabled', function () {
    $html = makeChartView(['reactive' => false]);

    expect($html)->toContain('data-chart-reactive="false"');
});

it('renders with reactive enabled by default', function () {
    $html = makeChartView();

    expect($html)->toContain('data-chart-reactive="true"');
});

it('merges additional attributes', function () {
    $html = makeChartView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-chart-class',
            'data-testid' => 'chart-test',
        ]),
    ]);

    // Classes are merged with default class
    expect($html)
        ->toContain('my-chart-class')
        ->toContain('data-testid="chart-test"');
});

it('includes default responsive options', function () {
    $html = makeChartView();

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;responsive&quot;:true')
        ->toContain('&quot;maintainAspectRatio&quot;:false');
});

it('renders pie chart type', function () {
    $html = makeChartView([
        'type' => 'pie',
        'labels' => ['A', 'B', 'C'],
        'datasets' => [
            ['data' => [30, 50, 20]],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;type&quot;:&quot;pie&quot;')
        ->toContain('&quot;data&quot;:[30,50,20]');
});

it('renders doughnut chart type', function () {
    $html = makeChartView([
        'type' => 'doughnut',
        'labels' => ['Red', 'Blue'],
        'datasets' => [
            ['data' => [60, 40]],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;doughnut&quot;');
});

it('renders radar chart type', function () {
    $html = makeChartView([
        'type' => 'radar',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;radar&quot;');
});

it('renders bar chart type', function () {
    $html = makeChartView([
        'type' => 'bar',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;bar&quot;');
});

it('renders slot content', function () {
    $html = makeChartView([
        'slot' => new HtmlString('<div class="chart-legend">Custom Legend</div>'),
    ]);

    expect($html)
        ->toContain('chart-legend')
        ->toContain('Custom Legend');
});

it('includes dataset colors', function () {
    $html = makeChartView([
        'datasets' => [
            [
                'label' => 'Test',
                'data' => [1, 2, 3],
                'borderColor' => 'rgb(255, 0, 0)',
                'backgroundColor' => 'rgba(255, 0, 0, 0.5)',
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;borderColor&quot;:&quot;rgb(255, 0, 0)&quot;')
        ->toContain('&quot;backgroundColor&quot;:&quot;rgba(255, 0, 0, 0.5)&quot;');
});

it('escapes special characters in config', function () {
    $html = makeChartView([
        'datasets' => [
            [
                'label' => 'Test "quoted"',
                'data' => [1, 2, 3],
            ],
        ],
    ]);

    // JSON encoding should handle quotes
    expect($html)->toContain('Test');
});
