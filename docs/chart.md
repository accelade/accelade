# Chart Components

Accelade provides two powerful chart components for data visualization: one using Chart.js and another using ApexCharts. Both integrate seamlessly with the Accelade ecosystem and support reactive updates.

## Installation

Chart.js and ApexCharts are bundled with Accelade - no additional installation required. The libraries are automatically loaded when you use the chart components.

## Chart.js Component

The `<x-accelade::chart>` component wraps Chart.js, providing a simple Blade interface for creating beautiful charts.

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | 'line' | Chart type: line, bar, pie, doughnut, radar, polarArea, bubble, scatter |
| `labels` | array | [] | X-axis labels (categories) |
| `datasets` | array | [] | Array of dataset objects with data, label, colors, etc. |
| `options` | array | [] | Chart.js configuration options |
| `height` | string | '400px' | Chart container height |
| `width` | string | '100%' | Chart container width |
| `reactive` | bool | true | Enable reactive updates via Accelade state |
| `id` | string | auto | Custom chart ID (auto-generated if not set) |

---

## Chart.js: Line Chart

Display trends over time with smooth or stepped lines. Ideal for showing continuous data like sales, traffic, or temperature changes.

### Basic Line Chart

```blade
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
    height="300px"
/>
```

### Multi-Series with Area Fill

```blade
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
/>
```

### Stepped Line Chart

```blade
<x-accelade::chart
    type="line"
    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri']"
    :datasets="[
        [
            'label' => 'Active Users',
            'data' => [120, 190, 130, 150, 180],
            'borderColor' => 'rgb(236, 72, 153)',
            'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
            'fill' => true,
            'stepped' => true,
        ],
    ]"
/>
```

---

## Chart.js: Bar Chart

Compare discrete categories with vertical or horizontal bars. Perfect for showing comparisons between groups.

### Vertical Bar Chart

```blade
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
        ],
    ]"
    :options="[
        'plugins' => ['legend' => ['display' => false]],
        'scales' => ['y' => ['beginAtZero' => true]],
    ]"
/>
```

### Grouped Bar Chart

```blade
<x-accelade::chart
    type="bar"
    :labels="['Jan', 'Feb', 'Mar', 'Apr']"
    :datasets="[
        ['label' => 'Desktop', 'data' => [65, 59, 80, 81], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
        ['label' => 'Mobile', 'data' => [45, 52, 60, 65], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
        ['label' => 'Tablet', 'data' => [25, 32, 40, 35], 'backgroundColor' => 'rgba(245, 158, 11, 0.8)'],
    ]"
/>
```

### Stacked Bar Chart

```blade
<x-accelade::chart
    type="bar"
    :labels="['Q1', 'Q2', 'Q3', 'Q4']"
    :datasets="[
        ['label' => 'Product A', 'data' => [50, 60, 70, 80], 'backgroundColor' => 'rgba(99, 102, 241, 0.8)'],
        ['label' => 'Product B', 'data' => [30, 40, 35, 45], 'backgroundColor' => 'rgba(34, 197, 94, 0.8)'],
    ]"
    :options="[
        'scales' => [
            'x' => ['stacked' => true],
            'y' => ['stacked' => true, 'beginAtZero' => true],
        ],
    ]"
/>
```

---

## Chart.js: Doughnut & Pie Chart

Show proportions and percentages with circular charts. Ideal for displaying parts of a whole.

### Doughnut Chart

```blade
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
    :options="['cutout' => '60%']"
/>
```

### Pie Chart

```blade
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
/>
```

---

## Chart.js: Radar Chart

Compare multiple variables on a radial grid. Great for skill assessments, performance metrics, or multi-dimensional data.

```blade
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
            'r' => ['beginAtZero' => true, 'max' => 100],
        ],
    ]"
/>
```

---

## Chart.js: Polar Area Chart

Similar to pie charts but with equal angles, showing magnitude through radius.

```blade
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
/>
```

---

## Chart.js: Scatter Chart

Plot individual data points on X/Y axes. Perfect for showing correlation, distribution, or clustering patterns.

```blade
<x-accelade::chart
    type="scatter"
    :datasets="[
        [
            'label' => 'Dataset A',
            'data' => [
                ['x' => -10, 'y' => 0],
                ['x' => 0, 'y' => 10],
                ['x' => 10, 'y' => 5],
                ['x' => 5, 'y' => -5],
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
/>
```

---

## Chart.js: Bubble Chart

Like scatter charts but with a third dimension shown through bubble size.

```blade
<x-accelade::chart
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
            'borderColor' => 'rgb(139, 92, 246)',
        ],
    ]"
/>
```

---

## ApexCharts Component

The `<x-accelade::apex-chart>` component wraps ApexCharts, offering more advanced features and chart types.

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | 'line' | Chart type: line, area, bar, radar, pie, donut, radialBar, heatmap, treemap |
| `series` | array | [] | Array of series with name and data |
| `categories` | array | [] | X-axis categories/labels |
| `options` | array | [] | ApexCharts configuration options |
| `height` | string | '400px' | Chart container height |
| `width` | string | '100%' | Chart container width |
| `reactive` | bool | true | Enable reactive updates via Accelade state |
| `id` | string | auto | Custom chart ID (auto-generated if not set) |

---

## ApexCharts: Area Chart

Smooth area charts with gradient fills. Perfect for visualizing trends with emphasis on volume.

### Gradient Area Chart

```blade
<x-accelade::apex-chart
    type="area"
    :series="[
        ['name' => 'Visitors', 'data' => [31, 40, 28, 51, 42, 109, 100]],
        ['name' => 'Page Views', 'data' => [11, 32, 45, 32, 34, 52, 41]],
    ]"
    :categories="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
    :options="[
        'colors' => ['#6366f1', '#22c55e'],
        'fill' => [
            'type' => 'gradient',
            'gradient' => ['opacityFrom' => 0.5, 'opacityTo' => 0.1],
        ],
    ]"
/>
```

### Stacked Area Chart

```blade
<x-accelade::apex-chart
    type="area"
    :series="[
        ['name' => 'South', 'data' => [31, 40, 28, 51, 42]],
        ['name' => 'North', 'data' => [11, 32, 45, 32, 34]],
        ['name' => 'Central', 'data' => [15, 22, 35, 25, 28]],
    ]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :options="[
        'colors' => ['#6366f1', '#22c55e', '#f59e0b'],
        'chart' => ['stacked' => true],
        'fill' => ['opacity' => 0.6],
    ]"
/>
```

---

## ApexCharts: Line Chart

Clean line charts with interactive features like zoom, pan, and data point tooltips.

### Multi-Series Line

```blade
<x-accelade::apex-chart
    type="line"
    :series="[
        ['name' => 'Desktop', 'data' => [45, 52, 38, 45, 19, 23, 30]],
        ['name' => 'Mobile', 'data' => [35, 41, 62, 42, 13, 18, 29]],
    ]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul']"
    :options="[
        'colors' => ['#3b82f6', '#22c55e'],
        'stroke' => ['width' => 3, 'curve' => 'smooth'],
        'markers' => ['size' => 5],
    ]"
/>
```

### Dashed Lines

```blade
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
/>
```

---

## ApexCharts: Bar Chart

Vertical and horizontal bar charts with rounded corners and customizable styling.

### Horizontal Bar

```blade
<x-accelade::apex-chart
    type="bar"
    :series="[['name' => 'Sales', 'data' => [400, 430, 448, 470, 540]]]"
    :categories="['Product A', 'Product B', 'Product C', 'Product D', 'Product E']"
    :options="[
        'colors' => ['#f59e0b'],
        'plotOptions' => [
            'bar' => ['horizontal' => true, 'borderRadius' => 4],
        ],
    ]"
/>
```

### Grouped Column

```blade
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
        'plotOptions' => ['bar' => ['borderRadius' => 4]],
    ]"
/>
```

### Stacked Column

```blade
<x-accelade::apex-chart
    type="bar"
    :series="[
        ['name' => 'Product A', 'data' => [44, 55, 41, 67, 22]],
        ['name' => 'Product B', 'data' => [13, 23, 20, 8, 13]],
    ]"
    :categories="['Jan', 'Feb', 'Mar', 'Apr', 'May']"
    :options="[
        'colors' => ['#6366f1', '#22c55e'],
        'chart' => ['stacked' => true],
        'plotOptions' => ['bar' => ['borderRadius' => 4]],
    ]"
/>
```

---

## ApexCharts: Radar Chart

Multi-axis chart for comparing multiple variables. Great for skill assessments.

```blade
<x-accelade::apex-chart
    type="radar"
    :series="[
        ['name' => 'Team A', 'data' => [80, 50, 30, 40, 100, 20]],
        ['name' => 'Team B', 'data' => [20, 30, 40, 80, 20, 80]],
    ]"
    :categories="['Frontend', 'Backend', 'DevOps', 'Design', 'Testing', 'Analytics']"
    :options="[
        'colors' => ['#ec4899', '#6366f1'],
        'stroke' => ['width' => 2],
        'fill' => ['opacity' => 0.2],
        'markers' => ['size' => 4],
    ]"
/>
```

---

## ApexCharts: Donut Chart

Circular chart with center cutout. Shows parts of a whole with optional center labels.

### Basic Donut

```blade
<x-accelade::apex-chart
    type="donut"
    :series="[44, 55, 13, 33]"
    :options="[
        'labels' => ['Marketing', 'Development', 'Operations', 'Support'],
        'colors' => ['#14b8a6', '#6366f1', '#f59e0b', '#ec4899'],
    ]"
/>
```

### Donut with Center Total

```blade
<x-accelade::apex-chart
    type="donut"
    :series="[44, 55, 41, 17]"
    :options="[
        'labels' => ['Active', 'Pending', 'Review', 'Blocked'],
        'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ef4444'],
        'plotOptions' => [
            'pie' => [
                'donut' => [
                    'size' => '65%',
                    'labels' => [
                        'show' => true,
                        'total' => ['show' => true, 'showAlways' => true, 'label' => 'Total Tasks'],
                    ],
                ],
            ],
        ],
    ]"
/>
```

---

## ApexCharts: Pie Chart

Classic circular chart showing proportions.

```blade
<x-accelade::apex-chart
    type="pie"
    :series="[44, 55, 13, 43, 22]"
    :options="[
        'labels' => ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
        'colors' => ['#84cc16', '#22c55e', '#14b8a6', '#06b6d4', '#0ea5e9'],
        'legend' => ['position' => 'bottom'],
    ]"
/>
```

---

## ApexCharts: Radial Bar Chart

Circular progress indicators showing completion percentages.

### Single Progress Indicator

```blade
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
/>
```

### Multiple Metrics

```blade
<x-accelade::apex-chart
    type="radialBar"
    :series="[44, 55, 67, 83]"
    :options="[
        'colors' => ['#6366f1', '#22c55e', '#f59e0b', '#ec4899'],
        'labels' => ['Apples', 'Oranges', 'Bananas', 'Berries'],
        'plotOptions' => [
            'radialBar' => [
                'dataLabels' => [
                    'total' => ['show' => true, 'label' => 'Average'],
                ],
            ],
        ],
    ]"
/>
```

---

## ApexCharts: Heatmap Chart

Grid-based visualization using color intensity to represent values.

```blade
<x-accelade::apex-chart
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
/>
```

---

## ApexCharts: Treemap Chart

Hierarchical data displayed as nested rectangles. Size represents value.

```blade
<x-accelade::apex-chart
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
/>
```

---

## JavaScript API

Both chart components expose a JavaScript API for programmatic control.

### Getting Chart Instances

```javascript
// Get any chart by ID
const chart = Accelade.chart.get('my-chart-id');

// Get Chart.js specific instance
const chartjs = Accelade.chart.chartjs.get('chartjs-id');

// Get ApexCharts specific instance
const apex = Accelade.chart.apexcharts.get('apex-id');

// Initialize all charts
Accelade.chart.initAll();
```

### Updating Charts

```javascript
const chart = Accelade.chart.get('my-chart');

// Update entire data
chart.updateData({
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [
        { label: 'Sales', data: [10, 20, 30] },
    ],
});

// Update only options
chart.updateOptions({
    plugins: { legend: { display: false } },
});

// Full update
chart.update({
    data: { labels: ['A', 'B'], datasets: [{ data: [1, 2] }] },
    options: { responsive: true },
});
```

### Managing Datasets

```javascript
// Add a new dataset
chart.addDataset({
    label: 'New Series',
    data: [5, 10, 15, 20],
    borderColor: '#22c55e',
});

// Remove dataset by index
chart.removeDataset(1);

// Update labels
chart.setLabels(['Q1', 'Q2', 'Q3', 'Q4']);
```

### Adding/Removing Data Points

```javascript
// Add a new data point to all datasets
chart.addData('Jun', [25, 30]); // label, data for each dataset

// Remove data point by index
chart.removeData(0); // removes first data point
```

### Other Methods

```javascript
// Resize chart
chart.resize();

// Export as base64 image
const imageUrl = chart.toBase64Image();

// Get underlying chart instance
const nativeChart = chart.getChart();

// Destroy chart
chart.destroy();
```

## Events

Charts emit events during their lifecycle:

```javascript
// Listen for chart initialization
document.addEventListener('accelade:chart:init', (e) => {
    console.log('Chart initialized:', e.detail.id);
});

// Listen for data updates
document.addEventListener('accelade:chart:dataUpdate', (e) => {
    console.log('Data updated:', e.detail);
});

// Listen on specific element
element.addEventListener('accelade:chart:update', (e) => {
    console.log('Chart updated');
});
```

Available events:
- `accelade:chart:init` - Chart initialized
- `accelade:chart:update` - Chart updated
- `accelade:chart:dataUpdate` - Data changed
- `accelade:chart:optionsUpdate` - Options changed
- `accelade:chart:datasetAdd` - Dataset added
- `accelade:chart:datasetRemove` - Dataset removed
- `accelade:chart:labelsUpdate` - Labels changed
- `accelade:chart:dataAdd` - Data point added
- `accelade:chart:dataRemove` - Data point removed
- `accelade:chart:destroy` - Chart destroyed

## Dark Mode Support

Accelade automatically styles ApexCharts elements for dark mode. The following elements are styled:
- Export/toolbar menus
- Tooltips
- Axis labels
- Grid lines
- Legend text
- Radar chart polygons

Dark mode is detected via `.dark` class or `[data-theme="dark"]` attribute on parent elements.

## Best Practices

1. **Choose the right library**: Use Chart.js for simpler charts, ApexCharts for advanced features
2. **Set appropriate heights**: Always specify height for proper rendering
3. **Use consistent colors**: Maintain visual consistency across charts
4. **Lazy load charts**: Use `reactive="false"` for charts that don't need updates
5. **Clean up**: Destroy charts when removing from DOM to prevent memory leaks
6. **Consider dark mode**: Test charts in both light and dark themes

## Next Steps

- [Toggle Component](toggle.md) - Boolean state management
- [Data Component](data.md) - Complex data binding
- [Notifications](notifications.md) - Toast notifications
