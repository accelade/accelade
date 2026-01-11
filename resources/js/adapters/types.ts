/**
 * Adapter Type Definitions
 * Interfaces for framework-specific adapters
 */

import type { AcceladeActions, AcceladeComponentConfig, StateChangeCallback } from '../core/types';
import type { CustomMethods } from '../core/factories/ScriptExecutor';

/**
 * Framework types supported by Accelade
 */
export type FrameworkType = 'vanilla' | 'vue' | 'react' | 'svelte' | 'angular';

/**
 * Component instance returned after initialization
 */
export interface ComponentInstance {
    id: string;
    element: HTMLElement;
    framework: FrameworkType;
    state: Record<string, unknown>;
    originalState: Record<string, unknown>;
    actions: AcceladeActions;
    customMethods: CustomMethods;
    syncProperties: Set<string>;
    stateAdapter: IStateAdapter;
    bindingAdapter: IBindingAdapter;
    dispose: () => void;
}

/**
 * State adapter interface - handles framework-specific reactivity
 */
export interface IStateAdapter {
    /**
     * Initialize state with given initial values
     */
    init(initialState: Record<string, unknown>): void;

    /**
     * Get current state (snapshot)
     */
    getState(): Record<string, unknown>;

    /**
     * Get a specific state value
     */
    get<T = unknown>(key: string): T | undefined;

    /**
     * Set a state value
     */
    set(key: string, value: unknown): void;

    /**
     * Set multiple state values at once
     */
    setMany(updates: Record<string, unknown>): void;

    /**
     * Subscribe to all state changes
     */
    subscribe(callback: StateChangeCallback): () => void;

    /**
     * Subscribe to specific key changes
     */
    subscribeKey(
        key: string,
        callback: (newVal: unknown, oldVal: unknown) => void
    ): () => void;

    /**
     * Get framework-native reactive state (for templates)
     * Returns the native reactive object (Proxy, reactive(), useState result, etc.)
     */
    getReactiveState(): unknown;

    /**
     * Check if state has a specific key
     */
    has(key: string): boolean;

    /**
     * Get all state keys
     */
    keys(): string[];

    /**
     * Dispose adapter and cleanup subscriptions
     */
    dispose(): void;
}

/**
 * Binding adapter interface - handles framework-specific DOM bindings
 */
export interface IBindingAdapter {
    /**
     * Initialize bindings for an element
     */
    init(element: HTMLElement, stateAdapter: IStateAdapter): void;

    /**
     * Bind text content (a-text, v-text, state:text)
     */
    bindText(element: HTMLElement, expression: string): void;

    /**
     * Bind HTML content (a-html, v-html)
     */
    bindHtml(element: HTMLElement, expression: string): void;

    /**
     * Bind visibility (a-show, v-show)
     */
    bindShow(element: HTMLElement, expression: string): void;

    /**
     * Bind conditional rendering (a-if, v-if)
     */
    bindIf(element: HTMLElement, expression: string): void;

    /**
     * Bind two-way input (a-model, v-model)
     */
    bindModel(element: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement, property: string): void;

    /**
     * Bind attributes (a-bind:attr, :attr, v-bind:attr)
     */
    bindAttribute(element: HTMLElement, attr: string, expression: string): void;

    /**
     * Bind event handler (a-on:event, @event, v-on:event)
     */
    bindEvent(
        element: HTMLElement,
        event: string,
        handler: string,
        actions: AcceladeActions,
        customMethods: CustomMethods
    ): void;

    /**
     * Bind class object (a-class, v-class, :class)
     */
    bindClass(element: HTMLElement, expression: string): void;

    /**
     * Bind style object (a-style, v-style, :style)
     */
    bindStyle(element: HTMLElement, expression: string): void;

    /**
     * Update all bindings (for frameworks that need manual updates)
     */
    update(): void;

    /**
     * Dispose bindings and cleanup
     */
    dispose(): void;
}

/**
 * Framework adapter interface - combines state and binding adapters
 */
export interface IFrameworkAdapter {
    /**
     * Framework type identifier
     */
    readonly type: FrameworkType;

    /**
     * Create a state adapter instance
     */
    createStateAdapter(): IStateAdapter;

    /**
     * Create a binding adapter instance
     */
    createBindingAdapter(): IBindingAdapter;

    /**
     * Check if this framework is available in the current environment
     */
    isAvailable(): boolean;

    /**
     * Initialize a component with this framework
     */
    initComponent(element: HTMLElement, config: AcceladeComponentConfig): ComponentInstance;

    /**
     * Dispose a component
     */
    disposeComponent(instance: ComponentInstance): void;

    /**
     * Get attribute prefix for this framework
     * e.g., 'a-' for vanilla, 'v-' for vue, 'state:' for react
     */
    getAttributePrefix(): string;

    /**
     * Get event attribute prefix
     * e.g., 'a-on:' for vanilla, 'v-on:' or '@' for vue
     */
    getEventPrefix(): string;

    /**
     * Get script attribute selector for this framework
     * e.g., 'script[a-script]' for vanilla
     */
    getScriptSelector(): string;

    /**
     * Get binding attribute names for this framework
     */
    getBindingAttributes(): BindingAttributeMap;
}

/**
 * Mapping of binding types to attribute names per framework
 */
export interface BindingAttributeMap {
    text: string;       // a-text, v-text, state:text
    html: string;       // a-html, v-html
    show: string;       // a-show, v-show
    if: string;         // a-if, v-if
    model: string;      // a-model, v-model
    class: string;      // a-class, :class
    style: string;      // a-style, :style
    bind: string;       // a-bind:, v-bind:, :
    on: string;         // a-on:, v-on:, @
    cloak: string;      // a-cloak, v-cloak, data-accelade-cloak
}

/**
 * Adapter factory function type
 */
export type AdapterFactory = () => IFrameworkAdapter;

/**
 * Adapter registration entry
 */
export interface AdapterRegistration {
    type: FrameworkType;
    factory: AdapterFactory;
    priority: number;
    detectFn?: () => boolean;
}

/**
 * Base configuration for adapters
 */
export interface AdapterConfig {
    debug?: boolean;
    syncOnChange?: boolean;
    syncDebounce?: number;
}

/**
 * Event binding info
 */
export interface EventBinding {
    element: HTMLElement;
    event: string;
    handler: EventListener;
}

/**
 * Cleanup function type
 */
export type CleanupFn = () => void;
