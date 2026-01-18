/**
 * ScriptExecutor - Execute custom scripts in component context
 */

import type { AcceladeActions, AcceladeComponentConfig } from '../types';
import type { NavigationOptions } from '../router';
import { navigate } from '../router';

/**
 * Custom methods returned from script execution
 * Uses a more flexible function signature to accommodate various method types
 */
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export type CustomMethods = Record<string, (...args: any[]) => any>;

/**
 * Event callback type
 */
export type EventCallback<T = unknown> = (data: T) => void;

/**
 * Script helper functions
 */
export interface ScriptHelpers {
    $set: (key: string, value: unknown) => void;
    $get: (key: string) => unknown;
    $toggle: (key: string) => void;
    $watch?: (key: string, callback: (newVal: unknown, oldVal: unknown) => void) => void;
    $emit?: <T = unknown>(event: string, data?: T) => void;
    $on?: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
    $once?: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
    $off?: <T = unknown>(event: string, callback: EventCallback<T>) => void;
}

/**
 * Script execution context
 */
export interface ScriptContext {
    state: Record<string, unknown>;
    actions: AcceladeActions;
    helpers: ScriptHelpers;
    setState: (key: string, value: unknown) => void;
    getState: () => Record<string, unknown>;
    originalState: Record<string, unknown>;
}

/**
 * Framework-specific script attribute names
 */
export type FrameworkType = 'vanilla' | 'vue' | 'react' | 'svelte' | 'angular';

const SCRIPT_SELECTORS: Record<FrameworkType, string> = {
    vanilla: 'script[a-script], accelade-script, accelade\\:script',
    vue: 'script[v-script], accelade-script, accelade\\:script',
    react: 'script[state-script], accelade-script, accelade\\:script',
    svelte: 'script[s-script], accelade-script, accelade\\:script',
    angular: 'script[ng-script], accelade-script, accelade\\:script',
};

/**
 * ScriptExecutor - Handles execution of inline scripts in components
 */
export class ScriptExecutor {
    /**
     * Get script selector for a framework
     */
    static getSelector(framework: FrameworkType): string {
        return SCRIPT_SELECTORS[framework] || SCRIPT_SELECTORS.vanilla;
    }

    /**
     * Find and extract script elements from a component
     */
    static findScripts(element: HTMLElement, framework: FrameworkType): HTMLScriptElement[] {
        const selector = this.getSelector(framework);
        return Array.from(element.querySelectorAll<HTMLScriptElement>(selector));
    }

    /**
     * Execute scripts and return custom methods
     */
    static execute(
        element: HTMLElement,
        context: ScriptContext,
        framework: FrameworkType = 'vanilla'
    ): CustomMethods {
        const customMethods: CustomMethods = {};
        const scriptElements = this.findScripts(element, framework);

        for (const scriptEl of scriptElements) {
            const code = scriptEl.textContent ?? '';
            if (!code.trim()) continue;

            try {
                const methods = this.executeScript(code, context);
                if (methods && typeof methods === 'object') {
                    Object.assign(customMethods, methods);
                }

                // Remove the script element from DOM (already executed)
                scriptEl.remove();
            } catch (e) {
                console.error('Accelade: Error executing script:', e);
            }
        }

        return customMethods;
    }

    /**
     * Execute a single script code block
     */
    static executeScript(code: string, context: ScriptContext): CustomMethods | void {
        const { state, actions, helpers, originalState } = context;
        const { $set, $get, $toggle, $watch, $emit, $on, $once, $off } = helpers;

        // Build function parameters based on available helpers
        const paramNames = [
            'state',
            'actions',
            '$set',
            '$get',
            '$toggle',
            '$navigate',
            'originalState',
            '$emit',
            '$on',
            '$once',
            '$off',
        ];

        const paramValues: unknown[] = [
            state,
            actions,
            $set,
            $get,
            $toggle,
            navigate,
            originalState,
            $emit,
            $on,
            $once,
            $off,
        ];

        // Add $watch if available (Vue-specific)
        if ($watch) {
            paramNames.push('$watch');
            paramValues.push($watch);
        }

        // Create the function with all context variables
        const scriptFn = new Function(...paramNames, code) as (
            ...args: unknown[]
        ) => CustomMethods | void;

        // Execute and return the result
        return scriptFn(...paramValues);
    }

    /**
     * Event bus methods interface
     */
    static eventBusMethods?: {
        emit: <T = unknown>(event: string, data?: T) => void;
        on: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
        once: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
        off: <T = unknown>(event: string, callback: EventCallback<T>) => void;
    };

    /**
     * Set event bus methods (called once during initialization)
     */
    static setEventBusMethods(methods: {
        emit: <T = unknown>(event: string, data?: T) => void;
        on: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
        once: <T = unknown>(event: string, callback: EventCallback<T>) => () => void;
        off: <T = unknown>(event: string, callback: EventCallback<T>) => void;
    }): void {
        this.eventBusMethods = methods;
    }

    /**
     * Create script helpers from state accessors
     */
    static createHelpers(
        getState: () => Record<string, unknown>,
        setState: (key: string, value: unknown) => void,
        watchFn?: (key: string, callback: (newVal: unknown, oldVal: unknown) => void) => void
    ): ScriptHelpers {
        const helpers: ScriptHelpers = {
            $set: setState,
            $get: (key: string): unknown => getState()[key],
            $toggle: (key: string): void => {
                const state = getState();
                setState(key, !state[key]);
            },
        };

        if (watchFn) {
            helpers.$watch = watchFn;
        }

        // Add event bus methods if available
        if (this.eventBusMethods) {
            helpers.$emit = this.eventBusMethods.emit;
            helpers.$on = this.eventBusMethods.on;
            helpers.$once = this.eventBusMethods.once;
            helpers.$off = this.eventBusMethods.off;
        }

        return helpers;
    }

    /**
     * Execute an action expression string
     */
    static executeAction(
        expr: string,
        state: Record<string, unknown>,
        actions: AcceladeActions,
        customMethods: CustomMethods,
        event?: Event
    ): void {
        // Try custom method first (simple call like "myMethod")
        if (customMethods[expr]) {
            (customMethods[expr] as (event?: Event) => void)(event);
            return;
        }

        // Handle action:key format (e.g., "increment:count", "toggle:showMessage")
        if (expr.includes(':') && !expr.includes('(')) {
            const [action, key] = expr.split(':');

            // Check custom methods first
            if (customMethods[action]) {
                (customMethods[action] as (key: string, event?: Event) => void)(key, event);
                return;
            }

            const actionMethod = actions[action as keyof AcceladeActions];
            if (typeof actionMethod === 'function') {
                (actionMethod as (key: string) => void)(key);
                return;
            }
        }

        // Handle method('arg') format
        const match = expr.match(/^(\w+)\s*\(\s*['"]?([^'"]*?)['"]?\s*\)$/);
        if (match) {
            const [, method, arg] = match;

            // Check custom methods first
            if (customMethods[method]) {
                (customMethods[method] as (...args: unknown[]) => void)(arg, event);
                return;
            }

            const actionMethod = actions[method as keyof AcceladeActions];
            if (typeof actionMethod === 'function') {
                (actionMethod as (key: string) => void)(arg);
                return;
            }
        }

        // Try direct action name (e.g., "increment")
        const directAction = actions[expr as keyof AcceladeActions];
        if (typeof directAction === 'function') {
            (directAction as () => void)();
            return;
        }

        // Try to evaluate as expression (dangerous but flexible)
        try {
            const fn = new Function('state', 'actions', 'methods', 'event', '$navigate', `
                with (state) {
                    with (actions) {
                        with (methods) {
                            ${expr}
                        }
                    }
                }
            `) as (
                state: Record<string, unknown>,
                actions: AcceladeActions,
                methods: CustomMethods,
                event: Event | undefined,
                navigate: typeof import('../router').navigate
            ) => void;

            fn(state, actions, customMethods, event, navigate);
        } catch {
            // Action execution failed silently
        }
    }
}

export default ScriptExecutor;
