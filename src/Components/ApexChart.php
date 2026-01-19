<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\View\Component;

class ApexChart extends Component
{
    /**
     * The chart type.
     */
    public string $type;

    /**
     * The chart series data.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $series;

    /**
     * The chart categories/labels.
     *
     * @var array<int, string>
     */
    public array $categories;

    /**
     * The chart options.
     *
     * @var array<string, mixed>
     */
    public array $options;

    /**
     * The chart height.
     */
    public string $height;

    /**
     * The chart width.
     */
    public string $width;

    /**
     * Whether the chart is reactive.
     */
    public bool $reactive;

    /**
     * Unique chart ID.
     */
    public string $chartId;

    /**
     * Create a new component instance.
     *
     * @param  array<int, array<string, mixed>>  $series
     * @param  array<int, string>  $categories
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        string $type = 'line',
        array $series = [],
        array $categories = [],
        array $options = [],
        string $height = '400px',
        string $width = '100%',
        bool $reactive = true,
        ?string $id = null,
    ) {
        $this->type = $type;
        $this->series = $series;
        $this->categories = $categories;
        $this->options = $options;
        $this->height = $height;
        $this->width = $width;
        $this->reactive = $reactive;
        $this->chartId = $id ?? 'apex-chart-'.bin2hex(random_bytes(4));
    }

    /**
     * Get the chart configuration for ApexCharts.
     *
     * @return array<string, mixed>
     */
    public function getChartConfig(): array
    {
        return array_replace_recursive(
            $this->getDefaultOptions(),
            $this->options,
            [
                'chart' => [
                    'type' => $this->type,
                    'height' => $this->height,
                    'width' => $this->width,
                ],
                'series' => $this->series,
                'xaxis' => [
                    'categories' => $this->categories,
                ],
            ]
        );
    }

    /**
     * Get default chart options.
     *
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [
            'chart' => [
                'toolbar' => [
                    'show' => true,
                ],
                'zoom' => [
                    'enabled' => true,
                ],
                'animations' => [
                    'enabled' => true,
                    'easing' => 'easeinout',
                    'speed' => 800,
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'legend' => [
                'show' => true,
                'position' => 'top',
            ],
            'grid' => [
                'borderColor' => '#e7e7e7',
                'strokeDashArray' => 0,
            ],
            'tooltip' => [
                'enabled' => true,
                'shared' => true,
            ],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('accelade::components.apex-chart');
    }
}
