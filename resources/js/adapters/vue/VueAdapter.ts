/**
 * VueAdapter - Framework adapter for Vue.js
 */

import { BaseAdapter } from '../BaseAdapter';
import type { IStateAdapter, IBindingAdapter, BindingAttributeMap } from '../types';
import { VueStateAdapter } from './VueStateAdapter';
import { VueBindingAdapter } from './VueBindingAdapter';

/**
 * Vue binding attribute map
 */
const VUE_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 'v-text',
    html: 'v-html',
    show: 'v-show',
    if: 'v-if',
    model: 'v-model',
    class: 'v-class',
    style: 'v-style',
    bind: 'v-bind:',
    on: 'v-on:',
    cloak: 'v-cloak',
};

/**
 * VueAdapter - Uses Vue's reactive() and effect() for reactivity
 */
export class VueAdapter extends BaseAdapter {
    readonly type = 'vue' as const;

    /**
     * Create a Vue state adapter
     */
    createStateAdapter(): IStateAdapter {
        return new VueStateAdapter();
    }

    /**
     * Create a Vue binding adapter
     */
    createBindingAdapter(): IBindingAdapter {
        return new VueBindingAdapter();
    }

    /**
     * Check if Vue is available
     */
    isAvailable(): boolean {
        // Check for Vue global or data attributes
        if (typeof window !== 'undefined') {
            // Check window.Vue global
            if ((window as unknown as Record<string, unknown>).Vue) {
                return true;
            }

            // Check for Vue reactive markers in DOM
            const hasVueElements = document.querySelector('[data-v-]') !== null;
            if (hasVueElements) {
                return true;
            }
        }

        // Always return true since we bundle Vue's reactivity system
        return true;
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 'v-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return 'v-on:';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[v-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attributes
     */
    getBindingAttributes(): BindingAttributeMap {
        return VUE_BINDING_ATTRIBUTES;
    }

    /**
     * Get shorthand event prefix (@)
     */
    getShorthandEventPrefix(): string {
        return '@';
    }

    /**
     * Get shorthand bind prefix (:)
     */
    getShorthandBindPrefix(): string {
        return ':';
    }
}

export default VueAdapter;
