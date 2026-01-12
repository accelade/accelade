/**
 * Teleport Manager
 *
 * Manages teleporting content from one DOM location to another
 * while preserving reactivity and component scope.
 */

import type { TeleportConfig, TeleportInstance, TeleportEventDetail } from './types';

/**
 * Parse configuration from element data attributes
 */
function parseConfig(element: HTMLElement): TeleportConfig {
    const id = element.dataset.teleportId ||
        `teleport-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    const to = element.dataset.teleportTo || '';
    const disabled = element.dataset.teleportDisabled === 'true';

    return {
        id,
        to,
        disabled,
    };
}

/**
 * Create a teleport instance
 */
function createTeleportInstance(element: HTMLElement): TeleportInstance {
    const config = parseConfig(element);

    // Create a placeholder comment to mark the original position
    const placeholder = document.createComment(`teleport:${config.id}`);

    // The content wrapper element
    let contentElement: HTMLElement | null = null;
    let targetElement: HTMLElement | null = null;
    let isTeleported = false;

    /**
     * Teleport the content to target
     */
    const teleport = (): boolean => {
        if (config.disabled || !config.to) {
            return false;
        }

        // Find target element
        targetElement = document.querySelector(config.to);
        if (!targetElement) {
            console.warn(`Accelade Teleport: Target "${config.to}" not found`);
            dispatchEvent(false, `Target "${config.to}" not found`);
            return false;
        }

        // Get the content (children of the source element)
        if (!contentElement) {
            // Create a wrapper for the teleported content
            contentElement = document.createElement('div');
            contentElement.dataset.teleportContent = config.id;
            contentElement.dataset.teleportFrom = config.id;

            // Move all children to the content wrapper
            while (element.firstChild) {
                contentElement.appendChild(element.firstChild);
            }

            // Insert placeholder
            element.appendChild(placeholder);
        }

        // Move content to target
        targetElement.appendChild(contentElement);
        isTeleported = true;

        // Mark source as teleported
        element.dataset.teleportActive = 'true';

        dispatchEvent(true);
        return true;
    };

    /**
     * Return content to original position
     */
    const returnContent = (): void => {
        if (!isTeleported || !contentElement) {
            return;
        }

        // Remove placeholder
        if (placeholder.parentNode === element) {
            element.removeChild(placeholder);
        }

        // Move content back
        while (contentElement.firstChild) {
            element.appendChild(contentElement.firstChild);
        }

        // Remove empty content wrapper from target
        if (contentElement.parentNode) {
            contentElement.parentNode.removeChild(contentElement);
        }

        contentElement = null;
        targetElement = null;
        isTeleported = false;

        delete element.dataset.teleportActive;
    };

    /**
     * Update target selector and re-teleport
     */
    const updateTarget = (selector: string): boolean => {
        // Return content first if already teleported
        if (isTeleported) {
            returnContent();
        }

        config.to = selector;
        return teleport();
    };

    /**
     * Dispatch teleport event
     */
    const dispatchEvent = (success: boolean, error?: string): void => {
        const detail: TeleportEventDetail = {
            id: config.id,
            to: config.to,
            success,
            error,
        };

        document.dispatchEvent(new CustomEvent('accelade:teleport', { detail }));
        element.dispatchEvent(new CustomEvent('teleport', { detail, bubbles: true }));
    };

    /**
     * Clean up
     */
    const dispose = (): void => {
        returnContent();
    };

    const instance: TeleportInstance = {
        id: config.id,
        config,
        sourceElement: element,
        get targetElement() { return targetElement; },
        get contentElement() { return contentElement; },
        get isTeleported() { return isTeleported; },
        teleport,
        return: returnContent,
        updateTarget,
        dispose,
    };

    return instance;
}

/**
 * Teleport Manager Singleton
 */
class TeleportManagerClass {
    private instances: Map<string, TeleportInstance> = new Map();
    private initialized = false;

    /**
     * Initialize the teleport system
     */
    init(): void {
        if (this.initialized) {
            return;
        }

        this.initialized = true;

        // Initialize existing teleport elements
        this.initTeleports();

        // Watch for new teleport elements via MutationObserver
        this.setupMutationObserver();
    }

    /**
     * Initialize all teleport elements in the document
     */
    initTeleports(container: HTMLElement | Document = document): void {
        const elements = container.querySelectorAll<HTMLElement>('[data-accelade-teleport]');

        elements.forEach((element) => {
            if (!element.dataset.teleportInitialized) {
                this.register(element);
            }
        });
    }

    /**
     * Register a teleport element
     */
    register(element: HTMLElement): TeleportInstance | null {
        if (element.dataset.teleportInitialized) {
            const existingId = element.dataset.teleportId;
            return existingId ? this.instances.get(existingId) || null : null;
        }

        const instance = createTeleportInstance(element);
        this.instances.set(instance.id, instance);
        element.dataset.teleportInitialized = 'true';
        element.dataset.teleportId = instance.id;

        // Auto-teleport if target is specified and not disabled
        if (instance.config.to && !instance.config.disabled) {
            // Wait for parent Accelade component to fully initialize bindings
            // Use setTimeout to ensure we run after all synchronous initialization
            // and after requestAnimationFrame callbacks from component init
            setTimeout(() => {
                // Double-check element is still in DOM
                if (element.isConnected) {
                    instance.teleport();
                }
            }, 50);
        }

        return instance;
    }

    /**
     * Unregister a teleport instance
     */
    unregister(id: string): void {
        const instance = this.instances.get(id);
        if (instance) {
            instance.dispose();
            this.instances.delete(id);
        }
    }

    /**
     * Get a teleport instance by ID
     */
    get(id: string): TeleportInstance | undefined {
        return this.instances.get(id);
    }

    /**
     * Get all teleport instances
     */
    getAll(): Map<string, TeleportInstance> {
        return new Map(this.instances);
    }

    /**
     * Teleport content by ID
     */
    teleport(id: string): boolean {
        const instance = this.instances.get(id);
        if (!instance) {
            console.warn(`Accelade Teleport: Instance "${id}" not found`);
            return false;
        }
        return instance.teleport();
    }

    /**
     * Return teleported content by ID
     */
    return(id: string): void {
        const instance = this.instances.get(id);
        if (instance) {
            instance.return();
        }
    }

    /**
     * Update target for a teleport instance
     */
    updateTarget(id: string, selector: string): boolean {
        const instance = this.instances.get(id);
        if (!instance) {
            return false;
        }
        return instance.updateTarget(selector);
    }

    /**
     * Setup mutation observer to auto-init new teleport elements
     */
    private setupMutationObserver(): void {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node instanceof HTMLElement) {
                        // Check if the node itself is a teleport
                        if (node.hasAttribute('data-accelade-teleport')) {
                            this.register(node);
                        }
                        // Check descendants
                        this.initTeleports(node);
                    }
                });

                // Handle removed nodes
                mutation.removedNodes.forEach((node) => {
                    if (node instanceof HTMLElement) {
                        const id = node.dataset.teleportId;
                        if (id) {
                            this.unregister(id);
                        }
                        // Check descendants
                        node.querySelectorAll<HTMLElement>('[data-teleport-id]').forEach((el) => {
                            const childId = el.dataset.teleportId;
                            if (childId) {
                                this.unregister(childId);
                            }
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    /**
     * Dispose all instances
     */
    dispose(): void {
        this.instances.forEach((instance) => {
            instance.dispose();
        });
        this.instances.clear();
    }
}

// Export singleton instance
export const teleportManager = new TeleportManagerClass();

/**
 * Initialize teleport system
 */
export function initTeleport(container?: HTMLElement | Document): void {
    teleportManager.init();
    if (container) {
        teleportManager.initTeleports(container);
    }
}

/**
 * Register a teleport element
 */
export function registerTeleport(element: HTMLElement): TeleportInstance | null {
    return teleportManager.register(element);
}

// Re-export types
export type { TeleportConfig, TeleportInstance, TeleportEventDetail } from './types';
