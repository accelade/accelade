/**
 * EventBus - Global event bus for component communication
 *
 * Provides a simple pub/sub mechanism for decoupled communication
 * between components without direct references.
 */

/**
 * Event callback function type
 */
export type EventCallback<T = unknown> = (data: T) => void;

/**
 * Subscription entry for tracking listeners
 */
interface Subscription {
    callback: EventCallback;
    once: boolean;
}

/**
 * EventBus class - Singleton event bus for the application
 */
class EventBus {
    private static instance: EventBus | null = null;
    private listeners: Map<string, Set<Subscription>> = new Map();

    private constructor() {
        // Private constructor for singleton
    }

    /**
     * Get the singleton instance
     */
    public static getInstance(): EventBus {
        if (!EventBus.instance) {
            EventBus.instance = new EventBus();
        }
        return EventBus.instance;
    }

    /**
     * Emit an event with optional data
     * @param event The event name
     * @param data Optional data to pass to listeners
     */
    public emit<T = unknown>(event: string, data?: T): void {
        const subscriptions = this.listeners.get(event);
        if (!subscriptions) return;

        // Create a copy to safely iterate while potentially removing 'once' listeners
        const subscriptionsCopy = Array.from(subscriptions);

        for (const subscription of subscriptionsCopy) {
            try {
                subscription.callback(data as T);
            } catch (error) {
                console.error(`[Accelade EventBus] Error in listener for "${event}":`, error);
            }

            // Remove one-time listeners
            if (subscription.once) {
                subscriptions.delete(subscription);
            }
        }

        // Clean up empty sets
        if (subscriptions.size === 0) {
            this.listeners.delete(event);
        }
    }

    /**
     * Listen for an event
     * @param event The event name
     * @param callback Function to call when event is emitted
     * @returns Unsubscribe function
     */
    public on<T = unknown>(event: string, callback: EventCallback<T>): () => void {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, new Set());
        }

        const subscription: Subscription = {
            callback: callback as EventCallback,
            once: false,
        };

        this.listeners.get(event)!.add(subscription);

        // Return unsubscribe function
        return () => {
            this.off(event, callback);
        };
    }

    /**
     * Listen for an event once (auto-removes after first call)
     * @param event The event name
     * @param callback Function to call when event is emitted
     * @returns Unsubscribe function
     */
    public once<T = unknown>(event: string, callback: EventCallback<T>): () => void {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, new Set());
        }

        const subscription: Subscription = {
            callback: callback as EventCallback,
            once: true,
        };

        this.listeners.get(event)!.add(subscription);

        // Return unsubscribe function
        return () => {
            const subs = this.listeners.get(event);
            if (subs) {
                subs.delete(subscription);
                if (subs.size === 0) {
                    this.listeners.delete(event);
                }
            }
        };
    }

    /**
     * Remove a specific listener
     * @param event The event name
     * @param callback The callback to remove
     */
    public off<T = unknown>(event: string, callback: EventCallback<T>): void {
        const subscriptions = this.listeners.get(event);
        if (!subscriptions) return;

        for (const subscription of subscriptions) {
            if (subscription.callback === callback) {
                subscriptions.delete(subscription);
                break;
            }
        }

        // Clean up empty sets
        if (subscriptions.size === 0) {
            this.listeners.delete(event);
        }
    }

    /**
     * Remove all listeners for an event
     * @param event The event name (optional, removes all if not provided)
     */
    public clear(event?: string): void {
        if (event) {
            this.listeners.delete(event);
        } else {
            this.listeners.clear();
        }
    }

    /**
     * Check if an event has listeners
     * @param event The event name
     */
    public hasListeners(event: string): boolean {
        const subscriptions = this.listeners.get(event);
        return subscriptions !== undefined && subscriptions.size > 0;
    }

    /**
     * Get the number of listeners for an event
     * @param event The event name
     */
    public listenerCount(event: string): number {
        return this.listeners.get(event)?.size ?? 0;
    }

    /**
     * Get all registered event names
     */
    public eventNames(): string[] {
        return Array.from(this.listeners.keys());
    }
}

// Export singleton getter
export const getEventBus = (): EventBus => EventBus.getInstance();

// Export convenience functions
export const emit = <T = unknown>(event: string, data?: T): void => {
    getEventBus().emit(event, data);
};

export const on = <T = unknown>(event: string, callback: EventCallback<T>): (() => void) => {
    return getEventBus().on(event, callback);
};

export const once = <T = unknown>(event: string, callback: EventCallback<T>): (() => void) => {
    return getEventBus().once(event, callback);
};

export const off = <T = unknown>(event: string, callback: EventCallback<T>): void => {
    getEventBus().off(event, callback);
};

export { EventBus };
