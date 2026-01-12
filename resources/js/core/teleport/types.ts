/**
 * Teleport Component Types
 *
 * Type definitions for the Teleport component that enables
 * relocating template sections to different DOM nodes.
 */

/**
 * Teleport configuration from data attributes
 */
export interface TeleportConfig {
    /**
     * Unique identifier for the teleport instance
     */
    id: string;

    /**
     * CSS selector for the target element
     */
    to: string;

    /**
     * Whether teleport is disabled (content stays in place)
     */
    disabled: boolean;
}

/**
 * Teleport instance
 */
export interface TeleportInstance {
    /**
     * Unique identifier
     */
    id: string;

    /**
     * Configuration
     */
    config: TeleportConfig;

    /**
     * Source element (placeholder)
     */
    sourceElement: HTMLElement;

    /**
     * Target element where content is teleported
     */
    targetElement: HTMLElement | null;

    /**
     * The actual content that was teleported
     */
    contentElement: HTMLElement | null;

    /**
     * Whether currently teleported
     */
    isTeleported: boolean;

    /**
     * Teleport the content to target
     */
    teleport: () => boolean;

    /**
     * Return content to original position
     */
    return: () => void;

    /**
     * Update target selector
     */
    updateTarget: (selector: string) => boolean;

    /**
     * Clean up
     */
    dispose: () => void;
}

/**
 * Teleport event detail
 */
export interface TeleportEventDetail {
    /**
     * Teleport instance ID
     */
    id: string;

    /**
     * Target selector
     */
    to: string;

    /**
     * Whether teleport succeeded
     */
    success: boolean;

    /**
     * Error message if failed
     */
    error?: string;
}
