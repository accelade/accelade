/**
 * SvelteStateAdapter - Svelte store-based state management
 *
 * Uses Svelte's writable stores for reactivity.
 * Can work with or without the Svelte runtime.
 */

import type { IStateAdapter } from '../types';
import type { StateChangeCallback } from '../../core/types';

/**
 * Minimal writable store interface (Svelte-compatible)
 */
interface WritableStore<T> {
    subscribe(callback: (value: T) => void): () => void;
    set(value: T): void;
    update(updater: (value: T) => T): void;
}

/**
 * Create a minimal writable store (Svelte-like)
 */
function writable<T>(initialValue: T): WritableStore<T> {
    let value = initialValue;
    const subscribers = new Set<(value: T) => void>();

    return {
        subscribe(callback: (value: T) => void): () => void {
            subscribers.add(callback);
            callback(value); // Svelte stores call immediately on subscribe
            return () => subscribers.delete(callback);
        },
        set(newValue: T): void {
            value = newValue;
            for (const subscriber of subscribers) {
                subscriber(value);
            }
        },
        update(updater: (value: T) => T): void {
            value = updater(value);
            for (const subscriber of subscribers) {
                subscriber(value);
            }
        },
    };
}

/**
 * SvelteStateAdapter - Uses Svelte-style stores for reactivity
 */
export class SvelteStateAdapter implements IStateAdapter {
    private store: WritableStore<Record<string, unknown>> | null = null;
    private currentState: Record<string, unknown> = {};
    private originalState: Record<string, unknown> = {};
    private unsubscribers: Array<() => void> = [];
    private keySubscriptions: Map<string, Set<(newVal: unknown, oldVal: unknown) => void>> = new Map();
    private generalSubscriptions: Set<{ callback: StateChangeCallback }> = new Set();

    /**
     * Initialize state with Svelte store
     */
    init(initialState: Record<string, unknown>): void {
        this.originalState = { ...initialState };
        this.currentState = { ...initialState };

        // Create the main store
        this.store = writable(this.currentState);

        // Subscribe to track changes
        const unsub = this.store.subscribe((state) => {
            const oldState = this.currentState;
            this.currentState = state;

            // Detect and notify changes
            for (const key of Object.keys(state)) {
                if (oldState[key] !== state[key]) {
                    this.notifyChange(state[key], oldState[key], key);
                }
            }
        });

        this.unsubscribers.push(unsub);
    }

    /**
     * Notify subscribers of state change
     */
    private notifyChange(newValue: unknown, oldValue: unknown, key: string): void {
        // Notify general subscribers
        for (const sub of this.generalSubscriptions) {
            sub.callback(newValue, oldValue, key);
        }

        // Notify key-specific subscribers
        const keySubs = this.keySubscriptions.get(key);
        if (keySubs) {
            for (const callback of keySubs) {
                callback(newValue, oldValue);
            }
        }
    }

    /**
     * Get current state snapshot
     */
    getState(): Record<string, unknown> {
        return { ...this.currentState };
    }

    /**
     * Get a specific state value
     */
    get<T = unknown>(key: string): T | undefined {
        return this.currentState[key] as T | undefined;
    }

    /**
     * Set a state value
     */
    set(key: string, value: unknown): void {
        if (this.store) {
            this.store.update((state) => ({ ...state, [key]: value }));
        }
    }

    /**
     * Set multiple values at once
     */
    setMany(updates: Record<string, unknown>): void {
        if (this.store) {
            this.store.update((state) => ({ ...state, ...updates }));
        }
    }

    /**
     * Subscribe to all state changes
     */
    subscribe(callback: StateChangeCallback): () => void {
        const subscription = { callback };
        this.generalSubscriptions.add(subscription);

        return () => {
            this.generalSubscriptions.delete(subscription);
        };
    }

    /**
     * Subscribe to specific key changes
     */
    subscribeKey(
        key: string,
        callback: (newVal: unknown, oldVal: unknown) => void
    ): () => void {
        let keySubs = this.keySubscriptions.get(key);
        if (!keySubs) {
            keySubs = new Set();
            this.keySubscriptions.set(key, keySubs);
        }
        keySubs.add(callback);

        return () => {
            keySubs?.delete(callback);
            if (keySubs?.size === 0) {
                this.keySubscriptions.delete(key);
            }
        };
    }

    /**
     * Get the Svelte store for reactive usage
     */
    getReactiveState(): WritableStore<Record<string, unknown>> {
        return this.store ?? writable({});
    }

    /**
     * Get original state for reset operations
     */
    getOriginalState(): Record<string, unknown> {
        return { ...this.originalState };
    }

    /**
     * Check if key exists
     */
    has(key: string): boolean {
        return key in this.currentState;
    }

    /**
     * Get all keys
     */
    keys(): string[] {
        return Object.keys(this.currentState);
    }

    /**
     * Get store for direct Svelte usage
     * In Svelte components: $store
     */
    getStore(): WritableStore<Record<string, unknown>> | null {
        return this.store;
    }

    /**
     * Dispose and cleanup
     */
    dispose(): void {
        // Unsubscribe from store
        for (const unsub of this.unsubscribers) {
            unsub();
        }
        this.unsubscribers = [];

        // Clear subscriptions
        this.generalSubscriptions.clear();
        this.keySubscriptions.clear();

        // Clear state
        this.store = null;
        this.currentState = {};
        this.originalState = {};
    }
}

export default SvelteStateAdapter;
