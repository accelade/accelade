/**
 * FlashFactory - Factory for creating flash data instances per component
 *
 * Parses component configuration and sets up flash data access.
 */

import type { IStateAdapter } from '../../adapters/types';
import type { FlashConfig, FlashData, FlashObject } from './types';
import { FlashManager } from './FlashManager';

/**
 * Flash instance returned by factory
 */
export interface FlashInstance {
    /**
     * The flash object for template access
     */
    flash: FlashObject;

    /**
     * Cleanup function
     */
    dispose: () => void;
}

/**
 * FlashFactory class
 */
export class FlashFactory {
    /**
     * Parse flash configuration from element attributes
     */
    static parseConfig(element: HTMLElement): FlashConfig | null {
        if (!element.hasAttribute('data-accelade-flash')) {
            return null;
        }

        // Get initial flash data from attribute
        const flashDataAttr = element.getAttribute('data-flash-data');
        let data: FlashData = {};

        if (flashDataAttr) {
            try {
                data = JSON.parse(flashDataAttr);
            } catch (e) {
                console.warn('[Accelade Flash] Failed to parse flash data:', e);
            }
        }

        // Check for auto-clear option
        const autoClear = element.getAttribute('data-flash-auto-clear') === 'true';

        return {
            data,
            autoClear,
        };
    }

    /**
     * Create a flash instance for a component
     */
    static create(
        componentId: string,
        element: HTMLElement,
        stateAdapter: IStateAdapter
    ): FlashInstance | undefined {
        const config = this.parseConfig(element);
        if (!config) {
            return undefined;
        }

        const manager = FlashManager.getInstance();

        // Initialize or merge flash data from server
        if (Object.keys(config.data).length > 0) {
            manager.merge(config.data);
        }

        // Create flash object for templates
        const flash = manager.createFlashObject();

        // Update component state with flash data
        stateAdapter.set('flash', flash);

        // Subscribe to flash changes
        const unsubscribe = manager.subscribe((flashData) => {
            // Update component state when flash data changes
            stateAdapter.set('flash', manager.createFlashObject());
        });

        return {
            flash,
            dispose: () => {
                unsubscribe();
            },
        };
    }

    /**
     * Get the global FlashManager instance
     */
    static getManager(): FlashManager {
        return FlashManager.getInstance();
    }

    /**
     * Update flash data (used during SPA navigation)
     */
    static updateFlashData(data: FlashData): void {
        FlashManager.getInstance().set(data);
    }

    /**
     * Merge flash data (preserves existing)
     */
    static mergeFlashData(data: FlashData): void {
        FlashManager.getInstance().merge(data);
    }

    /**
     * Clear all flash data
     */
    static clearFlashData(): void {
        FlashManager.getInstance().clear();
    }
}

export default FlashFactory;
