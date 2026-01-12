/**
 * Link Component Types
 *
 * Type definitions for the enhanced Link component with
 * HTTP methods, confirmation dialogs, and navigation options.
 */

/**
 * Supported HTTP methods for link navigation
 */
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

/**
 * Link configuration parsed from data attributes
 */
export interface LinkConfig {
    /**
     * Target URL for navigation
     */
    href: string;

    /**
     * HTTP method (default: GET)
     */
    method: HttpMethod;

    /**
     * Request payload data
     */
    data?: Record<string, unknown>;

    /**
     * Custom HTTP headers
     */
    headers?: Record<string, string>;

    /**
     * Treat as external link (full page navigation)
     */
    away?: boolean;

    /**
     * Show confirmation dialog
     */
    confirm?: boolean;

    /**
     * Confirmation dialog message
     */
    confirmText?: string;

    /**
     * Confirmation dialog title
     */
    confirmTitle?: string;

    /**
     * Confirm button label
     */
    confirmButton?: string;

    /**
     * Cancel button label
     */
    cancelButton?: string;

    /**
     * Render confirm button in danger style (red)
     */
    confirmDanger?: boolean;

    /**
     * Preserve scroll position after navigation
     */
    preserveScroll?: boolean;

    /**
     * Preserve component state after navigation
     */
    preserveState?: boolean;

    /**
     * Enable prefetching
     */
    prefetch?: boolean;

    /**
     * Replace history instead of push
     */
    replace?: boolean;
}

/**
 * Confirmation dialog options
 */
export interface ConfirmDialogOptions {
    /**
     * Dialog title (optional)
     */
    title?: string;

    /**
     * Dialog message
     */
    text: string;

    /**
     * Confirm button label
     */
    confirmButton: string;

    /**
     * Cancel button label
     */
    cancelButton: string;

    /**
     * Use danger styling for confirm button
     */
    danger: boolean;
}

/**
 * Confirm dialog result
 */
export interface ConfirmDialogResult {
    /**
     * Whether user confirmed
     */
    confirmed: boolean;
}

/**
 * Link click event detail
 */
export interface LinkClickDetail {
    /**
     * Target URL
     */
    href: string;

    /**
     * HTTP method
     */
    method: HttpMethod;

    /**
     * Request data
     */
    data?: Record<string, unknown>;

    /**
     * Whether navigation was cancelled
     */
    cancelled: boolean;
}
