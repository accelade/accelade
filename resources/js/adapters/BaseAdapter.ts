/**
 * BaseAdapter - Abstract base class with shared adapter logic
 */

import type {
    IFrameworkAdapter,
    IStateAdapter,
    IBindingAdapter,
    ComponentInstance,
    FrameworkType,
    BindingAttributeMap,
    CleanupFn,
} from './types';
import type { AcceladeComponentConfig, AcceladeActions } from '../core/types';
import { ConfigFactory } from '../core/factories/ConfigFactory';
import { SyncFactory } from '../core/factories/SyncFactory';
import { ActionsFactory } from '../core/factories/ActionsFactory';
import { ScriptExecutor } from '../core/factories/ScriptExecutor';
import type { CustomMethods } from '../core/factories/ScriptExecutor';

/**
 * Default binding attribute map (vanilla/base)
 */
const DEFAULT_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 'a-text',
    html: 'a-html',
    show: 'a-show',
    if: 'a-if',
    model: 'a-model',
    class: 'a-class',
    style: 'a-style',
    bind: 'a-bind:',
    on: '@',
    cloak: 'data-accelade-cloak',
};

/**
 * Abstract base adapter with shared functionality
 */
export abstract class BaseAdapter implements IFrameworkAdapter {
    abstract readonly type: FrameworkType;

    protected components: Map<string, ComponentInstance> = new Map();
    protected cleanupFns: Map<string, CleanupFn[]> = new Map();

    /**
     * Create a state adapter (framework-specific)
     */
    abstract createStateAdapter(): IStateAdapter;

    /**
     * Create a binding adapter (framework-specific)
     */
    abstract createBindingAdapter(): IBindingAdapter;

    /**
     * Check if framework is available
     */
    isAvailable(): boolean {
        return true; // Override in subclasses that need runtime checks
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 'a-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return '@';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[a-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attribute map
     */
    getBindingAttributes(): BindingAttributeMap {
        return DEFAULT_BINDING_ATTRIBUTES;
    }

    /**
     * Initialize a component
     */
    initComponent(element: HTMLElement, config: AcceladeComponentConfig): ComponentInstance {
        // Create state adapter
        const stateAdapter = this.createStateAdapter();
        stateAdapter.init({ ...config.state });

        // Store original state
        const originalState = { ...config.state };

        // Create actions
        const actions = ActionsFactory.create(
            () => stateAdapter.getState(),
            (key, value) => stateAdapter.set(key, value),
            originalState
        );

        // Create binding adapter
        const bindingAdapter = this.createBindingAdapter();
        bindingAdapter.init(element, stateAdapter);

        // Setup sync properties
        const syncProperties = new Set(config.sync);
        this.setupSyncWatchers(config.id, stateAdapter, syncProperties);

        // Create script helpers
        const helpers = ScriptExecutor.createHelpers(
            () => stateAdapter.getState(),
            (key, value) => stateAdapter.set(key, value),
            this.createWatchFn(stateAdapter)
        );

        // Execute custom scripts
        const customMethods = ScriptExecutor.execute(
            element,
            {
                state: stateAdapter.getState(),
                actions,
                helpers,
                setState: (key, value) => stateAdapter.set(key, value),
                getState: () => stateAdapter.getState(),
                originalState,
            },
            this.type
        );

        // Setup event bindings
        this.setupEventBindings(element, stateAdapter, actions, customMethods, bindingAdapter);

        // Setup reactive bindings
        this.setupReactiveBindings(element, stateAdapter, bindingAdapter);

        // Create component instance
        const instance: ComponentInstance = {
            id: config.id,
            element,
            framework: this.type,
            state: stateAdapter.getState(),
            originalState,
            actions,
            customMethods,
            syncProperties,
            stateAdapter,
            bindingAdapter,
            dispose: () => this.disposeComponent(instance),
        };

        // Store component
        this.components.set(config.id, instance);

        // Remove cloak
        this.removeCloak(element);

        return instance;
    }

    /**
     * Create watch function (override in framework-specific adapters)
     */
    protected createWatchFn(
        stateAdapter: IStateAdapter
    ): ((key: string, callback: (newVal: unknown, oldVal: unknown) => void) => void) | undefined {
        return (key, callback) => {
            stateAdapter.subscribeKey(key, callback);
        };
    }

    /**
     * Setup sync watchers for properties that should sync to server
     */
    protected setupSyncWatchers(
        componentId: string,
        stateAdapter: IStateAdapter,
        syncProperties: Set<string>
    ): void {
        if (syncProperties.size === 0) return;

        const cleanups: CleanupFn[] = [];

        for (const prop of syncProperties) {
            const unsubscribe = stateAdapter.subscribeKey(prop, (newVal) => {
                SyncFactory.sync(componentId, prop, newVal);
            });
            cleanups.push(unsubscribe);
        }

        this.addCleanups(componentId, cleanups);
    }

    /**
     * Setup event bindings
     */
    protected setupEventBindings(
        element: HTMLElement,
        stateAdapter: IStateAdapter,
        actions: AcceladeActions,
        customMethods: CustomMethods,
        bindingAdapter: IBindingAdapter
    ): void {
        const eventPrefix = this.getEventPrefix();
        const altEventPrefix = this.getAltEventPrefix();
        const allElements = element.querySelectorAll<HTMLElement>('*');

        allElements.forEach((el) => {
            Array.from(el.attributes).forEach((attr) => {
                let eventName: string | null = null;
                let handler: string | null = null;

                if (attr.name.startsWith(eventPrefix)) {
                    eventName = attr.name.slice(eventPrefix.length);
                    handler = attr.value;
                } else if (altEventPrefix && attr.name.startsWith(altEventPrefix)) {
                    eventName = attr.name.slice(altEventPrefix.length);
                    handler = attr.value;
                }

                if (eventName && handler) {
                    bindingAdapter.bindEvent(el, eventName, handler, actions, customMethods);
                }
            });
        });
    }

    /**
     * Get alternate event prefix (e.g., '@' for Vue)
     */
    protected getAltEventPrefix(): string | null {
        return null;
    }

    /**
     * Setup reactive bindings
     */
    protected setupReactiveBindings(
        element: HTMLElement,
        stateAdapter: IStateAdapter,
        bindingAdapter: IBindingAdapter
    ): void {
        const attrs = this.getBindingAttributes();

        // Text bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.text}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.text);
            if (expr) bindingAdapter.bindText(el, expr);
        });

        // HTML bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.html}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.html);
            if (expr) bindingAdapter.bindHtml(el, expr);
        });

        // Show bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.show}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.show);
            if (expr) bindingAdapter.bindShow(el, expr);
        });

        // If bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.if}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.if);
            if (expr) bindingAdapter.bindIf(el, expr);
        });

        // Model bindings
        element.querySelectorAll<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>(
            `[${attrs.model}]`
        ).forEach((el) => {
            const prop = el.getAttribute(attrs.model);
            if (prop) bindingAdapter.bindModel(el, prop);
        });

        // Class bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.class}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.class);
            if (expr) bindingAdapter.bindClass(el, expr);
        });

        // Style bindings
        element.querySelectorAll<HTMLElement>(`[${attrs.style}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.style);
            if (expr) bindingAdapter.bindStyle(el, expr);
        });

        // Attribute bindings (a-bind:*, :*)
        const bindPrefix = attrs.bind;
        element.querySelectorAll<HTMLElement>('*').forEach((el) => {
            Array.from(el.attributes).forEach((attr) => {
                if (attr.name.startsWith(bindPrefix)) {
                    const attrName = attr.name.slice(bindPrefix.length);
                    bindingAdapter.bindAttribute(el, attrName, attr.value);
                } else if (attr.name.startsWith(':') && !attr.name.startsWith('::')) {
                    const attrName = attr.name.slice(1);
                    bindingAdapter.bindAttribute(el, attrName, attr.value);
                }
            });
        });
    }

    /**
     * Remove cloak from element
     */
    protected removeCloak(element: HTMLElement): void {
        element.removeAttribute('data-accelade-cloak');
        element.removeAttribute('a-cloak');
        element.removeAttribute('v-cloak');
        element.classList.add('accelade-ready');
    }

    /**
     * Dispose a component
     */
    disposeComponent(instance: ComponentInstance): void {
        // Run cleanups
        const cleanups = this.cleanupFns.get(instance.id);
        if (cleanups) {
            cleanups.forEach((fn) => fn());
            this.cleanupFns.delete(instance.id);
        }

        // Cancel pending syncs
        SyncFactory.cancelComponent(instance.id);

        // Dispose adapters
        instance.stateAdapter.dispose();
        instance.bindingAdapter.dispose();

        // Remove from components map
        this.components.delete(instance.id);
    }

    /**
     * Add cleanup functions for a component
     */
    protected addCleanups(componentId: string, fns: CleanupFn[]): void {
        const existing = this.cleanupFns.get(componentId) ?? [];
        this.cleanupFns.set(componentId, [...existing, ...fns]);
    }

    /**
     * Get a component instance by ID
     */
    getComponent(id: string): ComponentInstance | undefined {
        return this.components.get(id);
    }

    /**
     * Get all component instances
     */
    getAllComponents(): ComponentInstance[] {
        return Array.from(this.components.values());
    }

    /**
     * Dispose all components
     */
    disposeAll(): void {
        for (const instance of this.components.values()) {
            this.disposeComponent(instance);
        }
    }
}

export default BaseAdapter;
