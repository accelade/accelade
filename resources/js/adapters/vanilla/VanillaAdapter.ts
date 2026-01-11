/**
 * VanillaAdapter - Framework adapter for vanilla JavaScript
 */

import { BaseAdapter } from '../BaseAdapter';
import type { IStateAdapter, IBindingAdapter, BindingAttributeMap } from '../types';
import { VanillaStateAdapter } from './VanillaStateAdapter';
import { VanillaBindingAdapter } from './VanillaBindingAdapter';

/**
 * Vanilla binding attribute map
 */
const VANILLA_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 'a-text',
    html: 'a-html',
    show: 'a-show',
    if: 'a-if',
    model: 'a-model',
    class: 'a-class',
    style: 'a-style',
    bind: 'a-bind:',
    on: 'a-on:',
    cloak: 'a-cloak',
};

/**
 * VanillaAdapter - Pure JavaScript adapter using Proxy
 */
export class VanillaAdapter extends BaseAdapter {
    readonly type = 'vanilla' as const;

    /**
     * Create a vanilla state adapter
     */
    createStateAdapter(): IStateAdapter {
        return new VanillaStateAdapter();
    }

    /**
     * Create a vanilla binding adapter
     */
    createBindingAdapter(): IBindingAdapter {
        return new VanillaBindingAdapter();
    }

    /**
     * Vanilla is always available
     */
    isAvailable(): boolean {
        return true;
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 'a-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return 'a-on:';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[a-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attributes
     */
    getBindingAttributes(): BindingAttributeMap {
        return VANILLA_BINDING_ATTRIBUTES;
    }
}

export default VanillaAdapter;
