/**
 * Chart.js Factory for Accelade
 *
 * Handles Chart.js integration with reactive updates and Accelade component lifecycle.
 */

import { Chart, registerables } from 'chart.js';
import type {
    ChartConfig,
    ChartData,
    ChartDataset,
    ChartInstance,
    ChartMethods,
    ChartOptions,
} from './types';

// Register all Chart.js components
Chart.register(...registerables);

// Expose Chart.js globally for potential external use
if (typeof window !== 'undefined') {
    (window as unknown as { Chart: typeof Chart }).Chart = Chart;
}

// Chart.js types (using the actual Chart.js types now)
type ChartJsInstance = InstanceType<typeof Chart>;
type ChartJsConstructor = typeof Chart;

// Track initialized charts
const chartInstances = new Map<string, ChartInstance>();

/**
 * Get the Chart.js constructor
 */
function getChartJs(): ChartJsConstructor {
    return Chart;
}

/**
 * Initialize a Chart.js chart from element
 */
export async function initChartJs(element: HTMLElement): Promise<ChartInstance | null> {
    const chartId = element.dataset.chartId;
    if (!chartId) {
        console.error('Accelade Chart: Missing data-chart-id attribute');
        return null;
    }

    // Check if already initialized
    if (chartInstances.has(chartId)) {
        return chartInstances.get(chartId) ?? null;
    }

    // Get canvas element
    const canvas = element.querySelector<HTMLCanvasElement>(`#${chartId}`);
    if (!canvas) {
        console.error(`Accelade Chart: Canvas element #${chartId} not found`);
        return null;
    }

    // Parse config
    const configStr = element.dataset.chartConfig;
    if (!configStr) {
        console.error('Accelade Chart: Missing data-chart-config attribute');
        return null;
    }

    let config: ChartConfig;
    try {
        config = JSON.parse(configStr);
    } catch (e) {
        console.error('Accelade Chart: Invalid chart config JSON', e);
        return null;
    }

    const reactive = element.dataset.chartReactive !== 'false';

    // Get canvas context
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Accelade Chart: Could not get canvas context');
        return null;
    }

    // Create chart (cast config to Chart.js expected type)
    const chartJsInstance = new Chart(ctx, config as ConstructorParameters<typeof Chart>[1]);

    // Create instance wrapper
    const instance: ChartInstance = {
        id: chartId,
        element,
        canvas,
        chart: chartJsInstance,
        library: 'chartjs',
        config,
        reactive,

        update(newConfig?: Partial<ChartConfig>): void {
            if (newConfig) {
                if (newConfig.data) {
                    chartJsInstance.data.labels = newConfig.data.labels as unknown[];
                    chartJsInstance.data.datasets = newConfig.data.datasets as typeof chartJsInstance.data.datasets;
                }
                if (newConfig.options) {
                    Object.assign(chartJsInstance.options, newConfig.options);
                }
            }
            chartJsInstance.update();
            dispatchChartEvent(element, 'update', { id: chartId });
        },

        updateData(data: ChartData): void {
            chartJsInstance.data.labels = data.labels as unknown[];
            chartJsInstance.data.datasets = data.datasets as typeof chartJsInstance.data.datasets;
            chartJsInstance.update();
            dispatchChartEvent(element, 'dataUpdate', { id: chartId, data });
        },

        updateOptions(options: ChartOptions): void {
            Object.assign(chartJsInstance.options, options);
            chartJsInstance.update();
            dispatchChartEvent(element, 'optionsUpdate', { id: chartId, options });
        },

        addDataset(dataset: ChartDataset): void {
            chartJsInstance.data.datasets.push(dataset as typeof chartJsInstance.data.datasets[0]);
            chartJsInstance.update();
            dispatchChartEvent(element, 'datasetAdd', { id: chartId, dataset });
        },

        removeDataset(index: number): void {
            chartJsInstance.data.datasets.splice(index, 1);
            chartJsInstance.update();
            dispatchChartEvent(element, 'datasetRemove', { id: chartId, index });
        },

        setLabels(labels: string[]): void {
            chartJsInstance.data.labels = labels as unknown[];
            chartJsInstance.update();
            dispatchChartEvent(element, 'labelsUpdate', { id: chartId, labels });
        },

        addData(label: string, data: number[]): void {
            (chartJsInstance.data.labels as string[]).push(label);
            chartJsInstance.data.datasets.forEach((dataset, i) => {
                (dataset.data as number[]).push(data[i] ?? 0);
            });
            chartJsInstance.update();
            dispatchChartEvent(element, 'dataAdd', { id: chartId, label, data });
        },

        removeData(index: number): void {
            (chartJsInstance.data.labels as string[]).splice(index, 1);
            chartJsInstance.data.datasets.forEach((dataset) => {
                (dataset.data as number[]).splice(index, 1);
            });
            chartJsInstance.update();
            dispatchChartEvent(element, 'dataRemove', { id: chartId, index });
        },

        destroy(): void {
            chartJsInstance.destroy();
            chartInstances.delete(chartId);
            dispatchChartEvent(element, 'destroy', { id: chartId });
        },

        resize(): void {
            chartJsInstance.resize();
        },

        toBase64Image(): string {
            return chartJsInstance.toBase64Image();
        },
    };

    chartInstances.set(chartId, instance);

    // Mark element as initialized
    element.classList.add('accelade-chart-ready');
    element.removeAttribute('data-accelade-cloak');

    dispatchChartEvent(element, 'init', { id: chartId });

    return instance;
}

/**
 * Create chart methods for Accelade component integration
 */
export function createChartJsMethods(instance: ChartInstance): ChartMethods {
    return {
        update: instance.update.bind(instance),
        updateData: instance.updateData.bind(instance),
        updateOptions: instance.updateOptions.bind(instance),
        addDataset: instance.addDataset.bind(instance),
        removeDataset: instance.removeDataset.bind(instance),
        setLabels: instance.setLabels.bind(instance),
        addData: instance.addData.bind(instance),
        removeData: instance.removeData.bind(instance),
        destroy: instance.destroy.bind(instance),
        resize: instance.resize.bind(instance),
        toBase64Image: instance.toBase64Image.bind(instance),
        getChart: () => instance.chart,
    };
}

/**
 * Get a chart instance by ID
 */
export function getChartInstance(id: string): ChartInstance | undefined {
    return chartInstances.get(id);
}

/**
 * Get all chart instances
 */
export function getAllChartInstances(): Map<string, ChartInstance> {
    return new Map(chartInstances);
}

/**
 * Initialize all Chart.js charts on the page
 */
export async function initAllChartJs(): Promise<void> {
    const elements = document.querySelectorAll<HTMLElement>(
        '[data-accelade-chart][data-chart-library="chartjs"]:not(.accelade-chart-ready)'
    );

    await Promise.all(Array.from(elements).map(initChartJs));
}

/**
 * Destroy a chart by ID
 */
export function destroyChart(id: string): void {
    const instance = chartInstances.get(id);
    if (instance) {
        instance.destroy();
    }
}

/**
 * Dispatch a chart event
 */
function dispatchChartEvent(
    element: HTMLElement,
    type: string,
    detail: Record<string, unknown>
): void {
    element.dispatchEvent(
        new CustomEvent(`accelade:chart:${type}`, {
            bubbles: true,
            detail,
        })
    );

    document.dispatchEvent(
        new CustomEvent(`accelade:chart:${type}`, {
            bubbles: true,
            detail,
        })
    );
}

export { ChartJsFactory };

/**
 * Chart.js Factory class
 */
class ChartJsFactory {
    static init = initChartJs;
    static initAll = initAllChartJs;
    static get = getChartInstance;
    static getAll = getAllChartInstances;
    static destroy = destroyChart;
    static createMethods = createChartJsMethods;
}
