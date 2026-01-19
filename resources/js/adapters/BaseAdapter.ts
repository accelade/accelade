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
import { DeferFactory } from '../core/factories/DeferFactory';
import type { DeferInstance } from '../core/factories/DeferFactory';
import { EchoFactory } from '../core/echo/EchoFactory';
import type { EchoComponentInstance } from '../core/echo/EchoFactory';
import { FlashFactory } from '../core/flash/FlashFactory';
import type { FlashInstance } from '../core/flash/FlashFactory';
import { createModal } from '../core/modal/ModalFactory';
import type { ModalAdapterInstance } from '../core/modal/ModalFactory';
import { createState } from '../core/state/StateFactory';
import type { StateInstance } from '../core/state/StateFactory';
import { createToggle, createToggleMethods } from '../core/toggle/ToggleFactory';
import type { ToggleInstance } from '../core/toggle/types';
import { createTransition } from '../core/transition/TransitionFactory';
import type { TransitionInstance } from '../core/transition/types';
import { createBridge, createMethodProxies, disposeBridge } from '../core/bridge';
import type { BridgeInstance } from '../core/bridge';
import { createTooltip, createTooltipMethods } from '../core/tooltip/TooltipFactory';
import type { TooltipInstance } from '../core/tooltip/types';
import { createDraggable, createDraggableMethods } from '../core/draggable/DraggableFactory';
import type { DraggableInstance } from '../core/draggable/types';

/**
 * Global stores for shared reactive state
 */
const globalStores: Map<string, Record<string, unknown>> = new Map();

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
     * Load state from storage (sessionStorage or localStorage)
     */
    protected loadFromStorage(key: string, useLocalStorage: boolean): Record<string, unknown> | null {
        try {
            const storage = useLocalStorage ? localStorage : sessionStorage;
            const stored = storage.getItem(`accelade:${key}`);
            if (stored) {
                return JSON.parse(stored) as Record<string, unknown>;
            }
        } catch {
            // Storage not available or invalid data
        }
        return null;
    }

    /**
     * Save state to storage (sessionStorage or localStorage)
     */
    protected saveToStorage(key: string, state: Record<string, unknown>, useLocalStorage: boolean): void {
        try {
            const storage = useLocalStorage ? localStorage : sessionStorage;
            storage.setItem(`accelade:${key}`, JSON.stringify(state));
        } catch {
            // Storage not available or quota exceeded
        }
    }

    /**
     * Get or create a global store
     */
    protected getOrCreateStore(storeName: string, initialState: Record<string, unknown>): Record<string, unknown> {
        if (globalStores.has(storeName)) {
            return globalStores.get(storeName)!;
        }
        const store = { ...initialState };
        globalStores.set(storeName, store);
        return store;
    }

    /**
     * Initialize a component
     */
    initComponent(element: HTMLElement, config: AcceladeComponentConfig): ComponentInstance {
        // Determine initial state (with storage loading)
        let initialState = { ...config.state };

        // If using a global store, get or create it
        if (config.storeName) {
            const store = this.getOrCreateStore(config.storeName, initialState);
            initialState = { ...store };
        }

        // If remember key is set, try to load from sessionStorage
        if (config.rememberKey) {
            const storedState = this.loadFromStorage(config.rememberKey, false);
            if (storedState) {
                initialState = { ...initialState, ...storedState };
            }
        }

        // If localStorage key is set, try to load from localStorage
        if (config.localStorageKey) {
            const storedState = this.loadFromStorage(config.localStorageKey, true);
            if (storedState) {
                initialState = { ...initialState, ...storedState };
            }
        }

        // Create state adapter
        const stateAdapter = this.createStateAdapter();
        stateAdapter.init(initialState);

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

        // Get config from data-accelade-config attribute
        const configStr = element.dataset.acceladeConfig;
        let componentConfig: Record<string, unknown> = {};
        if (configStr) {
            try {
                componentConfig = JSON.parse(configStr) as Record<string, unknown>;
            } catch {
                // Invalid JSON
            }
        }

        // Execute custom scripts
        // Pass reactive state (proxy) so scripts can directly modify state
        const customMethods = ScriptExecutor.execute(
            element,
            {
                state: stateAdapter.getReactiveState() as Record<string, unknown>,
                actions,
                helpers,
                setState: (key, value) => stateAdapter.set(key, value),
                getState: () => stateAdapter.getState(),
                originalState,
                $el: element,
                config: componentConfig,
            },
            this.type
        );

        // Setup Toggle component BEFORE event bindings (so toggle/setToggle are available)
        let toggleInstance: ToggleInstance | undefined;
        if (element.hasAttribute('data-accelade-toggle')) {
            toggleInstance = this.setupToggle(element, config.id, stateAdapter, customMethods);
        }

        // Setup Bridge component BEFORE event bindings (so PHP methods are available)
        let bridgeInstance: BridgeInstance | undefined;
        if (element.hasAttribute('data-accelade-bridge')) {
            bridgeInstance = this.setupBridge(element, config.id, stateAdapter, customMethods);
        }

        // Setup event bindings
        this.setupEventBindings(element, stateAdapter, actions, customMethods, bindingAdapter);

        // Setup reactive bindings
        this.setupReactiveBindings(element, stateAdapter, bindingAdapter);

        // Setup defer component if applicable
        let deferInstance: DeferInstance | undefined;
        if (element.hasAttribute('data-accelade-defer')) {
            deferInstance = this.setupDefer(element, config.id, stateAdapter, customMethods);
        }

        // Setup Echo event listener if applicable
        let echoInstance: EchoComponentInstance | undefined;
        if (element.hasAttribute('data-accelade-echo')) {
            echoInstance = this.setupEcho(element, config.id, stateAdapter);
        }

        // Setup Flash data if applicable
        let flashInstance: FlashInstance | undefined;
        if (element.hasAttribute('data-accelade-flash')) {
            flashInstance = this.setupFlash(element, config.id, stateAdapter);
        }

        // Setup Modal if applicable
        let modalInstance: ModalAdapterInstance | undefined;
        if (element.hasAttribute('data-accelade-modal')) {
            modalInstance = this.setupModal(element, config.id, stateAdapter);
        }

        // Setup State component if applicable
        let stateInstance: StateInstance | undefined;
        if (element.hasAttribute('data-accelade-state-component')) {
            stateInstance = this.setupState(element, config.id, stateAdapter);
        }

        // Setup Tooltip component if applicable
        let tooltipInstance: TooltipInstance | undefined;
        if (element.hasAttribute('data-accelade-tooltip')) {
            tooltipInstance = this.setupTooltip(element, config.id, stateAdapter, customMethods);
        }

        // Setup Draggable component if applicable
        let draggableInstance: DraggableInstance | undefined;
        if (element.hasAttribute('data-accelade-draggable')) {
            draggableInstance = this.setupDraggable(element, config.id, stateAdapter, customMethods);
        }

        // Setup Transition elements inside this component
        this.setupTransitions(element, config.id, stateAdapter);

        // Setup state attribute sync for lazy loading conditional triggers
        const stateAttrCleanup = stateAdapter.subscribe(() => {
            element.dataset.acceladeState = JSON.stringify(stateAdapter.getState());
        });
        this.addCleanups(config.id, [stateAttrCleanup]);

        // Setup storage persistence watchers
        if (config.rememberKey || config.localStorageKey || config.storeName) {
            const storageCleanup = stateAdapter.subscribe(() => {
                const currentState = stateAdapter.getState();

                // Save to sessionStorage if remember key is set
                if (config.rememberKey) {
                    this.saveToStorage(config.rememberKey, currentState, false);
                }

                // Save to localStorage if localStorage key is set
                if (config.localStorageKey) {
                    this.saveToStorage(config.localStorageKey, currentState, true);
                }

                // Update global store if this component uses one
                if (config.storeName && globalStores.has(config.storeName)) {
                    const store = globalStores.get(config.storeName)!;
                    Object.assign(store, currentState);
                }
            });
            this.addCleanups(config.id, [storageCleanup]);
        }

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
     * Setup defer component
     */
    protected setupDefer(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter,
        customMethods: CustomMethods
    ): DeferInstance | undefined {
        const config = DeferFactory.parseConfig(element);
        if (!config) {
            return undefined;
        }

        // Create setState function
        const setState = (key: string, value: unknown): void => {
            stateAdapter.set(key, value);
        };

        // Create getState function
        const getState = (): Record<string, unknown> => {
            return stateAdapter.getState();
        };

        // Create dispatchEvent function
        const dispatchEvent = (name: string, detail: unknown): void => {
            const event = new CustomEvent(`accelade:defer:${name}`, {
                detail,
                bubbles: true,
                cancelable: true,
            });
            element.dispatchEvent(event);
        };

        // Create defer instance
        const instance = DeferFactory.create(
            componentId,
            config,
            setState,
            getState,
            dispatchEvent
        );

        // Add reload to customMethods
        customMethods.reload = instance.reload;

        // Setup watch if configured
        if (config.watchValue) {
            const unsubscribe = stateAdapter.subscribeKey(config.watchValue, () => {
                DeferFactory.triggerReloadDebounced(
                    componentId,
                    config.watchDebounce ?? 150,
                    instance.reload
                );
            });
            this.addCleanups(componentId, [unsubscribe]);
        }

        // Add cleanup for defer instance
        this.addCleanups(componentId, [() => DeferFactory.dispose(componentId)]);

        return instance;
    }

    /**
     * Setup Echo event listener
     */
    protected setupEcho(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter
    ): EchoComponentInstance | undefined {
        const instance = EchoFactory.create(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add cleanup for Echo instance
        this.addCleanups(componentId, [() => EchoFactory.dispose(instance)]);

        return instance;
    }

    /**
     * Setup Flash data
     */
    protected setupFlash(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter
    ): FlashInstance | undefined {
        const instance = FlashFactory.create(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add cleanup for Flash instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup Modal component
     */
    protected setupModal(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter
    ): ModalAdapterInstance | undefined {
        const instance = createModal(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add cleanup for Modal instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup State component
     */
    protected setupState(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter
    ): StateInstance | undefined {
        const instance = createState(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add cleanup for State instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup Toggle component
     */
    protected setupToggle(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter,
        customMethods: CustomMethods
    ): ToggleInstance | undefined {
        const instance = createToggle(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add toggle methods to customMethods
        const toggleMethods = createToggleMethods(instance);
        customMethods.toggle = toggleMethods.toggle;
        customMethods.open = toggleMethods.open;
        customMethods.close = toggleMethods.close;
        customMethods.setToggle = toggleMethods.setToggle;

        // Add cleanup for Toggle instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup Tooltip component
     */
    protected setupTooltip(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter,
        customMethods: CustomMethods
    ): TooltipInstance | undefined {
        const instance = createTooltip(element);
        if (!instance) {
            return undefined;
        }

        // Add tooltip methods to customMethods
        const tooltipMethods = createTooltipMethods(instance);
        customMethods.showTooltip = tooltipMethods.showTooltip;
        customMethods.hideTooltip = tooltipMethods.hideTooltip;
        customMethods.toggleTooltip = tooltipMethods.toggleTooltip;
        customMethods.setTooltipContent = tooltipMethods.setTooltipContent;
        customMethods.setTooltipPosition = tooltipMethods.setTooltipPosition;

        // Add cleanup for Tooltip instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup Draggable component
     */
    protected setupDraggable(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter,
        customMethods: CustomMethods
    ): DraggableInstance | undefined {
        const instance = createDraggable(componentId, element, stateAdapter);
        if (!instance) {
            return undefined;
        }

        // Add draggable methods to customMethods
        const draggableMethods = createDraggableMethods(instance);
        customMethods.enableDrag = draggableMethods.enableDrag;
        customMethods.disableDrag = draggableMethods.disableDrag;
        customMethods.isDragEnabled = draggableMethods.isDragEnabled;
        customMethods.getDragItems = draggableMethods.getDragItems;
        customMethods.moveDragItem = draggableMethods.moveDragItem;
        customMethods.refreshDrag = draggableMethods.refreshDrag;

        // Add cleanup for Draggable instance
        this.addCleanups(componentId, [() => instance.dispose()]);

        return instance;
    }

    /**
     * Setup Transition elements inside a component
     */
    protected setupTransitions(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter
    ): TransitionInstance[] {
        const instances: TransitionInstance[] = [];
        const transitionElements = element.querySelectorAll<HTMLElement>('[data-accelade-transition]');

        transitionElements.forEach((transitionEl) => {
            const instance = createTransition(componentId, transitionEl, stateAdapter);
            instances.push(instance);

            // Add cleanup for Transition instance
            this.addCleanups(componentId, [() => instance.dispose()]);
        });

        return instances;
    }

    /**
     * Setup Bridge component for PHP two-way binding
     */
    protected setupBridge(
        element: HTMLElement,
        componentId: string,
        stateAdapter: IStateAdapter,
        customMethods: CustomMethods
    ): BridgeInstance | undefined {
        const instance = createBridge(
            element,
            componentId,
            (key, value) => stateAdapter.set(key, value),
            () => stateAdapter.getState()
        );

        if (!instance) {
            return undefined;
        }

        // Note: BridgeFactory already initializes props in state via setState
        // Do NOT store the Bridge proxy (instance.props) in state directly
        // as it would cause infinite recursion when setting nested paths

        // Add bridge method proxies to customMethods
        const methodProxies = createMethodProxies(instance);
        for (const [name, fn] of Object.entries(methodProxies)) {
            customMethods[name] = fn;
        }

        // Add $bridge helper function for direct access
        customMethods.$bridge = () => instance;

        // Add cleanup for Bridge instance
        this.addCleanups(componentId, [() => disposeBridge(instance.id)]);

        return instance;
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
