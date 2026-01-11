/**
 * DeferFactory - Handle deferred/async data loading
 */

import { ConfigFactory } from './ConfigFactory';
import { startProgress, doneProgress } from '../progress';

/**
 * Defer configuration
 */
export interface DeferConfig {
    url: string;
    method: string;
    acceptHeader: string;
    request?: Record<string, unknown> | string;
    headers?: Record<string, string>;
    poll?: number;
    manual?: boolean;
    watchValue?: string;
    watchDebounce?: number;
}

/**
 * Defer state
 */
export interface DeferState {
    processing: boolean;
    response: unknown;
    error: unknown;
}

/**
 * Defer result
 */
export interface DeferResult {
    success: boolean;
    data?: unknown;
    error?: Error;
}

/**
 * Defer instance for managing a single defer component
 */
export interface DeferInstance {
    id: string;
    config: DeferConfig;
    state: DeferState;
    reload: () => Promise<DeferResult>;
    dispose: () => void;
}

/**
 * DeferFactory - Manages deferred data loading
 */
export class DeferFactory {
    private static instances: Map<string, DeferInstance> = new Map();
    private static abortControllers: Map<string, AbortController> = new Map();
    private static pollTimers: Map<string, ReturnType<typeof setInterval>> = new Map();
    private static debounceTimers: Map<string, ReturnType<typeof setTimeout>> = new Map();

    /**
     * Parse defer configuration from an element
     */
    static parseConfig(element: HTMLElement): DeferConfig | null {
        const url = element.dataset.deferUrl;
        if (!url) {
            return null;
        }

        const method = element.dataset.deferMethod ?? 'GET';
        const acceptHeader = element.dataset.deferAccept ?? 'application/json';
        const poll = element.dataset.deferPoll ? parseInt(element.dataset.deferPoll, 10) : undefined;
        const manual = element.dataset.deferManual === 'true';
        const watchValue = element.dataset.deferWatch;
        const watchDebounce = element.dataset.deferWatchDebounce
            ? parseInt(element.dataset.deferWatchDebounce, 10)
            : 150;

        let request: Record<string, unknown> | string | undefined;
        if (element.dataset.deferRequest) {
            try {
                request = JSON.parse(element.dataset.deferRequest) as Record<string, unknown>;
            } catch {
                request = element.dataset.deferRequest;
            }
        }

        let headers: Record<string, string> | undefined;
        if (element.dataset.deferHeaders) {
            try {
                headers = JSON.parse(element.dataset.deferHeaders) as Record<string, string>;
            } catch {
                // Ignore invalid headers
            }
        }

        return {
            url,
            method: method.toUpperCase(),
            acceptHeader,
            request,
            headers,
            poll,
            manual,
            watchValue,
            watchDebounce,
        };
    }

    /**
     * Create a defer instance
     */
    static create(
        id: string,
        config: DeferConfig,
        setState: (key: string, value: unknown) => void,
        getState: () => Record<string, unknown>,
        dispatchEvent: (name: string, detail: unknown) => void
    ): DeferInstance {
        const state: DeferState = {
            processing: false,
            response: null,
            error: null,
        };

        const reload = async (): Promise<DeferResult> => {
            return this.fetch(id, config, setState, dispatchEvent);
        };

        const dispose = (): void => {
            this.dispose(id);
        };

        const instance: DeferInstance = {
            id,
            config,
            state,
            reload,
            dispose,
        };

        this.instances.set(id, instance);

        // Start polling if configured
        if (config.poll && config.poll > 0) {
            this.startPolling(id, config.poll, reload);
        }

        // Auto-load unless manual
        if (!config.manual) {
            reload();
        }

        return instance;
    }

    /**
     * Fetch data for a defer component
     */
    static async fetch(
        id: string,
        config: DeferConfig,
        setState: (key: string, value: unknown) => void,
        dispatchEvent: (name: string, detail: unknown) => void
    ): Promise<DeferResult> {
        // Cancel any pending request
        this.cancelPending(id);

        const abortController = new AbortController();
        this.abortControllers.set(id, abortController);

        // Set processing state
        setState('processing', true);
        setState('error', null);

        startProgress();

        try {
            // Resolve URL (support template literals)
            const url = this.resolveUrl(config.url);

            // Build headers
            const headers: Record<string, string> = {
                Accept: config.acceptHeader,
                ...config.headers,
            };

            // Add CSRF token for non-GET requests
            if (config.method !== 'GET') {
                headers['Content-Type'] = 'application/json';
                headers['X-CSRF-TOKEN'] = ConfigFactory.getCsrfToken();
            }

            // Build request options
            const requestOptions: RequestInit = {
                method: config.method,
                headers,
                signal: abortController.signal,
            };

            // Add body for non-GET requests
            if (config.method !== 'GET' && config.request) {
                if (typeof config.request === 'string') {
                    requestOptions.body = config.request;
                } else {
                    requestOptions.body = JSON.stringify(config.request);
                }
            }

            const response = await fetch(url, requestOptions);

            let data: unknown;
            const contentType = response.headers.get('content-type') ?? '';

            if (contentType.includes('application/json')) {
                data = await response.json();
            } else if (contentType.includes('text/')) {
                data = await response.text();
            } else {
                data = await response.blob();
            }

            doneProgress();

            // Update state
            setState('processing', false);
            setState('response', data);

            // Dispatch success event
            dispatchEvent('success', data);

            return { success: true, data };
        } catch (error) {
            doneProgress();

            if (error instanceof Error && error.name === 'AbortError') {
                setState('processing', false);
                return { success: false, error: new Error('Request aborted') };
            }

            const errorObj = error instanceof Error ? error : new Error(String(error));

            // Update state
            setState('processing', false);
            setState('error', errorObj.message);

            // Dispatch error event
            dispatchEvent('error', errorObj);

            return { success: false, error: errorObj };
        } finally {
            this.abortControllers.delete(id);
        }
    }

    /**
     * Resolve URL (support template literals with state values)
     */
    private static resolveUrl(url: string): string {
        // If URL contains template literal syntax, we need to evaluate it
        // For now, just return the URL as-is
        // Template literal evaluation happens at the binding level
        return url;
    }

    /**
     * Start polling for a defer component
     */
    private static startPolling(id: string, interval: number, reload: () => Promise<DeferResult>): void {
        // Clear any existing poll timer
        this.stopPolling(id);

        const timer = setInterval(() => {
            reload();
        }, interval);

        this.pollTimers.set(id, timer);
    }

    /**
     * Stop polling for a defer component
     */
    private static stopPolling(id: string): void {
        const timer = this.pollTimers.get(id);
        if (timer) {
            clearInterval(timer);
            this.pollTimers.delete(id);
        }
    }

    /**
     * Cancel pending request
     */
    static cancelPending(id: string): void {
        const controller = this.abortControllers.get(id);
        if (controller) {
            controller.abort();
            this.abortControllers.delete(id);
        }

        const debounceTimer = this.debounceTimers.get(id);
        if (debounceTimer) {
            clearTimeout(debounceTimer);
            this.debounceTimers.delete(id);
        }
    }

    /**
     * Dispose a defer instance
     */
    static dispose(id: string): void {
        this.cancelPending(id);
        this.stopPolling(id);
        this.instances.delete(id);
    }

    /**
     * Dispose all instances
     */
    static disposeAll(): void {
        for (const id of this.instances.keys()) {
            this.dispose(id);
        }
    }

    /**
     * Get an instance by ID
     */
    static getInstance(id: string): DeferInstance | undefined {
        return this.instances.get(id);
    }

    /**
     * Trigger reload with debounce (for watch functionality)
     */
    static triggerReloadDebounced(
        id: string,
        debounceMs: number,
        reload: () => Promise<DeferResult>
    ): void {
        // Clear existing debounce timer
        const existingTimer = this.debounceTimers.get(id);
        if (existingTimer) {
            clearTimeout(existingTimer);
        }

        // Set new debounce timer
        const timer = setTimeout(() => {
            this.debounceTimers.delete(id);
            reload();
        }, debounceMs);

        this.debounceTimers.set(id, timer);
    }
}

export default DeferFactory;
