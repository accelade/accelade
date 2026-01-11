/**
 * LazyLoader - Handles lazy loading of content
 *
 * Supports two modes:
 * 1. Inline mode: Content is already in the DOM but hidden, revealed after delay
 * 2. URL mode: Content is fetched from a URL via AJAX
 */

export interface LazyConfig {
    /** Delay before loading in milliseconds */
    delay?: number;
    /** Custom fetch options for URL mode */
    fetchOptions?: RequestInit;
}

export interface LazyInstance {
    id: string;
    element: HTMLElement;
    mode: 'inline' | 'url';
    url?: string;
    loaded: boolean;
    loading: boolean;
    show: boolean | string;
    conditional: boolean;
    delay: number;
    load: () => Promise<void>;
    reload: () => Promise<void>;
}

type LazyEventType = 'load' | 'loaded' | 'error';
type LazyEventCallback = (instance: LazyInstance, data?: unknown) => void;

/**
 * Lazy loader state for a condition
 */
interface ConditionState {
    lastValue: boolean;
    unsubscribe?: () => void;
}

class LazyLoaderManager {
    private instances: Map<string, LazyInstance> = new Map();
    private observers: Map<string, IntersectionObserver> = new Map();
    private eventListeners: Map<LazyEventType, Set<LazyEventCallback>> = new Map();
    private conditionStates: Map<string, ConditionState> = new Map();
    private globalConfig: LazyConfig = {
        delay: 0,
    };

    /**
     * Configure global lazy loading options
     */
    configure(config: Partial<LazyConfig>): void {
        this.globalConfig = { ...this.globalConfig, ...config };
    }

    /**
     * Initialize all lazy components on the page
     */
    init(): void {
        const elements = document.querySelectorAll<HTMLElement>('[data-accelade-lazy]');
        elements.forEach((el) => this.register(el));
    }

    /**
     * Register a lazy component
     */
    register(element: HTMLElement): LazyInstance | null {
        const id = element.dataset.lazyId;
        if (!id || this.instances.has(id)) {
            return this.instances.get(id!) ?? null;
        }

        const mode = (element.dataset.lazyMode || 'inline') as 'inline' | 'url';
        const url = element.dataset.lazyUrl;
        const showAttr = element.dataset.lazyShow || 'true';
        const conditional = element.dataset.lazyConditional === 'true';
        const delay = parseInt(element.dataset.lazyDelay || '0', 10);

        // Parse show value
        let show: boolean | string = showAttr === 'true';
        if (conditional) {
            show = showAttr; // Keep as expression string for conditional mode
        }

        const instance: LazyInstance = {
            id,
            element,
            mode,
            url,
            loaded: false,
            loading: false,
            show,
            conditional,
            delay: delay || this.globalConfig.delay || 0,
            load: () => this.load(id),
            reload: () => this.reload(id),
        };

        this.instances.set(id, instance);

        // Handle conditional mode
        if (conditional) {
            this.setupConditionalLoading(instance);
        } else if (show === true) {
            // Auto-load immediately or after delay
            this.scheduleLoad(instance);
        }

        return instance;
    }

    /**
     * Safely evaluate a simple boolean expression
     */
    private safeEvaluateExpression(expr: string, context: Record<string, unknown>): boolean {
        // Simple expression evaluator for common patterns
        // Supports: variable, !variable, variable === value, variable !== value
        const trimmed = expr.trim();

        // Handle negation
        if (trimmed.startsWith('!')) {
            const varName = trimmed.slice(1).trim();
            return !context[varName];
        }

        // Handle strict equality
        if (trimmed.includes('===')) {
            const [left, right] = trimmed.split('===').map((s) => s.trim());
            const leftVal = context[left];
            const rightVal = right.startsWith("'") || right.startsWith('"')
                ? right.slice(1, -1)
                : right === 'true' ? true : right === 'false' ? false : right;
            return leftVal === rightVal;
        }

        // Handle strict inequality
        if (trimmed.includes('!==')) {
            const [left, right] = trimmed.split('!==').map((s) => s.trim());
            const leftVal = context[left];
            const rightVal = right.startsWith("'") || right.startsWith('"')
                ? right.slice(1, -1)
                : right === 'true' ? true : right === 'false' ? false : right;
            return leftVal !== rightVal;
        }

        // Handle simple variable lookup
        if (/^[a-zA-Z_$][a-zA-Z0-9_$]*$/.test(trimmed)) {
            return !!context[trimmed];
        }

        // For complex expressions, use Function constructor (safer than eval)
        try {
            const fn = new Function(...Object.keys(context), `return ${expr}`);
            return !!fn(...Object.values(context));
        } catch {
            return false;
        }
    }

    /**
     * Setup conditional loading that watches for state changes
     */
    private setupConditionalLoading(instance: LazyInstance): void {
        // Initial state
        const conditionState: ConditionState = {
            lastValue: false,
        };
        this.conditionStates.set(instance.id, conditionState);

        // We'll check the condition periodically or use MutationObserver
        // For now, use a simple polling approach that checks Accelade component state
        const checkCondition = (): boolean => {
            const showExpr = instance.show as string;

            // Try to evaluate the expression in the context of any Accelade component
            try {
                // Look for parent Accelade component
                const acceladeEl = instance.element.closest('[data-accelade]');
                if (acceladeEl) {
                    const stateAttr = acceladeEl.getAttribute('data-accelade-state');
                    if (stateAttr) {
                        const state = JSON.parse(stateAttr);
                        return this.safeEvaluateExpression(showExpr, state);
                    }
                }

                // Fallback: try with empty context
                return this.safeEvaluateExpression(showExpr, {});
            } catch {
                return false;
            }
        };

        // Check initially
        const initialValue = checkCondition();
        conditionState.lastValue = initialValue;

        if (initialValue) {
            this.scheduleLoad(instance);
        }

        // Setup observer for state changes
        const observer = new MutationObserver(() => {
            const newValue = checkCondition();
            if (newValue !== conditionState.lastValue) {
                conditionState.lastValue = newValue;
                if (newValue) {
                    this.scheduleLoad(instance);
                } else {
                    this.hide(instance.id);
                }
            }
        });

        // Observe parent Accelade component for attribute changes
        const acceladeEl = instance.element.closest('[data-accelade]');
        if (acceladeEl) {
            observer.observe(acceladeEl, {
                attributes: true,
                attributeFilter: ['data-accelade-state'],
            });
        }

        // Store for cleanup
        this.observers.set(instance.id, observer as unknown as IntersectionObserver);
    }

    /**
     * Schedule loading with delay
     */
    private scheduleLoad(instance: LazyInstance): void {
        if (instance.delay > 0) {
            setTimeout(() => this.load(instance.id), instance.delay);
        } else {
            // Use requestAnimationFrame for smooth initial render
            requestAnimationFrame(() => this.load(instance.id));
        }
    }

    /**
     * Load content for a lazy component
     */
    async load(id: string): Promise<void> {
        const instance = this.instances.get(id);
        if (!instance || instance.loading) return;

        instance.loading = true;
        this.emit('load', instance);

        try {
            if (instance.mode === 'url' && instance.url) {
                await this.loadFromUrl(instance);
            } else {
                await this.loadInline(instance);
            }

            instance.loaded = true;
            instance.loading = false;
            this.emit('loaded', instance);
        } catch (error) {
            instance.loading = false;
            this.emit('error', instance, error);
            console.error('Accelade: Failed to load lazy content', error);
        }
    }

    /**
     * Load content from URL
     */
    private async loadFromUrl(instance: LazyInstance): Promise<void> {
        const method = instance.element.dataset.lazyMethod || 'GET';
        const dataAttr = instance.element.dataset.lazyData;
        let body: string | undefined;
        const headers: Record<string, string> = {
            Accept: 'text/html',
            'X-Requested-With': 'XMLHttpRequest',
        };

        // Get CSRF token
        const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        // Parse data for POST requests
        if (dataAttr && method === 'POST') {
            headers['Content-Type'] = 'application/json';
            body = dataAttr;
        }

        const response = await fetch(instance.url!, {
            method,
            headers,
            body,
            ...this.globalConfig.fetchOptions,
        });

        if (!response.ok) {
            throw new Error(`Failed to load lazy content: ${response.status}`);
        }

        const html = await response.text();
        const contentEl = instance.element.querySelector<HTMLElement>('[data-lazy-content]');

        if (contentEl) {
            contentEl.innerHTML = html;
        }

        this.showContent(instance);
    }

    /**
     * Load inline content (just reveal it)
     */
    private async loadInline(instance: LazyInstance): Promise<void> {
        // Small delay to ensure smooth animation
        await new Promise((resolve) => requestAnimationFrame(resolve));
        this.showContent(instance);
    }

    /**
     * Show the content and hide placeholder
     */
    private showContent(instance: LazyInstance): void {
        const placeholder = instance.element.querySelector<HTMLElement>('[data-lazy-placeholder]');
        const content = instance.element.querySelector<HTMLElement>('[data-lazy-content]');

        if (placeholder) {
            placeholder.classList.add('accelade-lazy-hiding');
            setTimeout(() => {
                placeholder.style.display = 'none';
            }, 200);
        }

        if (content) {
            content.style.display = '';
            // Trigger reflow
            void content.offsetHeight;
            content.classList.add('accelade-lazy-visible');
        }

        // Initialize any Accelade components inside the lazy content
        this.initAcceladeComponents(instance.element);
    }

    /**
     * Hide the content and show placeholder (for conditional loading)
     */
    hide(id: string): void {
        const instance = this.instances.get(id);
        if (!instance) return;

        const placeholder = instance.element.querySelector<HTMLElement>('[data-lazy-placeholder]');
        const content = instance.element.querySelector<HTMLElement>('[data-lazy-content]');

        if (content) {
            content.classList.remove('accelade-lazy-visible');
            setTimeout(() => {
                content.style.display = 'none';
            }, 200);
        }

        if (placeholder) {
            placeholder.style.display = '';
            placeholder.classList.remove('accelade-lazy-hiding');
        }
    }

    /**
     * Reload content (for conditional loading)
     */
    async reload(id: string): Promise<void> {
        const instance = this.instances.get(id);
        if (!instance) return;

        instance.loaded = false;
        await this.load(id);
    }

    /**
     * Initialize Accelade components within loaded content
     */
    private initAcceladeComponents(container: HTMLElement): void {
        // Check if Accelade is available globally
        const accelade = (window as unknown as Record<string, unknown>).Accelade as {
            initComponent?: (el: HTMLElement) => void;
        };

        if (accelade?.initComponent) {
            const components = container.querySelectorAll<HTMLElement>('[data-accelade]');
            components.forEach((el) => accelade.initComponent!(el));
        }
    }

    /**
     * Get a lazy instance by ID
     */
    get(id: string): LazyInstance | undefined {
        return this.instances.get(id);
    }

    /**
     * Get all lazy instances
     */
    getAll(): Map<string, LazyInstance> {
        return new Map(this.instances);
    }

    /**
     * Dispose a lazy instance
     */
    dispose(id: string): void {
        const observer = this.observers.get(id);
        if (observer) {
            observer.disconnect();
            this.observers.delete(id);
        }

        this.conditionStates.delete(id);
        this.instances.delete(id);
    }

    /**
     * Dispose all lazy instances
     */
    disposeAll(): void {
        this.observers.forEach((observer) => observer.disconnect());
        this.observers.clear();
        this.conditionStates.clear();
        this.instances.clear();
    }

    /**
     * Event handling
     */
    on(event: LazyEventType, callback: LazyEventCallback): () => void {
        if (!this.eventListeners.has(event)) {
            this.eventListeners.set(event, new Set());
        }
        this.eventListeners.get(event)!.add(callback);

        return () => {
            this.eventListeners.get(event)?.delete(callback);
        };
    }

    private emit(event: LazyEventType, instance: LazyInstance, data?: unknown): void {
        this.eventListeners.get(event)?.forEach((callback) => callback(instance, data));
    }
}

// Singleton instance
let lazyLoaderInstance: LazyLoaderManager | null = null;

export function getLazyLoader(): LazyLoaderManager {
    if (!lazyLoaderInstance) {
        lazyLoaderInstance = new LazyLoaderManager();
    }
    return lazyLoaderInstance;
}

export function initLazy(): void {
    getLazyLoader().init();
}

export function registerLazy(element: HTMLElement): LazyInstance | null {
    return getLazyLoader().register(element);
}

export function loadLazy(id: string): Promise<void> {
    return getLazyLoader().load(id);
}

export function configureLazy(config: Partial<LazyConfig>): void {
    getLazyLoader().configure(config);
}

export { LazyLoaderManager };
