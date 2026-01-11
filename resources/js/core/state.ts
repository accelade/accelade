/**
 * Accelade State Management
 * Framework-agnostic reactive state management
 */

import type {
    StateChangeCallback,
    StateSetOptions,
    StateChange,
    SyncUpdatePayload,
    BatchSyncUpdatePayload,
} from './types';

export interface AcceladeStateOptions {
    componentId?: string | null;
}

export class AcceladeState {
    private state: Record<string, unknown>;
    private listeners: Map<string, Set<StateChangeCallback>>;
    private syncEnabled: Set<string>;
    private componentId: string | null;
    private debounceTimers: Map<string, ReturnType<typeof setTimeout>>;

    constructor(initialState: Record<string, unknown> = {}, options: AcceladeStateOptions = {}) {
        this.state = { ...initialState };
        this.listeners = new Map();
        this.syncEnabled = new Set();
        this.componentId = options.componentId ?? null;
        this.debounceTimers = new Map();
    }

    /**
     * Get a state value
     */
    get<T = unknown>(key: string): T | undefined {
        return this.state[key] as T | undefined;
    }

    /**
     * Get all state
     */
    getAll(): Record<string, unknown> {
        return { ...this.state };
    }

    /**
     * Set a state value
     */
    set(key: string, value: unknown, options: StateSetOptions = {}): void {
        const oldValue = this.state[key];

        if (oldValue === value) return;

        this.state[key] = value;

        // Notify listeners
        this.notify(key, value, oldValue);

        // Sync to server if enabled
        if (options.sync !== false && this.syncEnabled.has(key)) {
            this.syncToServer(key, value);
        }
    }

    /**
     * Update multiple state values
     */
    setMany(updates: Record<string, unknown>, options: StateSetOptions = {}): void {
        const changes: StateChange[] = [];

        for (const [key, value] of Object.entries(updates)) {
            const oldValue = this.state[key];
            if (oldValue !== value) {
                this.state[key] = value;
                changes.push({ key, value, oldValue });
                this.notify(key, value, oldValue);
            }
        }

        // Batch sync if needed
        if (options.sync !== false && changes.length > 0) {
            const syncChanges = changes.filter(c => this.syncEnabled.has(c.key));
            if (syncChanges.length > 0) {
                this.batchSyncToServer(syncChanges);
            }
        }
    }

    /**
     * Enable sync for a property
     */
    enableSync(key: string): void {
        this.syncEnabled.add(key);
    }

    /**
     * Enable sync for multiple properties
     */
    enableSyncMany(keys: string[]): void {
        keys.forEach(key => this.syncEnabled.add(key));
    }

    /**
     * Subscribe to state changes
     */
    subscribe(key: string, callback: StateChangeCallback): () => void {
        if (!this.listeners.has(key)) {
            this.listeners.set(key, new Set());
        }
        this.listeners.get(key)!.add(callback);

        // Return unsubscribe function
        return () => {
            this.listeners.get(key)?.delete(callback);
        };
    }

    /**
     * Subscribe to all state changes
     */
    subscribeAll(callback: StateChangeCallback): () => void {
        const key = '*';
        if (!this.listeners.has(key)) {
            this.listeners.set(key, new Set());
        }
        this.listeners.get(key)!.add(callback);

        return () => {
            this.listeners.get(key)?.delete(callback);
        };
    }

    /**
     * Notify listeners of a change
     */
    private notify(key: string, newValue: unknown, oldValue: unknown): void {
        // Notify specific key listeners
        this.listeners.get(key)?.forEach(cb => cb(newValue, oldValue, key));

        // Notify wildcard listeners
        this.listeners.get('*')?.forEach(cb => cb(newValue, oldValue, key));
    }

    /**
     * Sync a single property to server (debounced)
     */
    private syncToServer(key: string, value: unknown): void {
        const debounce = window.AcceladeConfig?.syncDebounce ?? 300;

        // Clear existing timer
        const existingTimer = this.debounceTimers.get(key);
        if (existingTimer !== undefined) {
            clearTimeout(existingTimer);
        }

        // Set new debounced sync
        const timer = setTimeout(() => {
            void this.doSync(key, value);
            this.debounceTimers.delete(key);
        }, debounce);

        this.debounceTimers.set(key, timer);
    }

    /**
     * Perform the actual sync request
     */
    private async doSync(key: string, value: unknown): Promise<unknown> {
        const config = window.AcceladeConfig;

        try {
            const payload: SyncUpdatePayload = {
                component: this.componentId,
                property: key,
                value: value,
            };

            const response = await fetch(config?.updateUrl ?? '/accelade/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config?.csrfToken ?? '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                console.error('Accelade sync failed:', response.statusText);
            }

            return response.json() as Promise<unknown>;
        } catch (error) {
            console.error('Accelade sync error:', error);
            return undefined;
        }
    }

    /**
     * Batch sync multiple properties
     */
    private async batchSyncToServer(changes: StateChange[]): Promise<unknown> {
        const config = window.AcceladeConfig;

        try {
            const payload: BatchSyncUpdatePayload = {
                component: this.componentId,
                updates: changes.map(c => ({
                    property: c.key,
                    value: c.value,
                })),
            };

            const response = await fetch(config?.batchUpdateUrl ?? '/accelade/batch-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config?.csrfToken ?? '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            return response.json() as Promise<unknown>;
        } catch (error) {
            console.error('Accelade batch sync error:', error);
            return undefined;
        }
    }
}

export default AcceladeState;
