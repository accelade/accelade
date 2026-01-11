/**
 * Accelade Vue.js Integration
 * Uses Vue's reactivity system without runtime compilation
 */

import { reactive, watch, effect, type UnwrapNestedRefs } from 'vue';
import type { AcceladeComponentConfig, AcceladeActions } from '../core/types';
import { initRouter, navigate, getRouter, type NavigationOptions } from '../core/router';
import { getProgress, configureProgress, startProgress, doneProgress, type ProgressConfig } from '../core/progress';

/**
 * Custom methods type
 */
type CustomMethods = Record<string, (...args: unknown[]) => unknown>;

/**
 * Parse Accelade element data attributes
 */
function parseAcceladeElement(el: HTMLElement): AcceladeComponentConfig {
    const id = el.dataset.acceladeId ?? `accelade-${Math.random().toString(36).slice(2, 10)}`;
    const stateStr = el.dataset.acceladeState ?? '{}';
    const syncStr = el.dataset.acceladeSync ?? '';

    let state: Record<string, unknown> = {};
    try {
        state = JSON.parse(stateStr) as Record<string, unknown>;
    } catch {
        console.error('Accelade Vue: Invalid state JSON', stateStr);
    }

    const sync = syncStr ? syncStr.split(',').filter(Boolean) : [];

    return { id, state, sync };
}

/**
 * Sync state to server
 */
function syncToServer(componentId: string, property: string, value: unknown): void {
    const config = window.AcceladeConfig;
    const url = config?.updateUrl ?? '/accelade/update';
    const csrfToken = config?.csrfToken ?? '';

    // Show progress bar during sync
    startProgress();

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            component: componentId,
            property: property,
            value: value
        })
    })
    .then(() => {
        doneProgress();
    })
    .catch((err: unknown) => {
        console.error('Accelade Vue: Sync failed', err);
        doneProgress();
    });
}

/**
 * Process <script v-script>, <accelade-script>, or <accelade:script> blocks
 */
function processScripts(
    el: HTMLElement,
    state: UnwrapNestedRefs<Record<string, unknown>>,
    actions: AcceladeActions,
    originalState: Record<string, unknown>
): CustomMethods {
    const customMethods: CustomMethods = {};

    // Find script elements with v-script attribute, accelade-script tag, or accelade:script tag
    // Note: CSS selector needs escaped colon for accelade:script
    const scriptElements = el.querySelectorAll<HTMLScriptElement>('script[v-script], accelade-script, accelade\\:script');

    scriptElements.forEach((scriptEl) => {
        const code = scriptEl.textContent ?? '';
        if (!code.trim()) return;

        try {
            // Create a function with access to state, actions, and helpers
            const scriptFn = new Function(
                'state',
                'actions',
                '$set',
                '$get',
                '$toggle',
                '$navigate',
                '$watch',
                'originalState',
                code
            ) as (
                state: UnwrapNestedRefs<Record<string, unknown>>,
                actions: AcceladeActions,
                $set: (key: string, value: unknown) => void,
                $get: (key: string) => unknown,
                $toggle: (key: string) => void,
                $navigate: (url: string, options?: NavigationOptions) => Promise<boolean>,
                $watch: (key: string, callback: (newVal: unknown, oldVal: unknown) => void) => void,
                originalState: Record<string, unknown>
            ) => CustomMethods | void;

            // Helper functions
            const $set = (key: string, value: unknown): void => {
                state[key] = value;
            };
            const $get = (key: string): unknown => state[key];
            const $toggle = (key: string): void => {
                state[key] = !state[key];
            };
            const $watch = (key: string, callback: (newVal: unknown, oldVal: unknown) => void): void => {
                watch(() => state[key], callback);
            };

            // Execute and get returned methods
            const result = scriptFn(
                state,
                actions,
                $set,
                $get,
                $toggle,
                navigate,
                $watch,
                originalState
            );

            // If the script returns an object with methods, add them to customMethods
            if (result && typeof result === 'object') {
                Object.assign(customMethods, result);
            }

            // Remove the script element from DOM (already executed)
            scriptEl.remove();
        } catch (e) {
            console.error('Accelade Vue: Error executing script:', e);
        }
    });

    return customMethods;
}

/**
 * Initialize an Accelade Vue component
 */
function initVueComponent(el: HTMLElement, config: AcceladeComponentConfig): void {
    // Create reactive state
    const state = reactive({ ...config.state }) as UnwrapNestedRefs<Record<string, unknown>>;
    const originalState = { ...config.state };

    // Create actions
    const actions: AcceladeActions = {
        increment: (key = 'count', amount = 1): void => {
            const currentValue = state[key];
            state[key] = (parseInt(String(currentValue), 10) || 0) + amount;
        },
        decrement: (key = 'count', amount = 1): void => {
            const currentValue = state[key];
            state[key] = (parseInt(String(currentValue), 10) || 0) - amount;
        },
        set: (key: string, value: unknown): void => {
            state[key] = value;
        },
        toggle: (key: string): void => {
            state[key] = !state[key];
        },
        reset: (key: string): void => {
            const initialValue = originalState[key];
            if (typeof initialValue === 'number') {
                state[key] = 0;
            } else if (typeof initialValue === 'boolean') {
                state[key] = false;
            } else {
                state[key] = '';
            }
        },

        get: (key: string): unknown => {
            return state[key];
        },

        // Aliases with $ prefix for template usage
        $set: (key: string, value: unknown): void => {
            state[key] = value;
        },

        $get: (key: string): unknown => {
            return state[key];
        },

        $toggle: (key: string): void => {
            state[key] = !state[key];
        }
    };

    // Process custom scripts first
    const customMethods = processScripts(el, state, actions, originalState);

    // Bind event handlers (v-on:click, @click)
    bindEventHandlers(el, state, actions, customMethods);

    // Setup reactive effects for v-text, v-show, v-if, v-model
    setupReactiveBindings(el, state);

    // Watch for sync properties
    if (config.sync.length > 0) {
        config.sync.forEach(prop => {
            watch(
                () => state[prop],
                (newVal: unknown) => {
                    syncToServer(config.id, prop, newVal);
                }
            );
        });
    }

    // Remove cloak and add ready class for smooth reveal
    el.removeAttribute('data-accelade-cloak');
    el.removeAttribute('v-cloak');
    el.classList.add('accelade-ready');
}

/**
 * Bind event handlers
 */
function bindEventHandlers(
    el: HTMLElement,
    state: UnwrapNestedRefs<Record<string, unknown>>,
    actions: AcceladeActions,
    customMethods: CustomMethods
): void {
    const allElements = el.querySelectorAll<HTMLElement>('*');

    allElements.forEach((element) => {
        Array.from(element.attributes).forEach((attr) => {
            // Handle v-on:event and @event syntax
            if (attr.name.startsWith('v-on:') || attr.name.startsWith('@')) {
                const eventName = attr.name.startsWith('@')
                    ? attr.name.slice(1)
                    : attr.name.slice(5);
                const actionExpr = attr.value;

                element.addEventListener(eventName, (e: Event) => {
                    executeAction(actionExpr, state, actions, customMethods, e);
                });
            }
        });
    });
}

/**
 * Execute an action expression
 */
function executeAction(
    expr: string,
    state: UnwrapNestedRefs<Record<string, unknown>>,
    actions: AcceladeActions,
    customMethods: CustomMethods,
    _event: Event
): void {
    // Parse expressions like:
    // - increment('count')
    // - decrement('count')
    // - toggle('showMessage')
    // - customMethod()

    // Try custom method first (for simple method calls)
    if (customMethods[expr]) {
        (customMethods[expr] as (event: Event) => void)(_event);
        return;
    }

    // Handle method('arg') format
    const match = expr.match(/^(\w+)\s*\(\s*['"]?([^'"]*?)['"]?\s*\)$/);
    if (match) {
        const [, method, arg] = match;

        // Check custom methods first
        if (customMethods[method]) {
            (customMethods[method] as (...args: unknown[]) => void)(arg, _event);
            return;
        }

        const actionMethod = actions[method as keyof AcceladeActions];
        if (typeof actionMethod === 'function') {
            (actionMethod as (key: string) => void)(arg);
            return;
        }
    }

    // Try to evaluate as simple expression
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
            state: UnwrapNestedRefs<Record<string, unknown>>,
            actions: AcceladeActions,
            methods: CustomMethods,
            event: Event,
            $navigate: typeof navigate
        ) => void;
        fn(state, actions, customMethods, _event, navigate);
    } catch {
        // Action execution failed
    }
}

/**
 * Setup reactive bindings
 */
function setupReactiveBindings(
    el: HTMLElement,
    state: UnwrapNestedRefs<Record<string, unknown>>
): void {
    // v-text binding
    const vTextElements = el.querySelectorAll<HTMLElement>('[v-text]');
    vTextElements.forEach((element) => {
        const prop = element.getAttribute('v-text');
        if (!prop) return;

        effect(() => {
            const value = getNestedValue(state, prop);
            element.textContent = value !== undefined ? String(value) : '';
        });
    });

    // v-show binding
    const vShowElements = el.querySelectorAll<HTMLElement>('[v-show]');
    vShowElements.forEach((element) => {
        const expr = element.getAttribute('v-show');
        if (!expr) return;

        effect(() => {
            const result = evaluateExpression(expr, state);
            element.style.display = result ? '' : 'none';
        });
    });

    // v-if binding
    const vIfElements = el.querySelectorAll<HTMLElement>('[v-if]');
    vIfElements.forEach((element) => {
        const expr = element.getAttribute('v-if');
        if (!expr) return;

        const placeholder = document.createComment('v-if');
        let isInserted = true;

        effect(() => {
            const result = evaluateExpression(expr, state);
            if (result && !isInserted) {
                placeholder.replaceWith(element);
                isInserted = true;
            } else if (!result && isInserted) {
                element.replaceWith(placeholder);
                isInserted = false;
            }
        });
    });

    // v-model binding (two-way)
    const vModelElements = el.querySelectorAll<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>('[v-model]');
    vModelElements.forEach((element) => {
        const prop = element.getAttribute('v-model');
        if (!prop) return;

        const inputElement = element as HTMLInputElement;
        const eventType = (inputElement.type === 'checkbox' || inputElement.type === 'radio') ? 'change' : 'input';

        // Update DOM from state
        effect(() => {
            const value = state[prop];
            if (inputElement.type === 'checkbox') {
                inputElement.checked = Boolean(value);
            } else if (inputElement.type === 'radio') {
                inputElement.checked = inputElement.value === value;
            } else if (element.value !== String(value ?? '')) {
                element.value = value !== undefined ? String(value) : '';
            }
        });

        // Update state from DOM
        element.addEventListener(eventType, () => {
            if (inputElement.type === 'checkbox') {
                state[prop] = inputElement.checked;
            } else if (inputElement.type === 'radio') {
                state[prop] = inputElement.value;
            } else if (inputElement.type === 'number' || inputElement.type === 'range') {
                state[prop] = parseFloat(element.value) || 0;
            } else {
                state[prop] = element.value;
            }
        });
    });

    // v-bind:attr binding
    const allElements = el.querySelectorAll<HTMLElement>('*');
    allElements.forEach((element) => {
        Array.from(element.attributes).forEach((attr) => {
            if (attr.name.startsWith('v-bind:') || (attr.name.startsWith(':') && !attr.name.startsWith('::'))) {
                const attrName = attr.name.startsWith(':')
                    ? attr.name.slice(1)
                    : attr.name.slice(7);
                const expr = attr.value;

                effect(() => {
                    const value = evaluateExpression(expr, state);
                    if (attrName === 'class') {
                        if (typeof value === 'object' && value !== null) {
                            Object.entries(value as Record<string, boolean>).forEach(([className, condition]) => {
                                element.classList.toggle(className, Boolean(condition));
                            });
                        } else {
                            element.setAttribute('class', String(value));
                        }
                    } else if (value === false || value === null || value === undefined) {
                        element.removeAttribute(attrName);
                    } else if (value === true) {
                        element.setAttribute(attrName, '');
                    } else {
                        element.setAttribute(attrName, String(value));
                    }
                });
            }
        });
    });
}

/**
 * Evaluate an expression against state
 */
function evaluateExpression(
    expr: string,
    state: UnwrapNestedRefs<Record<string, unknown>>
): unknown {
    try {
        // Simple property access
        if (/^[\w.]+$/.test(expr)) {
            return getNestedValue(state, expr);
        }

        // Create evaluation function
        const keys = Object.keys(state);
        const values = Object.values(state);
        const fn = new Function(...keys, 'return ' + expr) as (...args: unknown[]) => unknown;
        return fn(...values);
    } catch {
        return undefined;
    }
}

/**
 * Get nested value from object
 */
function getNestedValue(obj: Record<string, unknown>, path: string): unknown {
    return path.split('.').reduce<unknown>(
        (current, key) => {
            if (current !== null && typeof current === 'object') {
                return (current as Record<string, unknown>)[key];
            }
            return undefined;
        },
        obj
    );
}

/**
 * Extended HTMLElement with initialization flag
 */
interface AcceladeHTMLElement extends HTMLElement {
    __accelade_vue_initialized?: boolean;
}

// Module-level initialization guard
let acceladeInitialized = false;

/**
 * Initialize all Accelade components on the page
 */
export function init(): void {
    const elements = document.querySelectorAll<AcceladeHTMLElement>('[data-accelade]');

    elements.forEach((el) => {
        if (el.__accelade_vue_initialized) return;

        const config = parseAcceladeElement(el);

        try {
            initVueComponent(el, config);
            el.__accelade_vue_initialized = true;
        } catch (e) {
            console.error('Accelade Vue: Failed to init component', config.id, e);
        }
    });

    // Initialize the router only once
    if (!acceladeInitialized) {
        initRouter();
        acceladeInitialized = true;
    }
}

// Progress API object
const progress = {
    configure: configureProgress,
    start: startProgress,
    done: doneProgress,
    instance: getProgress,
};

// Export for window
if (typeof window !== 'undefined') {
    // Configure progress from AcceladeConfig if available
    const progressConfig = window.AcceladeConfig?.progress;
    if (progressConfig && Object.keys(progressConfig).length > 0) {
        configureProgress(progressConfig as ProgressConfig);
    }

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => init());
    } else {
        init();
    }
}

// Export everything including progress
export { navigate, getRouter, initRouter, configureProgress, startProgress, doneProgress, progress };
export default { init, navigate, getRouter, initRouter, configureProgress, startProgress, doneProgress, progress };
