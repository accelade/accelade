/**
 * React Hooks for Accelade
 */

import {
    useState,
    useEffect,
    useCallback,
    useMemo,
    useRef,
    type Dispatch,
    type SetStateAction,
} from 'react';
import type { AcceladeActions } from '../../core/types';
import { navigate, type NavigationOptions } from '../../core/router';
import { SyncFactory } from '../../core/factories/SyncFactory';

/**
 * State type
 */
type StateRecord = Record<string, unknown>;

/**
 * Custom methods type
 */
type CustomMethods = Record<string, (...args: unknown[]) => unknown>;

/**
 * Hook result type
 */
export interface UseAcceladeResult<T extends StateRecord> {
    state: T;
    setState: Dispatch<SetStateAction<T>>;
    actions: AcceladeActions;
    customMethods: CustomMethods;
    $set: (key: string, value: unknown) => void;
    $get: (key: string) => unknown;
    $toggle: (key: string) => void;
    $navigate: (url: string, options?: NavigationOptions) => Promise<boolean>;
}

/**
 * Options for useAccelade hook
 */
export interface UseAcceladeOptions {
    componentId?: string;
    syncProperties?: string[];
    scripts?: string[];
    debounceSync?: number;
}

/**
 * useAccelade hook - React-style state management for Accelade
 *
 * @example
 * ```tsx
 * function Counter() {
 *   const { state, actions } = useAccelade({ count: 0 });
 *   return (
 *     <div>
 *       <span>{state.count}</span>
 *       <button onClick={() => actions.increment('count')}>+</button>
 *     </div>
 *   );
 * }
 * ```
 */
export function useAccelade<T extends StateRecord>(
    initialState: T,
    options: UseAcceladeOptions = {}
): UseAcceladeResult<T> {
    const [state, setState] = useState<T>(initialState);
    const { componentId, syncProperties = [], scripts = [], debounceSync = 300 } = options;
    const customMethodsRef = useRef<CustomMethods>({});
    const originalStateRef = useRef<T>(initialState);
    const prevSyncValuesRef = useRef<Record<string, unknown>>({});

    // Sync effect - debounced
    useEffect(() => {
        if (!componentId || syncProperties.length === 0) return;

        // Check which properties changed
        const changedProps = syncProperties.filter((prop) => {
            const prevValue = prevSyncValuesRef.current[prop];
            const currentValue = state[prop];
            return prevValue !== currentValue;
        });

        if (changedProps.length === 0) return;

        // Update prev values
        for (const prop of changedProps) {
            prevSyncValuesRef.current[prop] = state[prop];
        }

        // Batch sync changed properties
        const updates: Record<string, unknown> = {};
        for (const prop of changedProps) {
            updates[prop] = state[prop];
        }

        void SyncFactory.batchSync(componentId, updates, { debounce: debounceSync });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [componentId, debounceSync, ...syncProperties.map((prop) => state[prop])]);

    // Helper functions
    const $set = useCallback((key: string, value: unknown): void => {
        setState((prev) => ({ ...prev, [key]: value }));
    }, []);

    const $get = useCallback(
        (key: string): unknown => {
            return state[key];
        },
        [state]
    );

    const $toggle = useCallback((key: string): void => {
        setState((prev) => ({ ...prev, [key]: !prev[key] }));
    }, []);

    const $navigate = useCallback((url: string, options?: NavigationOptions): Promise<boolean> => {
        return navigate(url, options);
    }, []);

    // Actions
    const actions = useMemo<AcceladeActions>(
        () => ({
            increment: (key = 'count', amount = 1): void => {
                setState((prev) => ({
                    ...prev,
                    [key]: (parseInt(String(prev[key]), 10) || 0) + amount,
                }));
            },

            decrement: (key = 'count', amount = 1): void => {
                setState((prev) => ({
                    ...prev,
                    [key]: (parseInt(String(prev[key]), 10) || 0) - amount,
                }));
            },

            set: (key: string, value: unknown): void => {
                setState((prev) => ({ ...prev, [key]: value }));
            },

            toggle: (key: string): void => {
                setState((prev) => ({ ...prev, [key]: !prev[key] }));
            },

            reset: (key: string): void => {
                const original = originalStateRef.current;
                setState((prev) => ({
                    ...prev,
                    [key]:
                        typeof original[key] === 'number'
                            ? 0
                            : typeof original[key] === 'boolean'
                              ? false
                              : '',
                }));
            },
        }),
        []
    );

    // Process scripts on mount
    useEffect(() => {
        scripts.forEach((code) => {
            if (!code.trim()) return;

            try {
                const scriptFn = new Function(
                    'state',
                    'setState',
                    'actions',
                    '$set',
                    '$get',
                    '$toggle',
                    '$navigate',
                    'initialState',
                    code
                ) as (
                    state: T,
                    setState: Dispatch<SetStateAction<T>>,
                    actions: AcceladeActions,
                    $set: (key: string, value: unknown) => void,
                    $get: (key: string) => unknown,
                    $toggle: (key: string) => void,
                    $navigate: (url: string, options?: NavigationOptions) => Promise<boolean>,
                    initialState: T
                ) => CustomMethods | void;

                const result = scriptFn(
                    state,
                    setState,
                    actions,
                    $set,
                    $get,
                    $toggle,
                    $navigate,
                    initialState
                );

                if (result && typeof result === 'object') {
                    Object.assign(customMethodsRef.current, result);
                }
            } catch (e) {
                console.error('Accelade React: Error executing script:', e);
            }
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return {
        state,
        setState,
        actions,
        customMethods: customMethodsRef.current,
        $set,
        $get,
        $toggle,
        $navigate,
    };
}

/**
 * useAcceladeSync - Simplified hook just for syncing state to server
 */
export function useAcceladeSync<T extends StateRecord>(
    componentId: string,
    state: T,
    syncProperties: string[],
    debounce = 300
): void {
    const prevValuesRef = useRef<Record<string, unknown>>({});

    useEffect(() => {
        const changedProps = syncProperties.filter((prop) => {
            return prevValuesRef.current[prop] !== state[prop];
        });

        if (changedProps.length === 0) return;

        // Update prev values
        for (const prop of changedProps) {
            prevValuesRef.current[prop] = state[prop];
        }

        // Batch sync
        const updates: Record<string, unknown> = {};
        for (const prop of changedProps) {
            updates[prop] = state[prop];
        }

        void SyncFactory.batchSync(componentId, updates, { debounce });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [componentId, debounce, ...syncProperties.map((prop) => state[prop])]);
}

export default useAccelade;
