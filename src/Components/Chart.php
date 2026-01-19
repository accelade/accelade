<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\View\Component;

class Chart extends Component
{
    /**
     * The chart type.
     */
    public string $type;

    /**
     * The chart labels.
     *
     * @var array<int, string>
     */
    public array $labels;

    /**
     * The chart datasets.
     *
     * @var array<int, array<string, mixed>>
     */
    public array $datasets;

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
     * @param  array<int, string>  $labels
     * @param  array<int, array<string, mixed>>  $datasets
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        string $type = 'line',
        array $labels = [],
        array $datasets = [],
        array $options = [],
        string $height = '400px',
        string $width = '100%',
        bool $reactive = true,
        ?string $id = null,
    ) {
        $this->type = $type;
        $this->labels = $labels;
        $this->datasets = $datasets;
        $this->options = $options;
        $this->height = $height;
        $this->width = $width;
        $this->reactive = $reactive;
        $this->chartId = $id ?? 'chart-'.bin2hex(random_bytes(4));
    }

    /**
     * Get the chart configuration as JSON.
     *
     * @return array<string, mixed>
     */
    public function getChartConfig(): array
    {
        return [
            'type' => $this->type,
            'data' => [
                'labels' => $this->labels,
                'datasets' => $this->datasets,
            ],
            'options' => $this->mergeDefaultOptions($this->options),
        ];
    }

    /**
     * Merge default options with user options.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    protected function mergeDefaultOptions(array $options): array
    {
        $defaults = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];

        return array_replace_recursive($defaults, $options);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('accelade::components.chart');
    }
}
