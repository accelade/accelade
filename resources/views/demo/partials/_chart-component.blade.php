{{-- Chart Component Section - Data Visualization with Chart.js and ApexCharts --}}
@props(['prefix' => 'a'])

@php
    $textAttr = match($prefix) {
        'v' => 'v-text',
        'data-state' => 'data-state-text',
        's' => 's-text',
        'ng' => 'ng-text',
        default => 'a-text',
    };

    $showAttr = match($prefix) {
        'v' => 'v-show',
        'data-state' => 'data-state-show',
        's' => 's-show',
        'ng' => 'ng-show',
        default => 'a-show',
    };
@endphp

{{-- ============================================ --}}
{{-- CHART.JS SECTIONS --}}
{{-- ============================================ --}}

<!-- Chart.js: Line Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Line Chart</h3>
        <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Display trends over time with smooth or stepped lines. Ideal for showing continuous data like sales, traffic, or temperature changes.
    </p>

    <div class="space-y-4">
        <!-- Basic Line Chart -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Basic Line Chart</h4>
            <x-accelade::chart
                type="line"
                :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                :datasets="[
                    [
                        'label' => 'Revenue',
                        'data' => [65, 59, 80, 81, 56, 55],
                        'borderColor' => 'rgb(99, 102, 241)',
                        'backgroundColor' => 'transparent',
                        'tension' => 0,
                    ],
                ]"
                height="280px"
            />
        </div>

        <!-- Multi-Series Line Chart with Fill -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Multi-Series with Area Fill</h4>
            <x-accelade::chart
                type="line"
                :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                :datasets="[
                    [
                        'label' => 'Sales 2024',
                        'data' => [65, 59, 80, 81, 56, 55],
                        'borderColor' => 'rgb(99, 102, 241)',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Sales 2023',
                        'data' => [45, 52, 60, 65, 48, 50],
                        'borderColor' => 'rgb(34, 197, 94)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ]"
                height="280px"
            />
        </div>

        <!-- Stepped Line Chart -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Stepped Line Chart</h4>
            <x-accelade::chart
                type="line"
                :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                :datasets="[
                    [
                        'label' => 'Active Users',
                        'data' => [120, 190, 130, 150, 180, 90, 100],
                        'borderColor' => 'rgb(236, 72, 153)',
                        'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
                        'fill' => true,
                        'stepped' => true,
                    ],
                ]"
                height="280px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="line-chart.blade.php" class="mt-4">
{{-- Basic Line Chart --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [65, 59, 80, 81, 56],
            'borderColor' => 'rgb(99, 102, 241)',
            'tension' => 0.4,
        ],
    ]"
    height="300px"
/&gt;

{{-- Multi-Series with Fill --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Jan', 'Feb', 'Mar']"
    :datasets="[
        [
            'label' => '2024',
            'data' => [65, 59, 80],
            'borderColor' => 'rgb(99, 102, 241)',
            'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
            'fill' => true,
            'tension' => 0.4,
        ],
        [
            'label' => '2023',
            'data' => [45, 52, 60],
            'borderColor' => 'rgb(34, 197, 94)',
            'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
            'fill' => true,
            'tension' => 0.4,
        ],
    ]"
/&gt;

{{-- Stepped Line --}}
&lt;x-accelade::chart
    type="line"
    :labels="['Mon', 'Tue', 'Wed']"
    :datasets="[
        [
            'label' => 'Users',
            'data' => [120, 190, 130],
            'borderColor' => 'rgb(236, 72, 153)',
            'stepped' => true,
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Bar Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bar Chart</h3>
        <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Compare discrete categories with vertical or horizontal bars. Perfect for showing comparisons between groups.
    </p>

    <div class="space-y-4">
        <!-- Vertical Bar Chart -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Vertical Bar Chart</h4>
            <x-accelade::chart
                type="bar"
                :labels="['Q1', 'Q2', 'Q3', 'Q4']"
                :datasets="[
                    [
                        'label' => 'Revenue',
                        'data' => [12000, 19000, 15000, 22000],
                        'backgroundColor' => [
                            'rgba(99, 102, 241, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                        ],
                        'borderWidth' => 0,
                    ],
                ]"
                :options="[
                    'plugins' => [
                        'legend' => ['display' => false],
                    ],
                    'scales' => [
                        'y' => ['beginAtZero' => true],
                    ],
                ]"
                height="280px"
            />
        </div>

        <!-- Grouped Bar Chart -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Grouped Bar Chart</h4>
            <x-accelade::chart
                type="bar"
                :labels="['Jan', 'Feb', 'Mar', 'Apr']"
                :datasets="[
                    [
                        'label' => 'Desktop',
                        'data' => [65, 59, 80, 81],
                        'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    ],
                    [
                        'label' => 'Mobile',
                        'data' => [45, 52, 60, 65],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    ],
                    [
                        'label' => 'Tablet',
                        'data' => [25, 32, 40, 35],
                        'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                    ],
                ]"
                :options="[
                    'scales' => [
                        'y' => ['beginAtZero' => true],
                    ],
                ]"
                height="280px"
            />
        </div>

        <!-- Stacked Bar Chart -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Stacked Bar Chart</h4>
            <x-accelade::chart
                type="bar"
                :labels="['Q1', 'Q2', 'Q3', 'Q4']"
                :datasets="[
                    [
                        'label' => 'Product A',
                        'data' => [50, 60, 70, 80],
                        'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    ],
                    [
                        'label' => 'Product B',
                        'data' => [30, 40, 35, 45],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    ],
                    [
                        'label' => 'Product C',
                        'data' => [20, 25, 30, 35],
                        'backgroundColor' => 'rgba(236, 72, 153, 0.8)',
                    ],
                ]"
                :options="[
                    'scales' => [
                        'x' => ['stacked' => true],
                        'y' => ['stacked' => true, 'beginAtZero' => true],
                    ],
                ]"
                height="280px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="bar-chart.blade.php" class="mt-4">
{{-- Vertical Bar --}}
&lt;x-accelade::chart
    type="bar"
    :labels="['Q1', 'Q2', 'Q3', 'Q4']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [12000, 19000, 15000, 22000],
            'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
        ],
    ]"
/&gt;

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
    :options="[
        'scales' => [
            'x' => ['stacked' => true],
            'y' => ['stacked' => true],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Doughnut & Pie Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Doughnut & Pie Chart</h3>
        <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Show proportions and percentages with circular charts. Ideal for displaying parts of a whole.
    </p>

    <div class="grid md:grid-cols-2 gap-4">
        <!-- Doughnut Chart -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Doughnut Chart</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::chart
                    type="doughnut"
                    :labels="['Organic', 'Direct', 'Referral', 'Social']"
                    :datasets="[
                        [
                            'data' => [45, 25, 20, 10],
                            'backgroundColor' => [
                                'rgb(99, 102, 241)',
                                'rgb(34, 197, 94)',
                                'rgb(245, 158, 11)',
                                'rgb(236, 72, 153)',
                            ],
                            'borderWidth' => 0,
                        ],
                    ]"
                    :options="[
                        'cutout' => '60%',
                    ]"
                    height="260px"
                />
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Pie Chart</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::chart
                    type="pie"
                    :labels="['Chrome', 'Firefox', 'Safari', 'Edge', 'Other']"
                    :datasets="[
                        [
                            'data' => [65, 15, 10, 7, 3],
                            'backgroundColor' => [
                                'rgb(99, 102, 241)',
                                'rgb(245, 158, 11)',
                                'rgb(34, 197, 94)',
                                'rgb(14, 165, 233)',
                                'rgb(156, 163, 175)',
                            ],
                            'borderWidth' => 2,
                            'borderColor' => 'white',
                        ],
                    ]"
                    height="260px"
                />
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="doughnut-pie-chart.blade.php" class="mt-4">
{{-- Doughnut Chart --}}
&lt;x-accelade::chart
    type="doughnut"
    :labels="['Organic', 'Direct', 'Referral', 'Social']"
    :datasets="[
        [
            'data' => [45, 25, 20, 10],
            'backgroundColor' => [
                'rgb(99, 102, 241)',
                'rgb(34, 197, 94)',
                'rgb(245, 158, 11)',
                'rgb(236, 72, 153)',
            ],
        ],
    ]"
    :options="['cutout' => '60%']"
/&gt;

{{-- Pie Chart --}}
&lt;x-accelade::chart
    type="pie"
    :labels="['Chrome', 'Firefox', 'Safari']"
    :datasets="[
        [
            'data' => [65, 15, 10],
            'backgroundColor' => [
                'rgb(99, 102, 241)',
                'rgb(245, 158, 11)',
                'rgb(34, 197, 94)',
            ],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Radar Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radar Chart</h3>
        <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Compare multiple variables on a radial grid. Great for skill assessments, performance metrics, or multi-dimensional data.
    </p>

    <div class="space-y-4">
        <!-- Basic Radar -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Skill Comparison</h4>
            <div class="max-w-md mx-auto">
                <x-accelade::chart
                    type="radar"
                    :labels="['JavaScript', 'PHP', 'Python', 'Go', 'Rust', 'TypeScript']"
                    :datasets="[
                        [
                            'label' => 'Developer A',
                            'data' => [90, 85, 70, 50, 30, 95],
                            'borderColor' => 'rgb(99, 102, 241)',
                            'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                        ],
                        [
                            'label' => 'Developer B',
                            'data' => [60, 90, 85, 80, 70, 55],
                            'borderColor' => 'rgb(236, 72, 153)',
                            'backgroundColor' => 'rgba(236, 72, 153, 0.2)',
                        ],
                    ]"
                    :options="[
                        'scales' => [
                            'r' => [
                                'beginAtZero' => true,
                                'max' => 100,
                            ],
                        ],
                    ]"
                    height="320px"
                />
            </div>
        </div>

        <!-- Single Series Radar -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Performance Metrics</h4>
            <div class="max-w-md mx-auto">
                <x-accelade::chart
                    type="radar"
                    :labels="['Speed', 'Reliability', 'Comfort', 'Safety', 'Efficiency', 'Value']"
                    :datasets="[
                        [
                            'label' => 'Product Score',
                            'data' => [85, 90, 75, 95, 80, 70],
                            'borderColor' => 'rgb(34, 197, 94)',
                            'backgroundColor' => 'rgba(34, 197, 94, 0.3)',
                            'pointBackgroundColor' => 'rgb(34, 197, 94)',
                        ],
                    ]"
                    :options="[
                        'scales' => [
                            'r' => [
                                'beginAtZero' => true,
                                'max' => 100,
                                'ticks' => ['stepSize' => 20],
                            ],
                        ],
                    ]"
                    height="320px"
                />
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="radar-chart.blade.php" class="mt-4">
{{-- Radar Chart --}}
&lt;x-accelade::chart
    type="radar"
    :labels="['JavaScript', 'PHP', 'Python', 'Go', 'Rust']"
    :datasets="[
        [
            'label' => 'Developer A',
            'data' => [90, 85, 70, 50, 30],
            'borderColor' => 'rgb(99, 102, 241)',
            'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
        ],
        [
            'label' => 'Developer B',
            'data' => [60, 90, 85, 80, 70],
            'borderColor' => 'rgb(236, 72, 153)',
            'backgroundColor' => 'rgba(236, 72, 153, 0.2)',
        ],
    ]"
    :options="[
        'scales' => [
            'r' => ['beginAtZero' => true, 'max' => 100],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Polar Area Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Polar Area Chart</h3>
        <span class="text-xs px-2 py-1 bg-rose-500/20 text-rose-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Similar to pie charts but with equal angles, showing magnitude through radius. Useful for comparing values across categories.
    </p>

    <div class="rounded-xl p-4 border border-rose-500/30" style="background: rgba(244, 63, 94, 0.05);">
        <div class="max-w-sm mx-auto">
            <x-accelade::chart
                type="polarArea"
                :labels="['Red', 'Green', 'Yellow', 'Blue', 'Purple']"
                :datasets="[
                    [
                        'data' => [11, 16, 7, 14, 9],
                        'backgroundColor' => [
                            'rgba(244, 63, 94, 0.7)',
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(168, 85, 247, 0.7)',
                        ],
                    ],
                ]"
                height="300px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="polar-area-chart.blade.php" class="mt-4">
{{-- Polar Area Chart --}}
&lt;x-accelade::chart
    type="polarArea"
    :labels="['Red', 'Green', 'Yellow', 'Blue', 'Purple']"
    :datasets="[
        [
            'data' => [11, 16, 7, 14, 9],
            'backgroundColor' => [
                'rgba(244, 63, 94, 0.7)',
                'rgba(34, 197, 94, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(59, 130, 246, 0.7)',
                'rgba(168, 85, 247, 0.7)',
            ],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Scatter Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Scatter Chart</h3>
        <span class="text-xs px-2 py-1 bg-orange-500/20 text-orange-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Plot individual data points on X/Y axes. Perfect for showing correlation, distribution, or clustering patterns.
    </p>

    <div class="rounded-xl p-4 border border-orange-500/30" style="background: rgba(249, 115, 22, 0.05);">
        <x-accelade::chart
            type="scatter"
            :labels="[]"
            :datasets="[
                [
                    'label' => 'Dataset A',
                    'data' => [
                        ['x' => -10, 'y' => 0],
                        ['x' => 0, 'y' => 10],
                        ['x' => 10, 'y' => 5],
                        ['x' => 5, 'y' => -5],
                        ['x' => -5, 'y' => 8],
                        ['x' => 3, 'y' => 3],
                        ['x' => -8, 'y' => -3],
                    ],
                    'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
                    'pointRadius' => 8,
                ],
                [
                    'label' => 'Dataset B',
                    'data' => [
                        ['x' => -8, 'y' => 5],
                        ['x' => 2, 'y' => -8],
                        ['x' => 8, 'y' => 2],
                        ['x' => -3, 'y' => -6],
                        ['x' => 6, 'y' => 9],
                        ['x' => -6, 'y' => 4],
                    ],
                    'backgroundColor' => 'rgba(236, 72, 153, 0.8)',
                    'pointRadius' => 8,
                ],
            ]"
            :options="[
                'scales' => [
                    'x' => ['type' => 'linear', 'position' => 'bottom'],
                ],
            ]"
            height="300px"
        />
    </div>

    <x-accelade::code-block language="blade" filename="scatter-chart.blade.php" class="mt-4">
{{-- Scatter Chart --}}
&lt;x-accelade::chart
    type="scatter"
    :datasets="[
        [
            'label' => 'Dataset A',
            'data' => [
                ['x' => -10, 'y' => 0],
                ['x' => 0, 'y' => 10],
                ['x' => 10, 'y' => 5],
            ],
            'backgroundColor' => 'rgba(99, 102, 241, 0.8)',
            'pointRadius' => 8,
        ],
    ]"
    :options="[
        'scales' => [
            'x' => ['type' => 'linear', 'position' => 'bottom'],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- Chart.js: Bubble Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-violet-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bubble Chart</h3>
        <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">Chart.js</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Like scatter charts but with a third dimension shown through bubble size. Great for comparing three variables at once.
    </p>

    <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.05);">
        <x-accelade::chart
            type="bubble"
            :labels="[]"
            :datasets="[
                [
                    'label' => 'Products',
                    'data' => [
                        ['x' => 20, 'y' => 30, 'r' => 15],
                        ['x' => 40, 'y' => 10, 'r' => 10],
                        ['x' => 30, 'y' => 22, 'r' => 20],
                        ['x' => 10, 'y' => 25, 'r' => 8],
                        ['x' => 50, 'y' => 35, 'r' => 25],
                    ],
                    'backgroundColor' => 'rgba(139, 92, 246, 0.6)',
                    'borderColor' => 'rgb(139, 92, 246)',
                ],
                [
                    'label' => 'Services',
                    'data' => [
                        ['x' => 15, 'y' => 15, 'r' => 12],
                        ['x' => 35, 'y' => 28, 'r' => 18],
                        ['x' => 45, 'y' => 20, 'r' => 14],
                    ],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.6)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ]"
            height="320px"
        />
    </div>

    <x-accelade::code-block language="blade" filename="bubble-chart.blade.php" class="mt-4">
{{-- Bubble Chart (x, y, r = radius) --}}
&lt;x-accelade::chart
    type="bubble"
    :datasets="[
        [
            'label' => 'Products',
            'data' => [
                ['x' => 20, 'y' => 30, 'r' => 15],
                ['x' => 40, 'y' => 10, 'r' => 10],
                ['x' => 30, 'y' => 22, 'r' => 20],
            ],
            'backgroundColor' => 'rgba(139, 92, 246, 0.6)',
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

{{-- ============================================ --}}
{{-- APEXCHARTS SECTIONS --}}
{{-- ============================================ --}}

<!-- ApexCharts: Area Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-sky-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Area Chart</h3>
        <span class="text-xs px-2 py-1 bg-sky-500/20 text-sky-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Smooth area charts with gradient fills. Perfect for visualizing trends with emphasis on volume or magnitude.
    </p>

    <div class="space-y-4">
        <!-- Basic Area -->
        <div class="rounded-xl p-4 border border-sky-500/30" style="background: rgba(14, 165, 233, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Gradient Area Chart</h4>
            <x-accelade::apex-chart
                type="area"
                :series="[
                    [
                        'name' => 'Visitors',
                        'data' => [31, 40, 28, 51, 42, 109, 100],
                    ],
                    [
                        'name' => 'Page Views',
                        'data' => [11, 32, 45, 32, 34, 52, 41],
                    ],
                ]"
                :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                :options="[
                    'colors' => ['#6366f1', '#22c55e'],
                    'fill' => [
                        'type' => 'gradient',
                        'gradient' => [
                            'opacityFrom' => 0.5,
                            'opacityTo' => 0.1,
                        ],
                    ],
                ]"
                height="300px"
            />
        </div>

        <!-- Stacked Area -->
        <div class="rounded-xl p-4 border border-sky-500/30" style="background: rgba(14, 165, 233, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Stacked Area Chart</h4>
            <x-accelade::apex-chart
                type="area"
                :series="[
                    ['name' => 'South', 'data' => [31, 40, 28, 51, 42, 109, 100]],
                    ['name' => 'North', 'data' => [11, 32, 45, 32, 34, 52, 41]],
                    ['name' => 'Central', 'data' => [15, 22, 35, 25, 28, 42, 35]],
                ]"
                :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']"
                :options="[
                    'colors' => ['#6366f1', '#22c55e', '#f59e0b'],
                    'chart' => ['stacked' => true],
                    'fill' => ['opacity' => 0.6],
                ]"
                height="300px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-area-chart.blade.php" class="mt-4">
{{-- Gradient Area Chart --}}
&lt;x-accelade::apex-chart
    type="area"
    :series="[
        ['name' => 'Visitors', 'data' => [31, 40, 28, 51, 42]],
        ['name' => 'Page Views', 'data' => [11, 32, 45, 32, 34]],
    ]"
    :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri']"
    :options="[
        'colors' => ['#6366f1', '#22c55e'],
        'fill' => [
            'type' => 'gradient',
            'gradient' => ['opacityFrom' => 0.5, 'opacityTo' => 0.1],
        ],
    ]"
/&gt;

{{-- Stacked Area --}}
&lt;x-accelade::apex-chart
    type="area"
    :series="[
        ['name' => 'South', 'data' => [31, 40, 28]],
        ['name' => 'North', 'data' => [11, 32, 45]],
    ]"
    :categories="['Jan', 'Feb', 'Mar']"
    :options="[
        'chart' => ['stacked' => true],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Line Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Line Chart</h3>
        <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Clean line charts with interactive features like zoom, pan, and data point tooltips.
    </p>

    <div class="space-y-4">
        <!-- Basic Line -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Multi-Series Line</h4>
            <x-accelade::apex-chart
                type="line"
                :series="[
                    ['name' => 'Desktop', 'data' => [45, 52, 38, 45, 19, 23, 30]],
                    ['name' => 'Mobile', 'data' => [35, 41, 62, 42, 13, 18, 29]],
                    ['name' => 'Tablet', 'data' => [20, 28, 35, 30, 25, 32, 28]],
                ]"
                :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']"
                :options="[
                    'colors' => ['#3b82f6', '#22c55e', '#f59e0b'],
                    'stroke' => ['width' => 3, 'curve' => 'smooth'],
                    'markers' => ['size' => 5],
                ]"
                height="300px"
            />
        </div>

        <!-- Dashed Line -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Dashed Lines</h4>
            <x-accelade::apex-chart
                type="line"
                :series="[
                    ['name' => 'Actual', 'data' => [45, 52, 38, 45, 55, 60, 72]],
                    ['name' => 'Target', 'data' => [40, 48, 45, 50, 55, 60, 65]],
                ]"
                :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']"
                :options="[
                    'colors' => ['#3b82f6', '#94a3b8'],
                    'stroke' => [
                        'width' => [3, 3],
                        'dashArray' => [0, 5],
                    ],
                ]"
                height="300px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-line-chart.blade.php" class="mt-4">
{{-- Multi-Series Line --}}
&lt;x-accelade::apex-chart
    type="line"
    :series="[
        ['name' => 'Desktop', 'data' => [45, 52, 38, 45]],
        ['name' => 'Mobile', 'data' => [35, 41, 62, 42]],
    ]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr']"
    :options="[
        'stroke' => ['width' => 3, 'curve' => 'smooth'],
        'markers' => ['size' => 5],
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
    :options="[
        'stroke' => [
            'width' => [3, 3],
            'dashArray' => [0, 5],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Bar Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bar Chart</h3>
        <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Vertical and horizontal bar charts with rounded corners and customizable styling.
    </p>

    <div class="space-y-4">
        <!-- Horizontal Bar -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Horizontal Bar</h4>
            <x-accelade::apex-chart
                type="bar"
                :series="[
                    [
                        'name' => 'Sales',
                        'data' => [400, 430, 448, 470, 540],
                    ],
                ]"
                :categories="['Product A', 'Product B', 'Product C', 'Product D', 'Product E']"
                :options="[
                    'colors' => ['#f59e0b'],
                    'plotOptions' => [
                        'bar' => [
                            'horizontal' => true,
                            'borderRadius' => 4,
                        ],
                    ],
                ]"
                height="300px"
            />
        </div>

        <!-- Grouped Column -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Grouped Column</h4>
            <x-accelade::apex-chart
                type="bar"
                :series="[
                    ['name' => 'Q1', 'data' => [44, 55, 41, 67]],
                    ['name' => 'Q2', 'data' => [53, 32, 33, 52]],
                    ['name' => 'Q3', 'data' => [62, 45, 52, 44]],
                ]"
                :categories="['Region A', 'Region B', 'Region C', 'Region D']"
                :options="[
                    'colors' => ['#6366f1', '#22c55e', '#f59e0b'],
                    'plotOptions' => [
                        'bar' => ['borderRadius' => 4],
                    ],
                ]"
                height="300px"
            />
        </div>

        <!-- Stacked Column -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Stacked Column</h4>
            <x-accelade::apex-chart
                type="bar"
                :series="[
                    ['name' => 'Product A', 'data' => [44, 55, 41, 67, 22]],
                    ['name' => 'Product B', 'data' => [13, 23, 20, 8, 13]],
                    ['name' => 'Product C', 'data' => [11, 17, 15, 15, 21]],
                ]"
                :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
                :options="[
                    'colors' => ['#6366f1', '#22c55e', '#ec4899'],
                    'chart' => ['stacked' => true],
                    'plotOptions' => [
                        'bar' => ['borderRadius' => 4],
                    ],
                ]"
                height="300px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-bar-chart.blade.php" class="mt-4">
{{-- Horizontal Bar --}}
&lt;x-accelade::apex-chart
    type="bar"
    :series="[['name' => 'Sales', 'data' => [400, 430, 448]]]"
    :categories="['Product A', 'Product B', 'Product C']"
    :options="[
        'plotOptions' => [
            'bar' => ['horizontal' => true, 'borderRadius' => 4],
        ],
    ]"
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

<!-- ApexCharts: Radar Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-pink-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radar Chart</h3>
        <span class="text-xs px-2 py-1 bg-pink-500/20 text-pink-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Multi-axis chart for comparing multiple variables. Great for skill assessments and performance comparisons.
    </p>

    <div class="space-y-4">
        <!-- Basic Radar -->
        <div class="rounded-xl p-4 border border-pink-500/30" style="background: rgba(236, 72, 153, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Team Skills Comparison</h4>
            <div class="max-w-md mx-auto">
                <x-accelade::apex-chart
                    type="radar"
                    :series="[
                        [
                            'name' => 'Team A',
                            'data' => [80, 50, 30, 40, 100, 20],
                        ],
                        [
                            'name' => 'Team B',
                            'data' => [20, 30, 40, 80, 20, 80],
                        ],
                    ]"
                    :categories="['Frontend', 'Backend', 'DevOps', 'Design', 'Testing', 'Analytics']"
                    :options="[
                        'colors' => ['#ec4899', '#6366f1'],
                        'stroke' => ['width' => 2],
                        'fill' => ['opacity' => 0.2],
                        'markers' => ['size' => 4],
                    ]"
                    height="350px"
                />
            </div>
        </div>

        <!-- Polygon Radar -->
        <div class="rounded-xl p-4 border border-pink-500/30" style="background: rgba(236, 72, 153, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Polygon Style</h4>
            <div class="max-w-md mx-auto">
                <x-accelade::apex-chart
                    type="radar"
                    :series="[
                        ['name' => 'Score', 'data' => [65, 72, 58, 85, 70]],
                    ]"
                    :categories="['Speed', 'Quality', 'Cost', 'Support', 'Delivery']"
                    :options="[
                        'colors' => ['#22c55e'],
                        'stroke' => ['width' => 2],
                        'fill' => ['opacity' => 0.3],
                        'markers' => ['size' => 5],
                        'yaxis' => ['stepSize' => 20],
                        'plotOptions' => [
                            'radar' => ['polygons' => ['strokeColors' => '#e5e7eb']],
                        ],
                    ]"
                    height="350px"
                />
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-radar-chart.blade.php" class="mt-4">
{{-- Radar Chart --}}
&lt;x-accelade::apex-chart
    type="radar"
    :series="[
        ['name' => 'Team A', 'data' => [80, 50, 30, 40, 100]],
        ['name' => 'Team B', 'data' => [20, 30, 40, 80, 20]],
    ]"
    :categories="['Frontend', 'Backend', 'DevOps', 'Design', 'Testing']"
    :options="[
        'colors' => ['#ec4899', '#6366f1'],
        'stroke' => ['width' => 2],
        'fill' => ['opacity' => 0.2],
        'markers' => ['size' => 4],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Donut Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-teal-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Donut Chart</h3>
        <span class="text-xs px-2 py-1 bg-teal-500/20 text-teal-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Circular chart with center cutout. Shows parts of a whole with optional center labels for totals or highlights.
    </p>

    <div class="grid md:grid-cols-2 gap-4">
        <!-- Basic Donut -->
        <div class="rounded-xl p-4 border border-teal-500/30" style="background: rgba(20, 184, 166, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Basic Donut</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::apex-chart
                    type="donut"
                    :series="[44, 55, 13, 33]"
                    :options="[
                        'colors' => ['#14b8a6', '#6366f1', '#f59e0b', '#ec4899'],
                        'labels' => ['Marketing', 'Development', 'Operations', 'Support'],
                    ]"
                    height="280px"
                />
            </div>
        </div>

        <!-- Donut with Total -->
        <div class="rounded-xl p-4 border border-teal-500/30" style="background: rgba(20, 184, 166, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">With Center Total</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::apex-chart
                    type="donut"
                    :series="[44, 55, 41, 17]"
                    :options="[
                        'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ef4444'],
                        'labels' => ['Active', 'Pending', 'Review', 'Blocked'],
                        'plotOptions' => [
                            'pie' => [
                                'donut' => [
                                    'size' => '65%',
                                    'labels' => [
                                        'show' => true,
                                        'name' => ['show' => true],
                                        'value' => ['show' => true],
                                        'total' => [
                                            'show' => true,
                                            'showAlways' => true,
                                            'label' => 'Total Tasks',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]"
                    height="280px"
                />
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-donut-chart.blade.php" class="mt-4">
{{-- Basic Donut --}}
&lt;x-accelade::apex-chart
    type="donut"
    :series="[44, 55, 13, 33]"
    :options="[
        'labels' => ['Marketing', 'Development', 'Operations', 'Support'],
        'colors' => ['#14b8a6', '#6366f1', '#f59e0b', '#ec4899'],
    ]"
/&gt;

{{-- Donut with Center Total --}}
&lt;x-accelade::apex-chart
    type="donut"
    :series="[44, 55, 41, 17]"
    :options="[
        'labels' => ['Active', 'Pending', 'Review', 'Blocked'],
        'plotOptions' => [
            'pie' => [
                'donut' => [
                    'size' => '65%',
                    'labels' => [
                        'show' => true,
                        'total' => ['show' => true, 'showAlways' => true, 'label' => 'Total'],
                    ],
                ],
            ],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Pie Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-lime-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Pie Chart</h3>
        <span class="text-xs px-2 py-1 bg-lime-500/20 text-lime-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Classic circular chart showing proportions. Best for showing simple distributions with few categories.
    </p>

    <div class="rounded-xl p-4 border border-lime-500/30" style="background: rgba(132, 204, 22, 0.05);">
        <div class="max-w-sm mx-auto">
            <x-accelade::apex-chart
                type="pie"
                :series="[44, 55, 13, 43, 22]"
                :options="[
                    'labels' => ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
                    'colors' => ['#84cc16', '#22c55e', '#14b8a6', '#06b6d4', '#0ea5e9'],
                    'legend' => ['position' => 'bottom'],
                    'responsive' => [
                        [
                            'breakpoint' => 480,
                            'options' => [
                                'legend' => ['position' => 'bottom'],
                            ],
                        ],
                    ],
                ]"
                height="320px"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-pie-chart.blade.php" class="mt-4">
{{-- Pie Chart --}}
&lt;x-accelade::apex-chart
    type="pie"
    :series="[44, 55, 13, 43, 22]"
    :options="[
        'labels' => ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        'colors' => ['#84cc16', '#22c55e', '#14b8a6', '#06b6d4', '#0ea5e9'],
        'legend' => ['position' => 'bottom'],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Radial Bar Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-fuchsia-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Radial Bar Chart</h3>
        <span class="text-xs px-2 py-1 bg-fuchsia-500/20 text-fuchsia-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Circular progress indicators showing completion percentages. Great for dashboards and KPI displays.
    </p>

    <div class="grid md:grid-cols-2 gap-4">
        <!-- Single Radial -->
        <div class="rounded-xl p-4 border border-fuchsia-500/30" style="background: rgba(217, 70, 239, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Progress Indicator</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::apex-chart
                    type="radialBar"
                    :series="[70]"
                    :options="[
                        'colors' => ['#d946ef'],
                        'plotOptions' => [
                            'radialBar' => [
                                'hollow' => ['size' => '70%'],
                                'dataLabels' => [
                                    'name' => ['show' => false],
                                    'value' => ['fontSize' => '32px'],
                                ],
                            ],
                        ],
                        'labels' => ['Progress'],
                    ]"
                    height="280px"
                />
            </div>
        </div>

        <!-- Multiple Radials -->
        <div class="rounded-xl p-4 border border-fuchsia-500/30" style="background: rgba(217, 70, 239, 0.05);">
            <h4 class="font-medium mb-3" style="color: var(--docs-text);">Multiple Metrics</h4>
            <div class="max-w-xs mx-auto">
                <x-accelade::apex-chart
                    type="radialBar"
                    :series="[44, 55, 67, 83]"
                    :options="[
                        'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899'],
                        'plotOptions' => [
                            'radialBar' => [
                                'dataLabels' => [
                                    'total' => [
                                        'show' => true,
                                        'label' => 'Average',
                                    ],
                                ],
                            ],
                        ],
                        'labels' => ['Apples', 'Oranges', 'Bananas', 'Berries'],
                    ]"
                    height="280px"
                />
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="apex-radialbar-chart.blade.php" class="mt-4">
{{-- Single Radial Progress --}}
&lt;x-accelade::apex-chart
    type="radialBar"
    :series="[70]"
    :options="[
        'plotOptions' => [
            'radialBar' => [
                'hollow' => ['size' => '70%'],
                'dataLabels' => [
                    'value' => ['fontSize' => '32px'],
                ],
            ],
        ],
        'labels' => ['Progress'],
    ]"
/&gt;

{{-- Multiple Radials --}}
&lt;x-accelade::apex-chart
    type="radialBar"
    :series="[44, 55, 67, 83]"
    :options="[
        'labels' => ['Apples', 'Oranges', 'Bananas', 'Berries'],
        'plotOptions' => [
            'radialBar' => [
                'dataLabels' => [
                    'total' => ['show' => true, 'label' => 'Average'],
                ],
            ],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Heatmap Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Heatmap Chart</h3>
        <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Grid-based visualization using color intensity to represent values. Perfect for showing patterns in time-series data.
    </p>

    <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.05);">
        <x-accelade::apex-chart
            type="heatmap"
            :series="[
                ['name' => 'Mon', 'data' => [
                    ['x' => '00:00', 'y' => 22], ['x' => '04:00', 'y' => 12], ['x' => '08:00', 'y' => 45],
                    ['x' => '12:00', 'y' => 67], ['x' => '16:00', 'y' => 55], ['x' => '20:00', 'y' => 33],
                ]],
                ['name' => 'Tue', 'data' => [
                    ['x' => '00:00', 'y' => 18], ['x' => '04:00', 'y' => 10], ['x' => '08:00', 'y' => 52],
                    ['x' => '12:00', 'y' => 78], ['x' => '16:00', 'y' => 62], ['x' => '20:00', 'y' => 28],
                ]],
                ['name' => 'Wed', 'data' => [
                    ['x' => '00:00', 'y' => 25], ['x' => '04:00', 'y' => 8], ['x' => '08:00', 'y' => 58],
                    ['x' => '12:00', 'y' => 85], ['x' => '16:00', 'y' => 48], ['x' => '20:00', 'y' => 35],
                ]],
                ['name' => 'Thu', 'data' => [
                    ['x' => '00:00', 'y' => 15], ['x' => '04:00', 'y' => 5], ['x' => '08:00', 'y' => 42],
                    ['x' => '12:00', 'y' => 72], ['x' => '16:00', 'y' => 58], ['x' => '20:00', 'y' => 30],
                ]],
                ['name' => 'Fri', 'data' => [
                    ['x' => '00:00', 'y' => 28], ['x' => '04:00', 'y' => 15], ['x' => '08:00', 'y' => 48],
                    ['x' => '12:00', 'y' => 65], ['x' => '16:00', 'y' => 45], ['x' => '20:00', 'y' => 55],
                ]],
            ]"
            :options="[
                'colors' => ['#ef4444'],
                'dataLabels' => ['enabled' => false],
                'plotOptions' => [
                    'heatmap' => [
                        'radius' => 4,
                        'colorScale' => [
                            'ranges' => [
                                ['from' => 0, 'to' => 25, 'color' => '#fef2f2'],
                                ['from' => 26, 'to' => 50, 'color' => '#fca5a5'],
                                ['from' => 51, 'to' => 75, 'color' => '#f87171'],
                                ['from' => 76, 'to' => 100, 'color' => '#dc2626'],
                            ],
                        ],
                    ],
                ],
            ]"
            height="280px"
        />
    </div>

    <x-accelade::code-block language="blade" filename="apex-heatmap-chart.blade.php" class="mt-4">
{{-- Heatmap Chart --}}
&lt;x-accelade::apex-chart
    type="heatmap"
    :series="[
        ['name' => 'Mon', 'data' => [
            ['x' => '00:00', 'y' => 22],
            ['x' => '04:00', 'y' => 12],
            ['x' => '08:00', 'y' => 45],
        ]],
        ['name' => 'Tue', 'data' => [
            ['x' => '00:00', 'y' => 18],
            ['x' => '04:00', 'y' => 10],
            ['x' => '08:00', 'y' => 52],
        ]],
    ]"
    :options="[
        'colors' => ['#ef4444'],
        'plotOptions' => [
            'heatmap' => [
                'colorScale' => [
                    'ranges' => [
                        ['from' => 0, 'to' => 25, 'color' => '#fef2f2'],
                        ['from' => 26, 'to' => 50, 'color' => '#fca5a5'],
                        ['from' => 51, 'to' => 100, 'color' => '#dc2626'],
                    ],
                ],
            ],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

<!-- ApexCharts: Treemap Chart -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Treemap Chart</h3>
        <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">ApexCharts</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Hierarchical data displayed as nested rectangles. Size represents value, great for showing proportions in categories.
    </p>

    <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.05);">
        <x-accelade::apex-chart
            type="treemap"
            :series="[
                [
                    'data' => [
                        ['x' => 'New York', 'y' => 218],
                        ['x' => 'Los Angeles', 'y' => 149],
                        ['x' => 'Chicago', 'y' => 100],
                        ['x' => 'Houston', 'y' => 88],
                        ['x' => 'Phoenix', 'y' => 75],
                        ['x' => 'Philadelphia', 'y' => 65],
                        ['x' => 'San Antonio', 'y' => 55],
                        ['x' => 'San Diego', 'y' => 50],
                        ['x' => 'Dallas', 'y' => 48],
                        ['x' => 'San Jose', 'y' => 42],
                    ],
                ],
            ]"
            :options="[
                'colors' => ['#10b981'],
                'plotOptions' => [
                    'treemap' => [
                        'distributed' => true,
                        'enableShades' => true,
                    ],
                ],
                'dataLabels' => [
                    'enabled' => true,
                    'style' => ['fontSize' => '12px'],
                ],
            ]"
            height="320px"
        />
    </div>

    <x-accelade::code-block language="blade" filename="apex-treemap-chart.blade.php" class="mt-4">
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
        'plotOptions' => [
            'treemap' => ['distributed' => true, 'enableShades' => true],
        ],
    ]"
/&gt;
    </x-accelade::code-block>
</section>

{{-- ============================================ --}}
{{-- COMPONENT PROPS REFERENCE --}}
{{-- ============================================ --}}

<!-- Component Props Reference -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-slate-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Component Props Reference</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Complete reference for Chart.js and ApexCharts component properties.
    </p>

    <div class="space-y-6">
        <!-- Chart.js Props -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.05);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Chart.js</span>
                <code class="text-sm">&lt;x-accelade::chart&gt;</code>
            </h4>
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
                            <td class="py-2 px-3">Array of dataset objects with data, label, colors, etc.</td>
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
                            <td class="py-2 px-3">Enable reactive updates via Accelade state</td>
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

        <!-- ApexCharts Props -->
        <div class="rounded-xl p-4 border border-sky-500/30" style="background: rgba(14, 165, 233, 0.05);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-sky-500/20 text-sky-500 rounded">ApexCharts</span>
                <code class="text-sm">&lt;x-accelade::apex-chart&gt;</code>
            </h4>
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
                            <td class="py-2 px-3">Enable reactive updates via Accelade state</td>
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
