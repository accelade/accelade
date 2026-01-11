/**
 * Accelade Vanilla JavaScript Integration
 * Pure JavaScript reactivity without any framework dependencies
 */

import type { AcceladeComponentConfig, AcceladeActions } from '../core/types';
import { initRouter, navigate, getRouter, type NavigationOptions } from '../core/router';
import { getProgress, configureProgress, startProgress, doneProgress, type ProgressConfig } from '../core/progress';

/**
 * Reactive state with Proxy
 */
type ReactiveState = Record<string, unknown>;

/**
 * Component instance
 */
interface ComponentInstance {
    id: string;
    element: HTMLElement;
    state: ReactiveState;
    originalState: ReactiveState;
    templates: Map<HTMLElement, string>;
    actions: AcceladeActions;
    syncProperties: Set<string>;
    customMethods: Record<string, (...args: unknown[]) => unknown>;
}

/**
 * Extended HTMLElement with initialization flag
 */
interface AcceladeHTMLElement extends HTMLElement {
    __accelade_vanilla_initialized?: boolean;
}

/**
 * Main Accelade manager
 */
class AcceladeManager {
    private components: Map<string, ComponentInstance> = new Map();
    private initialized = false;

    /**
     * Parse element configuration
     */
    private parseAcceladeElement(el: HTMLElement): AcceladeComponentConfig {
        const id = el.dataset.acceladeId ?? `accelade-${Math.random().toString(36).slice(2, 10)}`;
        const stateStr = el.dataset.acceladeState ?? '{}';
        const syncStr = el.dataset.acceladeSync ?? '';

        let state: Record<string, unknown> = {};
        try {
            state = JSON.parse(stateStr) as Record<string, unknown>;
        } catch {
            console.error('Accelade: Invalid state JSON', stateStr);
        }

        const sync = syncStr ? syncStr.split(',').filter(Boolean) : [];

        return { id, state, sync };
    }

    /**
     * Create reactive proxy for state
     */
    private createReactiveProxy(component: ComponentInstance, obj: ReactiveState): ReactiveState {
        const manager = this;

        return new Proxy(obj, {
            set(target: ReactiveState, prop: string, value: unknown): boolean {
                const oldValue = target[prop];
                target[prop] = value;

                if (oldValue !== value) {
                    manager.updateComponent(component);

                    // Sync to server if property is marked for sync
                    if (component.syncProperties.has(prop)) {
                        manager.syncToServer(component.id, prop, value);
                    }
                }

                return true;
            },
            get(target: ReactiveState, prop: string): unknown {
                return target[prop];
            }
        });
    }

    /**
     * Sync state to server
     */
    private syncToServer(componentId: string, property: string, value: unknown): void {
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
        .catch(() => {
            doneProgress();
        });
    }

    /**
     * Create actions for a component
     */
    private createActions(component: ComponentInstance): AcceladeActions {
        return {
            increment: (key = 'count', amount = 1): void => {
                const currentValue = component.state[key];
                component.state[key] = (parseInt(String(currentValue), 10) || 0) + amount;
            },
            decrement: (key = 'count', amount = 1): void => {
                const currentValue = component.state[key];
                component.state[key] = (parseInt(String(currentValue), 10) || 0) - amount;
            },
            set: (key: string, value: unknown): void => {
                component.state[key] = value;
            },
            toggle: (key: string): void => {
                component.state[key] = !component.state[key];
            },
            reset: (key: string): void => {
                const originalValue = component.originalState[key];
                if (typeof originalValue === 'number') {
                    component.state[key] = 0;
                } else if (typeof originalValue === 'boolean') {
                    component.state[key] = false;
                } else {
                    component.state[key] = '';
                }
            },

            get: (key: string): unknown => {
                return component.state[key];
            },

            // Aliases with $ prefix for template usage
            $set: (key: string, value: unknown): void => {
                component.state[key] = value;
            },

            $get: (key: string): unknown => {
                return component.state[key];
            },

            $toggle: (key: string): void => {
                component.state[key] = !component.state[key];
            }
        };
    }

    /**
     * Process <script a-script>, <accelade-script>, or <accelade:script> blocks
     */
    private processScripts(component: ComponentInstance): void {
        const el = component.element;

        // Find script elements with a-script attribute, accelade-script tag, or accelade:script tag
        // Note: CSS selector needs escaped colon for accelade:script
        const scriptElements = el.querySelectorAll<HTMLScriptElement>('script[a-script], accelade-script, accelade\\:script');

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
                    'component',
                    code
                ) as (
                    state: ReactiveState,
                    actions: AcceladeActions,
                    $set: (key: string, value: unknown) => void,
                    $get: (key: string) => unknown,
                    $toggle: (key: string) => void,
                    $navigate: (url: string, options?: NavigationOptions) => Promise<boolean>,
                    component: ComponentInstance
                ) => Record<string, (...args: unknown[]) => unknown> | void;

                // Helper functions
                const $set = (key: string, value: unknown): void => {
                    component.state[key] = value;
                };
                const $get = (key: string): unknown => component.state[key];
                const $toggle = (key: string): void => {
                    component.state[key] = !component.state[key];
                };

                // Execute and get returned methods
                const result = scriptFn(
                    component.state,
                    component.actions,
                    $set,
                    $get,
                    $toggle,
                    navigate,
                    component
                );

                // If the script returns an object with methods, add them to customMethods
                if (result && typeof result === 'object') {
                    Object.assign(component.customMethods, result);
                }

                // Remove the script element from DOM (already executed)
                scriptEl.remove();
            } catch (e) {
                console.error('Accelade: Error executing script:', e);
            }
        });
    }

    /**
     * Initialize a component
     */
    initComponent(el: AcceladeHTMLElement): void {
        if (el.__accelade_vanilla_initialized) return;

        const config = this.parseAcceladeElement(el);

        // Create component instance
        const component: ComponentInstance = {
            id: config.id,
            element: el,
            state: {},
            originalState: { ...config.state },
            templates: new Map(),
            actions: {} as AcceladeActions,
            syncProperties: new Set(config.sync),
            customMethods: {}
        };

        // Create reactive state
        component.state = this.createReactiveProxy(component, { ...config.state });

        // Create actions
        component.actions = this.createActions(component);

        // Store component
        this.components.set(config.id, component);

        // Process custom scripts before binding events
        this.processScripts(component);

        // Store templates for elements that need them
        this.storeTemplates(component);

        // Bind event handlers
        this.bindEventHandlers(component);

        // Initial render
        this.updateComponent(component);

        // Remove cloak and add ready class for smooth reveal
        el.removeAttribute('data-accelade-cloak');
        el.removeAttribute('a-cloak');
        el.classList.add('accelade-ready');

        el.__accelade_vanilla_initialized = true;
    }

    /**
     * Store templates for elements
     */
    private storeTemplates(component: ComponentInstance): void {
        // Store a-for templates
        const forElements = component.element.querySelectorAll<HTMLElement>('[a-for]');
        forElements.forEach(el => {
            component.templates.set(el, el.innerHTML);
        });
    }

    /**
     * Bind event handlers
     */
    private bindEventHandlers(component: ComponentInstance): void {
        const allElements = component.element.querySelectorAll<HTMLElement>('*');

        allElements.forEach((element) => {
            Array.from(element.attributes).forEach((attr) => {
                let eventName: string | null = null;
                let actionExpr: string | null = null;

                // Handle @event syntax (primary)
                if (attr.name.startsWith('@')) {
                    eventName = attr.name.slice(1);
                    actionExpr = attr.value;
                }
                // Handle a-on:event syntax (backward compatibility)
                else if (attr.name.startsWith('a-on:')) {
                    eventName = attr.name.slice(5);
                    actionExpr = attr.value;
                }

                if (eventName && actionExpr) {
                    element.addEventListener(eventName, (e: Event) => {
                        this.executeAction(component, actionExpr!, e);
                    });
                }
            });
        });
    }

    /**
     * Execute an action expression
     */
    private executeAction(component: ComponentInstance, expr: string, _event: Event): void {
        // Handle action:key format (e.g., "increment:count", "toggle:showMessage")
        if (expr.includes(':')) {
            const [action, key] = expr.split(':');

            // Check custom methods first
            if (component.customMethods[action]) {
                (component.customMethods[action] as (key: string, event: Event) => void)(key, _event);
                return;
            }

            const actionMethod = component.actions[action as keyof AcceladeActions];
            if (typeof actionMethod === 'function') {
                (actionMethod as (key: string) => void)(key);
                return;
            }
        }

        // Handle method('key') format
        const match = expr.match(/^(\w+)\s*\(\s*['"]?([^'"]*?)['"]?\s*\)$/);
        if (match) {
            const [, method, arg] = match;

            // Check custom methods first
            if (component.customMethods[method]) {
                (component.customMethods[method] as (...args: unknown[]) => void)(arg, _event);
                return;
            }

            const actionMethod = component.actions[method as keyof AcceladeActions];
            if (typeof actionMethod === 'function') {
                (actionMethod as (key: string) => void)(arg);
                return;
            }
        }

        // Try custom method directly
        if (component.customMethods[expr]) {
            (component.customMethods[expr] as (event: Event) => void)(_event);
            return;
        }

        // Try direct action name
        const directAction = component.actions[expr as keyof AcceladeActions];
        if (typeof directAction === 'function') {
            (directAction as () => void)();
            return;
        }

        // Try to evaluate as expression
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
                state: ReactiveState,
                actions: AcceladeActions,
                methods: Record<string, (...args: unknown[]) => unknown>,
                event: Event,
                $navigate: typeof navigate
            ) => void;
            fn(component.state, component.actions, component.customMethods, _event, navigate);
        } catch {
            // Action execution failed
        }
    }

    /**
     * Update a component's DOM
     */
    private updateComponent(component: ComponentInstance): void {
        const el = component.element;
        const state = component.state;

        // Update the data-accelade-state attribute so MutationObservers can detect changes
        // This is important for lazy loading conditional triggers
        el.dataset.acceladeState = JSON.stringify(state);

        // a-text binding
        const textElements = el.querySelectorAll<HTMLElement>('[a-text]');
        textElements.forEach((element) => {
            const prop = element.getAttribute('a-text');
            if (!prop) return;

            const value = this.getNestedValue(state, prop);
            element.textContent = value !== undefined ? String(value) : '';
        });

        // a-show binding
        const showElements = el.querySelectorAll<HTMLElement>('[a-show]');
        showElements.forEach((element) => {
            const expr = element.getAttribute('a-show');
            if (!expr) return;

            const result = this.evaluateExpression(expr, state);
            element.style.display = result ? '' : 'none';
        });

        // a-if binding
        const ifElements = el.querySelectorAll<HTMLElement>('[a-if]');
        ifElements.forEach((element) => {
            const expr = element.getAttribute('a-if');
            if (!expr) return;

            const result = this.evaluateExpression(expr, state);

            // Store reference to placeholder
            const placeholderKey = '__accelade_placeholder';
            const elementWithPlaceholder = element as HTMLElement & { [placeholderKey]?: Comment };

            if (!result) {
                if (!elementWithPlaceholder[placeholderKey]) {
                    const placeholder = document.createComment('a-if');
                    elementWithPlaceholder[placeholderKey] = placeholder;
                }
                if (element.parentNode) {
                    element.parentNode.replaceChild(elementWithPlaceholder[placeholderKey]!, element);
                }
            } else if (elementWithPlaceholder[placeholderKey]?.parentNode) {
                elementWithPlaceholder[placeholderKey]!.parentNode.replaceChild(element, elementWithPlaceholder[placeholderKey]!);
            }
        });

        // a-model binding (two-way)
        const modelElements = el.querySelectorAll<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>('[a-model]');
        modelElements.forEach((element) => {
            const prop = element.getAttribute('a-model');
            if (!prop) return;

            const inputElement = element as HTMLInputElement;
            const value = state[prop];

            // Update DOM from state
            if (inputElement.type === 'checkbox') {
                if (inputElement.checked !== Boolean(value)) {
                    inputElement.checked = Boolean(value);
                }
            } else if (inputElement.type === 'radio') {
                inputElement.checked = inputElement.value === value;
            } else if (element.value !== String(value ?? '')) {
                element.value = value !== undefined ? String(value) : '';
            }

            // Bind input event if not already bound
            const boundKey = '__accelade_model_bound';
            const elementWithBound = element as typeof element & { [boundKey]?: boolean };
            if (!elementWithBound[boundKey]) {
                const eventType = (inputElement.type === 'checkbox' || inputElement.type === 'radio') ? 'change' : 'input';
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
                elementWithBound[boundKey] = true;
            }
        });

        // a-bind:attr binding
        const allElements = el.querySelectorAll<HTMLElement>('*');
        allElements.forEach((element) => {
            Array.from(element.attributes).forEach((attr) => {
                if (attr.name.startsWith('a-bind:') || (attr.name.startsWith(':') && !attr.name.startsWith('::'))) {
                    const attrName = attr.name.startsWith(':')
                        ? attr.name.slice(1)
                        : attr.name.slice(7);
                    const expr = attr.value;

                    const value = this.evaluateExpression(expr, state);
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
                }
            });
        });

        // a-class binding
        const classElements = el.querySelectorAll<HTMLElement>('[a-class]');
        classElements.forEach((element) => {
            const expr = element.getAttribute('a-class');
            if (!expr) return;

            try {
                const classObj = this.evaluateExpression(expr, state) as Record<string, boolean>;
                if (typeof classObj === 'object' && classObj !== null) {
                    Object.entries(classObj).forEach(([className, condition]) => {
                        element.classList.toggle(className, Boolean(condition));
                    });
                }
            } catch {
                // Ignore class binding errors
            }
        });
    }

    /**
     * Evaluate expression against state
     */
    private evaluateExpression(expr: string, state: ReactiveState): unknown {
        try {
            // Simple property access
            if (/^[\w.]+$/.test(expr)) {
                return this.getNestedValue(state, expr);
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
    private getNestedValue(obj: ReactiveState, path: string): unknown {
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
     * Initialize all Accelade components on the page
     */
    init(): void {
        const elements = document.querySelectorAll<AcceladeHTMLElement>('[data-accelade]');

        elements.forEach((el) => {
            try {
                this.initComponent(el);
            } catch (e) {
                console.error('Accelade: Failed to init component', e);
            }
        });

        // Initialize the router only once
        if (!this.initialized) {
            initRouter();
            this.initialized = true;
        }
    }
}

// Create singleton instance
const acceladeManager = new AcceladeManager();

/**
 * Initialize function
 */
export function init(): void {
    acceladeManager.init();
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
