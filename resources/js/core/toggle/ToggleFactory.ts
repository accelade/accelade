/**
 * Toggle Factory
 *
 * Creates Toggle instances for managing boolean state values.
 * Supports both single toggle mode and multi-key mode.
 */

import type { IStateAdapter } from '../../adapters/types';
import type { ToggleConfig, ToggleInstance, ToggleMethods } from './types';

/**
 * Default key for single toggle mode
 */
const DEFAULT_KEY = 'toggled';

/**
 * Parse toggle configuration from element
 */
function parseConfig(element: HTMLElement): ToggleConfig {
    const id = element.dataset.toggleId ||
        `toggle-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    const dataAttr = element.dataset.toggleData || '';
    const defaultValueAttr = element.dataset.toggleDefault;

    // Parse default value
    const defaultValue = defaultValueAttr === 'true';

    // Parse keys from data attribute
    let keys: string[] = [];
    if (dataAttr) {
        // Check if it's a boolean string (single toggle with default)
        if (dataAttr === 'true' || dataAttr === 'false') {
            keys = [DEFAULT_KEY];
        } else {
            // Parse comma-separated keys
            keys = dataAttr.split(',').map(k => k.trim()).filter(k => k.length > 0);
        }
    } else {
        keys = [DEFAULT_KEY];
    }

    return {
        id,
        keys,
        defaultValue,
    };
}

/**
 * Create initial toggle state
 */
function createInitialState(config: ToggleConfig): Record<string, boolean> {
    const state: Record<string, boolean> = {};

    for (const key of config.keys) {
        state[key] = config.defaultValue;
    }

    return state;
}

/**
 * Create a Toggle instance
 */
export function createToggle(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): ToggleInstance | undefined {
    const config = parseConfig(element);
    const isMultiKey = config.keys.length > 1 || (config.keys.length === 1 && config.keys[0] !== DEFAULT_KEY);

    // Initialize state with toggle values
    const initialState = createInitialState(config);
    for (const [key, value] of Object.entries(initialState)) {
        // Only set if not already set in state
        if (stateAdapter.get(key) === undefined) {
            stateAdapter.set(key, value);
        }
    }

    /**
     * Toggle a value
     */
    const toggle = (key?: string): void => {
        const targetKey = isMultiKey ? key : DEFAULT_KEY;
        if (!targetKey) {
            console.warn('Toggle: Key required for multi-key toggle');
            return;
        }

        const currentValue = stateAdapter.get(targetKey);
        stateAdapter.set(targetKey, !currentValue);

        // Dispatch event
        dispatchToggleEvent(element, targetKey, !currentValue);
    };

    /**
     * Open (set to true)
     */
    const open = (key?: string): void => {
        const targetKey = isMultiKey ? key : DEFAULT_KEY;
        if (!targetKey) return;
        stateAdapter.set(targetKey, true);
        dispatchToggleEvent(element, targetKey, true);
    };

    /**
     * Close (set to false)
     */
    const close = (key?: string): void => {
        const targetKey = isMultiKey ? key : DEFAULT_KEY;
        if (!targetKey) return;
        stateAdapter.set(targetKey, false);
        dispatchToggleEvent(element, targetKey, false);
    };

    /**
     * Set a specific toggle value
     */
    const setToggle = (keyOrValue: string | boolean, value?: boolean): void => {
        if (isMultiKey) {
            // Multi-key mode: first arg is key, second is value
            if (typeof keyOrValue !== 'string') {
                console.warn('Toggle: Key must be a string in multi-key mode');
                return;
            }
            // Handle string 'true'/'false' from expression evaluation
            let targetValue: boolean;
            if (typeof value === 'boolean') {
                targetValue = value;
            } else if (value === 'true' || value === undefined) {
                targetValue = true;
            } else if (value === 'false') {
                targetValue = false;
            } else {
                targetValue = Boolean(value);
            }
            stateAdapter.set(keyOrValue, targetValue);
            dispatchToggleEvent(element, keyOrValue, targetValue);
        } else {
            // Single toggle mode: first arg is value
            // Handle string 'true'/'false' from expression evaluation
            let targetValue: boolean;
            if (typeof keyOrValue === 'boolean') {
                targetValue = keyOrValue;
            } else if (keyOrValue === 'true') {
                targetValue = true;
            } else if (keyOrValue === 'false') {
                targetValue = false;
            } else {
                targetValue = true; // default for unknown values
            }
            stateAdapter.set(DEFAULT_KEY, targetValue);
            dispatchToggleEvent(element, DEFAULT_KEY, targetValue);
        }
    };

    /**
     * Get toggled state
     */
    const getToggled = (key?: string): boolean => {
        const targetKey = isMultiKey ? key : DEFAULT_KEY;
        if (!targetKey) {
            return false;
        }
        return Boolean(stateAdapter.get(targetKey));
    };

    /**
     * Dispatch toggle event
     */
    const dispatchToggleEvent = (el: HTMLElement, key: string, value: boolean): void => {
        const detail = { key, value, id: config.id };
        el.dispatchEvent(new CustomEvent('toggle', { detail, bubbles: true }));
        document.dispatchEvent(new CustomEvent('accelade:toggle', { detail }));
    };

    /**
     * Dispose
     */
    const dispose = (): void => {
        // Cleanup if needed
    };

    return {
        id: config.id,
        config,
        element,
        isMultiKey,
        toggle,
        open,
        close,
        setToggle,
        getToggled,
        dispose,
    };
}

/**
 * Create toggle methods for template usage
 */
export function createToggleMethods(instance: ToggleInstance): ToggleMethods {
    return {
        toggle: instance.toggle,
        open: instance.open,
        close: instance.close,
        setToggle: instance.setToggle,
    };
}

/**
 * ToggleFactory namespace for module exports
 */
export const ToggleFactory = {
    parseConfig,
    create: createToggle,
    createMethods: createToggleMethods,
};
