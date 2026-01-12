/**
 * Modal Component Types
 *
 * Type definitions for the Modal/Slideover component with
 * async content loading and customizable appearance.
 */

/**
 * Modal max-width options
 */
export type ModalMaxWidth = 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl' | '5xl' | '6xl' | '7xl';

/**
 * Modal position (vertical alignment)
 */
export type ModalPosition = 'top' | 'center' | 'bottom';

/**
 * Slideover position (horizontal alignment)
 */
export type SlideoverPosition = 'left' | 'right';

/**
 * Modal type
 */
export type ModalType = 'modal' | 'slideover' | 'bottom-sheet';

/**
 * Modal configuration
 */
export interface ModalConfig {
    /**
     * Unique identifier for the modal
     */
    id?: string;

    /**
     * Modal name for pre-loaded/named modals
     */
    name?: string;

    /**
     * Type of modal (modal or slideover)
     */
    type: ModalType;

    /**
     * Maximum width of the modal
     */
    maxWidth: ModalMaxWidth;

    /**
     * Position for modal (top, center, bottom)
     */
    position: ModalPosition;

    /**
     * Position for slideover (left, right)
     */
    slideoverPosition: SlideoverPosition;

    /**
     * Require explicit close (disable ESC and outside click)
     */
    closeExplicitly: boolean;

    /**
     * Show close button
     */
    closeButton: boolean;

    /**
     * Open immediately on page load
     */
    opened: boolean;
}

/**
 * Modal instance
 */
export interface ModalInstance {
    /**
     * Unique ID
     */
    id: string;

    /**
     * Modal name (if named)
     */
    name?: string;

    /**
     * Configuration
     */
    config: ModalConfig;

    /**
     * Modal element
     */
    element: HTMLElement;

    /**
     * Whether modal is currently open
     */
    isOpen: boolean;

    /**
     * Open the modal
     */
    open: () => void;

    /**
     * Close the modal
     */
    close: () => void;

    /**
     * Set open state
     */
    setIsOpen: (open: boolean) => void;

    /**
     * Cleanup function
     */
    dispose: () => void;
}

/**
 * Modal open options for programmatic opening
 */
export interface ModalOpenOptions {
    /**
     * URL to load content from
     */
    url?: string;

    /**
     * HTML content to display
     */
    content?: string;

    /**
     * Modal type
     */
    type?: ModalType;

    /**
     * Max width
     */
    maxWidth?: ModalMaxWidth;

    /**
     * Position
     */
    position?: ModalPosition;

    /**
     * Slideover position
     */
    slideoverPosition?: SlideoverPosition;

    /**
     * Close explicitly
     */
    closeExplicitly?: boolean;

    /**
     * Show close button
     */
    closeButton?: boolean;

    /**
     * Callback when modal closes
     */
    onClose?: () => void;
}

/**
 * Link modal options parsed from data attributes
 */
export interface LinkModalOptions {
    /**
     * Open as modal
     */
    modal: boolean;

    /**
     * Open as slideover
     */
    slideover: boolean;

    /**
     * Open as bottom sheet
     */
    bottomSheet: boolean;

    /**
     * Max width override
     */
    maxWidth?: ModalMaxWidth;

    /**
     * Position override
     */
    position?: ModalPosition;

    /**
     * Slideover position override
     */
    slideoverPosition?: SlideoverPosition;
}

/**
 * Modal event detail
 */
export interface ModalEventDetail {
    /**
     * Modal ID
     */
    id: string;

    /**
     * Modal name
     */
    name?: string;

    /**
     * Whether modal is open
     */
    isOpen: boolean;
}
