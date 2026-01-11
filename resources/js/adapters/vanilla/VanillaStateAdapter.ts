/**
 * VanillaStateAdapter - Proxy-based reactive state for vanilla JavaScript
 */

import type { IStateAdapter } from '../types';
import type { StateChangeCallback } from '../../core/types';

/**
 * Subscription entry
 */
interface Subscription {
    callback: StateChangeCallback;
    key?: string;
}

/**
 * VanillaStateAdapter - Uses Proxy for reactivity without framework dependencies
 */
export class VanillaStateAdapter implements IStateAdapter {
    private state: Record<string, unknown> = {};
    private proxy: Record<string, unknown> | null = null;
    private subscriptions: Set<Subscription> = new Set();
    private keySubscriptions: Map<string, Set<(newVal: unknown, oldVal: unknown) => void>> = new Map();

    /**
     * Initialize state with given values
     */
    init(initialState: Record<string, unknown>): void {
        this.state = { ...initialState };
        this.proxy = this.createProxy();
    }

    /**
     * Create reactive proxy
     */
    private createProxy(): Record<string, unknown> {
        const adapter = this;

        return new Proxy(this.state, {
            set(target: Record<string, unknown>, prop: string, value: unknown): boolean {
                const oldValue = target[prop];
                target[prop] = value;

                if (oldValue !== value) {
                    adapter.notifyChange(value, oldValue, prop);
                }

                return true;
            },

            get(target: Record<string, unknown>, prop: string): unknown {
                return target[prop];
            },

            deleteProperty(target: Record<string, unknown>, prop: string): boolean {
                const oldValue = target[prop];
                const existed = prop in target;
                delete target[prop];

                if (existed) {
                    adapter.notifyChange(undefined, oldValue, prop);
                }

                return true;
            },
        });
    }

    /**
     * Notify subscribers of state change
     */
    private notifyChange(newValue: unknown, oldValue: unknown, key: string): void {
        // Notify general subscribers
        for (const sub of this.subscriptions) {
            if (!sub.key || sub.key === key) {
                sub.callback(newValue, oldValue, key);
            }
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
        return { ...this.state };
    }

    /**
     * Get a specific state value
     */
    get<T = unknown>(key: string): T | undefined {
        return this.state[key] as T | undefined;
    }

    /**
     * Set a state value
     */
    set(key: string, value: unknown): void {
        if (this.proxy) {
            this.proxy[key] = value;
        } else {
            const oldValue = this.state[key];
            this.state[key] = value;
            if (oldValue !== value) {
                this.notifyChange(value, oldValue, key);
            }
        }
    }

    /**
     * Set multiple values at once
     */
    setMany(updates: Record<string, unknown>): void {
        for (const [key, value] of Object.entries(updates)) {
            this.set(key, value);
        }
    }

    /**
     * Subscribe to all state changes
     */
    subscribe(callback: StateChangeCallback): () => void {
        const subscription: Subscription = { callback };
        this.subscriptions.add(subscription);

        return () => {
            this.subscriptions.delete(subscription);
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
     * Get the reactive proxy
     */
    getReactiveState(): Record<string, unknown> {
        return this.proxy ?? this.state;
    }

    /**
     * Check if key exists
     */
    has(key: string): boolean {
        return key in this.state;
    }

    /**
     * Get all keys
     */
    keys(): string[] {
        return Object.keys(this.state);
    }

    /**
     * Dispose and cleanup
     */
    dispose(): void {
        this.subscriptions.clear();
        this.keySubscriptions.clear();
        this.proxy = null;
        this.state = {};
    }
}

export default VanillaStateAdapter;
