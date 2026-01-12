/**
 * Rehydrate Manager
 *
 * Manages rehydrate instances that enable selective section
 * reloading without full page refresh.
 */

import type { RehydrateConfig, RehydrateInstance, RehydrateEventDetail } from './types';

/**
 * Default configuration
 */
const defaultConfig: Partial<RehydrateConfig> = {
    on: [],
    poll: 0,
    preserveScroll: true,
};

/**
 * RehydrateManager singleton class
 */
class RehydrateManager {
    private instances: Map<string, RehydrateInstance> = new Map();
    private eventListeners: Map<string, Set<string>> = new Map(); // event -> instance IDs
    private initialized = false;

    /**
     * Initialize the rehydrate manager
     */
    init(): void {
        if (this.initialized) return;

        // Listen for custom Accelade events
        document.addEventListener('accelade:emit', this.handleEmit.bind(this) as EventListener);

        this.initialized = true;
    }

    /**
     * Handle emitted events
     */
    private handleEmit(e: CustomEvent<{ event: string; data?: unknown }>): void {
        const { event } = e.detail;
        this.triggerByEvent(event);
    }

    /**
     * Trigger rehydration for all instances listening to an event
     */
    triggerByEvent(eventName: string): void {
        const instanceIds = this.eventListeners.get(eventName);
        if (!instanceIds) return;

        instanceIds.forEach((id) => {
            const instance = this.instances.get(id);
            if (instance) {
                void instance.rehydrate();
            }
        });
    }

    /**
     * Emit an event that can trigger rehydration
     */
    emit(eventName: string, data?: unknown): void {
        document.dispatchEvent(
            new CustomEvent('accelade:emit', {
                detail: { event: eventName, data },
            })
        );
    }

    /**
     * Register a rehydrate instance
     */
    register(element: HTMLElement): RehydrateInstance | null {
        const config = this.parseConfig(element);
        if (!config) return null;

        // Check if already registered
        if (this.instances.has(config.id)) {
            return this.instances.get(config.id)!;
        }

        let pollInterval: ReturnType<typeof setInterval> | null = null;
        let isLoading = false;

        const instance: RehydrateInstance = {
            id: config.id,
            config,
            element,
            isLoading: false,

            rehydrate: async () => {
                if (isLoading) return;
                isLoading = true;
                instance.isLoading = true;

                try {
                    await this.rehydrateElement(instance);

                    // Dispatch success event
                    this.dispatchEvent('accelade:rehydrate', {
                        id: config.id,
                        success: true,
                    });
                } catch (error) {
                    console.error('Rehydrate failed:', error);

                    // Dispatch failure event
                    this.dispatchEvent('accelade:rehydrate', {
                        id: config.id,
                        success: false,
                    });
                } finally {
                    isLoading = false;
                    instance.isLoading = false;
                }
            },

            startPolling: () => {
                if (config.poll > 0 && !pollInterval) {
                    pollInterval = setInterval(() => {
                        void instance.rehydrate();
                    }, config.poll);
                }
            },

            stopPolling: () => {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            },

            dispose: () => {
                instance.stopPolling();
                this.unregisterEventListeners(config.id, config.on);
                this.instances.delete(config.id);
            },
        };

        // Store instance
        this.instances.set(config.id, instance);

        // Register event listeners
        this.registerEventListeners(config.id, config.on);

        // Start polling if configured
        if (config.poll > 0) {
            instance.startPolling();
        }

        return instance;
    }

    /**
     * Parse configuration from element data attributes
     */
    private parseConfig(element: HTMLElement): RehydrateConfig | null {
        const id = element.dataset.rehydrateId || `rehydrate-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

        // Parse events
        let on: string[] = [];
        const onAttr = element.dataset.rehydrateOn;
        if (onAttr) {
            try {
                // Try parsing as JSON array
                on = JSON.parse(onAttr);
            } catch {
                // Single event string
                on = [onAttr];
            }
        }

        // Parse poll interval
        const pollAttr = element.dataset.rehydratePoll;
        const poll = pollAttr ? parseInt(pollAttr, 10) : 0;

        // Get URL (defaults to current page)
        const url = element.dataset.rehydrateUrl || window.location.href;

        // Preserve scroll
        const preserveScroll = element.hasAttribute('data-rehydrate-preserve-scroll');

        return {
            id,
            on,
            poll: isNaN(poll) ? 0 : poll,
            url,
            preserveScroll: preserveScroll || defaultConfig.preserveScroll!,
        };
    }

    /**
     * Register event listeners for an instance
     */
    private registerEventListeners(instanceId: string, events: string[]): void {
        events.forEach((event) => {
            if (!this.eventListeners.has(event)) {
                this.eventListeners.set(event, new Set());
            }
            this.eventListeners.get(event)!.add(instanceId);
        });
    }

    /**
     * Unregister event listeners for an instance
     */
    private unregisterEventListeners(instanceId: string, events: string[]): void {
        events.forEach((event) => {
            const listeners = this.eventListeners.get(event);
            if (listeners) {
                listeners.delete(instanceId);
                if (listeners.size === 0) {
                    this.eventListeners.delete(event);
                }
            }
        });
    }

    /**
     * Rehydrate an element by fetching fresh content
     */
    private async rehydrateElement(instance: RehydrateInstance): Promise<void> {
        const { element, config } = instance;

        // Save scroll position
        const scrollX = config.preserveScroll ? window.scrollX : 0;
        const scrollY = config.preserveScroll ? window.scrollY : 0;

        // Add loading class
        element.classList.add('accelade-rehydrating');

        try {
            // Fetch fresh content
            const response = await fetch(config.url, {
                method: 'GET',
                headers: {
                    'X-Accelade-Rehydrate': config.id,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const html = await response.text();

            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Find the matching rehydrate element in the response
            const newElement = doc.querySelector(`[data-rehydrate-id="${config.id}"]`);

            if (newElement) {
                // Update the content
                element.innerHTML = newElement.innerHTML;

                // Re-initialize any Accelade components inside
                if (window.Accelade?.init) {
                    window.Accelade.init(element);
                }
            }

            // Restore scroll position
            if (config.preserveScroll) {
                window.scrollTo({ top: scrollY, left: scrollX, behavior: 'instant' });
            }
        } finally {
            // Remove loading class
            element.classList.remove('accelade-rehydrating');
        }
    }

    /**
     * Dispatch a custom event
     */
    private dispatchEvent(name: string, detail: RehydrateEventDetail): void {
        document.dispatchEvent(new CustomEvent(name, { detail }));
    }

    /**
     * Get an instance by ID
     */
    get(id: string): RehydrateInstance | undefined {
        return this.instances.get(id);
    }

    /**
     * Get all registered instances
     */
    getAll(): RehydrateInstance[] {
        return Array.from(this.instances.values());
    }

    /**
     * Manually trigger rehydration for an instance by ID
     */
    async rehydrate(id: string): Promise<void> {
        const instance = this.instances.get(id);
        if (instance) {
            await instance.rehydrate();
        }
    }

    /**
     * Rehydrate all instances
     */
    async rehydrateAll(): Promise<void> {
        const promises = Array.from(this.instances.values()).map((instance) =>
            instance.rehydrate()
        );
        await Promise.all(promises);
    }

    /**
     * Dispose all instances
     */
    disposeAll(): void {
        this.instances.forEach((instance) => instance.dispose());
        this.instances.clear();
        this.eventListeners.clear();
    }
}

// Export singleton instance
const rehydrateManager = new RehydrateManager();
export default rehydrateManager;
export { RehydrateManager };
