/**
 * Rehydrate Component Types
 *
 * Type definitions for the Rehydrate component that enables
 * selective section reloading without full page refresh.
 */

/**
 * Rehydrate configuration parsed from data attributes
 */
export interface RehydrateConfig {
    /**
     * Unique identifier for the rehydrate instance
     */
    id: string;

    /**
     * Event name(s) that trigger rehydration
     */
    on: string[];

    /**
     * Polling interval in milliseconds (0 = disabled)
     */
    poll: number;

    /**
     * URL to fetch content from (defaults to current page)
     */
    url: string;

    /**
     * Preserve scroll position after rehydration
     */
    preserveScroll: boolean;
}

/**
 * Rehydrate instance
 */
export interface RehydrateInstance {
    /**
     * Unique ID
     */
    id: string;

    /**
     * Configuration
     */
    config: RehydrateConfig;

    /**
     * Component element
     */
    element: HTMLElement;

    /**
     * Whether rehydration is in progress
     */
    isLoading: boolean;

    /**
     * Trigger rehydration manually
     */
    rehydrate: () => Promise<void>;

    /**
     * Start polling (if configured)
     */
    startPolling: () => void;

    /**
     * Stop polling
     */
    stopPolling: () => void;

    /**
     * Cleanup function
     */
    dispose: () => void;
}

/**
 * Rehydrate event detail
 */
export interface RehydrateEventDetail {
    /**
     * Rehydrate instance ID
     */
    id: string;

    /**
     * Event that triggered rehydration (if any)
     */
    event?: string;

    /**
     * Whether rehydration was successful
     */
    success: boolean;
}
