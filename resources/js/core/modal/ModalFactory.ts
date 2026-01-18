/**
 * ModalFactory - Factory for creating and managing modal instances
 *
 * Integrates with BaseAdapter to setup modal components.
 */

import type { IStateAdapter } from '../../adapters/types';
import modalManagerInstance, { type IModalManager } from './ModalManager';
import type { ModalInstance, ModalConfig } from './types';

// Re-export modalManager for use in router (typed as IModalManager to hide private members)
export const modalManager: IModalManager = modalManagerInstance;

/**
 * Modal instance with adapter integration
 */
export interface ModalAdapterInstance {
    /**
     * Modal instance
     */
    modal: ModalInstance;

    /**
     * Dispose function
     */
    dispose: () => void;
}

/**
 * Create a modal instance from an element
 */
export function createModal(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): ModalAdapterInstance | undefined {
    // Parse configuration from element
    const config = modalManagerInstance.parseConfig(element);

    // Set component ID if not set
    if (!config.id) {
        config.id = componentId;
    }

    // Register modal with manager
    const instance = modalManagerInstance.register(element, config);

    // Update state adapter with modal state
    stateAdapter.set('isOpen', instance.isOpen);

    // Expose modal methods in state for template access
    const modalObject = {
        close: instance.close,
        open: instance.open,
        setIsOpen: instance.setIsOpen,
    };

    // Add modal object to state for @click="modal.close" pattern
    stateAdapter.set('modal', modalObject);

    // Subscribe to open/close to update state
    const handleOpen = (): void => {
        stateAdapter.set('isOpen', true);
    };

    const handleClose = (): void => {
        stateAdapter.set('isOpen', false);
    };

    document.addEventListener('accelade:modal-open', ((e: CustomEvent) => {
        if (e.detail?.id === instance.id) {
            handleOpen();
        }
    }) as EventListener);

    document.addEventListener('accelade:modal-close', ((e: CustomEvent) => {
        if (e.detail?.id === instance.id) {
            handleClose();
        }
    }) as EventListener);

    return {
        modal: instance,
        dispose: () => instance.dispose(),
    };
}

/**
 * Initialize modal links on the page
 */
export function initModalLinks(): void {
    // Find all links with modal/slideover attributes
    document.querySelectorAll<HTMLAnchorElement>('a[data-modal], a[data-slideover]').forEach((link) => {
        // Skip if already initialized
        if (link.hasAttribute('data-modal-initialized')) return;
        link.setAttribute('data-modal-initialized', 'true');

        link.addEventListener('click', async (e) => {
            e.preventDefault();

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

            // Open modal with URL
            await modalManager.openUrl(href, {
                type: options.slideover ? 'slideover' : 'modal',
                maxWidth: options.maxWidth,
                position: options.position,
                slideoverPosition: options.slideoverPosition,
            });
        });
    });
}

/**
 * Initialize all modals on the page
 */
export function initModals(): void {
    // Initialize hash listener for named modals
    modalManager.initHashListener();

    // Initialize modal links
    initModalLinks();
}

export default {
    create: createModal,
    initLinks: initModalLinks,
    init: initModals,
};
