/**
 * Accelade SPA Router
 * Client-side navigation without full page reloads
 */

import { getProgress, startProgress, doneProgress, type ProgressConfig } from './progress';
import { handleLinkClick, initLinks, parseLinkConfig } from './link/LinkManager';
import { modalManager, initModals } from './modal/ModalFactory';

export interface NavigationOptions {
    /** Whether to push to browser history (default: true) */
    pushState?: boolean;
    /** Whether to scroll to top after navigation (default: true) */
    scrollToTop?: boolean;
    /** Whether to preserve scroll position (default: false) */
    preserveScroll?: boolean;
    /** Whether to preserve component state (default: false) */
    preserveState?: boolean;
    /** Custom headers for the fetch request */
    headers?: Record<string, string>;
    /** Target container selector (default: body or [data-accelade-page]) */
    target?: string;
    /** Skip progress bar for this navigation */
    skipProgress?: boolean;
    /** Try to restore from cache (default: false, true for back/forward) */
    useCache?: boolean;
}

export interface RouterConfig {
    /** Selector for links to intercept */
    linkSelector: string;
    /** Selector for the page content container */
    pageSelector: string;
    /** CSS class added during navigation */
    loadingClass: string;
    /** Enable progress bar (default: true) */
    showProgress?: boolean;
    /** Progress bar configuration */
    progress?: ProgressConfig;
    /** Page transition duration in ms (default: 150) */
    transitionDuration?: number;
    /** Max pages to keep in memory (default: 10, 0 = disabled) */
    maxKeepAlive?: number;
    /** Default preserve scroll behavior (default: false) */
    defaultPreserveScroll?: boolean;
    /** Callback before navigation starts */
    onBeforeNavigate?: (url: string) => boolean | void;
    /** Callback after navigation completes */
    onAfterNavigate?: (url: string) => void;
    /** Callback on navigation error */
    onError?: (error: Error, url: string) => void;
    /** Callback when navigation starts */
    onStart?: (url: string) => void;
    /** Callback when navigation finishes (success or error) */
    onFinish?: (url: string, success: boolean) => void;
}

/**
 * Cached page entry for keep-alive feature
 */
interface CachedPage {
    /** The HTML content of the page container */
    html: string;
    /** The page title */
    title: string;
    /** Scroll position */
    scrollX: number;
    scrollY: number;
    /** Component states */
    states: Map<string, Record<string, unknown>>;
    /** Timestamp when cached */
    timestamp: number;
}

const defaultConfig: RouterConfig = {
    linkSelector: 'a[data-accelade-link], a[a-link], [data-spa-link]',
    pageSelector: '[data-accelade-page], main, body',
    loadingClass: 'accelade-loading',
    showProgress: true,
    transitionDuration: 150,
    maxKeepAlive: 10,
    defaultPreserveScroll: false,
};

/**
 * Get navigation config from AcceladeConfig
 */
function getNavigationConfig(): Partial<RouterConfig> {
    const config: Partial<RouterConfig> = {};

    if (typeof window !== 'undefined' && (window as any).AcceladeConfig?.navigation) {
        const nav = (window as any).AcceladeConfig.navigation;
        if (typeof nav.max_keep_alive === 'number') {
            config.maxKeepAlive = nav.max_keep_alive;
        }
        if (typeof nav.transition_duration === 'number') {
            config.transitionDuration = nav.transition_duration;
        }
        if (typeof nav.preserve_scroll === 'boolean') {
            config.defaultPreserveScroll = nav.preserve_scroll;
        }
    }

    return config;
}

/** Selector for modal/slideover/bottom-sheet links */
const modalLinkSelector = 'a[data-modal], a[data-slideover], a[data-bottom-sheet]';

/**
 * Accelade Router class
 */
export class AcceladeRouter {
    private config: RouterConfig;
    private abortController: AbortController | null = null;
    private initialized = false;
    /** Page cache for keep-alive feature */
    private pageCache: Map<string, CachedPage> = new Map();

    constructor(config: Partial<RouterConfig> = {}) {
        // Merge: defaults -> AcceladeConfig -> user config
        const navConfig = getNavigationConfig();
        this.config = { ...defaultConfig, ...navConfig, ...config };

        // Configure progress bar if provided
        if (this.config.progress) {
            getProgress(this.config.progress);
        }
    }

    /**
     * Get the max keep-alive limit
     */
    get maxKeepAlive(): number {
        return this.config.maxKeepAlive ?? 10;
    }

    /**
     * Cache the current page state
     */
    private cachePage(url: string, container: HTMLElement): void {
        if (this.maxKeepAlive <= 0) return;

        // Normalize URL
        const normalizedUrl = this.normalizeUrl(url);

        // Capture component states
        const states = new Map<string, Record<string, unknown>>();
        const components = container.querySelectorAll<HTMLElement>('[data-accelade]');
        components.forEach((el, index) => {
            const id = el.dataset.acceladeId ?? `component-${index}`;
            const stateStr = el.dataset.acceladeState;
            if (stateStr) {
                try {
                    states.set(id, JSON.parse(stateStr));
                } catch {
                    // Ignore parse errors
                }
            }
        });

        // Create cache entry
        const entry: CachedPage = {
            html: container.innerHTML,
            title: document.title,
            scrollX: window.scrollX,
            scrollY: window.scrollY,
            states,
            timestamp: Date.now(),
        };

        // Add to cache
        this.pageCache.set(normalizedUrl, entry);

        // Enforce cache size limit (remove oldest entries)
        if (this.pageCache.size > this.maxKeepAlive) {
            const entriesToRemove = this.pageCache.size - this.maxKeepAlive;
            const keys = Array.from(this.pageCache.keys());
            for (let i = 0; i < entriesToRemove; i++) {
                this.pageCache.delete(keys[i]);
            }
        }
    }

    /**
     * Get a cached page
     */
    private getCachedPage(url: string): CachedPage | undefined {
        return this.pageCache.get(this.normalizeUrl(url));
    }

    /**
     * Normalize URL for cache key
     */
    private normalizeUrl(url: string): string {
        const parsed = new URL(url, window.location.origin);
        return parsed.pathname + parsed.search;
    }

    /**
     * Clear the page cache
     */
    clearCache(): void {
        this.pageCache.clear();
    }

    /**
     * Get cache size
     */
    getCacheSize(): number {
        return this.pageCache.size;
    }

    /**
     * Saved persistent elements during navigation
     */
    private persistentElements: Map<string, HTMLElement> = new Map();

    /**
     * Save persistent elements before navigation
     */
    private savePersistentElements(): void {
        this.persistentElements.clear();

        const elements = document.querySelectorAll<HTMLElement>('[data-accelade-persistent]');
        elements.forEach((el) => {
            const id = el.getAttribute('data-accelade-persistent') || `persistent-${this.persistentElements.size}`;
            // Clone the element to preserve its state
            const clone = el.cloneNode(true) as HTMLElement;
            this.persistentElements.set(id, clone);

            // Store reference to original for state preservation (media playback, etc.)
            (clone as any)._originalElement = el;
        });
    }

    /**
     * Restore persistent elements after navigation
     */
    private restorePersistentElements(container: HTMLElement): void {
        if (this.persistentElements.size === 0) return;

        // Find placeholder elements in the new content
        const placeholders = container.querySelectorAll<HTMLElement>('[data-accelade-persistent]');

        placeholders.forEach((placeholder) => {
            const id = placeholder.getAttribute('data-accelade-persistent');
            if (!id) return;

            const saved = this.persistentElements.get(id);
            if (saved) {
                // Get the original element if it still exists in DOM
                const original = (saved as any)._originalElement as HTMLElement | undefined;

                if (original && original.isConnected) {
                    // Move the original element (preserves media playback state)
                    placeholder.replaceWith(original);
                } else {
                    // Use the cloned element
                    placeholder.replaceWith(saved);
                }
            }
        });

        this.persistentElements.clear();
    }

    /**
     * Check if page has persistent elements
     */
    hasPersistentElements(): boolean {
        return document.querySelectorAll('[data-accelade-persistent]').length > 0;
    }

    /**
     * Initialize the router
     */
    init(): void {
        if (this.initialized) return;

        // Intercept link clicks
        document.addEventListener('click', this.handleClick.bind(this));

        // Handle browser back/forward
        window.addEventListener('popstate', this.handlePopState.bind(this));

        // Initialize modal system
        initModals();

        this.initialized = true;
    }

    /**
     * Configure the progress bar
     */
    configureProgress(config: ProgressConfig): void {
        getProgress(config);
    }

    /**
     * Handle link clicks
     */
    private handleClick(event: MouseEvent): void {
        const target = event.target as HTMLElement;

        // Check for modal/slideover links first
        const modalLink = target.closest<HTMLAnchorElement>(modalLinkSelector);
        if (modalLink) {
            // Skip if modifier keys are pressed
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

            event.preventDefault();
            this.handleModalLink(modalLink);
            return;
        }

        const link = target.closest<HTMLAnchorElement>(this.config.linkSelector);

        if (!link) return;

        // Skip if modifier keys are pressed (allow opening in new tab)
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

        // Skip if link has target="_blank"
        if (link.target === '_blank') return;

        // Skip downloads
        if (link.hasAttribute('download')) return;

        // Parse link config to check for special handling
        const linkConfig = parseLinkConfig(link);

        // Handle external links (away attribute)
        if (linkConfig.away) {
            // Let the LinkManager handle it (may show confirmation)
            event.preventDefault();
            void handleLinkClick(link, event);
            return;
        }

        // Skip external links (different origin)
        const url = new URL(link.href, window.location.origin);
        if (url.origin !== window.location.origin) return;

        // Skip hash-only links for named modals
        if (url.pathname === window.location.pathname && url.hash) {
            // Check if this is a named modal
            const modalName = url.hash.slice(1);
            if (modalManager.getByName(modalName)) {
                event.preventDefault();
                modalManager.openNamed(modalName);
                return;
            }
            return;
        }

        event.preventDefault();

        // Use LinkManager for enhanced handling (confirmation, methods, etc.)
        void handleLinkClick(link, event);
    }

    /**
     * Handle modal/slideover/bottom-sheet link clicks
     */
    private async handleModalLink(link: HTMLAnchorElement): Promise<void> {
        const href = link.getAttribute('href');
        if (!href) return;

        // Check for hash link (pre-loaded modal)
        if (href.startsWith('#')) {
            const name = href.slice(1);
            modalManager.openNamed(name);
            return;
        }

        // Parse options from link
        const options = modalManager.parseLinkOptions(link);
        if (!options) return;

        // Determine modal type
        let type: 'modal' | 'slideover' | 'bottom-sheet' = 'modal';
        if (options.bottomSheet) {
            type = 'bottom-sheet';
        } else if (options.slideover) {
            type = 'slideover';
        }

        // Open modal with URL
        await modalManager.openUrl(href, {
            type,
            maxWidth: options.maxWidth,
            position: options.position,
            slideoverPosition: options.slideoverPosition,
        });
    }

    /**
     * Handle browser back/forward navigation
     */
    private handlePopState(_event: PopStateEvent): void {
        // Try to restore from cache for back/forward navigation
        void this.navigate(window.location.href, { pushState: false, useCache: true });
    }

    /**
     * Saved component states for preserve-state navigation
     */
    private savedStates: Map<string, Record<string, unknown>> = new Map();

    /**
     * Restore a page from cache
     */
    private async restoreFromCache(
        cached: CachedPage,
        container: HTMLElement,
        transitionDuration: number
    ): Promise<boolean> {
        // Remove leaving class, add entering class
        container.classList.remove('accelade-leaving');
        container.classList.add('accelade-entering');

        // Restore the HTML
        container.innerHTML = cached.html;

        // Restore persistent elements (media players, etc.)
        this.restorePersistentElements(container);

        // Restore component states
        const components = container.querySelectorAll<HTMLElement>('[data-accelade]');
        components.forEach((el, index) => {
            const id = el.dataset.acceladeId ?? `component-${index}`;
            const savedState = cached.states.get(id);
            if (savedState) {
                el.dataset.acceladeState = JSON.stringify(savedState);
            }
        });

        // Update page title
        document.title = cached.title;

        // Re-initialize Accelade components
        await this.wait(10);
        if ((window as any).Accelade?.init) {
            (window as any).Accelade.init();
        }

        // Remove entering class
        await this.wait(20);
        container.classList.remove('accelade-entering');

        // Re-bind SPA links
        this.bindLinks(container);

        // Restore scroll position
        window.scrollTo({
            top: cached.scrollY,
            left: cached.scrollX,
            behavior: 'instant'
        });

        return true;
    }

    /**
     * Navigate to a URL
     */
    async navigate(url: string, options: NavigationOptions = {}): Promise<boolean> {
        const {
            pushState = true,
            scrollToTop = true,
            preserveScroll = this.config.defaultPreserveScroll ?? false,
            preserveState = false,
            headers = {},
            target,
            skipProgress = false,
            useCache = false,
        } = options;

        const showProgress = this.config.showProgress && !skipProgress;
        const transitionDuration = this.config.transitionDuration ?? 150;

        // Capture scroll position if preserveScroll is enabled
        const savedScrollX = preserveScroll ? window.scrollX : 0;
        const savedScrollY = preserveScroll ? window.scrollY : 0;

        // Capture component states if preserveState is enabled
        if (preserveState) {
            this.captureComponentStates();
        }

        // Check if this is a framework switch (demo pages only)
        // If navigating to a DIFFERENT framework demo, do a full page reload
        const currentFramework = document.querySelector('meta[name="accelade-framework"]')?.getAttribute('content');
        if (currentFramework) {
            const urlPath = new URL(url, window.location.origin).pathname;

            // Only check for framework switches on demo routes
            const frameworkFromUrl = this.detectFrameworkFromUrl(urlPath);

            if (frameworkFromUrl && frameworkFromUrl !== currentFramework) {
                if (showProgress) startProgress();
                window.location.href = url;
                return true;
            }
        }

        // Allow cancellation of navigation
        if (this.config.onBeforeNavigate) {
            const result = this.config.onBeforeNavigate(url);
            if (result === false) return false;
        }

        // Abort any pending navigation
        if (this.abortController) {
            this.abortController.abort();
        }
        this.abortController = new AbortController();

        // Find the target container
        const containerSelector = target ?? this.config.pageSelector;
        const container = document.querySelector<HTMLElement>(containerSelector);

        if (!container) {
            console.error('Accelade Router: Container not found:', containerSelector);
            return false;
        }

        // Cache current page before navigating (for keep-alive)
        if (this.maxKeepAlive > 0) {
            this.cachePage(window.location.href, container);
        }

        // Check if we can restore from cache
        const cachedPage = useCache ? this.getCachedPage(url) : undefined;

        // Start progress bar (skip if restoring from cache)
        if (showProgress && !cachedPage) {
            startProgress();
        }

        // Callback: onStart
        if (this.config.onStart) {
            this.config.onStart(url);
        }

        // Save persistent elements before navigation
        this.savePersistentElements();

        // Add leaving class for exit animation
        container.classList.add('accelade-leaving');

        // Wait for exit animation
        await this.wait(transitionDuration);

        let success = false;

        try {
            // Try to restore from cache first (for back/forward navigation)
            if (cachedPage) {
                success = await this.restoreFromCache(cachedPage, container, transitionDuration);

                // Update browser history
                if (pushState) {
                    window.history.pushState({ url }, cachedPage.title, url);
                }

                // Callback: onAfterNavigate
                if (this.config.onAfterNavigate) {
                    this.config.onAfterNavigate(url);
                }

                // Callback: onFinish
                if (this.config.onFinish) {
                    this.config.onFinish(url, true);
                }

                this.abortController = null;
                return true;
            }

            // Fetch the page from server
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Accelade-SPA': 'true',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                    ...headers,
                },
                signal: this.abortController.signal,
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const html = await response.text();

            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Find the content in the response
            const newContainer = doc.querySelector<HTMLElement>(containerSelector);
            const newContent = newContainer?.innerHTML ?? doc.body.innerHTML;

            // Remove leaving class, add entering class
            container.classList.remove('accelade-leaving');
            container.classList.add('accelade-entering');

            // Update the page content (components will have data-accelade-cloak)
            container.innerHTML = newContent;

            // Restore persistent elements (media players, etc.)
            this.restorePersistentElements(container);

            // Execute inline scripts in the new content
            this.executeScripts(container);

            // Update the page title
            const newTitle = doc.querySelector('title')?.textContent;
            if (newTitle) {
                document.title = newTitle;
            }

            // Update browser history
            if (pushState) {
                window.history.pushState({ url }, newTitle ?? '', url);
            }

            // Restore component states BEFORE init if preserveState was enabled
            // This ensures init sees the restored state
            if (preserveState) {
                this.restoreComponentStates();
            }

            // Re-initialize Accelade components in the new content
            await this.wait(10);
            if (window.Accelade?.init) {
                window.Accelade.init();
            }

            // Remove entering class after a frame
            await this.wait(20);
            container.classList.remove('accelade-entering');

            // Re-bind SPA links in new content
            this.bindLinks(container);

            // Handle scroll position
            if (preserveScroll) {
                // Restore previous scroll position
                window.scrollTo({ top: savedScrollY, left: savedScrollX, behavior: 'instant' });
            } else if (scrollToTop) {
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'instant' });
            }

            // Callback
            if (this.config.onAfterNavigate) {
                this.config.onAfterNavigate(url);
            }

            success = true;
            return true;
        } catch (error) {
            if ((error as Error).name === 'AbortError') {
                // Navigation was cancelled
                return false;
            }

            console.error('Accelade Router: Navigation failed:', error);

            // Remove transition classes on error
            container.classList.remove('accelade-leaving', 'accelade-entering');

            if (this.config.onError) {
                this.config.onError(error as Error, url);
            } else {
                // Fallback to traditional navigation
                window.location.href = url;
            }

            return false;
        } finally {
            // Complete progress bar
            if (showProgress) {
                doneProgress();
            }

            // Callback: onFinish
            if (this.config.onFinish) {
                this.config.onFinish(url, success);
            }

            this.abortController = null;
        }
    }

    /**
     * Promise-based wait helper
     */
    private wait(ms: number): Promise<void> {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Execute scripts in a container
     * When innerHTML is set, script tags don't execute automatically
     * IMPORTANT: Be very selective about which scripts to execute to avoid loops
     */
    private executeScripts(container: HTMLElement): void {
        const scripts = container.querySelectorAll<HTMLScriptElement>('script');

        scripts.forEach((oldScript) => {
            // Skip accelade:script tags - these are processed by the framework
            if (oldScript.hasAttribute('a-script') ||
                oldScript.hasAttribute('v-script') ||
                oldScript.hasAttribute('state-script')) {
                return;
            }

            // Skip if it's an accelade bundle script (already loaded)
            if (oldScript.src && oldScript.src.includes('accelade')) {
                return;
            }

            // Skip Vite/build-related scripts
            if (oldScript.src && (
                oldScript.src.includes('@vite') ||
                oldScript.src.includes('vite') ||
                oldScript.src.includes('node_modules')
            )) {
                return;
            }

            // Skip module scripts that might cause issues
            if (oldScript.type === 'module' && oldScript.src) {
                return;
            }

            // Skip inline scripts that call Accelade.init() to prevent loops
            const content = oldScript.textContent ?? '';
            if (content.includes('Accelade.init') || content.includes('Accelade?.init')) {
                return;
            }

            // Create a new script element to force execution
            const newScript = document.createElement('script');

            // Copy attributes
            Array.from(oldScript.attributes).forEach((attr) => {
                newScript.setAttribute(attr.name, attr.value);
            });

            // Copy content
            newScript.textContent = content;

            // Replace old script with new one (this triggers execution)
            oldScript.parentNode?.replaceChild(newScript, oldScript);
        });
    }

    /**
     * Bind SPA behavior to links within a container
     */
    bindLinks(container: HTMLElement): void {
        // Initialize link features (prefetching, etc.)
        initLinks(container);
    }

    /**
     * Prefetch a URL (for performance)
     */
    async prefetch(url: string): Promise<void> {
        try {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
        } catch {
            // Ignore prefetch errors
        }
    }

    /**
     * Get current URL
     */
    getCurrentUrl(): string {
        return window.location.href;
    }

    /**
     * Check if a URL is the current page
     */
    isCurrentUrl(url: string): boolean {
        const current = new URL(window.location.href);
        const check = new URL(url, window.location.origin);
        return current.pathname === check.pathname && current.search === check.search;
    }

    /**
     * Detect framework from URL path (for demo routes)
     * Returns null if not a framework demo route
     */
    private detectFrameworkFromUrl(urlPath: string): string | null {
        if (urlPath.includes('/demo/vanilla')) return 'vanilla';
        if (urlPath.includes('/demo/vue')) return 'vue';
        if (urlPath.includes('/demo/react')) return 'react';
        if (urlPath.includes('/demo/svelte')) return 'svelte';
        if (urlPath.includes('/demo/angular')) return 'angular';
        return null;
    }

    /**
     * Capture component states before navigation
     */
    private captureComponentStates(): void {
        this.savedStates.clear();

        const components = document.querySelectorAll<HTMLElement>('[data-accelade]');
        components.forEach((el) => {
            const id = el.dataset.acceladeId;
            const stateStr = el.dataset.acceladeState;

            if (id && stateStr) {
                try {
                    // Try to get the current live state from the component
                    // This works because we store state in data attributes or can access via window
                    const state = JSON.parse(stateStr) as Record<string, unknown>;
                    this.savedStates.set(id, state);
                } catch {
                    // Ignore parse errors
                }
            }
        });
    }

    /**
     * Restore component states after navigation
     */
    private restoreComponentStates(): void {
        if (this.savedStates.size === 0) return;

        const components = document.querySelectorAll<HTMLElement>('[data-accelade]');
        components.forEach((el) => {
            const id = el.dataset.acceladeId;

            // Try to find a matching saved state
            // Since IDs are generated randomly, we match by position/order
            const savedStateKeys = Array.from(this.savedStates.keys());
            const componentIndex = Array.from(components).indexOf(el);

            if (componentIndex < savedStateKeys.length) {
                const savedKey = savedStateKeys[componentIndex];
                const savedState = this.savedStates.get(savedKey);

                if (savedState) {
                    // Update the data attribute so init picks it up
                    el.dataset.acceladeState = JSON.stringify(savedState);
                }
            }
        });

        this.savedStates.clear();
    }
}

// Singleton instance
let routerInstance: AcceladeRouter | null = null;

/**
 * Get or create the router instance
 */
export function getRouter(config?: Partial<RouterConfig>): AcceladeRouter {
    if (!routerInstance) {
        routerInstance = new AcceladeRouter(config);
    }
    return routerInstance;
}

/**
 * Initialize the router
 */
export function initRouter(config?: Partial<RouterConfig>): AcceladeRouter {
    const router = getRouter(config);
    router.init();
    return router;
}

/**
 * Navigate to a URL
 */
export function navigate(url: string, options?: NavigationOptions): Promise<boolean> {
    return getRouter().navigate(url, options);
}

// Re-export progress functions for convenience
export {
    getProgress,
    startProgress,
    doneProgress,
    type ProgressConfig,
} from './progress';

// Re-export link functions
export {
    showConfirmDialog,
    confirm,
    confirmDanger,
} from './link/ConfirmDialog';

export {
    parseLinkConfig,
    handleLinkClick,
    initLinks,
} from './link/LinkManager';

export type { LinkConfig, HttpMethod, ConfirmDialogOptions } from './link/types';

// Re-export modal functions
export { modalManager, initModals, initModalLinks } from './modal/ModalFactory';
export type { ModalConfig, ModalInstance, ModalOpenOptions, ModalMaxWidth, ModalPosition, SlideoverPosition } from './modal/types';

export default {
    AcceladeRouter,
    getRouter,
    initRouter,
    navigate,
    modal: modalManager,
};
