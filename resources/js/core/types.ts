/**
 * Accelade Type Definitions
 */

// Import router types
import type { AcceladeRouter, NavigationOptions } from './router';
import type { ProgressConfig, AcceladeProgress } from './progress';

// Global window extension
declare global {
    interface Window {
        AcceladeConfig?: AcceladeConfig;
        Accelade?: AcceladeInstance;
    }
}

// Re-export router types
export type { NavigationOptions };

/**
 * Progress bar API
 */
export interface AcceladeProgressAPI {
    configure: (config: ProgressConfig) => void;
    start: () => void;
    done: (force?: boolean) => void;
    instance: (config?: ProgressConfig) => AcceladeProgress;
}

/**
 * Framework type
 */
export type FrameworkType = 'vanilla' | 'vue' | 'react' | 'svelte' | 'angular';

/**
 * Shared data type
 */
export type SharedData = Record<string, unknown>;

/**
 * Accelade configuration from server
 */
export interface AcceladeConfig {
    framework?: FrameworkType;
    syncDebounce?: number;
    csrfToken?: string;
    updateUrl?: string;
    batchUpdateUrl?: string;
    progress?: Partial<ProgressConfig>;
    debug?: boolean;
    shared?: SharedData;
}

/**
 * Accelade global instance
 */
export interface AcceladeInstance {
    init: (options?: { framework?: FrameworkType; debug?: boolean }) => void;
    navigate?: (url: string, options?: NavigationOptions) => Promise<boolean>;
    router?: AcceladeRouter;
    progress?: AcceladeProgressAPI;
    debug?: boolean;
    devtools?: unknown;
    getFramework?: () => FrameworkType;
    getComponent?: (id: string) => unknown;
    getComponents?: () => Map<string, unknown>;
    [key: string]: unknown;
}

/**
 * Parsed component configuration
 */
export interface AcceladeComponentConfig {
    id: string;
    state: Record<string, unknown>;
    sync: string[];
    props?: Record<string, unknown>;
    rememberKey?: string;
    localStorageKey?: string;
    storeName?: string;
}

/**
 * State change callback
 */
export type StateChangeCallback = (
    newValue: unknown,
    oldValue: unknown,
    key: string
) => void;

/**
 * State set options
 */
export interface StateSetOptions {
    sync?: boolean;
}

/**
 * State change record
 */
export interface StateChange {
    key: string;
    value: unknown;
    oldValue: unknown;
}

/**
 * Sync update payload
 */
export interface SyncUpdatePayload {
    component: string | null;
    property: string;
    value: unknown;
}

/**
 * Batch sync update payload
 */
export interface BatchSyncUpdatePayload {
    component: string | null;
    updates: Array<{
        property: string;
        value: unknown;
    }>;
}

/**
 * Event callback type for event bus
 */
export type EventCallback<T = unknown> = (data: T) => void;

/**
 * Accelade actions for state manipulation
 */
export interface AcceladeActions {
    increment: (key?: string, amount?: number) => void;
    decrement: (key?: string, amount?: number) => void;
    set: (key: string, value: unknown) => void;
    get: (key: string) => unknown;
    toggle: (key: string) => void;
    reset: (key: string) => void;
    // Aliases with $ prefix for template usage
    $set: (key: string, value: unknown) => void;
    $get: (key: string) => unknown;
    $toggle: (key: string) => void;
    $store: (name: string) => Record<string, unknown> | null;
    // Event bus methods
    $emit: <T = unknown>(event: string, data?: T) => void;
    $on: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
    $once: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
    $off: <T = unknown>(event: string, callback: EventCallback<T>) => void;
}

/**
 * Hook result for useAccelade
 */
export interface UseAcceladeResult<T extends Record<string, unknown>> {
    state: T;
    setState: React.Dispatch<React.SetStateAction<T>>;
    actions: AcceladeActions;
}

/**
 * Accelade provider props
 */
export interface AcceladeProviderProps<T extends Record<string, unknown>> {
    initialState: T;
    componentId?: string;
    syncProperties?: string[];
    children: React.ReactNode | ((accelade: UseAcceladeResult<T>) => React.ReactNode);
}

/**
 * Counter component props
 */
export interface AcceladeCounterProps {
    initialCount?: number;
    sync?: string;
}

/**
 * Generic component props
 */
export interface AcceladeComponentProps {
    template: string;
    config: AcceladeComponentConfig;
}

export {};
