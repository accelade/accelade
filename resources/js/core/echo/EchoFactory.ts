/**
 * EchoFactory - Factory for creating Echo event listeners per component
 *
 * Handles parsing component config, setting up channel subscriptions,
 * processing event actions (redirect, refresh, toast), and cleanup.
 */

import { getEchoManager } from './EchoManager';
import type {
    EchoConfig,
    EchoEvent,
    EchoAction,
    AcceladeEventPayload,
    ChannelType,
} from './types';
import type { IStateAdapter } from '../../adapters/types';

/**
 * Echo component instance info (distinct from types.ts EchoComponentInstance which is Laravel Echo interface)
 */
export interface EchoComponentInstance {
    componentId: string;
    config: EchoConfig;
    stateAdapter: IStateAdapter;
    unsubscribe: () => void;
}

/**
 * Parse Echo configuration from DOM element
 */
export function parseEchoConfig(element: HTMLElement): EchoConfig | null {
    const channel = element.getAttribute('data-echo-channel');
    if (!channel) {
        return null;
    }

    const isPrivate = element.getAttribute('data-echo-private') === 'true';
    const isPresence = element.getAttribute('data-echo-presence') === 'true';

    let channelType: ChannelType = 'public';
    if (isPresence) {
        channelType = 'presence';
    } else if (isPrivate) {
        channelType = 'private';
    }

    return {
        channel,
        channelType,
        listen: element.getAttribute('data-echo-listen') || '',
        preserveScroll: element.getAttribute('data-echo-preserve-scroll') === 'true',
    };
}

/**
 * Create an Echo listener for a component
 */
export function createEchoComponentInstance(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): EchoComponentInstance | null {
    const config = parseEchoConfig(element);
    if (!config) {
        return null;
    }

    const manager = getEchoManager();
    if (!manager.isAvailable()) {
        // Update state to indicate not subscribed
        stateAdapter.set('subscribed', false);
        return null;
    }

    // Parse event names
    const events = config.listen
        .split(',')
        .map(e => e.trim())
        .filter(e => e.length > 0);

    if (events.length === 0) {
        console.warn('[Accelade] No events specified for Echo component');
        stateAdapter.set('subscribed', false);
        return null;
    }

    // Create event handler
    const handleEvent = (data: unknown, eventName: string) => {
        const payload = data as AcceladeEventPayload;

        // Add event to state
        const currentEvents = (stateAdapter.get('events') as EchoEvent[]) || [];
        const newEvent: EchoEvent = {
            name: eventName,
            data: payload as Record<string, unknown>,
            timestamp: Date.now(),
        };
        stateAdapter.set('events', [...currentEvents, newEvent]);

        // Process Accelade action if present
        if (payload._accelade) {
            processAction(payload._accelade, config);
        }

        // Dispatch custom event for advanced usage
        element.dispatchEvent(
            new CustomEvent('accelade:echo', {
                detail: { name: eventName, data: payload },
                bubbles: true,
            })
        );
    };

    // Subscribe to channel
    const unsubscribe = manager.subscribe(
        config.channel,
        config.channelType,
        events,
        handleEvent
    );

    // Update state to subscribed
    stateAdapter.set('subscribed', true);

    return {
        componentId,
        config,
        stateAdapter,
        unsubscribe,
    };
}

/**
 * Process an Accelade action from event payload
 */
function processAction(action: EchoAction, config: EchoConfig): void {
    switch (action.action) {
        case 'redirect':
            if (action.url) {
                // Use Accelade router if available, otherwise direct navigation
                if (typeof window !== 'undefined' && (window as any).Accelade?.router?.navigate) {
                    (window as any).Accelade.router.navigate(action.url);
                } else {
                    window.location.href = action.url;
                }
            }
            break;

        case 'refresh':
            if (config.preserveScroll) {
                // Store scroll position
                const scrollPos = { x: window.scrollX, y: window.scrollY };
                sessionStorage.setItem('accelade:scroll', JSON.stringify(scrollPos));
            }

            // Use Accelade router reload if available
            if (typeof window !== 'undefined' && (window as any).Accelade?.router?.instance) {
                const router = (window as any).Accelade.router.instance();
                if (router && typeof router.reload === 'function') {
                    router.reload();
                    restoreScrollIfNeeded(config);
                    return;
                }
            }

            // Fallback to standard reload
            window.location.reload();
            break;

        case 'toast':
            if (typeof window !== 'undefined' && (window as any).Accelade?.notify) {
                const notify = (window as any).Accelade.notify;
                const type = action.type || 'info';
                const title = action.title || action.message || '';
                const body = action.title ? (action.message || '') : '';

                switch (type) {
                    case 'success':
                        notify.success(title, body);
                        break;
                    case 'warning':
                        notify.warning(title, body);
                        break;
                    case 'danger':
                        notify.danger(title, body);
                        break;
                    case 'info':
                    default:
                        notify.info(title, body);
                        break;
                }
            }
            break;
    }
}

/**
 * Restore scroll position if preserve-scroll was enabled
 */
function restoreScrollIfNeeded(config: EchoConfig): void {
    if (!config.preserveScroll) return;

    // Try to restore after a short delay (after page update)
    setTimeout(() => {
        const stored = sessionStorage.getItem('accelade:scroll');
        if (stored) {
            try {
                const { x, y } = JSON.parse(stored);
                window.scrollTo(x, y);
            } catch (e) {
                // Ignore parse errors
            }
            sessionStorage.removeItem('accelade:scroll');
        }
    }, 100);
}

/**
 * Cleanup an Echo instance
 */
export function disposeEchoComponentInstance(instance: EchoComponentInstance): void {
    instance.unsubscribe();
    instance.stateAdapter.set('subscribed', false);
}

export const EchoFactory = {
    parseConfig: parseEchoConfig,
    create: createEchoComponentInstance,
    dispose: disposeEchoComponentInstance,
};
