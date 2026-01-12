/**
 * State Component Types
 *
 * Type definitions for the State component that provides
 * unified access to errors, flash data, and shared data.
 */

/**
 * Validation errors object
 */
export interface ValidationErrors {
    [key: string]: string | string[];
}

/**
 * Flash data object
 */
export interface FlashData {
    [key: string]: unknown;
}

/**
 * Shared data object
 */
export interface SharedData {
    [key: string]: unknown;
}

/**
 * State object exposed to templates
 */
export interface StateObject {
    /**
     * First error message for each field
     */
    errors: Record<string, string>;

    /**
     * Raw error bag (all messages per field)
     */
    rawErrors: ValidationErrors;

    /**
     * Whether any errors exist
     */
    hasErrors: boolean;

    /**
     * Flash data values
     */
    flash: FlashData;

    /**
     * Shared data values
     */
    shared: SharedData;
}

/**
 * State helper methods
 */
export interface StateHelpers {
    /**
     * Check if a field has an error
     */
    hasError: (key: string) => boolean;

    /**
     * Get the first error for a field
     */
    getError: (key: string) => string | null;

    /**
     * Get all errors for a field
     */
    getErrors: (key: string) => string[];

    /**
     * Check if flash data exists
     */
    hasFlash: (key: string) => boolean;

    /**
     * Get flash data value
     */
    getFlash: <T = unknown>(key: string) => T | null;

    /**
     * Check if shared data exists
     */
    hasShared: (key: string) => boolean;

    /**
     * Get shared data value
     */
    getShared: <T = unknown>(key: string) => T | null;
}

/**
 * State configuration from data attributes
 */
export interface StateConfig {
    /**
     * Unique identifier
     */
    id: string;

    /**
     * Initial errors (from server)
     */
    errors: ValidationErrors;

    /**
     * Initial flash data (from server)
     */
    flash: FlashData;

    /**
     * Initial shared data (from server)
     */
    shared: SharedData;
}

/**
 * State instance
 */
export interface StateInstance {
    /**
     * Unique ID
     */
    id: string;

    /**
     * Configuration
     */
    config: StateConfig;

    /**
     * Component element
     */
    element: HTMLElement;

    /**
     * State object
     */
    state: StateObject;

    /**
     * Helper methods
     */
    helpers: StateHelpers;

    /**
     * Cleanup function
     */
    dispose: () => void;
}
