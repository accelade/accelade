/**
 * SharedDataManager - Manages globally shared data from Laravel backend
 *
 * Provides reactive access to data shared via Accelade::share() on the backend.
 * Data persists across SPA navigation and is available in all components.
 */

import type { SharedData } from '../types';

/**
 * Callback for shared data changes
 */
export type SharedDataChangeCallback = (
    key: string,
    newValue: unknown,
    oldValue: unknown
) => void;

/**
 * SharedDataManager class
 */
export class SharedDataManager {
    private static instance: SharedDataManager | null = null;

    private data: SharedData = {};

    private listeners: Map<string, Set<SharedDataChangeCallback>> = new Map();

    private globalListeners: Set<SharedDataChangeCallback> = new Set();

    /**
     * Get singleton instance
     */
    static getInstance(): SharedDataManager {
        if (!this.instance) {
            this.instance = new SharedDataManager();
        }
        return this.instance;
    }

    /**
     * Initialize with data from server
     */
    init(initialData?: SharedData): void {
        if (initialData) {
            this.data = { ...initialData };
        }
    }

    /**
     * Get a shared value by key
     */
    get<T = unknown>(key: string, defaultValue?: T): T {
        const keys = key.split('.');
        let value: unknown = this.data;

        for (const k of keys) {
            if (value === null || value === undefined || typeof value !== 'object') {
                return defaultValue as T;
            }
            value = (value as Record<string, unknown>)[k];
        }

        return (value !== undefined ? value : defaultValue) as T;
    }

    /**
     * Check if a key exists
     */
    has(key: string): boolean {
        const keys = key.split('.');
        let value: unknown = this.data;

        for (const k of keys) {
            if (value === null || value === undefined || typeof value !== 'object') {
                return false;
            }
            value = (value as Record<string, unknown>)[k];
        }

        return value !== undefined;
    }

    /**
     * Get all shared data
     */
    all(): SharedData {
        return { ...this.data };
    }

    /**
     * Set a shared value (client-side only, useful for optimistic updates)
     */
    set(key: string, value: unknown): void {
        const keys = key.split('.');
        const oldValue = this.get(key);

        if (keys.length === 1) {
            this.data[key] = value;
        } else {
            let current: Record<string, unknown> = this.data;
            for (let i = 0; i < keys.length - 1; i++) {
                const k = keys[i];
                if (!(k in current) || typeof current[k] !== 'object') {
                    current[k] = {};
                }
                current = current[k] as Record<string, unknown>;
            }
            current[keys[keys.length - 1]] = value;
        }

        this.notifyListeners(key, value, oldValue);
    }

    /**
     * Merge new data into shared data
     */
    merge(data: SharedData): void {
        for (const [key, value] of Object.entries(data)) {
            this.set(key, value);
        }
    }

    /**
     * Subscribe to changes for a specific key
     */
    subscribe(key: string, callback: SharedDataChangeCallback): () => void {
        if (!this.listeners.has(key)) {
            this.listeners.set(key, new Set());
        }
        this.listeners.get(key)!.add(callback);

        return () => {
            this.listeners.get(key)?.delete(callback);
        };
    }

    /**
     * Subscribe to all changes
     */
    subscribeAll(callback: SharedDataChangeCallback): () => void {
        this.globalListeners.add(callback);

        return () => {
            this.globalListeners.delete(callback);
        };
    }

    /**
     * Notify listeners of a change
     */
    private notifyListeners(key: string, newValue: unknown, oldValue: unknown): void {
        // Notify key-specific listeners
        this.listeners.get(key)?.forEach(callback => {
            callback(key, newValue, oldValue);
        });

        // Notify global listeners
        this.globalListeners.forEach(callback => {
            callback(key, newValue, oldValue);
        });
    }

    /**
     * Create a reactive proxy for shared data (for use in templates)
     */
    createProxy(): SharedData {
        const self = this;
        return new Proxy(this.data, {
            get(target, prop: string) {
                return self.get(prop);
            },
            set(target, prop: string, value) {
                self.set(prop, value);
                return true;
            },
            has(target, prop: string) {
                return self.has(prop);
            },
            ownKeys() {
                return Object.keys(self.data);
            },
            getOwnPropertyDescriptor(target, prop: string) {
                if (prop in self.data) {
                    return {
                        enumerable: true,
                        configurable: true,
                        value: self.get(prop),
                    };
                }
                return undefined;
            },
        });
    }

    /**
     * Reset shared data (useful for testing)
     */
    reset(): void {
        this.data = {};
        this.listeners.clear();
        this.globalListeners.clear();
    }

    /**
     * Reset singleton instance (for testing)
     */
    static resetInstance(): void {
        this.instance = null;
    }
}

export default SharedDataManager;
