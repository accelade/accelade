/**
 * SyncFactory - Handle server synchronization
 */

import { ConfigFactory } from './ConfigFactory';
import { startProgress, doneProgress } from '../progress';

/**
 * Sync request options
 */
export interface SyncOptions {
    showProgress?: boolean;
    debounce?: boolean | number; // true = use default, number = custom ms
}

/**
 * Sync result
 */
export interface SyncResult {
    success: boolean;
    data?: unknown;
    error?: Error;
}

/**
 * Pending sync request
 */
interface PendingSyncRequest {
    componentId: string;
    property: string;
    value: unknown;
    resolve: (result: SyncResult) => void;
    reject: (error: Error) => void;
}

/**
 * SyncFactory - Manages state synchronization with server
 */
export class SyncFactory {
    private static pendingRequests: Map<string, PendingSyncRequest> = new Map();
    private static debounceTimers: Map<string, ReturnType<typeof setTimeout>> = new Map();
    private static abortControllers: Map<string, AbortController> = new Map();

    /**
     * Sync a single property to the server
     */
    static async sync(
        componentId: string,
        property: string,
        value: unknown,
        options: SyncOptions = {}
    ): Promise<SyncResult> {
        const { showProgress = true, debounce = true } = options;
        const key = `${componentId}:${property}`;

        // Cancel any pending request for this key
        this.cancelPending(key);

        return new Promise((resolve, reject) => {
            const request: PendingSyncRequest = {
                componentId,
                property,
                value,
                resolve,
                reject,
            };

            if (debounce) {
                const debounceTime = ConfigFactory.getSyncDebounce();
                const timer = setTimeout(() => {
                    this.executeSync(request, showProgress);
                }, debounceTime);
                this.debounceTimers.set(key, timer);
            } else {
                this.executeSync(request, showProgress);
            }

            this.pendingRequests.set(key, request);
        });
    }

    /**
     * Sync multiple properties at once (batch)
     */
    static async batchSync(
        componentId: string,
        updates: Record<string, unknown>,
        options: SyncOptions = {}
    ): Promise<SyncResult> {
        const { showProgress = true } = options;
        const url = ConfigFactory.getBatchUpdateUrl();
        const csrfToken = ConfigFactory.getCsrfToken();

        const abortController = new AbortController();
        const key = `batch:${componentId}`;
        this.abortControllers.set(key, abortController);

        if (showProgress) {
            startProgress();
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    component: componentId,
                    updates: Object.entries(updates).map(([property, value]) => ({
                        property,
                        value,
                    })),
                }),
                signal: abortController.signal,
            });

            const data = await response.json();

            if (showProgress) {
                doneProgress();
            }

            return { success: response.ok, data };
        } catch (error) {
            if (showProgress) {
                doneProgress();
            }

            if (error instanceof Error && error.name === 'AbortError') {
                return { success: false, error: new Error('Request aborted') };
            }

            console.error('Accelade: Batch sync failed', error);
            return { success: false, error: error instanceof Error ? error : new Error(String(error)) };
        } finally {
            this.abortControllers.delete(key);
        }
    }

    /**
     * Execute a sync request
     */
    private static async executeSync(
        request: PendingSyncRequest,
        showProgress: boolean
    ): Promise<void> {
        const { componentId, property, value, resolve } = request;
        const url = ConfigFactory.getUpdateUrl();
        const csrfToken = ConfigFactory.getCsrfToken();
        const key = `${componentId}:${property}`;

        const abortController = new AbortController();
        this.abortControllers.set(key, abortController);

        if (showProgress) {
            startProgress();
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    component: componentId,
                    property,
                    value,
                }),
                signal: abortController.signal,
            });

            const data = await response.json();

            if (showProgress) {
                doneProgress();
            }

            resolve({ success: response.ok, data });
        } catch (error) {
            if (showProgress) {
                doneProgress();
            }

            if (error instanceof Error && error.name === 'AbortError') {
                resolve({ success: false, error: new Error('Request aborted') });
                return;
            }

            console.error('Accelade: Sync failed', error);
            resolve({ success: false, error: error instanceof Error ? error : new Error(String(error)) });
        } finally {
            this.pendingRequests.delete(key);
            this.abortControllers.delete(key);
            this.debounceTimers.delete(key);
        }
    }

    /**
     * Cancel a pending request
     */
    static cancelPending(key: string): void {
        // Clear debounce timer
        const timer = this.debounceTimers.get(key);
        if (timer) {
            clearTimeout(timer);
            this.debounceTimers.delete(key);
        }

        // Abort in-flight request
        const controller = this.abortControllers.get(key);
        if (controller) {
            controller.abort();
            this.abortControllers.delete(key);
        }

        // Remove from pending
        this.pendingRequests.delete(key);
    }

    /**
     * Cancel all pending requests for a component
     */
    static cancelComponent(componentId: string): void {
        for (const key of this.pendingRequests.keys()) {
            if (key.startsWith(`${componentId}:`)) {
                this.cancelPending(key);
            }
        }
        this.cancelPending(`batch:${componentId}`);
    }

    /**
     * Cancel all pending requests
     */
    static cancelAll(): void {
        for (const key of this.pendingRequests.keys()) {
            this.cancelPending(key);
        }
        for (const key of this.abortControllers.keys()) {
            this.cancelPending(key);
        }
    }

    /**
     * Check if there are pending requests
     */
    static hasPending(): boolean {
        return this.pendingRequests.size > 0 || this.abortControllers.size > 0;
    }

    /**
     * Get pending request count
     */
    static getPendingCount(): number {
        return this.pendingRequests.size;
    }
}

export default SyncFactory;
