/**
 * Flash Data Types
 *
 * Type definitions for Laravel session flash data integration.
 */

/**
 * Flash data object structure
 */
export interface FlashData {
    [key: string]: unknown;
}

/**
 * Flash object exposed to templates
 * Provides methods to check and access flash data
 */
export interface FlashObject extends FlashData {
    /**
     * Check if a flash key exists and has a truthy value
     */
    has: (key: string) => boolean;

    /**
     * Get all flash data as an object
     */
    all: () => FlashData;

    /**
     * Get a flash value by key with optional default
     */
    get: <T = unknown>(key: string, defaultValue?: T) => T;
}

/**
 * Flash component configuration from data attributes
 */
export interface FlashConfig {
    /**
     * Initial flash data from server
     */
    data: FlashData;

    /**
     * Whether to automatically clear flash on first read
     */
    autoClear?: boolean;
}
