<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeApexChartView(array $props = []): string
{
    $defaults = [
        'type' => 'line',
        'series' => [
            [
                'name' => 'Sales',
                'data' => [10, 20, 30],
            ],
        ],
        'categories' => ['Jan', 'Feb', 'Mar'],
        'options' => [],
        'height' => '400px',
        'width' => '100%',
        'reactive' => true,
        'id' => null,
        'slot' => new HtmlString(''),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/apex-chart.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic apex chart component', function () {
    $html = makeApexChartView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-chart')
        ->toContain('data-chart-library="apexcharts"');
});

it('generates unique id when not provided', function () {
    $html = makeApexChartView();

    expect($html)->toMatch('/data-chart-id="apex-chart-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeApexChartView([
        'id' => 'my-apex-chart',
    ]);

    expect($html)
        ->toContain('data-chart-id="my-apex-chart"')
        ->toContain('id="my-apex-chart"');
});

it('renders with correct chart type', function () {
    $html = makeApexChartView(['type' => 'area']);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;area&quot;');
});

it('renders with series data', function () {
    $html = makeApexChartView([
        'series' => [
            [
                'name' => 'Revenue',
                'data' => [100, 200, 300],
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;name&quot;:&quot;Revenue&quot;')
        ->toContain('&quot;data&quot;:[100,200,300]');
});

it('renders with multiple series', function () {
    $html = makeApexChartView([
        'series' => [
            ['name' => 'Series A', 'data' => [1, 2, 3]],
            ['name' => 'Series B', 'data' => [4, 5, 6]],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;name&quot;:&quot;Series A&quot;')
        ->toContain('&quot;name&quot;:&quot;Series B&quot;');
});

it('renders with categories', function () {
    $html = makeApexChartView([
        'categories' => ['Q1', 'Q2', 'Q3', 'Q4'],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;categories&quot;:[&quot;Q1&quot;,&quot;Q2&quot;,&quot;Q3&quot;,&quot;Q4&quot;]');
});

it('renders with custom options', function () {
    $html = makeApexChartView([
        'options' => [
            'colors' => ['#6366f1', '#22c55e'],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;colors&quot;:[&quot;#6366f1&quot;,&quot;#22c55e&quot;]');
});

it('renders with custom dimensions', function () {
    $html = makeApexChartView([
        'height' => '350px',
        'width' => '600px',
    ]);

    expect($html)
        ->toContain('height: 350px')
        ->toContain('width: 600px');
});

it('renders with reactive disabled', function () {
    $html = makeApexChartView(['reactive' => false]);

    expect($html)->toContain('data-chart-reactive="false"');
});

it('renders with reactive enabled by default', function () {
    $html = makeApexChartView();

    expect($html)->toContain('data-chart-reactive="true"');
});

it('merges additional attributes', function () {
    $html = makeApexChartView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'apex-chart-wrapper',
            'data-testid' => 'apex-test',
        ]),
    ]);

    // Classes are merged with default class
    expect($html)
        ->toContain('apex-chart-wrapper')
        ->toContain('data-testid="apex-test"');
});

it('includes default animation options', function () {
    $html = makeApexChartView();

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;animations&quot;')
        ->toContain('&quot;enabled&quot;:true');
});

it('renders area chart type', function () {
    $html = makeApexChartView([
        'type' => 'area',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;area&quot;');
});

it('renders bar chart type', function () {
    $html = makeApexChartView([
        'type' => 'bar',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;bar&quot;');
});

it('renders radar chart type', function () {
    $html = makeApexChartView([
        'type' => 'radar',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;radar&quot;');
});

it('renders donut chart type', function () {
    $html = makeApexChartView([
        'type' => 'donut',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;donut&quot;');
});

it('renders pie chart type', function () {
    $html = makeApexChartView([
        'type' => 'pie',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;pie&quot;');
});

it('renders radialBar chart type', function () {
    $html = makeApexChartView([
        'type' => 'radialBar',
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;type&quot;:&quot;radialBar&quot;');
});

it('renders slot content', function () {
    $html = makeApexChartView([
        'slot' => new HtmlString('<div class="apex-legend">Custom Legend</div>'),
    ]);

    expect($html)
        ->toContain('apex-legend')
        ->toContain('Custom Legend');
});

it('includes stroke options', function () {
    $html = makeApexChartView([
        'options' => [
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;curve&quot;:&quot;smooth&quot;')
        ->toContain('&quot;width&quot;:3');
});

it('includes fill options for gradients', function () {
    $html = makeApexChartView([
        'options' => [
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'opacityFrom' => 0.5,
                    'opacityTo' => 0.1,
                ],
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;type&quot;:&quot;gradient&quot;')
        ->toContain('&quot;opacityFrom&quot;:0.5');
});

it('includes plotOptions for horizontal bar', function () {
    $html = makeApexChartView([
        'type' => 'bar',
        'options' => [
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                ],
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;horizontal&quot;:true');
});

it('includes legend options', function () {
    $html = makeApexChartView([
        'options' => [
            'legend' => [
                'show' => true,
                'position' => 'bottom',
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)
        ->toContain('&quot;show&quot;:true')
        ->toContain('&quot;position&quot;:&quot;bottom&quot;');
});

it('includes tooltip options', function () {
    $html = makeApexChartView([
        'options' => [
            'tooltip' => [
                'enabled' => true,
                'shared' => true,
            ],
        ],
    ]);

    // JSON is HTML-escaped in attributes
    expect($html)->toContain('&quot;shared&quot;:true');
});

it('renders uses div container instead of canvas', function () {
    $html = makeApexChartView(['id' => 'apex-test']);

    // ApexCharts uses div, not canvas
    expect($html)
        ->toContain('<div id="apex-test"></div>')
        ->not->toContain('<canvas');
});
