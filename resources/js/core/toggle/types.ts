/**
 * Toggle Component Types
 *
 * Types for the Toggle component which provides a simplified interface
 * for managing boolean state values.
 */

/**
 * Toggle configuration parsed from element attributes
 */
export interface ToggleConfig {
    /** Unique identifier for the toggle instance */
    id: string;
    /** Initial toggle keys with their default values */
    keys: string[];
    /** Default value for single toggle mode */
    defaultValue: boolean;
}

/**
 * Toggle state - can be a single boolean or multiple named booleans
 */
export type ToggleState = Record<string, boolean>;

/**
 * Toggle instance returned by the factory
 */
export interface ToggleInstance {
    /** Unique identifier */
    id: string;
    /** Configuration */
    config: ToggleConfig;
    /** The source element */
    element: HTMLElement;
    /** Whether this is multi-key mode */
    isMultiKey: boolean;
    /**
     * Toggle a value
     * @param key - Optional key for multi-toggle mode
     */
    toggle: (key?: string) => void;
    /**
     * Open (set to true)
     * @param key - Optional key for multi-toggle mode
     */
    open: (key?: string) => void;
    /**
     * Close (set to false)
     * @param key - Optional key for multi-toggle mode
     */
    close: (key?: string) => void;
    /**
     * Set a specific toggle value
     * @param keyOrValue - Key (for multi) or value (for single)
     * @param value - Value (only for multi-key mode)
     */
    setToggle: (keyOrValue: string | boolean, value?: boolean) => void;
    /**
     * Get the current toggled state
     * @param key - Optional key for multi-toggle mode
     */
    getToggled: (key?: string) => boolean;
    /** Dispose the toggle instance */
    dispose: () => void;
}

/**
 * Toggle methods exposed to templates
 */
export interface ToggleMethods {
    /** Toggle function */
    toggle: (key?: string) => void;
    /** Open (set to true) */
    open: (key?: string) => void;
    /** Close (set to false) */
    close: (key?: string) => void;
    /** Set toggle function */
    setToggle: (keyOrValue: string | boolean, value?: boolean) => void;
}
