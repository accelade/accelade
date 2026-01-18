/**
 * LinkManager - Handles enhanced link functionality
 *
 * Manages link clicks with HTTP methods, confirmation dialogs,
 * request data, and navigation options.
 */

import type { LinkConfig, HttpMethod } from './types';
import { showConfirmDialog } from './ConfirmDialog';
import { getRouter } from '../router';

/**
 * Parse a link element's configuration from data attributes
 */
export function parseLinkConfig(element: HTMLAnchorElement): LinkConfig {
    const href = element.getAttribute('href') ?? '';

    // Parse HTTP method
    const methodAttr = element.getAttribute('data-method') ?? element.getAttribute('method');
    const method = (methodAttr?.toUpperCase() as HttpMethod) || 'GET';

    // Parse request data
    let data: Record<string, unknown> | undefined;
    const dataAttr = element.getAttribute('data-data');
    if (dataAttr) {
        try {
            data = JSON.parse(dataAttr);
        } catch {
            console.warn('[Accelade Link] Failed to parse data attribute:', dataAttr);
        }
    }

    // Parse headers
    let headers: Record<string, string> | undefined;
    const headersAttr = element.getAttribute('data-headers');
    if (headersAttr) {
        try {
            headers = JSON.parse(headersAttr);
        } catch {
            console.warn('[Accelade Link] Failed to parse headers attribute:', headersAttr);
        }
    }

    // Parse confirmation options
    const hasConfirm = element.hasAttribute('data-confirm') || element.hasAttribute('confirm');
    const confirmText = element.getAttribute('data-confirm-text') ??
        element.getAttribute('confirm-text') ??
        element.getAttribute('data-confirm') ??
        (hasConfirm ? 'Are you sure you want to continue?' : undefined);
    const confirmTitle = element.getAttribute('data-confirm-title') ?? element.getAttribute('confirm-title');
    const confirmButton = element.getAttribute('data-confirm-button') ?? element.getAttribute('confirm-button') ?? 'Confirm';
    const cancelButton = element.getAttribute('data-cancel-button') ?? element.getAttribute('cancel-button') ?? 'Cancel';
    const confirmDanger = element.hasAttribute('data-confirm-danger') || element.hasAttribute('confirm-danger');

    // Parse navigation options
    const preserveScroll = element.hasAttribute('data-preserve-scroll') || element.hasAttribute('preserve-scroll');
    const preserveState = element.hasAttribute('data-preserve-state') || element.hasAttribute('preserve-state');
    const prefetch = element.hasAttribute('data-prefetch') || element.hasAttribute('prefetch');
    const replace = element.hasAttribute('data-replace') || element.hasAttribute('replace');
    const away = element.hasAttribute('data-away') || element.hasAttribute('away');

    return {
        href,
        method,
        data,
        headers,
        away,
        confirm: hasConfirm || !!confirmText,
        confirmText,
        confirmTitle,
        confirmButton,
        cancelButton,
        confirmDanger,
        preserveScroll,
        preserveState,
        prefetch,
        replace,
    };
}

/**
 * Handle a link click with enhanced features
 */
export async function handleLinkClick(
    element: HTMLAnchorElement,
    event: MouseEvent
): Promise<boolean> {
    const config = parseLinkConfig(element);

    // Dispatch custom event before handling
    const beforeEvent = new CustomEvent('accelade:link-before', {
        detail: { href: config.href, method: config.method, data: config.data, cancelled: false },
        bubbles: true,
        cancelable: true,
    });
    element.dispatchEvent(beforeEvent);

    if (beforeEvent.defaultPrevented || (beforeEvent.detail as { cancelled: boolean }).cancelled) {
        return false;
    }

    // Handle external links (away)
    if (config.away) {
        // Show confirmation if needed
        if (config.confirm && config.confirmText) {
            const result = await showConfirmDialog({
                title: config.confirmTitle,
                text: config.confirmText,
                confirmButton: config.confirmButton ?? 'Continue',
                cancelButton: config.cancelButton ?? 'Cancel',
                danger: config.confirmDanger ?? false,
            });

            if (!result.confirmed) {
                return false;
            }
        }

        // Navigate away (open in new tab)
        window.open(config.href, '_blank', 'noopener,noreferrer');
        return true;
    }

    // Show confirmation dialog if needed
    if (config.confirm && config.confirmText) {
        const result = await showConfirmDialog({
            title: config.confirmTitle,
            text: config.confirmText,
            confirmButton: config.confirmButton ?? 'Confirm',
            cancelButton: config.cancelButton ?? 'Cancel',
            danger: config.confirmDanger ?? false,
        });

        if (!result.confirmed) {
            return false;
        }
    }

    // Handle non-GET methods (form-like submissions)
    if (config.method !== 'GET') {
        return handleFormSubmit(config);
    }

    // Handle GET navigation via router
    const router = getRouter();
    return router.navigate(config.href, {
        preserveScroll: config.preserveScroll,
        preserveState: config.preserveState,
        pushState: !config.replace,
        headers: config.headers,
    });
}

/**
 * Handle form-like submissions (POST, PUT, PATCH, DELETE)
 */
async function handleFormSubmit(config: LinkConfig): Promise<boolean> {
    try {
        // Get CSRF token
        const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ??
            (document.querySelector<HTMLInputElement>('input[name="_token"]')?.value);

        const headers: Record<string, string> = {
            'Content-Type': 'application/json',
            'Accept': 'text/html, application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Accelade-SPA': 'true',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            ...(config.headers ?? {}),
        };

        // For methods other than GET/POST, add method spoofing
        const actualMethod = config.method === 'GET' || config.method === 'POST' ? config.method : 'POST';
        const data: Record<string, unknown> = {
            ...(config.data ?? {}),
        };

        // Add method spoofing for Laravel
        if (config.method !== 'GET' && config.method !== 'POST') {
            data._method = config.method;
        }

        const response = await fetch(config.href, {
            method: actualMethod,
            headers,
            body: Object.keys(data).length > 0 ? JSON.stringify(data) : undefined,
        });

        // Handle redirect responses
        if (response.redirected) {
            const router = getRouter();
            return router.navigate(response.url, {
                preserveScroll: config.preserveScroll,
                preserveState: config.preserveState,
                pushState: !config.replace,
            });
        }

        // Handle JSON responses
        const contentType = response.headers.get('content-type');
        if (contentType?.includes('application/json')) {
            const json = await response.json();

            // Check for redirect in JSON response
            if (json.redirect) {
                const router = getRouter();
                return router.navigate(json.redirect, {
                    preserveScroll: config.preserveScroll,
                    preserveState: config.preserveState,
                    pushState: !config.replace,
                });
            }

            // Dispatch event with response data
            document.dispatchEvent(new CustomEvent('accelade:link-response', {
                detail: { href: config.href, method: config.method, response: json },
            }));

            return true;
        }

        // Handle HTML responses (page update)
        if (contentType?.includes('text/html')) {
            const html = await response.text();
            const router = getRouter();

            // Get the final URL (may differ due to redirects)
            const finalUrl = response.url || config.href;

            // Update the page content
            // This is a simplified version - the router handles the full update
            return router.navigate(finalUrl, {
                preserveScroll: config.preserveScroll,
                preserveState: config.preserveState,
                pushState: !config.replace,
            });
        }

        return true;
    } catch (error) {
        console.error('[Accelade Link] Form submission failed:', error);

        // Dispatch error event
        document.dispatchEvent(new CustomEvent('accelade:link-error', {
            detail: { href: config.href, method: config.method, error },
        }));

        return false;
    }
}

/**
 * Setup prefetching for a link
 */
export function setupPrefetch(element: HTMLAnchorElement): void {
    const href = element.getAttribute('href');
    if (!href) return;

    // Prefetch on hover
    element.addEventListener('mouseenter', () => {
        const router = getRouter();
        void router.prefetch(href);
    }, { once: true });
}

/**
 * Initialize link handling for a container
 */
export function initLinks(container: HTMLElement = document.body): void {
    // Find all enhanced links
    const links = container.querySelectorAll<HTMLAnchorElement>(
        'a[data-accelade-link], a[a-link], a[data-spa-link]'
    );

    links.forEach((link) => {
        // Setup prefetching if enabled
        if (link.hasAttribute('data-prefetch') || link.hasAttribute('prefetch')) {
            setupPrefetch(link);
        }
    });
}

export default {
    parseLinkConfig,
    handleLinkClick,
    setupPrefetch,
    initLinks,
};
