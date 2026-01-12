/**
 * FlashManager - Manages Laravel session flash data on the client
 *
 * Provides reactive access to flash data shared from Laravel backend.
 * Flash data persists across SPA navigation until consumed.
 */

import type { FlashData, FlashObject, FlashConfig } from './types';

/**
 * Callback for flash data changes
 */
export type FlashChangeCallback = (flashData: FlashData) => void;

/**
 * FlashManager singleton class
 */
export class FlashManager {
    private static instance: FlashManager | null = null;

    private data: FlashData = {};

    private listeners: Set<FlashChangeCallback> = new Set();

    /**
     * Get singleton instance
     */
    static getInstance(): FlashManager {
        if (!this.instance) {
            this.instance = new FlashManager();
        }
        return this.instance;
    }

    /**
     * Initialize with flash data from server
     */
    init(initialData?: FlashData): void {
        if (initialData) {
            this.data = { ...initialData };
            this.notifyListeners();
        }
    }

    /**
     * Merge new flash data (used during SPA navigation)
     */
    merge(newData: FlashData): void {
        this.data = { ...this.data, ...newData };
        this.notifyListeners();
    }

    /**
     * Set flash data (replaces existing)
     */
    set(data: FlashData): void {
        this.data = { ...data };
        this.notifyListeners();
    }

    /**
     * Get a flash value by key
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
     * Check if a flash key exists and has a truthy value
     */
    has(key: string): boolean {
        const value = this.get(key);
        return value !== undefined && value !== null && value !== '';
    }

    /**
     * Get all flash data
     */
    all(): FlashData {
        return { ...this.data };
    }

    /**
     * Clear a specific flash key
     */
    forget(key: string): void {
        delete this.data[key];
        this.notifyListeners();
    }

    /**
     * Clear all flash data
     */
    clear(): void {
        this.data = {};
        this.notifyListeners();
    }

    /**
     * Subscribe to flash data changes
     */
    subscribe(callback: FlashChangeCallback): () => void {
        this.listeners.add(callback);

        return () => {
            this.listeners.delete(callback);
        };
    }

    /**
     * Create a FlashObject for use in templates
     */
    createFlashObject(): FlashObject {
        const self = this;

        // Create a proxy that exposes both data properties and methods
        return new Proxy({} as FlashObject, {
            get(target, prop: string) {
                // Built-in methods
                if (prop === 'has') {
                    return (key: string) => self.has(key);
                }
                if (prop === 'all') {
                    return () => self.all();
                }
                if (prop === 'get') {
                    return <T>(key: string, defaultValue?: T) => self.get(key, defaultValue);
                }

                // Access flash data properties directly
                return self.get(prop);
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
                // Methods are not enumerable
                if (['has', 'all', 'get'].includes(prop)) {
                    return {
                        enumerable: false,
                        configurable: true,
                        value: (target as Record<string, unknown>)[prop],
                    };
                }
                return undefined;
            },
        });
    }

    /**
     * Notify all listeners of data change
     */
    private notifyListeners(): void {
        const data = this.all();
        this.listeners.forEach(callback => {
            callback(data);
        });
    }

    /**
     * Reset manager (for testing)
     */
    reset(): void {
        this.data = {};
        this.listeners.clear();
    }

    /**
     * Reset singleton instance (for testing)
     */
    static resetInstance(): void {
        this.instance = null;
    }
}

export default FlashManager;
