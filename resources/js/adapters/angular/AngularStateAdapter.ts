/**
 * AngularStateAdapter - Angular signal/RxJS-based state management
 *
 * Provides Angular-compatible state management that can work with:
 * - Angular Signals (v16+)
 * - RxJS BehaviorSubject (older versions)
 * - Or standalone mode without Angular
 */

import type { IStateAdapter } from '../types';
import type { StateChangeCallback } from '../../core/types';

/**
 * Signal-like interface (Angular 16+ compatible)
 */
interface SignalLike<T> {
    (): T;
    set(value: T): void;
    update(fn: (value: T) => T): void;
}

/**
 * BehaviorSubject-like interface (RxJS compatible)
 */
interface BehaviorSubjectLike<T> {
    getValue(): T;
    next(value: T): void;
    subscribe(callback: (value: T) => void): { unsubscribe(): void };
}

/**
 * Create a minimal signal (Angular-like)
 */
function createSignal<T>(initialValue: T): SignalLike<T> {
    let value = initialValue;
    const subscribers = new Set<() => void>();

    const signal = (() => value) as SignalLike<T>;

    signal.set = (newValue: T) => {
        value = newValue;
        for (const subscriber of subscribers) {
            subscriber();
        }
    };

    signal.update = (fn: (value: T) => T) => {
        value = fn(value);
        for (const subscriber of subscribers) {
            subscriber();
        }
    };

    return signal;
}

/**
 * Create a minimal BehaviorSubject (RxJS-like)
 */
function createBehaviorSubject<T>(initialValue: T): BehaviorSubjectLike<T> {
    let value = initialValue;
    const subscribers = new Set<(value: T) => void>();

    return {
        getValue: () => value,
        next: (newValue: T) => {
            value = newValue;
            for (const subscriber of subscribers) {
                subscriber(value);
            }
        },
        subscribe: (callback: (value: T) => void) => {
            subscribers.add(callback);
            callback(value); // Emit current value immediately (BehaviorSubject behavior)
            return {
                unsubscribe: () => subscribers.delete(callback),
            };
        },
    };
}

/**
 * AngularStateAdapter - Uses Angular-style signals/observables for reactivity
 */
export class AngularStateAdapter implements IStateAdapter {
    private state: Record<string, unknown> = {};
    private originalState: Record<string, unknown> = {};
    private signals: Map<string, SignalLike<unknown>> = new Map();
    private subject: BehaviorSubjectLike<Record<string, unknown>> | null = null;
    private subscriptions: Array<{ unsubscribe(): void }> = [];
    private generalCallbacks: Set<{ callback: StateChangeCallback }> = new Set();
    private keyCallbacks: Map<string, Set<(newVal: unknown, oldVal: unknown) => void>> = new Map();

    /**
     * Initialize state
     */
    init(initialState: Record<string, unknown>): void {
        this.originalState = { ...initialState };
        this.state = { ...initialState };

        // Create signals for each key (Angular 16+ style)
        for (const [key, value] of Object.entries(initialState)) {
            this.signals.set(key, createSignal(value));
        }

        // Create a BehaviorSubject for the full state (RxJS style)
        this.subject = createBehaviorSubject(this.state);
    }

    /**
     * Notify subscribers of state change
     */
    private notifyChange(newValue: unknown, oldValue: unknown, key: string): void {
        // Notify general subscribers
        for (const sub of this.generalCallbacks) {
            sub.callback(newValue, oldValue, key);
        }

        // Notify key-specific subscribers
        const keySubs = this.keyCallbacks.get(key);
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
     * Get a specific state value (supports nested paths like "props.count")
     */
    get<T = unknown>(key: string): T | undefined {
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
        const oldValue = this.get(key);

        if (!key.includes('.')) {
            // Simple key
            this.state[key] = value;

            // Update the signal if it exists
            const signal = this.signals.get(key);
            if (signal) {
                signal.set(value);
            } else {
                this.signals.set(key, createSignal(value));
            }
        } else {
            // Handle nested path
            const parts = key.split('.');
            const lastKey = parts.pop()!;
            let current: Record<string, unknown> = this.state;

            for (const part of parts) {
                if (!(part in current) || typeof current[part] !== 'object' || current[part] === null) {
                    current[part] = {};
                }
                current = current[part] as Record<string, unknown>;
            }

            current[lastKey] = value;

            // Update the root signal
            const rootKey = parts[0];
            if (rootKey) {
                const signal = this.signals.get(rootKey);
                if (signal) {
                    signal.set(this.state[rootKey]);
                }
            }
        }

        // Update the BehaviorSubject
        if (this.subject) {
            this.subject.next({ ...this.state });
        }

        if (oldValue !== value) {
            this.notifyChange(value, oldValue, key);
        }
    }

    /**
     * Set multiple values at once
     */
    setMany(updates: Record<string, unknown>): void {
        for (const [key, value] of Object.entries(updates)) {
            const oldValue = this.state[key];
            this.state[key] = value;

            // Update or create signal
            const signal = this.signals.get(key);
            if (signal) {
                signal.set(value);
            } else {
                this.signals.set(key, createSignal(value));
            }

            if (oldValue !== value) {
                this.notifyChange(value, oldValue, key);
            }
        }

        // Update the BehaviorSubject once
        if (this.subject) {
            this.subject.next({ ...this.state });
        }
    }

    /**
     * Subscribe to all state changes
     */
    subscribe(callback: StateChangeCallback): () => void {
        const subscription = { callback };
        this.generalCallbacks.add(subscription);

        return () => {
            this.generalCallbacks.delete(subscription);
        };
    }

    /**
     * Subscribe to specific key changes
     */
    subscribeKey(
        key: string,
        callback: (newVal: unknown, oldVal: unknown) => void
    ): () => void {
        let keySubs = this.keyCallbacks.get(key);
        if (!keySubs) {
            keySubs = new Set();
            this.keyCallbacks.set(key, keySubs);
        }
        keySubs.add(callback);

        return () => {
            keySubs?.delete(callback);
            if (keySubs?.size === 0) {
                this.keyCallbacks.delete(key);
            }
        };
    }

    /**
     * Get the reactive state (returns the BehaviorSubject for RxJS usage)
     */
    getReactiveState(): BehaviorSubjectLike<Record<string, unknown>> {
        return this.subject ?? createBehaviorSubject(this.state);
    }

    /**
     * Get a signal for a specific key (Angular 16+ usage)
     */
    getSignal<T = unknown>(key: string): SignalLike<T> | undefined {
        return this.signals.get(key) as SignalLike<T> | undefined;
    }

    /**
     * Get the BehaviorSubject for RxJS usage
     */
    getSubject(): BehaviorSubjectLike<Record<string, unknown>> | null {
        return this.subject;
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
     * Select a slice of state (Angular NgRx-like selector)
     */
    select<T>(selector: (state: Record<string, unknown>) => T): T {
        return selector(this.state);
    }

    /**
     * Dispose and cleanup
     */
    dispose(): void {
        // Unsubscribe all
        for (const sub of this.subscriptions) {
            sub.unsubscribe();
        }
        this.subscriptions = [];

        // Clear callbacks
        this.generalCallbacks.clear();
        this.keyCallbacks.clear();

        // Clear signals
        this.signals.clear();

        // Clear state
        this.subject = null;
        this.state = {};
        this.originalState = {};
    }
}

export default AngularStateAdapter;
