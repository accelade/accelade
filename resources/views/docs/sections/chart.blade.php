@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="chart" :documentation="$documentation" :hasDemo="$hasDemo">
    {{-- Introduction --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Chart Components</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Reactive charts with Chart.js and ApexCharts. Bundled with Accelade - no additional installation required.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-4 rounded-lg border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.05);">
                <h4 class="font-medium mb-2 text-indigo-500">Chart.js</h4>
                <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Simple, flexible charts for common use cases.</p>
                <x-accelade::chart
                    type="line"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                    :datasets="[
                        [
                            'label' => 'Revenue',
                            'data' => [12, 19, 15, 22, 18, 25],
                            'borderColor' => 'rgb(99, 102, 241)',
                            'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                            'fill' => true,
                            'tension' => 0.4,
                        ],
                    ]"
                    height="180px"
                />
            </div>

            <div class="p-4 rounded-lg border border-sky-500/30" style="background: rgba(14, 165, 233, 0.05);">
                <h4 class="font-medium mb-2 text-sky-500">ApexCharts</h4>
                <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Advanced features with interactive charts.</p>
                <x-accelade::apex-chart
                    type="area"
                    :series="[['name' => 'Sales', 'data' => [31, 40, 28, 51, 42, 55]]]"
                    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                    :options="['colors' => ['#0ea5e9'], 'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => 0.5, 'opacityTo' => 0.1]]]"
                    height="180px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="chart-basic.blade.php">
{{-- Chart.js Component --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [12, 19, 15, 22, 18],
            'borderColor' => 'rgb(99, 102, 241)',
            'fill' => true,
            'tension' => 0.4,
        ],
    ]"
    height="300px"
/&gt;

{{-- ApexCharts Component --}}
&lt;x-accelade::apex-chart
    type="area"
    :series="[['name' => 'Sales', 'data' => [31, 40, 28, 51, 42]]]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :options="['colors' => ['#0ea5e9']]"
    height="300px"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- Chart.js: Line Chart --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Line Chart</h3>
            <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Chart.js</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Display trends with smooth, stepped, or multi-series lines.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Multi-Series with Fill</h5>
                <x-accelade::chart
                    type="line"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
                    :datasets="[
                        ['label' => '2024', 'data' => [65, 59, 80, 81, 56], 'borderColor' => 'rgb(99, 102, 241)', 'backgroundColor' => 'rgba(99, 102, 241, 0.1)', 'fill' => true, 'tension' => 0.4],
                        ['label' => '2023', 'data' => [45, 52, 60, 65, 48], 'borderColor' => 'rgb(34, 197, 94)', 'backgroundColor' => 'rgba(34, 197, 94, 0.1)', 'fill' => true, 'tension' => 0.4],
                    ]"
                    height="180px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Stepped Line</h5>
                <x-accelade::chart
                    type="line"
                    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri']"
                    :datasets="[
                        ['label' => 'Users', 'data' => [120, 190, 130, 150, 180], 'borderColor' => 'rgb(236, 72, 153)', 'backgroundColor' => 'rgba(236, 72, 153, 0.1)', 'fill' => true, 'stepped' => true],
                    ]"
                    height="180px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="line-chart.blade.php">
{{-- Multi-Series with Fill --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :datasets="[
        ['label' => '2024', 'data' => [65, 59, 80, 81, 56], 'borderColor' => 'rgb(99, 102, 241)', 'fill' => true, 'tension' => 0.4],
        ['label' => '2023', 'data' => [45, 52, 60, 65, 48], 'borderColor' => 'rgb(34, 197, 94)', 'fill' => true, 'tension' => 0.4],
    ]"
/&gt;

{{-- Stepped Line --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Mon', 'Tue', 'Wed']"
    :datasets="[
        ['label' => 'Users', 'data' => [120, 190, 130], 'borderColor' => 'rgb(236, 72, 153)', 'stepped' => true],
    ]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- Chart.js: Bar Chart --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bar Chart</h3>
            <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Chart.js</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Compare categories with vertical bars, grouped or stacked.
        </p>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Vertical Bar</h5>
                <x-accelade::chart
                    type="bar"
                    :labels="['Q1', 'Q2', 'Q3', 'Q4']"
                    :datasets="[['data' => [12, 19, 8, 15], 'backgroundColor' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899']]]"
                    :options="['plugins' => ['legend' => ['display' => false]]]"
                    height="150px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Grouped</h5>
                <x-accelade::chart
                    type="bar"
                    :labels="['Jan', 'Feb', 'Mar']"
                    :datasets="[
                        ['label' => 'A', 'data' => [65, 59, 80], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
                        ['label' => 'B', 'data' => [45, 52, 60], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
                    ]"
                    height="150px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Stacked</h5>
                <x-accelade::chart
                    type="bar"
                    :labels="['Q1', 'Q2', 'Q3']"
                    :datasets="[
                        ['label' => 'A', 'data' => [50, 60, 70], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
                        ['label' => 'B', 'data' => [30, 40, 35], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
                    ]"
                    :options="['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]]"
                    height="150px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="bar-chart.blade.php">
{{-- Grouped Bar --}}
&lt;x-accelade::chart
    type="bar"
    :labels="['Jan', 'Feb', 'Mar']"
    :datasets="[
        ['label' => 'Desktop', 'data' => [65, 59, 80], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
        ['label' => 'Mobile', 'data' => [45, 52, 60], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
    ]"
/&gt;

{{-- Stacked Bar --}}
&lt;x-accelade::chart
    type="bar"
    :labels="['Q1', 'Q2', 'Q3', 'Q4']"
    :datasets="[
        ['label' => 'A', 'data' => [50, 60, 70, 80], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
        ['label' => 'B', 'data' => [30, 40, 35, 45], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
    ]"
    :options="['scales' => ['x' => ['stacked' => true], 'y' => ['stacked' => true]]]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- Chart.js: Doughnut & Pie --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Doughnut & Pie Chart</h3>
            <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Chart.js</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Show proportions and percentages with circular charts.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Doughnut</h5>
                <div class="max-w-[200px] mx-auto">
                    <x-accelade::chart
                        type="doughnut"
                        :labels="['Organic', 'Direct', 'Referral', 'Social']"
                        :datasets="[['data' => [45, 25, 20, 10], 'backgroundColor' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899'], 'borderWidth' => 0]]"
                        :options="['cutout' => '60%']"
                        height="180px"
                    />
                </div>
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Pie</h5>
                <div class="max-w-[200px] mx-auto">
                    <x-accelade::chart
                        type="pie"
                        :labels="['Chrome', 'Firefox', 'Safari', 'Edge']"
                        :datasets="[['data' => [65, 15, 10, 10], 'backgroundColor' => ['#6366f1', '#f59e0b', '#22c55e', '#0ea5e9']]]"
                        height="180px"
                    />
                </div>
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="doughnut-pie.blade.php">
{{-- Doughnut Chart --}}
&lt;x-accelade::chart
    type="doughnut"
    :labels="['Organic', 'Direct', 'Referral', 'Social']"
    :datasets="[['data' => [45, 25, 20, 10], 'backgroundColor' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899']]]"
    :options="['cutout' => '60%']"
/&gt;

{{-- Pie Chart --}}
&lt;x-accelade::chart
    type="pie"
    :labels="['Chrome', 'Firefox', 'Safari']"
    :datasets="[['data' => [65, 15, 20], 'backgroundColor' => ['#6366f1', '#f59e0b', '#22c55e']]]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- Chart.js: Radar & Polar Area --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radar & Polar Area</h3>
            <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Chart.js</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Compare multiple variables on radial grids.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Radar Chart</h5>
                <div class="max-w-[250px] mx-auto">
                    <x-accelade::chart
                        type="radar"
                        :labels="['JS', 'PHP', 'Python', 'Go', 'Rust']"
                        :datasets="[
                            ['label' => 'Dev A', 'data' => [90, 85, 70, 50, 30], 'borderColor' => '#6366f1', 'backgroundColor' => 'rgba(99, 102, 241, 0.2)'],
                            ['label' => 'Dev B', 'data' => [60, 90, 85, 80, 70], 'borderColor' => '#ec4899', 'backgroundColor' => 'rgba(236, 72, 153, 0.2)'],
                        ]"
                        :options="['scales' => ['r' => ['beginAtZero' => true, 'max' => 100]]]"
                        height="200px"
                    />
                </div>
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Polar Area</h5>
                <div class="max-w-[200px] mx-auto">
                    <x-accelade::chart
                        type="polarArea"
                        :labels="['Red', 'Green', 'Yellow', 'Blue', 'Purple']"
                        :datasets="[['data' => [11, 16, 7, 14, 9], 'backgroundColor' => ['rgba(244, 63, 94, 0.7)', 'rgba(34, 197, 94, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(168, 85, 247, 0.7)']]]"
                        height="200px"
                    />
                </div>
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="radar-polar.blade.php">
{{-- Radar Chart --}}
&lt;x-accelade::chart
    type="radar"
    :labels="['JS', 'PHP', 'Python', 'Go', 'Rust']"
    :datasets="[
        ['label' => 'Dev A', 'data' => [90, 85, 70, 50, 30], 'borderColor' => '#6366f1', 'backgroundColor' => 'rgba(99, 102, 241, 0.2)'],
        ['label' => 'Dev B', 'data' => [60, 90, 85, 80, 70], 'borderColor' => '#ec4899', 'backgroundColor' => 'rgba(236, 72, 153, 0.2)'],
    ]"
    :options="['scales' => ['r' => ['beginAtZero' => true, 'max' => 100]]]"
/&gt;

{{-- Polar Area --}}
&lt;x-accelade::chart
    type="polarArea"
    :labels="['Red', 'Green', 'Yellow', 'Blue']"
    :datasets="[['data' => [11, 16, 7, 14], 'backgroundColor' => ['rgba(244, 63, 94, 0.7)', 'rgba(34, 197, 94, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(59, 130, 246, 0.7)']]]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- Chart.js: Scatter & Bubble --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Scatter & Bubble</h3>
            <span class="text-xs px-2 py-1 bg-orange-500/20 text-orange-500 rounded">Chart.js</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Plot data points on X/Y axes with optional size dimension.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Scatter</h5>
                <x-accelade::chart
                    type="scatter"
                    :labels="[]"
                    :datasets="[
                        ['label' => 'A', 'data' => [['x' => -10, 'y' => 0], ['x' => 0, 'y' => 10], ['x' => 10, 'y' => 5], ['x' => 5, 'y' => -5]], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)', 'pointRadius' => 8],
                        ['label' => 'B', 'data' => [['x' => -8, 'y' => 5], ['x' => 2, 'y' => -8], ['x' => 8, 'y' => 2]], 'backgroundColor' => 'rgba(236, 72, 153, 0.8)', 'pointRadius' => 8],
                    ]"
                    :options="['scales' => ['x' => ['type' => 'linear', 'position' => 'bottom']]]"
                    height="180px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Bubble</h5>
                <x-accelade::chart
                    type="bubble"
                    :labels="[]"
                    :datasets="[
                        ['label' => 'Products', 'data' => [['x' => 20, 'y' => 30, 'r' => 15], ['x' => 40, 'y' => 10, 'r' => 10], ['x' => 30, 'y' => 22, 'r' => 20]], 'backgroundColor' => 'rgba(139, 92, 246, 0.6)'],
                        ['label' => 'Services', 'data' => [['x' => 15, 'y' => 15, 'r' => 12], ['x' => 35, 'y' => 28, 'r' => 18]], 'backgroundColor' => 'rgba(34, 197, 94, 0.6)'],
                    ]"
                    height="180px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="scatter-bubble.blade.php">
{{-- Scatter Chart --}}
&lt;x-accelade::chart
    type="scatter"
    :datasets="[
        ['label' => 'A', 'data' => [['x' => -10, 'y' => 0], ['x' => 0, 'y' => 10], ['x' => 10, 'y' => 5]], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)', 'pointRadius' => 8],
    ]"
    :options="['scales' => ['x' => ['type' => 'linear', 'position' => 'bottom']]]"
/&gt;

{{-- Bubble Chart (x, y, r = radius) --}}
&lt;x-accelade::chart
    type="bubble"
    :datasets="[
        ['label' => 'Products', 'data' => [['x' => 20, 'y' => 30, 'r' => 15], ['x' => 40, 'y' => 10, 'r' => 10]], 'backgroundColor' => 'rgba(139, 92, 246, 0.6)'],
    ]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- ApexCharts: Area & Line --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-sky-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Area & Line Chart</h3>
            <span class="text-xs px-2 py-1 bg-sky-500/20 text-sky-500 rounded">ApexCharts</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Gradient area charts and interactive line charts with zoom and pan.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Gradient Area</h5>
                <x-accelade::apex-chart
                    type="area"
                    :series="[
                        ['name' => 'Visitors', 'data' => [31, 40, 28, 51, 42, 55]],
                        ['name' => 'Views', 'data' => [11, 32, 45, 32, 34, 41]],
                    ]"
                    :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                    :options="['colors' => ['#6366f1', '#22c55e'], 'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => 0.5, 'opacityTo' => 0.1]]]"
                    height="180px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Dashed Lines</h5>
                <x-accelade::apex-chart
                    type="line"
                    :series="[
                        ['name' => 'Actual', 'data' => [45, 52, 38, 45, 55, 60]],
                        ['name' => 'Target', 'data' => [40, 48, 45, 50, 55, 60]],
                    ]"
                    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                    :options="['colors' => ['#3b82f6', '#94a3b8'], 'stroke' => ['width' => [3, 3], 'dashArray' => [0, 5]], 'markers' => ['size' => 4]]"
                    height="180px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="apex-area-line.blade.php">
{{-- Gradient Area --}}
&lt;x-accelade::apex-chart
    type="area"
    :series="[['name' => 'Visitors', 'data' => [31, 40, 28, 51, 42]]]"
    :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri']"
    :options="[
        'colors' => ['#6366f1'],
        'fill' => ['type' => 'gradient', 'gradient' => ['opacityFrom' => 0.5, 'opacityTo' => 0.1]],
    ]"
/&gt;

{{-- Dashed Lines --}}
&lt;x-accelade::apex-chart
    type="line"
    :series="[
        ['name' => 'Actual', 'data' => [45, 52, 38]],
        ['name' => 'Target', 'data' => [40, 48, 45]],
    ]"
    :categories="['Jan', 'Feb', 'Mar']"
    :options="['stroke' => ['width' => [3, 3], 'dashArray' => [0, 5]]]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- ApexCharts: Bar --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bar Chart</h3>
            <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">ApexCharts</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Horizontal bars, grouped columns, and stacked charts with rounded corners.
        </p>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Horizontal</h5>
                <x-accelade::apex-chart
                    type="bar"
                    :series="[['name' => 'Sales', 'data' => [400, 430, 448, 470]]]"
                    :categories="['A', 'B', 'C', 'D']"
                    :options="['colors' => ['#f59e0b'], 'plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4]]]"
                    height="150px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Grouped</h5>
                <x-accelade::apex-chart
                    type="bar"
                    :series="[
                        ['name' => 'Q1', 'data' => [44, 55, 41]],
                        ['name' => 'Q2', 'data' => [53, 32, 33]],
                    ]"
                    :categories="['A', 'B', 'C']"
                    :options="['colors' => ['#6366f1', '#22c55e'], 'plotOptions' => ['bar' => ['borderRadius' => 4]]]"
                    height="150px"
                />
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Stacked</h5>
                <x-accelade::apex-chart
                    type="bar"
                    :series="[
                        ['name' => 'A', 'data' => [44, 55, 41]],
                        ['name' => 'B', 'data' => [13, 23, 20]],
                    ]"
                    :categories="['Jan', 'Feb', 'Mar']"
                    :options="['colors' => ['#6366f1', '#ec4899'], 'chart' => ['stacked' => true], 'plotOptions' => ['bar' => ['borderRadius' => 4]]]"
                    height="150px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="apex-bar.blade.php">
{{-- Horizontal Bar --}}
&lt;x-accelade::apex-chart
    type="bar"
    :series="[['name' => 'Sales', 'data' => [400, 430, 448]]]"
    :categories="['Product A', 'Product B', 'Product C']"
    :options="['plotOptions' => ['bar' => ['horizontal' => true, 'borderRadius' => 4]]]"
/&gt;

{{-- Stacked Column --}}
&lt;x-accelade::apex-chart
    type="bar"
    :series="[
        ['name' => 'A', 'data' => [44, 55, 41]],
        ['name' => 'B', 'data' => [13, 23, 20]],
    ]"
    :categories="['Jan', 'Feb', 'Mar']"
    :options="['chart' => ['stacked' => true]]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- ApexCharts: Radar & Donut --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-pink-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radar & Donut</h3>
            <span class="text-xs px-2 py-1 bg-pink-500/20 text-pink-500 rounded">ApexCharts</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Multi-axis radar charts and donut charts with center labels.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Radar</h5>
                <div class="max-w-[250px] mx-auto">
                    <x-accelade::apex-chart
                        type="radar"
                        :series="[
                            ['name' => 'Team A', 'data' => [80, 50, 30, 40, 100]],
                            ['name' => 'Team B', 'data' => [20, 30, 40, 80, 20]],
                        ]"
                        :categories="['Frontend', 'Backend', 'DevOps', 'Design', 'Testing']"
                        :options="['colors' => ['#ec4899', '#6366f1'], 'stroke' => ['width' => 2], 'fill' => ['opacity' => 0.2], 'markers' => ['size' => 4]]"
                        height="200px"
                    />
                </div>
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Donut with Total</h5>
                <div class="max-w-[200px] mx-auto">
                    <x-accelade::apex-chart
                        type="donut"
                        :series="[44, 55, 41, 17]"
                        :options="[
                            'labels' => ['Active', 'Pending', 'Review', 'Blocked'],
                            'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ef4444'],
                            'plotOptions' => ['pie' => ['donut' => ['size' => '65%', 'labels' => ['show' => true, 'total' => ['show' => true, 'showAlways' => true, 'label' => 'Total']]]]],
                        ]"
                        height="200px"
                    />
                </div>
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="apex-radar-donut.blade.php">
{{-- Radar Chart --}}
&lt;x-accelade::apex-chart
    type="radar"
    :series="[
        ['name' => 'Team A', 'data' => [80, 50, 30, 40, 100]],
        ['name' => 'Team B', 'data' => [20, 30, 40, 80, 20]],
    ]"
    :categories="['Frontend', 'Backend', 'DevOps', 'Design', 'Testing']"
    :options="['colors' => ['#ec4899', '#6366f1'], 'stroke' => ['width' => 2], 'fill' => ['opacity' => 0.2]]"
/&gt;

{{-- Donut with Center Total --}}
&lt;x-accelade::apex-chart
    type="donut"
    :series="[44, 55, 41, 17]"
    :options="[
        'labels' => ['Active', 'Pending', 'Review', 'Blocked'],
        'plotOptions' => ['pie' => ['donut' => ['size' => '65%', 'labels' => ['show' => true, 'total' => ['show' => true, 'showAlways' => true]]]]],
    ]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- ApexCharts: Radial Bar & Heatmap --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-fuchsia-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radial Bar & Heatmap</h3>
            <span class="text-xs px-2 py-1 bg-fuchsia-500/20 text-fuchsia-500 rounded">ApexCharts</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Circular progress indicators and grid-based color intensity charts.
        </p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Radial Progress</h5>
                <div class="max-w-[200px] mx-auto">
                    <x-accelade::apex-chart
                        type="radialBar"
                        :series="[44, 55, 67, 83]"
                        :options="[
                            'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899'],
                            'labels' => ['Apples', 'Oranges', 'Bananas', 'Berries'],
                            'plotOptions' => ['radialBar' => ['dataLabels' => ['total' => ['show' => true, 'label' => 'Avg']]]],
                        ]"
                        height="200px"
                    />
                </div>
            </div>
            <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Heatmap</h5>
                <x-accelade::apex-chart
                    type="heatmap"
                    :series="[
                        ['name' => 'Mon', 'data' => [['x' => '9am', 'y' => 22], ['x' => '12pm', 'y' => 67], ['x' => '3pm', 'y' => 55], ['x' => '6pm', 'y' => 33]]],
                        ['name' => 'Tue', 'data' => [['x' => '9am', 'y' => 18], ['x' => '12pm', 'y' => 78], ['x' => '3pm', 'y' => 62], ['x' => '6pm', 'y' => 28]]],
                        ['name' => 'Wed', 'data' => [['x' => '9am', 'y' => 25], ['x' => '12pm', 'y' => 85], ['x' => '3pm', 'y' => 48], ['x' => '6pm', 'y' => 35]]],
                    ]"
                    :options="['colors' => ['#ef4444'], 'dataLabels' => ['enabled' => false], 'plotOptions' => ['heatmap' => ['radius' => 4]]]"
                    height="180px"
                />
            </div>
        </div>

        <x-accelade::code-block language="blade" filename="apex-radial-heatmap.blade.php">
{{-- Radial Bar --}}
&lt;x-accelade::apex-chart
    type="radialBar"
    :series="[44, 55, 67, 83]"
    :options="[
        'labels' => ['Apples', 'Oranges', 'Bananas', 'Berries'],
        'plotOptions' => ['radialBar' => ['dataLabels' => ['total' => ['show' => true]]]],
    ]"
/&gt;

{{-- Heatmap --}}
&lt;x-accelade::apex-chart
    type="heatmap"
    :series="[
        ['name' => 'Mon', 'data' => [['x' => '9am', 'y' => 22], ['x' => '12pm', 'y' => 67]]],
        ['name' => 'Tue', 'data' => [['x' => '9am', 'y' => 18], ['x' => '12pm', 'y' => 78]]],
    ]"
    :options="['colors' => ['#ef4444']]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- ApexCharts: Treemap --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Treemap</h3>
            <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">ApexCharts</span>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Hierarchical data as nested rectangles where size represents value.
        </p>

        <div class="mb-4">
            <x-accelade::apex-chart
                type="treemap"
                :series="[['data' => [['x' => 'New York', 'y' => 218], ['x' => 'Los Angeles', 'y' => 149], ['x' => 'Chicago', 'y' => 100], ['x' => 'Houston', 'y' => 88], ['x' => 'Phoenix', 'y' => 75], ['x' => 'Philadelphia', 'y' => 65]]]]"
                :options="['colors' => ['#10b981'], 'plotOptions' => ['treemap' => ['distributed' => true, 'enableShades' => true]], 'dataLabels' => ['enabled' => true, 'style' => ['fontSize' => '12px']]]"
                height="250px"
            />
        </div>

        <x-accelade::code-block language="blade" filename="apex-treemap.blade.php">
{{-- Treemap Chart --}}
&lt;x-accelade::apex-chart
    type="treemap"
    :series="[
        [
            'data' => [
                ['x' => 'New York', 'y' => 218],
                ['x' => 'Los Angeles', 'y' => 149],
                ['x' => 'Chicago', 'y' => 100],
                ['x' => 'Houston', 'y' => 88],
            ],
        ],
    ]"
    :options="[
        'colors' => ['#10b981'],
        'plotOptions' => ['treemap' => ['distributed' => true, 'enableShades' => true]],
    ]"
/&gt;
        </x-accelade::code-block>
    </section>

    {{-- JavaScript API --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">JavaScript API</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Access chart instances programmatically to update data, options, or destroy charts.
        </p>

        <x-accelade::code-block language="javascript" filename="chart-api.js">
// Get a chart instance by ID
const chart = Accelade.chart.get('my-chart-id');

// Update chart data
chart.updateData({
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [{ label: 'Sales', data: [10, 20, 30] }],
});

// Update options
chart.updateOptions({
    plugins: { legend: { display: false } },
});

// Add a new dataset
chart.addDataset({
    label: 'New Series',
    data: [5, 10, 15],
    borderColor: '#22c55e',
});

// Remove dataset by index
chart.removeDataset(1);

// Add data point to all datasets
chart.addData('Apr', [25, 20]);

// Export chart as image
const imageUrl = chart.toBase64Image();

// Destroy chart
chart.destroy();

// Initialize all charts on the page
Accelade.chart.initAll();
        </x-accelade::code-block>
    </section>

    {{-- Props Reference --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-slate-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Props Reference</h3>
        </div>

        <div class="space-y-4">
            <div>
                <h4 class="font-medium mb-2 text-indigo-500">Chart.js Component Props</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[var(--docs-border)]">
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                            </tr>
                        </thead>
                        <tbody style="color: var(--docs-text-muted);">
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">type</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'line'</td>
                                <td class="py-2 px-3">line, bar, pie, doughnut, radar, polarArea, scatter, bubble</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">labels</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">X-axis labels (categories)</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">datasets</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">Array of dataset objects with data, label, colors</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">options</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">Chart.js configuration options</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">height</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'400px'</td>
                                <td class="py-2 px-3">Chart container height</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">width</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'100%'</td>
                                <td class="py-2 px-3">Chart container width</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-indigo-500">reactive</code></td>
                                <td class="py-2 px-3">bool</td>
                                <td class="py-2 px-3">true</td>
                                <td class="py-2 px-3">Enable reactive updates</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3"><code class="text-indigo-500">id</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">auto</td>
                                <td class="py-2 px-3">Custom chart ID (auto-generated if not set)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="font-medium mb-2 text-sky-500">ApexCharts Component Props</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[var(--docs-border)]">
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                                <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                            </tr>
                        </thead>
                        <tbody style="color: var(--docs-text-muted);">
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">type</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'line'</td>
                                <td class="py-2 px-3">line, area, bar, radar, pie, donut, radialBar, heatmap, treemap</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">series</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">Array of series with name and data</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">categories</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">X-axis categories/labels</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">options</code></td>
                                <td class="py-2 px-3">array</td>
                                <td class="py-2 px-3">[]</td>
                                <td class="py-2 px-3">ApexCharts configuration options</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">height</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'400px'</td>
                                <td class="py-2 px-3">Chart container height</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">width</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">'100%'</td>
                                <td class="py-2 px-3">Chart container width</td>
                            </tr>
                            <tr class="border-b border-[var(--docs-border)]">
                                <td class="py-2 px-3"><code class="text-sky-500">reactive</code></td>
                                <td class="py-2 px-3">bool</td>
                                <td class="py-2 px-3">true</td>
                                <td class="py-2 px-3">Enable reactive updates</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3"><code class="text-sky-500">id</code></td>
                                <td class="py-2 px-3">string</td>
                                <td class="py-2 px-3">auto</td>
                                <td class="py-2 px-3">Custom chart ID (auto-generated if not set)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</x-accelade::layouts.docs>
