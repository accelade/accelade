/**
 * Chart types supported by the chart components
 */
export type ChartType =
    | 'line'
    | 'bar'
    | 'pie'
    | 'doughnut'
    | 'radar'
    | 'polarArea'
    | 'bubble'
    | 'scatter';

/**
 * Chart dataset configuration
 */
export interface ChartDataset {
    label?: string;
    data: number[] | { x: number; y: number; r?: number }[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    borderWidth?: number;
    fill?: boolean | string;
    tension?: number;
    pointRadius?: number;
    pointHoverRadius?: number;
    pointBackgroundColor?: string | string[];
    pointBorderColor?: string | string[];
    hoverBackgroundColor?: string | string[];
    hoverBorderColor?: string | string[];
    stack?: string;
    order?: number;
    hidden?: boolean;
    [key: string]: unknown;
}

/**
 * Chart data configuration
 */
export interface ChartData {
    labels: string[];
    datasets: ChartDataset[];
}

/**
 * Chart options configuration
 */
export interface ChartOptions {
    responsive?: boolean;
    maintainAspectRatio?: boolean;
    aspectRatio?: number;
    animation?: {
        duration?: number;
        easing?: string;
        [key: string]: unknown;
    };
    plugins?: {
        legend?: {
            display?: boolean;
            position?: 'top' | 'bottom' | 'left' | 'right';
            labels?: {
                color?: string;
                font?: { size?: number; family?: string };
                [key: string]: unknown;
            };
            [key: string]: unknown;
        };
        title?: {
            display?: boolean;
            text?: string;
            color?: string;
            font?: { size?: number; weight?: string };
            [key: string]: unknown;
        };
        tooltip?: {
            enabled?: boolean;
            mode?: string;
            intersect?: boolean;
            [key: string]: unknown;
        };
        [key: string]: unknown;
    };
    scales?: {
        x?: {
            display?: boolean;
            title?: { display?: boolean; text?: string };
            grid?: { display?: boolean; color?: string };
            ticks?: { color?: string };
            stacked?: boolean;
            [key: string]: unknown;
        };
        y?: {
            display?: boolean;
            title?: { display?: boolean; text?: string };
            grid?: { display?: boolean; color?: string };
            ticks?: { color?: string };
            stacked?: boolean;
            beginAtZero?: boolean;
            [key: string]: unknown;
        };
        [key: string]: unknown;
    };
    interaction?: {
        mode?: string;
        intersect?: boolean;
        [key: string]: unknown;
    };
    onClick?: (event: unknown, elements: unknown[], chart: unknown) => void;
    onHover?: (event: unknown, elements: unknown[], chart: unknown) => void;
    [key: string]: unknown;
}

/**
 * Chart configuration
 */
export interface ChartConfig {
    type: ChartType;
    data: ChartData;
    options?: ChartOptions;
}

/**
 * Chart instance interface
 */
export interface ChartInstance {
    id: string;
    element: HTMLElement;
    canvas: HTMLCanvasElement;
    chart: unknown; // Chart.js or ApexCharts instance
    library: 'chartjs' | 'apexcharts';
    config: ChartConfig;
    reactive: boolean;
    update: (config?: Partial<ChartConfig>) => void;
    updateData: (data: ChartData) => void;
    updateOptions: (options: ChartOptions) => void;
    addDataset: (dataset: ChartDataset) => void;
    removeDataset: (index: number) => void;
    setLabels: (labels: string[]) => void;
    addData: (label: string, data: number[]) => void;
    removeData: (index: number) => void;
    destroy: () => void;
    resize: () => void;
    toBase64Image: () => string;
}

/**
 * Chart methods exposed to Accelade components
 */
export interface ChartMethods {
    update: (config?: Partial<ChartConfig>) => void;
    updateData: (data: ChartData) => void;
    updateOptions: (options: ChartOptions) => void;
    addDataset: (dataset: ChartDataset) => void;
    removeDataset: (index: number) => void;
    setLabels: (labels: string[]) => void;
    addData: (label: string, data: number[]) => void;
    removeData: (index: number) => void;
    destroy: () => void;
    resize: () => void;
    toBase64Image: () => string;
    getChart: () => unknown;
}

/**
 * Chart event detail
 */
export interface ChartEventDetail {
    id: string;
    type: string;
    data?: unknown;
}

/**
 * Chart color presets
 */
export const CHART_COLORS = {
    primary: 'rgb(99, 102, 241)',
    secondary: 'rgb(107, 114, 128)',
    success: 'rgb(34, 197, 94)',
    danger: 'rgb(239, 68, 68)',
    warning: 'rgb(245, 158, 11)',
    info: 'rgb(14, 165, 233)',
    purple: 'rgb(168, 85, 247)',
    pink: 'rgb(236, 72, 153)',
    teal: 'rgb(20, 184, 166)',
    orange: 'rgb(249, 115, 22)',
} as const;

/**
 * Chart color palette for multiple datasets
 */
export const CHART_PALETTE = [
    CHART_COLORS.primary,
    CHART_COLORS.success,
    CHART_COLORS.warning,
    CHART_COLORS.danger,
    CHART_COLORS.info,
    CHART_COLORS.purple,
    CHART_COLORS.pink,
    CHART_COLORS.teal,
    CHART_COLORS.orange,
    CHART_COLORS.secondary,
];

/**
 * Create a transparent version of a color
 */
export function withAlpha(color: string, alpha: number): string {
    // Handle rgb format
    if (color.startsWith('rgb(')) {
        return color.replace('rgb(', 'rgba(').replace(')', `, ${alpha})`);
    }
    // Handle rgba format
    if (color.startsWith('rgba(')) {
        return color.replace(/,\s*[\d.]+\)$/, `, ${alpha})`);
    }
    // Handle hex format
    if (color.startsWith('#')) {
        const hex = color.slice(1);
        const r = parseInt(hex.slice(0, 2), 16);
        const g = parseInt(hex.slice(2, 4), 16);
        const b = parseInt(hex.slice(4, 6), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
    return color;
}
