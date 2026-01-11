/**
 * SvelteAdapter - Framework adapter for Svelte
 */

import { BaseAdapter } from '../BaseAdapter';
import type { IStateAdapter, IBindingAdapter, BindingAttributeMap } from '../types';
import { SvelteStateAdapter } from './SvelteStateAdapter';
import { SvelteBindingAdapter } from './SvelteBindingAdapter';

/**
 * Svelte binding attribute map
 * Using s-* prefix to avoid CSS selector issues with colons
 */
const SVELTE_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 's-text',
    html: 's-html',
    show: 's-show',
    if: 's-if',
    model: 's-model',
    class: 's-class',
    style: 's-style',
    bind: 's-bind-',
    on: 's-on-',
    cloak: 's-cloak',
};

/**
 * SvelteAdapter - Uses Svelte-style stores for reactivity
 */
export class SvelteAdapter extends BaseAdapter {
    readonly type = 'svelte' as const;

    /**
     * Create a Svelte state adapter
     */
    createStateAdapter(): IStateAdapter {
        return new SvelteStateAdapter();
    }

    /**
     * Create a Svelte binding adapter
     */
    createBindingAdapter(): IBindingAdapter {
        return new SvelteBindingAdapter();
    }

    /**
     * Check if Svelte is available
     */
    isAvailable(): boolean {
        if (typeof window !== 'undefined') {
            // Check for Svelte-specific class naming convention
            const hasSvelteElements = document.querySelector('[class*="svelte-"]') !== null;
            if (hasSvelteElements) {
                return true;
            }

            // Check for Svelte runtime
            if ((window as unknown as Record<string, unknown>).__svelte) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 's-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return 's-on-';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[svelte-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attributes
     */
    getBindingAttributes(): BindingAttributeMap {
        return SVELTE_BINDING_ATTRIBUTES;
    }
}

export default SvelteAdapter;
