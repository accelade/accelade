/**
 * ActionsFactory - Create standard state actions
 */

import type { AcceladeActions } from '../types';
import { emit as eventBusEmit, on as eventBusOn, once as eventBusOnce, off as eventBusOff, type EventCallback } from '../events';

/**
 * State getter function type
 */
export type StateGetter = () => Record<string, unknown>;

/**
 * State setter function type
 */
export type StateSetter = (key: string, value: unknown) => void;

/**
 * ActionsFactory - Creates standard actions for state manipulation
 */
export class ActionsFactory {
    /**
     * Create standard actions for a component
     */
    static create(
        getState: StateGetter,
        setState: StateSetter,
        originalState: Record<string, unknown>
    ): AcceladeActions {
        // Helper functions with $ prefix (common usage in templates)
        const $set = (key: string, value: unknown): void => {
            setState(key, value);
        };

        const $get = (key: string): unknown => {
            return getState()[key];
        };

        const $toggle = (key: string): void => {
            const state = getState();
            setState(key, !state[key]);
        };

        return {
            /**
             * Increment a numeric value
             */
            increment: (key = 'count', amount = 1): void => {
                const state = getState();
                const currentValue = state[key];
                const numValue = parseInt(String(currentValue), 10) || 0;
                setState(key, numValue + amount);
            },

            /**
             * Decrement a numeric value
             */
            decrement: (key = 'count', amount = 1): void => {
                const state = getState();
                const currentValue = state[key];
                const numValue = parseInt(String(currentValue), 10) || 0;
                setState(key, numValue - amount);
            },

            /**
             * Set a value directly
             */
            set: $set,

            /**
             * Toggle a boolean value
             */
            toggle: $toggle,

            /**
             * Get a value (for completeness)
             */
            get: $get,

            /**
             * Reset a value to its default (based on original type)
             */
            reset: (key: string): void => {
                const originalValue = originalState[key];

                if (typeof originalValue === 'number') {
                    setState(key, 0);
                } else if (typeof originalValue === 'boolean') {
                    setState(key, false);
                } else if (Array.isArray(originalValue)) {
                    setState(key, []);
                } else if (originalValue !== null && typeof originalValue === 'object') {
                    setState(key, {});
                } else {
                    setState(key, '');
                }
            },

            // Aliases with $ prefix for template usage
            $set,
            $get,
            $toggle,

            // Event bus methods
            $emit: eventBusEmit,
            $on: eventBusOn,
            $once: eventBusOnce,
            $off: eventBusOff,
        };
    }

    /**
     * Create extended actions with additional methods
     */
    static createExtended(
        getState: StateGetter,
        setState: StateSetter,
        originalState: Record<string, unknown>
    ): AcceladeActions & ExtendedActions {
        const baseActions = this.create(getState, setState, originalState);

        return {
            ...baseActions,

            /**
             * Push an item to an array
             */
            push: (key: string, value: unknown): void => {
                const state = getState();
                const arr = Array.isArray(state[key]) ? [...(state[key] as unknown[])] : [];
                arr.push(value);
                setState(key, arr);
            },

            /**
             * Remove an item from an array by index
             */
            remove: (key: string, index: number): void => {
                const state = getState();
                if (Array.isArray(state[key])) {
                    const arr = [...(state[key] as unknown[])];
                    arr.splice(index, 1);
                    setState(key, arr);
                }
            },

            /**
             * Clear an array or object
             */
            clear: (key: string): void => {
                const state = getState();
                if (Array.isArray(state[key])) {
                    setState(key, []);
                } else if (state[key] !== null && typeof state[key] === 'object') {
                    setState(key, {});
                } else {
                    setState(key, '');
                }
            },

            /**
             * Reset to original value
             */
            resetToOriginal: (key: string): void => {
                const originalValue = originalState[key];
                setState(key, originalValue !== undefined ? JSON.parse(JSON.stringify(originalValue)) : undefined);
            },

            /**
             * Reset all state to original
             */
            resetAll: (): void => {
                for (const key of Object.keys(originalState)) {
                    const originalValue = originalState[key];
                    setState(key, JSON.parse(JSON.stringify(originalValue)));
                }
            },

            /**
             * Multiply a numeric value
             */
            multiply: (key: string, factor: number): void => {
                const state = getState();
                const currentValue = state[key];
                const numValue = parseFloat(String(currentValue)) || 0;
                setState(key, numValue * factor);
            },

            /**
             * Append to a string
             */
            append: (key: string, value: string): void => {
                const state = getState();
                const currentValue = String(state[key] ?? '');
                setState(key, currentValue + value);
            },
        };
    }
}

/**
 * Extended actions interface
 */
export interface ExtendedActions {
    push: (key: string, value: unknown) => void;
    remove: (key: string, index: number) => void;
    clear: (key: string) => void;
    resetToOriginal: (key: string) => void;
    resetAll: () => void;
    multiply: (key: string, factor: number) => void;
    append: (key: string, value: string) => void;
}

export default ActionsFactory;
