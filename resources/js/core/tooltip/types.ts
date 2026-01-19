/**
 * Tooltip Types
 *
 * Type definitions for the Tooltip directive system.
 * Supports Filament-like API with themes, RTL, and width control.
 */

/**
 * Tooltip position
 */
export type TooltipPosition =
    | 'top'
    | 'top-start'
    | 'top-end'
    | 'bottom'
    | 'bottom-start'
    | 'bottom-end'
    | 'left'
    | 'left-start'
    | 'left-end'
    | 'right'
    | 'right-start'
    | 'right-end';

/**
 * Tooltip trigger type
 */
export type TooltipTrigger = 'hover' | 'click' | 'focus' | 'manual';

/**
 * Tooltip theme
 */
export type TooltipTheme = 'light' | 'dark' | 'auto';

/**
 * Animation configuration for tooltip
 */
export interface TooltipAnimationConfig {
    enter?: string;
    enterFrom?: string;
    enterTo?: string;
    leave?: string;
    leaveFrom?: string;
    leaveTo?: string;
    duration?: number;
}

/**
 * Tooltip configuration from a-tooltip attribute
 */
export interface TooltipConfig {
    /** Unique identifier for the tooltip instance */
    id: string;
    /** Tooltip content text */
    content: string;
    /** Position relative to trigger element */
    position: TooltipPosition;
    /** Trigger type */
    trigger: TooltipTrigger;
    /** Theme: light, dark, or auto (follows system) */
    theme: TooltipTheme;
    /** Show delay in ms */
    delay: number;
    /** Hide delay in ms */
    hideDelay: number;
    /** Show arrow indicator */
    arrow: boolean;
    /** Allow hovering the tooltip (for interactive content) */
    interactive: boolean;
    /** Max width (e.g., '200px', '20rem', 'none' for full width) */
    maxWidth: string | null;
    /** Offset from trigger element in pixels */
    offset: number;
    /** Enable RTL support (auto-detects if not set) */
    rtl: boolean;
    /** Storage key for persisting config */
    storageKey?: string;
    /** Animation configuration */
    animation?: TooltipAnimationConfig;
}

/**
 * Global tooltip settings stored in localStorage
 */
export interface TooltipGlobalSettings {
    theme: TooltipTheme;
    position: TooltipPosition;
    delay: number;
    maxWidth: string | null;
}

/**
 * Tooltip instance
 */
export interface TooltipInstance {
    id: string;
    config: TooltipConfig;
    element: HTMLElement;
    tooltipElement: HTMLElement | null;
    show: () => void;
    hide: () => void;
    toggle: () => void;
    setContent: (content: string) => void;
    setTheme: (theme: TooltipTheme) => void;
    setPosition: (position: TooltipPosition) => void;
    isVisible: () => boolean;
    dispose: () => void;
}

/**
 * Tooltip methods exposed to templates
 */
export interface TooltipMethods {
    showTooltip: () => void;
    hideTooltip: () => void;
    toggleTooltip: () => void;
    setTooltipContent: (content: string) => void;
    setTooltipTheme: (theme: TooltipTheme) => void;
    setTooltipPosition: (position: TooltipPosition) => void;
}
