/**
 * ErrorHandler - Client-side error handling for Accelade
 *
 * Provides graceful error handling for AJAX responses and component errors.
 * Can show toast notifications, log errors, and handle special actions like
 * refresh or redirect based on server response.
 */

/**
 * Error configuration from server
 */
export interface ErrorConfig {
    suppressErrors: boolean;
    showToasts: boolean;
    logErrors: boolean;
    debug: boolean;
}

/**
 * Accelade error response structure
 */
export interface AcceladeErrorResponse {
    success: boolean;
    message?: string;
    errors?: Record<string, string[]>;
    _accelade?: {
        type?: 'validation' | 'http' | 'exception';
        status?: number;
        action?: 'refresh' | 'redirect';
        url?: string;
        toast?: {
            type: 'success' | 'info' | 'warning' | 'danger';
            title: string;
            body?: string;
        };
        debug?: {
            exception?: string;
            message?: string;
            file?: string;
            line?: number;
            trace?: Array<{
                file?: string;
                line?: number;
                function?: string;
                class?: string;
            }>;
        };
    };
}

/**
 * Default error configuration
 */
let config: ErrorConfig = {
    suppressErrors: true,
    showToasts: true,
    logErrors: true,
    debug: false,
};

/**
 * Initialize error handler with configuration
 */
export function init(errorConfig?: Partial<ErrorConfig>): void {
    if (errorConfig) {
        config = { ...config, ...errorConfig };
    }

    // Setup global error handlers if suppressing errors
    if (config.suppressErrors) {
        setupGlobalHandlers();
    }
}

/**
 * Get current configuration
 */
export function getConfig(): ErrorConfig {
    return { ...config };
}

/**
 * Setup global error handlers
 */
function setupGlobalHandlers(): void {
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', (event) => {
        if (config.suppressErrors) {
            event.preventDefault();
            handleError(event.reason, 'Unhandled Promise Rejection');
        }
    });

    // Handle runtime errors
    window.addEventListener('error', (event) => {
        if (config.suppressErrors) {
            // Only suppress Accelade-related errors
            const message = event.message || '';
            if (message.includes('Accelade') || message.includes('accelade')) {
                event.preventDefault();
                handleError(event.error || new Error(message), 'Runtime Error');
            }
        }
    });
}

/**
 * Handle an error
 */
export function handleError(error: unknown, context?: string): void {
    // Log to console if enabled
    if (config.logErrors) {
        const prefix = context ? `[Accelade ${context}]` : '[Accelade Error]';
        console.error(prefix, error);
    }

    // Show toast if enabled and notifications available
    if (config.showToasts) {
        const message = error instanceof Error ? error.message : String(error);
        showErrorToast('Error', message);
    }
}

/**
 * Handle an AJAX error response
 */
export function handleAjaxError(response: AcceladeErrorResponse, statusCode?: number): void {
    const accelade = response._accelade;

    // Log error if enabled
    if (config.logErrors) {
        console.error('[Accelade AJAX Error]', {
            status: statusCode,
            message: response.message,
            type: accelade?.type,
            debug: accelade?.debug,
        });
    }

    // Handle special actions
    if (accelade?.action === 'refresh') {
        // Refresh the page
        window.location.reload();
        return;
    }

    if (accelade?.action === 'redirect' && accelade.url) {
        // Redirect to URL
        window.location.href = accelade.url;
        return;
    }

    // Show toast notification
    if (config.showToasts && accelade?.toast) {
        showToast(
            accelade.toast.type,
            accelade.toast.title,
            accelade.toast.body
        );
    } else if (config.showToasts && response.message) {
        // Fallback toast
        showErrorToast('Error', response.message);
    }

    // Log debug info in development
    if (config.debug && accelade?.debug) {
        console.group('[Accelade Debug Info]');
        console.log('Exception:', accelade.debug.exception);
        console.log('Message:', accelade.debug.message);
        console.log('File:', accelade.debug.file);
        console.log('Line:', accelade.debug.line);
        if (accelade.debug.trace) {
            console.log('Trace:', accelade.debug.trace);
        }
        console.groupEnd();
    }
}

/**
 * Handle a fetch error (network error, timeout, etc.)
 */
export function handleFetchError(error: Error, url?: string): void {
    if (config.logErrors) {
        console.error('[Accelade Fetch Error]', {
            message: error.message,
            url,
        });
    }

    if (config.showToasts) {
        if (error.name === 'AbortError') {
            showWarningToast('Request Cancelled', 'The request was cancelled.');
        } else if (!navigator.onLine) {
            showWarningToast('No Connection', 'Please check your internet connection.');
        } else {
            showErrorToast('Network Error', 'Failed to connect to the server.');
        }
    }
}

/**
 * Handle validation errors
 */
export function handleValidationError(response: AcceladeErrorResponse): Record<string, string[]> | null {
    if (response._accelade?.type !== 'validation' || !response.errors) {
        return null;
    }

    // Log validation errors
    if (config.logErrors) {
        console.warn('[Accelade Validation Error]', response.errors);
    }

    // Show toast if enabled
    if (config.showToasts && response._accelade?.toast) {
        showToast(
            response._accelade.toast.type,
            response._accelade.toast.title,
            response._accelade.toast.body
        );
    }

    return response.errors;
}

/**
 * Show a toast notification
 */
function showToast(type: string, title: string, body?: string): void {
    const Accelade = (window as unknown as { Accelade?: { notify?: Record<string, (title: string, body?: string) => void> } }).Accelade;

    if (Accelade?.notify) {
        const notifyMethod = Accelade.notify[type];
        if (typeof notifyMethod === 'function') {
            notifyMethod(title, body ?? '');
            return;
        }
    }

    // Fallback: log to console
    console.warn(`[${type.toUpperCase()}] ${title}${body ? `: ${body}` : ''}`);
}

/**
 * Show an error toast
 */
function showErrorToast(title: string, body?: string): void {
    showToast('danger', title, body);
}

/**
 * Show a warning toast
 */
function showWarningToast(title: string, body?: string): void {
    showToast('warning', title, body);
}

/**
 * Create a wrapper for fetch that handles errors
 */
export function createErrorHandlingFetch(
    originalFetch: typeof fetch = window.fetch.bind(window)
): typeof fetch {
    return async (input: RequestInfo | URL, init?: RequestInit): Promise<Response> => {
        try {
            const response = await originalFetch(input, init);

            // Check for error responses
            if (!response.ok) {
                // Clone response to read body while preserving original
                const clone = response.clone();

                try {
                    const data = await clone.json() as AcceladeErrorResponse;

                    // Check if it's an Accelade error response
                    if (data._accelade) {
                        handleAjaxError(data, response.status);
                    }
                } catch {
                    // Not JSON or not Accelade response, ignore
                }
            }

            return response;
        } catch (error) {
            // Handle network errors
            if (error instanceof Error) {
                handleFetchError(error, typeof input === 'string' ? input : input.toString());
            }
            throw error;
        }
    };
}

/**
 * Process a response and handle any Accelade errors
 * Returns true if an error was handled, false otherwise
 */
export function processResponse(response: AcceladeErrorResponse): boolean {
    if (!response._accelade) {
        return false;
    }

    if (!response.success) {
        handleAjaxError(response);
        return true;
    }

    // Handle success toasts
    if (response._accelade.toast) {
        showToast(
            response._accelade.toast.type,
            response._accelade.toast.title,
            response._accelade.toast.body
        );
    }

    return false;
}

export default {
    init,
    getConfig,
    handleError,
    handleAjaxError,
    handleFetchError,
    handleValidationError,
    processResponse,
    createErrorHandlingFetch,
};
