/**
 * ReactAdapter - Framework adapter for React
 */

import { BaseAdapter } from '../BaseAdapter';
import type { IStateAdapter, IBindingAdapter, BindingAttributeMap } from '../types';
import { ReactStateAdapter } from './ReactStateAdapter';
import { ReactBindingAdapter } from './ReactBindingAdapter';

/**
 * React binding attribute map
 * Using data-state-* format to avoid CSS selector issues with colons
 */
const REACT_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 'data-state-text',
    html: 'data-state-html',
    show: 'data-state-show',
    if: 'data-state-if',
    model: 'data-state-model',
    class: 'data-state-class',
    style: 'data-state-style',
    bind: 'data-state-',
    on: 'data-on-',
    cloak: 'data-accelade-cloak',
};

/**
 * ReactAdapter - Uses React's useState/useReducer for state management
 */
export class ReactAdapter extends BaseAdapter {
    readonly type = 'react' as const;

    /**
     * Create a React state adapter
     */
    createStateAdapter(): IStateAdapter {
        return new ReactStateAdapter();
    }

    /**
     * Create a React binding adapter
     */
    createBindingAdapter(): IBindingAdapter {
        return new ReactBindingAdapter();
    }

    /**
     * Check if React is available
     */
    isAvailable(): boolean {
        if (typeof window !== 'undefined') {
            // Check window.React global
            if ((window as unknown as Record<string, unknown>).React) {
                return true;
            }

            // Check for React root markers in DOM
            const hasReactRoot = document.querySelector('[data-reactroot]') !== null;
            if (hasReactRoot) {
                return true;
            }

            // Check for React 18+ root markers
            const hasReact18Root = document.querySelector('[data-react-root]') !== null;
            if (hasReact18Root) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 'data-state-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return 'data-on-';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[state-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attributes
     */
    getBindingAttributes(): BindingAttributeMap {
        return REACT_BINDING_ATTRIBUTES;
    }
}

export default ReactAdapter;
