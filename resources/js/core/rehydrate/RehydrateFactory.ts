/**
 * Rehydrate Factory
 *
 * Factory for creating and managing rehydrate instances
 * with adapter integration.
 */

import rehydrateManager from './RehydrateManager';
import type { RehydrateInstance } from './types';

/**
 * Initialize rehydrate components within a container
 */
export function initRehydrate(container: HTMLElement | Document = document): RehydrateInstance[] {
    const instances: RehydrateInstance[] = [];

    // Find all rehydrate elements
    const elements = container.querySelectorAll<HTMLElement>('[data-accelade-rehydrate]');

    elements.forEach((element) => {
        const instance = rehydrateManager.register(element);
        if (instance) {
            instances.push(instance);
        }
    });

    return instances;
}

/**
 * Initialize the rehydrate system
 */
export function initRehydrateSystem(): void {
    rehydrateManager.init();
}

/**
 * Emit an event that can trigger rehydration
 */
export function emit(eventName: string, data?: unknown): void {
    rehydrateManager.emit(eventName, data);
}

// Re-export manager and types
export { rehydrateManager };
export type { RehydrateConfig, RehydrateInstance, RehydrateEventDetail } from './types';
