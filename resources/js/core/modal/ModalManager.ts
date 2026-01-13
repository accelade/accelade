/**
 * ModalManager - Singleton manager for modals and slideovers
 *
 * Handles modal creation, opening, closing, stacking, and events.
 */

import type {
    ModalConfig,
    ModalInstance,
    ModalOpenOptions,
    ModalMaxWidth,
    ModalPosition,
    SlideoverPosition,
    ModalType,
    LinkModalOptions,
} from './types';

/**
 * Default modal configuration
 */
const defaultConfig: ModalConfig = {
    type: 'modal',
    maxWidth: '2xl',
    position: 'center',
    slideoverPosition: 'right',
    closeExplicitly: false,
    closeButton: true,
    opened: false,
};

/**
 * Default slideover configuration
 */
const defaultSlideoverConfig: Partial<ModalConfig> = {
    type: 'slideover',
    maxWidth: 'md',
};

/**
 * Default bottom sheet configuration
 */
const defaultBottomSheetConfig: Partial<ModalConfig> = {
    type: 'bottom-sheet',
    maxWidth: '2xl',
};

/**
 * Max-width CSS values
 */
const maxWidthClasses: Record<ModalMaxWidth, string> = {
    sm: '24rem',
    md: '28rem',
    lg: '32rem',
    xl: '36rem',
    '2xl': '42rem',
    '3xl': '48rem',
    '4xl': '56rem',
    '5xl': '64rem',
    '6xl': '72rem',
    '7xl': '80rem',
};

/**
 * CSS for modals and slideovers (RTL and Dark mode aware)
 */
const modalStyles = `
:root {
    --accelade-modal-bg: #ffffff;
    --accelade-modal-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    --accelade-modal-shadow-side: -10px 0 40px -5px rgba(0, 0, 0, 0.2);
    --accelade-modal-shadow-bottom: 0 -10px 40px -5px rgba(0, 0, 0, 0.2);
    --accelade-modal-close-color: #6b7280;
    --accelade-modal-close-hover-bg: #f3f4f6;
    --accelade-modal-close-hover-color: #1f2937;
    --accelade-modal-close-focus-ring: #6366f1;
    --accelade-modal-close-focus-ring-bg: white;
    --accelade-modal-handle-bg: #d1d5db;
    --accelade-modal-loading-color: #6b7280;
}
.dark, [data-theme="dark"] {
    --accelade-modal-bg: #1e293b;
    --accelade-modal-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    --accelade-modal-shadow-side: -10px 0 40px -5px rgba(0, 0, 0, 0.4);
    --accelade-modal-shadow-bottom: 0 -10px 40px -5px rgba(0, 0, 0, 0.4);
    --accelade-modal-close-color: #94a3b8;
    --accelade-modal-close-hover-bg: #334155;
    --accelade-modal-close-hover-color: #f1f5f9;
    --accelade-modal-close-focus-ring: #818cf8;
    --accelade-modal-close-focus-ring-bg: #1e293b;
    --accelade-modal-handle-bg: #475569;
    --accelade-modal-loading-color: #94a3b8;
}
.accelade-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 99990;
    opacity: 0;
    transition: opacity 0.2s ease-out;
    overflow-y: auto;
}
.accelade-modal-overlay.show {
    opacity: 1;
}
.accelade-modal-overlay.modal {
    display: flex;
    padding: 1rem;
}
.accelade-modal-overlay.modal.position-top {
    align-items: flex-start;
    justify-content: center;
}
.accelade-modal-overlay.modal.position-center {
    align-items: center;
    justify-content: center;
}
.accelade-modal-overlay.modal.position-bottom {
    align-items: flex-end;
    justify-content: center;
}
.accelade-modal-overlay.slideover {
    display: flex;
}
.accelade-modal-overlay.slideover.position-left {
    justify-content: flex-start;
}
.accelade-modal-overlay.slideover.position-right {
    justify-content: flex-end;
}
.accelade-modal-panel {
    background: var(--accelade-modal-bg);
    position: relative;
    transition: transform 0.2s ease-out, opacity 0.2s ease-out;
}
.accelade-modal-overlay.modal .accelade-modal-panel {
    border-radius: 0.75rem;
    box-shadow: var(--accelade-modal-shadow);
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
    transform: scale(0.95);
    opacity: 0;
}
.accelade-modal-overlay.modal.show .accelade-modal-panel {
    transform: scale(1);
    opacity: 1;
}
.accelade-modal-overlay.slideover .accelade-modal-panel {
    height: 100vh;
    max-height: 100vh;
    overflow-y: auto;
    box-shadow: var(--accelade-modal-shadow-side);
}
.accelade-modal-overlay.slideover.position-left .accelade-modal-panel {
    transform: translateX(-100%);
}
.accelade-modal-overlay.slideover.position-right .accelade-modal-panel {
    transform: translateX(100%);
}
.accelade-modal-overlay.slideover.show .accelade-modal-panel {
    transform: translateX(0);
}
.accelade-modal-overlay.bottom-sheet {
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.accelade-modal-overlay.bottom-sheet .accelade-modal-panel {
    border-radius: 1rem 1rem 0 0;
    box-shadow: var(--accelade-modal-shadow-bottom);
    max-height: 90vh;
    overflow-y: auto;
    transform: translateY(100%);
    width: 100%;
}
.accelade-modal-overlay.bottom-sheet.show .accelade-modal-panel {
    transform: translateY(0);
}
.accelade-modal-overlay.bottom-sheet .accelade-modal-handle {
    width: 2.5rem;
    height: 0.25rem;
    background: var(--accelade-modal-handle-bg);
    border-radius: 9999px;
    margin: 0.75rem auto 0.5rem;
}
.accelade-modal-close {
    position: absolute;
    top: 0.75rem;
    inset-inline-end: 0.75rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--accelade-modal-close-color);
    cursor: pointer;
    border-radius: 0.375rem;
    transition: background 0.15s ease, color 0.15s ease;
    z-index: 10;
}
.accelade-modal-close:hover {
    background: var(--accelade-modal-close-hover-bg);
    color: var(--accelade-modal-close-hover-color);
}
.accelade-modal-close:focus {
    outline: none;
    box-shadow: 0 0 0 2px var(--accelade-modal-close-focus-ring-bg), 0 0 0 4px var(--accelade-modal-close-focus-ring);
}
.accelade-modal-close svg {
    width: 1.25rem;
    height: 1.25rem;
}
.accelade-modal-content {
    padding: 1.5rem;
}
.accelade-modal-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 8rem;
    color: var(--accelade-modal-loading-color);
}
.accelade-modal-loading svg {
    width: 2rem;
    height: 2rem;
    animation: accelade-spin 1s linear infinite;
}
@keyframes accelade-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
`;

/**
 * Generate unique ID
 */
let modalIdCounter = 0;
function generateId(): string {
    return `accelade-modal-${++modalIdCounter}`;
}

/**
 * ModalManager singleton
 */
class ModalManager {
    private modals: Map<string, ModalInstance> = new Map();
    private namedModals: Map<string, ModalInstance> = new Map();
    private stack: string[] = [];
    private stylesInjected = false;

    /**
     * Ensure styles are injected
     */
    private ensureStyles(): void {
        if (this.stylesInjected) return;
        if (document.getElementById('accelade-modal-styles')) {
            this.stylesInjected = true;
            return;
        }

        const style = document.createElement('style');
        style.id = 'accelade-modal-styles';
        style.textContent = modalStyles;
        document.head.appendChild(style);
        this.stylesInjected = true;
    }

    /**
     * Parse config from element attributes
     */
    parseConfig(element: HTMLElement): ModalConfig {
        let type: ModalType = 'modal';
        if (element.hasAttribute('data-bottom-sheet')) {
            type = 'bottom-sheet';
        } else if (element.hasAttribute('data-slideover')) {
            type = 'slideover';
        }

        let defaults = { ...defaultConfig };
        if (type === 'slideover') {
            defaults = { ...defaultConfig, ...defaultSlideoverConfig };
        } else if (type === 'bottom-sheet') {
            defaults = { ...defaultConfig, ...defaultBottomSheetConfig };
        }

        return {
            id: element.getAttribute('data-modal-id') ?? undefined,
            name: element.getAttribute('data-modal-name') ?? undefined,
            type,
            maxWidth: (element.getAttribute('data-max-width') as ModalMaxWidth) ?? defaults.maxWidth,
            position: (element.getAttribute('data-position') as ModalPosition) ?? defaults.position,
            slideoverPosition: (element.getAttribute('data-slideover-position') as SlideoverPosition) ?? defaults.slideoverPosition,
            closeExplicitly: element.hasAttribute('data-close-explicitly'),
            closeButton: !element.hasAttribute('data-no-close-button'),
            opened: element.hasAttribute('data-opened'),
        };
    }

    /**
     * Parse link modal options from anchor element
     */
    parseLinkOptions(element: HTMLAnchorElement): LinkModalOptions | null {
        const isModal = element.hasAttribute('data-modal');
        const isSlideover = element.hasAttribute('data-slideover');
        const isBottomSheet = element.hasAttribute('data-bottom-sheet');

        if (!isModal && !isSlideover && !isBottomSheet) {
            return null;
        }

        return {
            modal: isModal,
            slideover: isSlideover,
            bottomSheet: isBottomSheet,
            maxWidth: element.getAttribute('data-modal-max-width') as ModalMaxWidth | undefined,
            position: element.getAttribute('data-modal-position') as ModalPosition | undefined,
            slideoverPosition: element.getAttribute('data-slideover-position') as SlideoverPosition | undefined,
        };
    }

    /**
     * Register a pre-loaded modal from the page
     */
    register(element: HTMLElement, config: ModalConfig): ModalInstance {
        this.ensureStyles();

        const id = config.id ?? generateId();
        const instance = this.createInstance(id, element, config);

        this.modals.set(id, instance);

        if (config.name) {
            this.namedModals.set(config.name, instance);
        }

        // Open immediately if configured
        if (config.opened) {
            requestAnimationFrame(() => instance.open());
        }

        return instance;
    }

    /**
     * Create a modal instance
     */
    private createInstance(id: string, element: HTMLElement, config: ModalConfig): ModalInstance {
        let isOpen = false;
        let overlay: HTMLElement | null = null;

        const open = (): void => {
            if (isOpen) return;

            this.ensureStyles();
            isOpen = true;

            // Create overlay
            overlay = this.createOverlay(config, element.innerHTML);

            // Add to DOM
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';

            // Add to stack
            this.stack.push(id);

            // Setup event handlers
            this.setupOverlayEvents(overlay, instance);

            // Animate in
            requestAnimationFrame(() => {
                overlay?.classList.add('show');
            });

            // Dispatch event
            this.dispatchEvent('open', instance);
        };

        const close = (): void => {
            if (!isOpen || !overlay) return;

            isOpen = false;
            overlay.classList.remove('show');

            // Remove from stack
            const stackIndex = this.stack.indexOf(id);
            if (stackIndex > -1) {
                this.stack.splice(stackIndex, 1);
            }

            // Restore body scroll if no more modals
            if (this.stack.length === 0) {
                document.body.style.overflow = '';
            }

            // Remove after animation
            const overlayToRemove = overlay;
            setTimeout(() => {
                overlayToRemove.remove();
            }, 200);

            overlay = null;

            // Dispatch event
            this.dispatchEvent('close', instance);
        };

        const setIsOpen = (open: boolean): void => {
            if (open) {
                instance.open();
            } else {
                instance.close();
            }
        };

        const dispose = (): void => {
            close();
            this.modals.delete(id);
            if (config.name) {
                this.namedModals.delete(config.name);
            }
        };

        const instance: ModalInstance = {
            id,
            name: config.name,
            config,
            element,
            get isOpen() {
                return isOpen;
            },
            open,
            close,
            setIsOpen,
            dispose,
        };

        return instance;
    }

    /**
     * Create overlay element
     */
    private createOverlay(config: ModalConfig, content: string): HTMLElement {
        const overlay = document.createElement('div');
        overlay.className = `accelade-modal-overlay ${config.type}`;
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');

        // Position class
        if (config.type === 'modal') {
            overlay.classList.add(`position-${config.position}`);
        } else if (config.type === 'slideover') {
            overlay.classList.add(`position-${config.slideoverPosition}`);
        }
        // bottom-sheet doesn't need position class (always bottom)

        // Panel
        const panel = document.createElement('div');
        panel.className = 'accelade-modal-panel';
        panel.style.width = '100%';
        panel.style.maxWidth = maxWidthClasses[config.maxWidth];

        // Add handle for bottom sheet
        if (config.type === 'bottom-sheet') {
            const handle = document.createElement('div');
            handle.className = 'accelade-modal-handle';
            panel.appendChild(handle);
        }

        // Close button
        if (config.closeButton) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'accelade-modal-close';
            closeBtn.type = 'button';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.innerHTML = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
            panel.appendChild(closeBtn);
        }

        // Content
        const contentEl = document.createElement('div');
        contentEl.className = 'accelade-modal-content';
        contentEl.innerHTML = content;
        panel.appendChild(contentEl);

        overlay.appendChild(panel);
        return overlay;
    }

    /**
     * Setup event handlers for overlay
     */
    private setupOverlayEvents(overlay: HTMLElement, instance: ModalInstance): void {
        // Close button
        const closeBtn = overlay.querySelector('.accelade-modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => instance.close());
        }

        // Outside click
        if (!instance.config.closeExplicitly) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    instance.close();
                }
            });
        }

        // Escape key
        if (!instance.config.closeExplicitly) {
            const handleKeydown = (e: KeyboardEvent): void => {
                if (e.key === 'Escape' && this.stack[this.stack.length - 1] === instance.id) {
                    instance.close();
                    document.removeEventListener('keydown', handleKeydown);
                }
            };
            document.addEventListener('keydown', handleKeydown);
        }

        // Expose modal object to content for close handlers
        const panel = overlay.querySelector('.accelade-modal-panel');
        if (panel) {
            (panel as HTMLElement & { modal: ModalInstance }).modal = instance;

            // Find all elements with @click="modal.close" and bind them
            panel.querySelectorAll('[\\@click*="modal.close"], [data-modal-close]').forEach((el) => {
                el.addEventListener('click', () => instance.close());
            });
        }
    }

    /**
     * Open a modal by URL (async content loading)
     */
    async openUrl(url: string, options: Omit<ModalOpenOptions, 'url' | 'content'> = {}): Promise<ModalInstance> {
        this.ensureStyles();

        const id = generateId();
        const type = options.type ?? (options.slideoverPosition ? 'slideover' : 'modal');
        let defaults = { ...defaultConfig };
        if (type === 'slideover') {
            defaults = { ...defaultConfig, ...defaultSlideoverConfig };
        } else if (type === 'bottom-sheet') {
            defaults = { ...defaultConfig, ...defaultBottomSheetConfig };
        }

        const config: ModalConfig = {
            id,
            type,
            maxWidth: options.maxWidth ?? defaults.maxWidth,
            position: options.position ?? defaults.position,
            slideoverPosition: options.slideoverPosition ?? defaults.slideoverPosition,
            closeExplicitly: options.closeExplicitly ?? defaults.closeExplicitly,
            closeButton: options.closeButton ?? defaults.closeButton,
            opened: true,
        };

        // Create loading overlay
        const loadingContent = `<div class="accelade-modal-loading"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></div>`;

        // Create temp element for registration
        const tempElement = document.createElement('div');
        tempElement.innerHTML = loadingContent;

        const instance = this.createInstance(id, tempElement, config);
        this.modals.set(id, instance);

        // Open with loading state
        instance.open();

        // Store onClose callback
        if (options.onClose) {
            const originalClose = instance.close;
            instance.close = (): void => {
                originalClose();
                options.onClose?.();
            };
        }

        // Fetch content
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Accelade-Modal': 'true',
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'text/html, application/xhtml+xml',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const html = await response.text();

            // Update content
            const overlay = document.querySelector(`.accelade-modal-overlay`) as HTMLElement;
            if (overlay) {
                const contentEl = overlay.querySelector('.accelade-modal-content');
                if (contentEl) {
                    contentEl.innerHTML = html;

                    // Re-setup close handlers
                    this.setupOverlayEvents(overlay, instance);

                    // Initialize any Accelade components in the new content
                    if (window.Accelade?.init) {
                        window.Accelade.init();
                    }
                }
            }
        } catch (error) {
            console.error('[Accelade Modal] Failed to load content:', error);
            instance.close();
        }

        return instance;
    }

    /**
     * Open a modal with HTML content
     */
    open(options: ModalOpenOptions): ModalInstance {
        if (options.url) {
            // Return a promise-based instance for URL loading
            const placeholder = document.createElement('div');
            const instance = this.createInstance(generateId(), placeholder, {
                ...defaultConfig,
                type: options.type ?? 'modal',
                maxWidth: options.maxWidth ?? defaultConfig.maxWidth,
                position: options.position ?? defaultConfig.position,
                slideoverPosition: options.slideoverPosition ?? defaultConfig.slideoverPosition,
                closeExplicitly: options.closeExplicitly ?? defaultConfig.closeExplicitly,
                closeButton: options.closeButton ?? defaultConfig.closeButton,
                opened: true,
            });

            this.openUrl(options.url, options);
            return instance;
        }

        this.ensureStyles();

        const id = generateId();
        const type = options.type ?? 'modal';
        const defaults = type === 'slideover'
            ? { ...defaultConfig, ...defaultSlideoverConfig }
            : { ...defaultConfig };

        const config: ModalConfig = {
            id,
            type,
            maxWidth: options.maxWidth ?? defaults.maxWidth,
            position: options.position ?? defaults.position,
            slideoverPosition: options.slideoverPosition ?? defaults.slideoverPosition,
            closeExplicitly: options.closeExplicitly ?? defaults.closeExplicitly,
            closeButton: options.closeButton ?? defaults.closeButton,
            opened: true,
        };

        const tempElement = document.createElement('div');
        tempElement.innerHTML = options.content ?? '';

        const instance = this.createInstance(id, tempElement, config);
        this.modals.set(id, instance);

        if (options.onClose) {
            const originalClose = instance.close;
            instance.close = (): void => {
                originalClose();
                options.onClose?.();
            };
        }

        instance.open();
        return instance;
    }

    /**
     * Open a named modal (pre-loaded)
     */
    openNamed(name: string): ModalInstance | undefined {
        const instance = this.namedModals.get(name);
        if (instance) {
            instance.open();
        }
        return instance;
    }

    /**
     * Close a modal by ID
     */
    close(id: string): void {
        const instance = this.modals.get(id);
        if (instance) {
            instance.close();
        }
    }

    /**
     * Close the topmost modal
     */
    closeLast(): void {
        const lastId = this.stack[this.stack.length - 1];
        if (lastId) {
            this.close(lastId);
        }
    }

    /**
     * Close all modals
     */
    closeAll(): void {
        [...this.stack].reverse().forEach((id) => this.close(id));
    }

    /**
     * Get a modal by ID
     */
    get(id: string): ModalInstance | undefined {
        return this.modals.get(id);
    }

    /**
     * Get a modal by name
     */
    getByName(name: string): ModalInstance | undefined {
        return this.namedModals.get(name);
    }

    /**
     * Check if any modal is open
     */
    hasOpen(): boolean {
        return this.stack.length > 0;
    }

    /**
     * Dispatch modal event
     */
    private dispatchEvent(type: 'open' | 'close', instance: ModalInstance): void {
        const event = new CustomEvent(`accelade:modal-${type}`, {
            detail: {
                id: instance.id,
                name: instance.name,
                isOpen: instance.isOpen,
            },
            bubbles: true,
        });
        document.dispatchEvent(event);
    }

    /**
     * Handle hash-based modal opening
     */
    handleHashChange(): void {
        const hash = window.location.hash.slice(1);
        if (hash) {
            const instance = this.namedModals.get(hash);
            if (instance) {
                instance.open();
            }
        }
    }

    /**
     * Initialize hash listener
     */
    initHashListener(): void {
        window.addEventListener('hashchange', () => this.handleHashChange());

        // Check initial hash
        if (window.location.hash) {
            this.handleHashChange();
        }
    }
}

// Singleton instance
export const modalManager = new ModalManager();

// Export for window.Accelade.modal
export default modalManager;

// Extend window type
declare global {
    interface Window {
        Accelade?: {
            init?: () => void;
            modal?: ModalManager;
            [key: string]: unknown;
        };
    }
}
