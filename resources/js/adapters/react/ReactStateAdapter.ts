/**
 * ReactStateAdapter - React useState-based state management
 *
 * Note: This adapter is designed to work with React's hooks system.
 * Unlike Vue/Vanilla adapters, React requires component re-renders for state updates.
 * This adapter provides a bridge between the adapter interface and React's useState.
 */

import type { IStateAdapter } from '../types';
import type { StateChangeCallback } from '../../core/types';

/**
 * State setter function type (like React's setState)
 */
type StateSetter = (updater: Record<string, unknown> | ((prev: Record<string, unknown>) => Record<string, unknown>)) => void;

/**
 * ReactStateAdapter - Bridges IStateAdapter interface with React's useState
 *
 * Usage:
 * In a React component:
 * ```
 * const [state, setState] = useState(initialState);
 * const adapter = new ReactStateAdapter();
 * adapter.connectReactState(state, setState);
 * ```
 */
export class ReactStateAdapter implements IStateAdapter {
    private state: Record<string, unknown> = {};
    private originalState: Record<string, unknown> = {};
    private setState: StateSetter | null = null;
    private subscriptions: Set<{ callback: StateChangeCallback; key?: string }> = new Set();
    private keySubscriptions: Map<string, Set<(newVal: unknown, oldVal: unknown) => void>> = new Map();

    /**
     * Initialize state with given values
     */
    init(initialState: Record<string, unknown>): void {
        this.originalState = { ...initialState };
        this.state = { ...initialState };
    }

    /**
     * Connect to React's useState setter
     * Call this from within a React component to enable reactive updates
     */
    connectReactState(
        currentState: Record<string, unknown>,
        setter: StateSetter
    ): void {
        this.state = currentState;
        this.setState = setter;
    }

    /**
     * Update internal state reference (called from React component when state changes)
     */
    syncFromReact(newState: Record<string, unknown>): void {
        const oldState = { ...this.state };
        this.state = newState;

        // Notify subscribers of changes
        for (const key of Object.keys(newState)) {
            if (oldState[key] !== newState[key]) {
                this.notifyChange(newState[key], oldState[key], key);
            }
        }
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
     * Set a state value (triggers React re-render via setState)
     */
    set(key: string, value: unknown): void {
        const oldValue = this.state[key];

        if (this.setState) {
            // Use React's setState to trigger re-render
            this.setState((prev) => ({ ...prev, [key]: value }));
        } else {
            // Fallback for when not connected to React
            this.state[key] = value;
        }

        if (oldValue !== value) {
            this.notifyChange(value, oldValue, key);
        }
    }

    /**
     * Set multiple values at once
     */
    setMany(updates: Record<string, unknown>): void {
        if (this.setState) {
            this.setState((prev) => ({ ...prev, ...updates }));
        } else {
            Object.assign(this.state, updates);
        }

        // Notify for each changed key
        for (const [key, value] of Object.entries(updates)) {
            const oldValue = this.state[key];
            if (oldValue !== value) {
                this.notifyChange(value, oldValue, key);
            }
        }
    }

    /**
     * Subscribe to all state changes
     */
    subscribe(callback: StateChangeCallback): () => void {
        const subscription = { callback };
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
     * Get the reactive state object
     * In React, this returns the current state object
     */
    getReactiveState(): Record<string, unknown> {
        return this.state;
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
        this.setState = null;
        this.state = {};
        this.originalState = {};
    }
}

export default ReactStateAdapter;
