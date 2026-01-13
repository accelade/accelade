/**
 * VueStateAdapter - Vue reactive state management
 */

import { reactive, watch, type UnwrapNestedRefs } from 'vue';
import type { IStateAdapter } from '../types';
import type { StateChangeCallback } from '../../core/types';

/**
 * VueStateAdapter - Uses Vue's reactive() for state management
 */
export class VueStateAdapter implements IStateAdapter {
    private state: UnwrapNestedRefs<Record<string, unknown>> | null = null;
    private originalState: Record<string, unknown> = {};
    private watchers: Array<() => void> = [];

    /**
     * Initialize state with Vue's reactive()
     */
    init(initialState: Record<string, unknown>): void {
        this.originalState = { ...initialState };
        this.state = reactive({ ...initialState }) as UnwrapNestedRefs<Record<string, unknown>>;
    }

    /**
     * Get current state snapshot (non-reactive copy)
     */
    getState(): Record<string, unknown> {
        if (!this.state) return {};
        return { ...this.state };
    }

    /**
     * Get a specific state value (supports nested paths like "props.count")
     */
    get<T = unknown>(key: string): T | undefined {
        if (!this.state) return undefined;

        if (!key.includes('.')) {
            return this.state[key] as T | undefined;
        }

        // Handle nested path
        const parts = key.split('.');
        let current: unknown = this.state;
        for (const part of parts) {
            if (current === null || current === undefined) {
                return undefined;
            }
            current = (current as Record<string, unknown>)[part];
        }
        return current as T | undefined;
    }

    /**
     * Set a state value (supports nested paths like "props.count")
     */
    set(key: string, value: unknown): void {
        if (!this.state) return;

        if (!key.includes('.')) {
            this.state[key] = value;
            return;
        }

        // Handle nested path
        const parts = key.split('.');
        const lastKey = parts.pop()!;
        let current: Record<string, unknown> = this.state;

        // Navigate to parent object, creating nested objects if needed
        for (const part of parts) {
            if (!(part in current) || typeof current[part] !== 'object' || current[part] === null) {
                current[part] = {};
            }
            current = current[part] as Record<string, unknown>;
        }

        // Set the value
        current[lastKey] = value;
    }

    /**
     * Set multiple values at once
     */
    setMany(updates: Record<string, unknown>): void {
        if (!this.state) return;
        for (const [key, value] of Object.entries(updates)) {
            this.state[key] = value;
        }
    }

    /**
     * Subscribe to all state changes using Vue's watch
     */
    subscribe(callback: StateChangeCallback): () => void {
        if (!this.state) return () => {};

        // Watch all properties
        const keys = Object.keys(this.state);
        const unwatchers: Array<() => void> = [];

        for (const key of keys) {
            const unwatch = watch(
                () => this.state![key],
                (newValue, oldValue) => {
                    callback(newValue, oldValue, key);
                }
            );
            unwatchers.push(unwatch);
        }

        // Store watchers for cleanup
        this.watchers.push(...unwatchers);

        return () => {
            for (const unwatch of unwatchers) {
                unwatch();
                const idx = this.watchers.indexOf(unwatch);
                if (idx !== -1) this.watchers.splice(idx, 1);
            }
        };
    }

    /**
     * Subscribe to specific key changes
     */
    subscribeKey(
        key: string,
        callback: (newVal: unknown, oldVal: unknown) => void
    ): () => void {
        if (!this.state) return () => {};

        const unwatch = watch(
            () => this.state![key],
            (newValue, oldValue) => {
                callback(newValue, oldValue);
            }
        );

        this.watchers.push(unwatch);

        return () => {
            unwatch();
            const idx = this.watchers.indexOf(unwatch);
            if (idx !== -1) this.watchers.splice(idx, 1);
        };
    }

    /**
     * Get the reactive Vue state object
     */
    getReactiveState(): UnwrapNestedRefs<Record<string, unknown>> {
        return this.state ?? reactive({});
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
        return this.state ? key in this.state : false;
    }

    /**
     * Get all keys
     */
    keys(): string[] {
        return this.state ? Object.keys(this.state) : [];
    }

    /**
     * Dispose and cleanup
     */
    dispose(): void {
        // Stop all watchers
        for (const unwatch of this.watchers) {
            unwatch();
        }
        this.watchers = [];
        this.state = null;
        this.originalState = {};
    }
}

export default VueStateAdapter;
