/**
 * EchoManager - Singleton manager for Laravel Echo connections
 *
 * Handles channel subscriptions, event listening, and cleanup.
 * Gracefully degrades if Laravel Echo is not available.
 */

import type {
    EchoInstance,
    EchoChannel,
    ChannelType,
    EchoSubscription,
} from './types';

/**
 * Callback function type for event listeners
 */
type EventCallback = (data: unknown, eventName: string) => void;

/**
 * Subscription entry for tracking
 */
interface SubscriptionEntry {
    channel: EchoChannel;
    type: ChannelType;
    listeners: Map<string, Set<EventCallback>>;
    refCount: number;
}

/**
 * EchoManager - Manages Laravel Echo channel subscriptions
 */
class EchoManager {
    private static instance: EchoManager | null = null;
    private echo: EchoInstance | null = null;
    private subscriptions: Map<string, SubscriptionEntry> = new Map();
    private initialized: boolean = false;

    private constructor() {
        // Private constructor for singleton
    }

    /**
     * Get the singleton instance
     */
    public static getInstance(): EchoManager {
        if (!EchoManager.instance) {
            EchoManager.instance = new EchoManager();
        }
        return EchoManager.instance;
    }

    /**
     * Initialize the manager with Laravel Echo
     * @returns true if Echo is available and initialized
     */
    public init(): boolean {
        if (this.initialized) {
            return this.echo !== null;
        }

        this.initialized = true;

        if (typeof window !== 'undefined' && window.Echo) {
            this.echo = window.Echo;
            return true;
        }

        console.warn(
            '[Accelade] Laravel Echo not found. Event component will not function. ' +
            'Make sure Laravel Echo is configured and window.Echo is available.'
        );
        return false;
    }

    /**
     * Check if Echo is available
     */
    public isAvailable(): boolean {
        if (!this.initialized) {
            this.init();
        }
        return this.echo !== null;
    }

    /**
     * Subscribe to a channel and listen for events
     * @param channelName The channel name
     * @param channelType The channel type (public, private, presence)
     * @param events Array of event names to listen for
     * @param callback Function to call when events are received
     * @returns Unsubscribe function
     */
    public subscribe(
        channelName: string,
        channelType: ChannelType,
        events: string[],
        callback: EventCallback
    ): () => void {
        if (!this.isAvailable() || !this.echo) {
            return () => {}; // No-op if Echo not available
        }

        const channelKey = this.getChannelKey(channelName, channelType);
        let entry = this.subscriptions.get(channelKey);

        // Create new subscription if needed
        if (!entry) {
            const channel = this.getChannel(channelName, channelType);
            entry = {
                channel,
                type: channelType,
                listeners: new Map(),
                refCount: 0,
            };
            this.subscriptions.set(channelKey, entry);
        }

        entry.refCount++;

        // Setup listeners for each event
        for (const eventName of events) {
            if (!entry.listeners.has(eventName)) {
                entry.listeners.set(eventName, new Set());

                // Setup the actual Echo listener
                entry.channel.listen(eventName, (data: unknown) => {
                    const callbacks = entry!.listeners.get(eventName);
                    if (callbacks) {
                        callbacks.forEach(cb => cb(data, eventName));
                    }
                });
            }

            entry.listeners.get(eventName)!.add(callback);
        }

        // Return unsubscribe function
        return () => {
            this.unsubscribe(channelKey, events, callback);
        };
    }

    /**
     * Unsubscribe from specific events on a channel
     */
    private unsubscribe(
        channelKey: string,
        events: string[],
        callback: EventCallback
    ): void {
        const entry = this.subscriptions.get(channelKey);
        if (!entry) return;

        // Remove callback from each event
        for (const eventName of events) {
            const callbacks = entry.listeners.get(eventName);
            if (callbacks) {
                callbacks.delete(callback);

                // If no more callbacks, stop listening to this event
                if (callbacks.size === 0) {
                    entry.channel.stopListening(eventName);
                    entry.listeners.delete(eventName);
                }
            }
        }

        entry.refCount--;

        // Leave channel if no more references
        if (entry.refCount <= 0) {
            this.leaveChannel(channelKey, entry.type);
            this.subscriptions.delete(channelKey);
        }
    }

    /**
     * Get or create a channel
     */
    private getChannel(name: string, type: ChannelType): EchoChannel {
        if (!this.echo) {
            throw new Error('Echo not initialized');
        }

        switch (type) {
            case 'private':
                return this.echo.private(name);
            case 'presence':
                return this.echo.join(name);
            case 'public':
            default:
                return this.echo.channel(name);
        }
    }

    /**
     * Leave a channel
     */
    private leaveChannel(channelKey: string, type: ChannelType): void {
        if (!this.echo) return;

        // Extract channel name from key
        const name = channelKey.replace(/^(public|private|presence):/, '');

        try {
            if (type === 'presence') {
                this.echo.leave(name);
            } else {
                this.echo.leaveChannel(
                    type === 'private' ? `private-${name}` : name
                );
            }
        } catch (error) {
            console.warn('[Accelade] Error leaving channel:', error);
        }
    }

    /**
     * Generate a unique key for a channel
     */
    private getChannelKey(name: string, type: ChannelType): string {
        return `${type}:${name}`;
    }

    /**
     * Get subscription count (for debugging)
     */
    public getSubscriptionCount(): number {
        return this.subscriptions.size;
    }

    /**
     * Cleanup all subscriptions
     */
    public cleanup(): void {
        for (const [key, entry] of this.subscriptions) {
            this.leaveChannel(key, entry.type);
        }
        this.subscriptions.clear();
    }
}

// Export singleton getter
export const getEchoManager = (): EchoManager => EchoManager.getInstance();

export { EchoManager };
