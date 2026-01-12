/**
 * State Factory
 *
 * Factory for creating State component instances that provide
 * unified access to errors, flash data, and shared data.
 */

import type {
    StateConfig,
    StateInstance,
    StateObject,
    StateHelpers,
    ValidationErrors,
    FlashData,
    SharedData,
} from './types';
import type { IStateAdapter } from '../../adapters/types';
import { SharedDataManager } from '../shared';

/**
 * Parse configuration from element data attributes
 */
function parseConfig(element: HTMLElement): StateConfig {
    const id = element.dataset.stateId || `state-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    // Parse errors
    let errors: ValidationErrors = {};
    const errorsAttr = element.dataset.stateErrors;
    if (errorsAttr) {
        try {
            errors = JSON.parse(errorsAttr);
        } catch {
            // Invalid JSON, keep empty
        }
    }

    // Parse flash data
    let flash: FlashData = {};
    const flashAttr = element.dataset.stateFlash;
    if (flashAttr) {
        try {
            flash = JSON.parse(flashAttr);
        } catch {
            // Invalid JSON, keep empty
        }
    }

    // Parse shared data (or get from global)
    let shared: SharedData = {};
    const sharedAttr = element.dataset.stateShared;
    if (sharedAttr) {
        try {
            shared = JSON.parse(sharedAttr);
        } catch {
            // Invalid JSON, keep empty
        }
    } else {
        // Use global shared data
        shared = SharedDataManager.getInstance().all();
    }

    return {
        id,
        errors,
        flash,
        shared,
    };
}

/**
 * Create state object from config
 */
function createStateObject(config: StateConfig): StateObject {
    // Convert raw errors to first-error-only format
    const errors: Record<string, string> = {};
    for (const [key, value] of Object.entries(config.errors)) {
        if (Array.isArray(value)) {
            errors[key] = value[0] || '';
        } else {
            errors[key] = value;
        }
    }

    return {
        errors,
        rawErrors: config.errors,
        hasErrors: Object.keys(config.errors).length > 0,
        flash: config.flash,
        shared: config.shared,
    };
}

/**
 * Create helper methods
 */
function createHelpers(state: StateObject): StateHelpers {
    return {
        hasError: (key: string): boolean => {
            return key in state.rawErrors;
        },

        getError: (key: string): string | null => {
            return state.errors[key] || null;
        },

        getErrors: (key: string): string[] => {
            const errors = state.rawErrors[key];
            if (!errors) return [];
            return Array.isArray(errors) ? errors : [errors];
        },

        hasFlash: (key: string): boolean => {
            return key in state.flash;
        },

        getFlash: <T = unknown>(key: string): T | null => {
            return (state.flash[key] as T) ?? null;
        },

        hasShared: (key: string): boolean => {
            // Support dot notation
            const keys = key.split('.');
            let current: unknown = state.shared;

            for (const k of keys) {
                if (current === null || current === undefined || typeof current !== 'object') {
                    return false;
                }
                if (!(k in (current as Record<string, unknown>))) {
                    return false;
                }
                current = (current as Record<string, unknown>)[k];
            }

            return true;
        },

        getShared: <T = unknown>(key: string): T | null => {
            // Support dot notation
            const keys = key.split('.');
            let current: unknown = state.shared;

            for (const k of keys) {
                if (current === null || current === undefined || typeof current !== 'object') {
                    return null;
                }
                current = (current as Record<string, unknown>)[k];
            }

            return (current as T) ?? null;
        },
    };
}

/**
 * Create a state instance
 */
export function createState(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): StateInstance | null {
    const config = parseConfig(element);
    const stateObject = createStateObject(config);
    const helpers = createHelpers(stateObject);

    // Set initial state on the adapter
    stateAdapter.set('state', stateObject);
    stateAdapter.set('hasError', helpers.hasError);
    stateAdapter.set('getError', helpers.getError);
    stateAdapter.set('getErrors', helpers.getErrors);
    stateAdapter.set('hasFlash', helpers.hasFlash);
    stateAdapter.set('getFlash', helpers.getFlash);
    stateAdapter.set('hasShared', helpers.hasShared);
    stateAdapter.set('getShared', helpers.getShared);

    const instance: StateInstance = {
        id: config.id,
        config,
        element,
        state: stateObject,
        helpers,
        dispose: () => {
            // Cleanup if needed
        },
    };

    return instance;
}

/**
 * Initialize state components within a container
 */
export function initState(container: HTMLElement | Document = document): StateInstance[] {
    const instances: StateInstance[] = [];

    // This is typically handled by the adapter, but we can scan for standalone state components
    const elements = container.querySelectorAll<HTMLElement>('[data-accelade-state-component]');

    // Note: State components are typically initialized through the adapter
    // This function is for standalone initialization if needed

    return instances;
}

// Re-export types
export type { StateConfig, StateInstance, StateObject, StateHelpers, ValidationErrors, FlashData, SharedData } from './types';
