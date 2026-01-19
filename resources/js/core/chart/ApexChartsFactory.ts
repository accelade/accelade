/**
 * ApexCharts Factory for Accelade
 *
 * Handles ApexCharts integration with reactive updates and Accelade component lifecycle.
 */

import ApexChartsLib from 'apexcharts';
import type {
    ChartConfig,
    ChartData,
    ChartDataset,
    ChartInstance,
    ChartMethods,
    ChartOptions,
} from './types';

// Expose ApexCharts globally for potential external use
if (typeof window !== 'undefined') {
    (window as unknown as { ApexCharts: typeof ApexChartsLib }).ApexCharts = ApexChartsLib;
}

// ApexCharts types
interface ApexChartsOptions {
    chart?: {
        type?: string;
        height?: string | number;
        width?: string | number;
        id?: string;
        [key: string]: unknown;
    };
    series?: ApexChartsSeries[];
    xaxis?: {
        categories?: string[];
        [key: string]: unknown;
    };
    [key: string]: unknown;
}

interface ApexChartsSeries {
    name?: string;
    data: number[] | { x: string | number; y: number }[];
    [key: string]: unknown;
}

// Track initialized charts
const chartInstances = new Map<string, ChartInstance>();

/**
 * Convert Accelade ChartConfig to ApexCharts format
 */
function toApexOptions(config: ChartConfig): ApexChartsOptions {
    return {
        chart: {
            type: config.type,
            ...(config.options?.plugins?.title?.display && {
                toolbar: { show: true },
            }),
        },
        series: config.data.datasets.map((dataset) => ({
            name: dataset.label ?? '',
            data: dataset.data as number[],
            ...(dataset.backgroundColor && {
                color: Array.isArray(dataset.backgroundColor)
                    ? dataset.backgroundColor[0]
                    : dataset.backgroundColor,
            }),
        })),
        xaxis: {
            categories: config.data.labels,
        },
        ...config.options,
    };
}

/**
 * Initialize an ApexCharts chart from element
 */
export async function initApexCharts(element: HTMLElement): Promise<ChartInstance | null> {
    const chartId = element.dataset.chartId;
    if (!chartId) {
        console.error('Accelade Chart: Missing data-chart-id attribute');
        return null;
    }

    // Check if already initialized
    if (chartInstances.has(chartId)) {
        return chartInstances.get(chartId) ?? null;
    }

    // Get container element
    const container = element.querySelector<HTMLElement>(`#${chartId}`);
    if (!container) {
        console.error(`Accelade Chart: Container element #${chartId} not found`);
        return null;
    }

    // Parse config
    const configStr = element.dataset.chartConfig;
    if (!configStr) {
        console.error('Accelade Chart: Missing data-chart-config attribute');
        return null;
    }

    let apexConfig: ApexChartsOptions;
    try {
        apexConfig = JSON.parse(configStr);
    } catch (e) {
        console.error('Accelade Chart: Invalid chart config JSON', e);
        return null;
    }

    const reactive = element.dataset.chartReactive !== 'false';

    // Add chart ID to config
    apexConfig.chart = {
        ...apexConfig.chart,
        id: chartId,
    };

    // Create chart
    const apexInstance = new ApexChartsLib(container, apexConfig);
    apexInstance.render();

    // Store config in normalized format
    const config: ChartConfig = {
        type: (apexConfig.chart?.type ?? 'line') as ChartConfig['type'],
        data: {
            labels: apexConfig.xaxis?.categories ?? [],
            datasets: (apexConfig.series ?? []).map((s) => ({
                label: s.name,
                data: s.data as number[],
            })),
        },
        options: apexConfig as ChartOptions,
    };

    // Create instance wrapper
    const instance: ChartInstance = {
        id: chartId,
        element,
        canvas: container as unknown as HTMLCanvasElement, // ApexCharts uses div
        chart: apexInstance,
        library: 'apexcharts',
        config,
        reactive,

        update(newConfig?: Partial<ChartConfig>): void {
            if (newConfig) {
                const apexOptions: ApexChartsOptions = {};
                if (newConfig.data) {
                    apexOptions.series = newConfig.data.datasets.map((d) => ({
                        name: d.label,
                        data: d.data as number[],
                    }));
                    apexOptions.xaxis = { categories: newConfig.data.labels };
                }
                if (newConfig.options) {
                    Object.assign(apexOptions, newConfig.options);
                }
                apexInstance.updateOptions(apexOptions, true, true);
            }
            dispatchChartEvent(element, 'update', { id: chartId });
        },

        updateData(data: ChartData): void {
            const series = data.datasets.map((d) => ({
                name: d.label ?? '',
                data: d.data as number[],
            }));
            apexInstance.updateSeries(series);
            apexInstance.updateOptions({ xaxis: { categories: data.labels } });
            dispatchChartEvent(element, 'dataUpdate', { id: chartId, data });
        },

        updateOptions(options: ChartOptions): void {
            apexInstance.updateOptions(options as ApexChartsOptions);
            dispatchChartEvent(element, 'optionsUpdate', { id: chartId, options });
        },

        addDataset(dataset: ChartDataset): void {
            apexInstance.appendSeries([{
                name: dataset.label ?? '',
                data: dataset.data as number[],
            }]);
            dispatchChartEvent(element, 'datasetAdd', { id: chartId, dataset });
        },

        removeDataset(index: number): void {
            // ApexCharts doesn't have direct remove, need to update all series
            const currentSeries = (apexConfig.series ?? []).filter((_, i) => i !== index);
            apexInstance.updateSeries(currentSeries);
            dispatchChartEvent(element, 'datasetRemove', { id: chartId, index });
        },

        setLabels(labels: string[]): void {
            apexInstance.updateOptions({
                xaxis: { categories: labels },
            });
            dispatchChartEvent(element, 'labelsUpdate', { id: chartId, labels });
        },

        addData(label: string, data: number[]): void {
            const currentCategories = apexConfig.xaxis?.categories ?? [];
            currentCategories.push(label);

            const series = (apexConfig.series ?? []).map((s, i) => ({
                ...s,
                data: [...(s.data as number[]), data[i] ?? 0],
            }));

            apexInstance.updateOptions({ xaxis: { categories: currentCategories } });
            apexInstance.updateSeries(series);
            dispatchChartEvent(element, 'dataAdd', { id: chartId, label, data });
        },

        removeData(index: number): void {
            const currentCategories = (apexConfig.xaxis?.categories ?? []).filter(
                (_, i) => i !== index
            );

            const series = (apexConfig.series ?? []).map((s) => ({
                ...s,
                data: (s.data as number[]).filter((_, i) => i !== index),
            }));

            apexInstance.updateOptions({ xaxis: { categories: currentCategories } });
            apexInstance.updateSeries(series);
            dispatchChartEvent(element, 'dataRemove', { id: chartId, index });
        },

        destroy(): void {
            apexInstance.destroy();
            chartInstances.delete(chartId);
            dispatchChartEvent(element, 'destroy', { id: chartId });
        },

        resize(): void {
            // ApexCharts auto-resizes, but we can trigger update
            apexInstance.updateOptions({});
        },

        toBase64Image(): string {
            // ApexCharts returns a promise, but we need sync
            // Return empty for now - use getChart().dataURI() for async
            return '';
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
export function createApexChartsMethods(instance: ChartInstance): ChartMethods {
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
export function getApexChartInstance(id: string): ChartInstance | undefined {
    return chartInstances.get(id);
}

/**
 * Get all chart instances
 */
export function getAllApexChartInstances(): Map<string, ChartInstance> {
    return new Map(chartInstances);
}

/**
 * Initialize all ApexCharts on the page
 */
export async function initAllApexCharts(): Promise<void> {
    const elements = document.querySelectorAll<HTMLElement>(
        '[data-accelade-chart][data-chart-library="apexcharts"]:not(.accelade-chart-ready)'
    );

    await Promise.all(Array.from(elements).map(initApexCharts));
}

/**
 * Destroy a chart by ID
 */
export function destroyApexChart(id: string): void {
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

export { ApexChartsFactory };

/**
 * ApexCharts Factory class
 */
class ApexChartsFactory {
    static init = initApexCharts;
    static initAll = initAllApexCharts;
    static get = getApexChartInstance;
    static getAll = getAllApexChartInstances;
    static destroy = destroyApexChart;
    static createMethods = createApexChartsMethods;
}
