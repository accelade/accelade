/**
 * Accelade - Unified Entry Point
 *
 * Single bundle with runtime framework detection supporting:
 * - Vanilla JavaScript (Proxy-based)
 * - Vue.js (reactive/effect)
 * - React (useState/hooks)
 * - Svelte (stores)
 * - Angular (signals/RxJS)
 */

// Types
export type {
    AcceladeComponentConfig,
    AcceladeActions,
    StateChangeCallback,
} from './core/types';

export type {
    FrameworkType,
    ComponentInstance,
    IStateAdapter,
    IBindingAdapter,
    IFrameworkAdapter,
} from './adapters/types';

// Core utilities
export { ConfigFactory } from './core/factories/ConfigFactory';
export { SyncFactory } from './core/factories/SyncFactory';
export { ActionsFactory } from './core/factories/ActionsFactory';
export { ScriptExecutor } from './core/factories/ScriptExecutor';
export {
    evaluateExpression,
    evaluateBooleanExpression,
    evaluateStringExpression,
    interpolate,
    parseClassObject,
} from './core/expressions';

// Router
export {
    initRouter,
    navigate,
    getRouter,
    type NavigationOptions,
    type RouterConfig,
} from './core/router';

// Progress
export {
    getProgress,
    configureProgress,
    startProgress,
    doneProgress,
    type ProgressConfig,
} from './core/progress';

// Notifications
export {
    NotificationManager,
    type NotificationData,
    type NotificationConfig,
    type NotificationStatus,
    type NotificationPosition,
} from './core/notification';

// Debug system
export {
    DebugManager,
    StateHistory,
    NetworkInspector,
    type DebugConfig,
    type AcceladeDevtools,
    type StateSnapshot,
    type NetworkRecord,
} from './core/debug';

// Shared data
export {
    SharedDataManager,
    type SharedDataChangeCallback,
} from './core/shared';

// Text interpolation
export {
    TextInterpolator,
    createTextInterpolator,
} from './core/interpolation';

// Lazy loading
export {
    LazyLoaderManager,
    getLazyLoader,
    initLazy,
    registerLazy,
    loadLazy,
    configureLazy,
    type LazyConfig,
    type LazyInstance,
} from './core/lazy';

// Framework registry
export { FrameworkRegistry } from './registry/FrameworkRegistry';

// Adapters
export {
    BaseAdapter,
    VanillaAdapter,
    VueAdapter,
    ReactAdapter,
    SvelteAdapter,
    AngularAdapter,
} from './adapters';

// React hooks and components
export {
    useAccelade,
    useAcceladeSync,
    AcceladeProvider,
    useAcceladeContext,
    AcceladeLink,
    Show,
    For,
    Switch,
    Match,
    type UseAcceladeResult,
    type UseAcceladeOptions,
    type AcceladeProviderProps,
    type AcceladeLinkProps,
} from './adapters/react';

// Import for initialization
import { FrameworkRegistry } from './registry/FrameworkRegistry';
import { VanillaAdapter } from './adapters/vanilla';
import { VueAdapter } from './adapters/vue';
import { ReactAdapter } from './adapters/react';
import { SvelteAdapter } from './adapters/svelte';
import { AngularAdapter } from './adapters/angular';
import { ConfigFactory } from './core/factories/ConfigFactory';
import { DebugManager } from './core/debug';
import { SharedDataManager } from './core/shared';
import { initRouter, navigate, getRouter } from './core/router';
import {
    getProgress,
    configureProgress,
    startProgress,
    doneProgress,
    type ProgressConfig,
} from './core/progress';
import { NotificationManager } from './core/notification';
import type { FrameworkType, ComponentInstance } from './adapters/types';
import type { SharedData } from './core/types';
import { initLazy, getLazyLoader, registerLazy, loadLazy, configureLazy, type LazyConfig } from './core/lazy';

// Singleton notification manager
let notificationManager: NotificationManager | null = null;

function getNotify(): NotificationManager {
    if (!notificationManager) {
        notificationManager = new NotificationManager();
    }
    return notificationManager;
}

// Active component instances
const components = new Map<string, ComponentInstance>();

/**
 * Register all framework adapters
 */
function registerAdapters(): void {
    // Register adapters with priority (higher priority = checked first)
    FrameworkRegistry.register('vue', () => new VueAdapter(), { priority: 80 });
    FrameworkRegistry.register('react', () => new ReactAdapter(), { priority: 70 });
    FrameworkRegistry.register('svelte', () => new SvelteAdapter(), { priority: 60 });
    FrameworkRegistry.register('angular', () => new AngularAdapter(), { priority: 50 });
    FrameworkRegistry.register('vanilla', () => new VanillaAdapter(), { priority: 10 });
}

/**
 * Extended HTMLElement with initialization flag
 */
interface AcceladeHTMLElement extends HTMLElement {
    __accelade_initialized?: boolean;
    __accelade_component_id?: string;
}

/**
 * Initialize an Accelade component
 */
function initComponent(el: AcceladeHTMLElement, framework?: FrameworkType): ComponentInstance | null {
    if (el.__accelade_initialized) {
        const existingId = el.__accelade_component_id;
        return existingId ? components.get(existingId) ?? null : null;
    }

    try {
        const config = ConfigFactory.parseElement(el);
        const adapter = FrameworkRegistry.getAdapter(framework);
        const instance = adapter.initComponent(el, config);

        // Track the component
        components.set(instance.id, instance);
        el.__accelade_initialized = true;
        el.__accelade_component_id = instance.id;

        // Register with debug manager
        const debug = DebugManager.getInstance();
        debug.registerComponent(instance);

        // Remove cloak attribute for smooth reveal
        el.removeAttribute('data-accelade-cloak');
        el.removeAttribute('v-cloak');
        el.removeAttribute('a-cloak');
        el.classList.add('accelade-ready');

        return instance;
    } catch (e) {
        console.error('Accelade: Failed to init component', e);
        return null;
    }
}

/**
 * Dispose a component
 */
function disposeComponent(componentId: string): void {
    const instance = components.get(componentId);
    if (!instance) return;

    const adapter = FrameworkRegistry.getAdapter(instance.framework);
    adapter.disposeComponent(instance);

    // Unregister from debug manager
    const debug = DebugManager.getInstance();
    debug.unregisterComponent(componentId);

    components.delete(componentId);
}

// Module-level initialization guard
let acceladeInitialized = false;

/**
 * Initialize all Accelade components on the page
 */
function init(options?: { framework?: FrameworkType; debug?: boolean }): void {
    const elements = document.querySelectorAll<AcceladeHTMLElement>('[data-accelade]');

    elements.forEach((el) => {
        initComponent(el, options?.framework);
    });

    // Initialize the router only once
    if (!acceladeInitialized) {
        initRouter();
        acceladeInitialized = true;

        // Initialize debug if requested
        if (options?.debug) {
            const debug = DebugManager.getInstance();
            debug.setEnabled(true);
        }
    }
}

/**
 * Get a component instance by ID
 */
function getComponent(componentId: string): ComponentInstance | undefined {
    return components.get(componentId);
}

/**
 * Get all component instances
 */
function getComponents(): Map<string, ComponentInstance> {
    return new Map(components);
}

/**
 * Get detected framework
 */
function getFramework(): FrameworkType {
    return FrameworkRegistry.detect();
}

// Progress API object
const progress = {
    configure: configureProgress,
    start: startProgress,
    done: doneProgress,
    instance: getProgress,
};

// Router API object
const router = {
    init: initRouter,
    navigate,
    instance: getRouter,
};

// Notification API object
const notify = {
    success: (title: string, message = '') => getNotify().success(title, message),
    info: (title: string, message = '') => getNotify().info(title, message),
    warning: (title: string, message = '') => getNotify().warning(title, message),
    danger: (title: string, message = '') => getNotify().danger(title, message),
    show: (data: Parameters<typeof NotificationManager.prototype.show>[0]) => getNotify().show(data),
    dismiss: (id: string) => getNotify().dismiss(id),
    configure: (config: Parameters<typeof NotificationManager.prototype.configure>[0]) => getNotify().configure(config),
    instance: getNotify,
};

// Shared data API object
const shared = {
    /**
     * Get a shared value by key (supports dot notation)
     */
    get: <T = unknown>(key: string, defaultValue?: T): T =>
        SharedDataManager.getInstance().get(key, defaultValue),

    /**
     * Check if a shared key exists
     */
    has: (key: string): boolean =>
        SharedDataManager.getInstance().has(key),

    /**
     * Get all shared data
     */
    all: (): SharedData =>
        SharedDataManager.getInstance().all(),

    /**
     * Set a shared value (client-side only)
     */
    set: (key: string, value: unknown): void =>
        SharedDataManager.getInstance().set(key, value),

    /**
     * Merge data into shared data
     */
    merge: (data: SharedData): void =>
        SharedDataManager.getInstance().merge(data),

    /**
     * Subscribe to changes for a specific key
     */
    subscribe: (key: string, callback: (key: string, newValue: unknown, oldValue: unknown) => void): (() => void) =>
        SharedDataManager.getInstance().subscribe(key, callback),

    /**
     * Subscribe to all changes
     */
    subscribeAll: (callback: (key: string, newValue: unknown, oldValue: unknown) => void): (() => void) =>
        SharedDataManager.getInstance().subscribeAll(callback),

    /**
     * Get the SharedDataManager instance
     */
    instance: (): SharedDataManager =>
        SharedDataManager.getInstance(),
};

// Lazy loading API object
const lazy = {
    /**
     * Initialize all lazy components on the page
     */
    init: initLazy,

    /**
     * Register a lazy component element
     */
    register: registerLazy,

    /**
     * Load a lazy component by ID
     */
    load: loadLazy,

    /**
     * Configure lazy loading options
     */
    configure: configureLazy,

    /**
     * Get a lazy instance by ID
     */
    get: (id: string) => getLazyLoader().get(id),

    /**
     * Get all lazy instances
     */
    getAll: () => getLazyLoader().getAll(),

    /**
     * Reload a lazy component
     */
    reload: (id: string) => getLazyLoader().reload(id),

    /**
     * Hide a lazy component (show placeholder)
     */
    hide: (id: string) => getLazyLoader().hide(id),

    /**
     * Subscribe to lazy events
     */
    on: (event: 'load' | 'loaded' | 'error', callback: Parameters<typeof getLazyLoader>['0']['on'] extends (e: infer E, c: infer C) => void ? C : never) =>
        getLazyLoader().on(event, callback as Parameters<ReturnType<typeof getLazyLoader>['on']>[1]),

    /**
     * Get the LazyLoaderManager instance
     */
    instance: getLazyLoader,
};

/**
 * Main Accelade API
 */
const Accelade = {
    // Core
    init,
    initComponent,
    disposeComponent,
    getComponent,
    getComponents,

    // Framework
    getFramework,
    registry: FrameworkRegistry,

    // Navigation
    navigate,
    router,

    // Progress
    progress,

    // Notifications
    notify,

    // Shared data
    shared,

    // Lazy loading
    lazy,

    // Debug (set via DebugManager when enabled)
    debug: false as boolean,
    devtools: null as unknown,
};

// Export for window
if (typeof window !== 'undefined') {
    // Register all adapters
    registerAdapters();

    // Configure progress from AcceladeConfig if available
    const globalConfig = ConfigFactory.getGlobalConfig();
    if (globalConfig.progress && Object.keys(globalConfig.progress).length > 0) {
        configureProgress(globalConfig.progress as ProgressConfig);
    }

    // Initialize shared data from config
    if (globalConfig.shared) {
        SharedDataManager.getInstance().init(globalConfig.shared as SharedData);
    }

    // Check for debug mode
    if (globalConfig.debug) {
        const debug = DebugManager.getInstance();
        debug.setEnabled(true);
    }

    // Expose on window
    (window as unknown as Record<string, unknown>).Accelade = Accelade;

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            init();
            initLazy();
        });
    } else {
        init();
        initLazy();
    }
}

// Default export
export default Accelade;
