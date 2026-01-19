/**
 * Chart Module Index
 *
 * Exports all chart-related functionality for Accelade.
 */

// Types
export type {
    ChartType,
    ChartDataset,
    ChartData,
    ChartOptions,
    ChartConfig,
    ChartInstance,
    ChartMethods,
    ChartEventDetail,
} from './types';

export { CHART_COLORS, CHART_PALETTE, withAlpha } from './types';

// Chart.js Factory
export {
    ChartJsFactory,
    initChartJs,
    initAllChartJs,
    getChartInstance as getChartJsInstance,
    getAllChartInstances as getAllChartJsInstances,
    destroyChart as destroyChartJs,
    createChartJsMethods,
} from './ChartJsFactory';

// ApexCharts Factory
export {
    ApexChartsFactory,
    initApexCharts,
    initAllApexCharts,
    getApexChartInstance,
    getAllApexChartInstances,
    destroyApexChart,
    createApexChartsMethods,
} from './ApexChartsFactory';

// Unified chart interface
import { initAllChartJs, getChartInstance } from './ChartJsFactory';
import { initAllApexCharts, getApexChartInstance } from './ApexChartsFactory';
import type { ChartInstance } from './types';

/**
 * Initialize all charts on the page (both Chart.js and ApexCharts)
 */
export async function initAllCharts(): Promise<void> {
    await Promise.all([initAllChartJs(), initAllApexCharts()]);
}

/**
 * Get a chart instance by ID (from either library)
 */
export function getChart(id: string): ChartInstance | undefined {
    return getChartInstance(id) ?? getApexChartInstance(id);
}

/**
 * Unified Chart Factory
 */
export const ChartFactory = {
    initAll: initAllCharts,
    get: getChart,
    chartjs: {
        init: initAllChartJs,
        get: getChartInstance,
    },
    apexcharts: {
        init: initAllApexCharts,
        get: getApexChartInstance,
    },
};
