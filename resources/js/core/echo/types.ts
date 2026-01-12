/**
 * Echo Types - TypeScript definitions for Laravel Echo integration
 */

/**
 * Channel types supported by Laravel Echo
 */
export type ChannelType = 'public' | 'private' | 'presence';

/**
 * Action types that can be triggered by broadcast events
 */
export type EchoActionType = 'redirect' | 'refresh' | 'toast';

/**
 * Configuration for the Event component
 */
export interface EchoConfig {
    /** The channel name to subscribe to */
    channel: string;
    /** Channel type: public, private, or presence */
    channelType: ChannelType;
    /** Comma-separated list of event names to listen for */
    listen: string;
    /** Whether to preserve scroll position on refresh */
    preserveScroll: boolean;
}

/**
 * Captured event data stored in component state
 */
export interface EchoEvent {
    /** The event name that was received */
    name: string;
    /** The event payload data */
    data: Record<string, unknown>;
    /** Timestamp when the event was received */
    timestamp: number;
}

/**
 * Action payload embedded in broadcast event data
 */
export interface EchoAction {
    /** The action type to execute */
    action: EchoActionType;
    /** URL for redirect action */
    url?: string;
    /** Toast message for toast action */
    message?: string;
    /** Toast type (success, info, warning, danger) */
    type?: 'success' | 'info' | 'warning' | 'danger';
    /** Toast title */
    title?: string;
}

/**
 * Accelade-specific payload in broadcast events
 */
export interface AcceladeEventPayload {
    _accelade?: EchoAction;
    [key: string]: unknown;
}

/**
 * Component state for the Event component
 */
export interface EchoComponentState {
    /** Whether the component is subscribed to the channel */
    subscribed: boolean;
    /** Array of captured events */
    events: EchoEvent[];
}

/**
 * Laravel Echo Channel interface (minimal)
 */
export interface EchoChannel {
    listen(event: string, callback: (data: unknown) => void): EchoChannel;
    stopListening(event: string): EchoChannel;
}

/**
 * Laravel Echo instance interface (minimal)
 */
export interface EchoInstance {
    channel(name: string): EchoChannel;
    private(name: string): EchoChannel;
    join(name: string): EchoChannel;
    leave(name: string): void;
    leaveChannel(name: string): void;
}

/**
 * Subscription info for tracking active subscriptions
 */
export interface EchoSubscription {
    /** The channel instance */
    channel: EchoChannel;
    /** Channel type */
    type: ChannelType;
    /** Events being listened to */
    events: string[];
    /** Cleanup function to unsubscribe */
    unsubscribe: () => void;
}

/**
 * Echo instance stored on callback for cleanup
 */
export interface EchoInstanceInfo {
    componentId: string;
    channel: string;
    channelType: ChannelType;
    events: string[];
    stateAdapter: unknown;
    preserveScroll: boolean;
}

declare global {
    interface Window {
        Echo?: EchoInstance;
    }
}
