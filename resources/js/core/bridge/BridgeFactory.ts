/**
 * BridgeFactory - Handles Bridge component communication with PHP backend
 *
 * Bridge components provide two-way binding between PHP Blade components
 * and JavaScript, allowing:
 * - Access to PHP properties as reactive `props`
 * - Calling PHP methods via AJAX
 * - Receiving responses (redirects, toasts, data updates)
 */

import { navigate } from '../router';
import { emit as eventEmit } from '../events';
import { handleAjaxError, handleFetchError, type AcceladeErrorResponse } from '../errors/ErrorHandler';

/**
 * Bridge configuration from PHP
 */
export interface BridgeConfig {
    id: string;
    component: string;
    props: Record<string, unknown>;
    methods: string[];
    state: string; // Encrypted state payload
    callUrl: string;
    syncUrl: string;
}

/**
 * Bridge response from PHP
 */
export interface BridgeCallResponse {
    success: boolean;
    message?: string;
    data?: Record<string, unknown>;
    props?: Record<string, unknown>;
    state?: string;
    redirect?: string;
    refresh?: boolean;
    preserveScroll?: boolean;
    toast?: {
        type: string;
        title: string;
        body?: string;
    };
    events?: Array<{
        name: string;
        data: Record<string, unknown>;
    }>;
}

/**
 * Bridge instance for a component
 */
export interface BridgeInstance {
    id: string;
    config: BridgeConfig;
    props: Record<string, unknown>;
    call: (method: string, ...args: unknown[]) => Promise<BridgeCallResponse>;
    sync: (props: Record<string, unknown>) => Promise<void>;
    getState: () => string;
    dispose: () => void;
}

/**
 * Active bridge instances
 */
const instances = new Map<string, BridgeInstance>();

/**
 * CSRF token getter
 */
function getCsrfToken(): string {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.getAttribute('content') ?? '';
}

/**
 * Create a bridge instance for a component
 */
export function createBridge(
    element: HTMLElement,
    componentId: string,
    setState: (key: string, value: unknown) => void,
    getState: () => Record<string, unknown>
): BridgeInstance | undefined {
    // Parse bridge config from data attribute
    const configJson = element.dataset.acceladeBridge;
    if (!configJson) {
        return undefined;
    }

    let config: BridgeConfig;
    try {
        config = JSON.parse(configJson);
    } catch {
        console.error('[Accelade Bridge] Invalid config JSON');
        return undefined;
    }

    // Current encrypted state
    let currentState = config.state;

    // Flag to prevent recursive setState calls when syncing from server
    let isSyncingFromServer = false;

    // Create props proxy that syncs changes
    const props = new Proxy({ ...config.props }, {
        set(target, prop, value) {
            const key = String(prop);
            target[key] = value;

            // Only update component state if not syncing from server
            // This prevents infinite recursion when props are updated from AJAX response
            if (!isSyncingFromServer) {
                setState(`props.${key}`, value);
            }

            return true;
        },
        get(target, prop) {
            return target[String(prop)];
        },
    });

    // Initialize component state with props (without triggering sync)
    isSyncingFromServer = true;
    for (const [key, value] of Object.entries(config.props)) {
        props[key] = value;
    }
    isSyncingFromServer = false;

    // Set initial state with all props at once
    setState('props', { ...config.props });

    /**
     * Call a PHP method via AJAX
     */
    async function call(method: string, ...args: unknown[]): Promise<BridgeCallResponse> {
        // Verify method is allowed
        if (!config.methods.includes(method)) {
            console.error(`[Accelade Bridge] Method '${method}' is not callable`);
            return { success: false, message: `Method '${method}' is not callable` };
        }

        try {
            const response = await fetch(config.callUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    state: currentState,
                    method,
                    args,
                }),
            });

            const result: BridgeCallResponse = await response.json();

            // Handle error responses
            if (!response.ok || !result.success) {
                // Check for Accelade error response format
                const errorResult = result as unknown as AcceladeErrorResponse;
                if (errorResult._accelade) {
                    handleAjaxError(errorResult, response.status);
                }
                // Still return the result so caller can handle it
                return result;
            }

            // Update state if provided
            if (result.state) {
                currentState = result.state;
            }

            // Update props if provided (with sync flag to prevent recursion)
            if (result.props) {
                isSyncingFromServer = true;
                for (const [key, value] of Object.entries(result.props)) {
                    props[key] = value;
                }
                isSyncingFromServer = false;

                // Update component state all at once
                setState('props', { ...props });
            }

            // Handle redirect
            if (result.redirect) {
                navigate(result.redirect);
                return result;
            }

            // Handle refresh
            if (result.refresh) {
                if (result.preserveScroll) {
                    const scrollY = window.scrollY;
                    window.location.reload();
                    window.scrollTo(0, scrollY);
                } else {
                    window.location.reload();
                }
                return result;
            }

            // Handle toast (already triggered server-side, but also emit client-side)
            if (result.toast) {
                const Accelade = (window as unknown as Record<string, unknown>).Accelade as {
                    notify?: {
                        [key: string]: (title: string, body?: string) => void;
                    };
                };

                if (Accelade?.notify) {
                    const notifyMethod = Accelade.notify[result.toast.type];
                    if (typeof notifyMethod === 'function') {
                        notifyMethod(result.toast.title, result.toast.body ?? '');
                    }
                }
            }

            // Handle events
            if (result.events) {
                for (const event of result.events) {
                    eventEmit(event.name, event.data);
                }
            }

            return result;
        } catch (error) {
            // Handle network/fetch errors
            if (error instanceof Error) {
                handleFetchError(error, config.callUrl);
            }
            return {
                success: false,
                message: error instanceof Error ? error.message : 'Unknown error',
            };
        }
    }

    /**
     * Sync property changes to PHP
     */
    async function sync(propsToSync: Record<string, unknown>): Promise<void> {
        try {
            const response = await fetch(config.syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    state: currentState,
                    props: propsToSync,
                }),
            });

            const result = await response.json();

            // Update state if provided
            if (result.state) {
                currentState = result.state;
            }

            // Update props if provided (with sync flag to prevent recursion)
            if (result.props) {
                isSyncingFromServer = true;
                for (const [key, value] of Object.entries(result.props)) {
                    props[key] = value;
                }
                isSyncingFromServer = false;

                // Update component state all at once
                setState('props', { ...props });
            }
        } catch (error) {
            // Handle network/fetch errors
            if (error instanceof Error) {
                handleFetchError(error, config.syncUrl);
            }
        }
    }

    /**
     * Get current encrypted state
     */
    function getBridgeState(): string {
        return currentState;
    }

    /**
     * Dispose the bridge instance
     */
    function dispose(): void {
        instances.delete(config.id);
    }

    const instance: BridgeInstance = {
        id: config.id,
        config,
        props,
        call,
        sync,
        getState: getBridgeState,
        dispose,
    };

    instances.set(config.id, instance);

    return instance;
}

/**
 * Get a bridge instance by ID
 */
export function getBridge(id: string): BridgeInstance | undefined {
    return instances.get(id);
}

/**
 * Get all bridge instances
 */
export function getAllBridges(): Map<string, BridgeInstance> {
    return new Map(instances);
}

/**
 * Create method proxies for a bridge instance
 * Returns an object where each method name maps to a function that calls the PHP method
 */
export function createMethodProxies(instance: BridgeInstance): Record<string, (...args: unknown[]) => Promise<BridgeCallResponse>> {
    const proxies: Record<string, (...args: unknown[]) => Promise<BridgeCallResponse>> = {};

    for (const methodName of instance.config.methods) {
        proxies[methodName] = (...args: unknown[]) => instance.call(methodName, ...args);
    }

    return proxies;
}

/**
 * Dispose a bridge instance
 */
export function disposeBridge(id: string): void {
    const instance = instances.get(id);
    if (instance) {
        instance.dispose();
    }
}

export default {
    create: createBridge,
    get: getBridge,
    getAll: getAllBridges,
    createMethodProxies,
    dispose: disposeBridge,
};
