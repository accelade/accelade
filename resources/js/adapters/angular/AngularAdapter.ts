/**
 * AngularAdapter - Framework adapter for Angular
 */

import { BaseAdapter } from '../BaseAdapter';
import type { IStateAdapter, IBindingAdapter, BindingAttributeMap } from '../types';
import { AngularStateAdapter } from './AngularStateAdapter';
import { AngularBindingAdapter } from './AngularBindingAdapter';

/**
 * Angular binding attribute map
 * Using ng-* prefix to avoid CSS selector issues with brackets/parens
 */
const ANGULAR_BINDING_ATTRIBUTES: BindingAttributeMap = {
    text: 'ng-text',
    html: 'ng-html',
    show: 'ng-show',
    if: 'ng-if',
    model: 'ng-model',
    class: 'ng-class',
    style: 'ng-style',
    bind: 'ng-bind-',
    on: 'ng-on-',
    cloak: 'ng-cloak',
};

/**
 * AngularAdapter - Uses Angular-style signals/RxJS for reactivity
 */
export class AngularAdapter extends BaseAdapter {
    readonly type = 'angular' as const;

    /**
     * Create an Angular state adapter
     */
    createStateAdapter(): IStateAdapter {
        return new AngularStateAdapter();
    }

    /**
     * Create an Angular binding adapter
     */
    createBindingAdapter(): IBindingAdapter {
        return new AngularBindingAdapter();
    }

    /**
     * Check if Angular is available
     */
    isAvailable(): boolean {
        if (typeof window !== 'undefined') {
            // Check for Angular global (ng)
            if ((window as unknown as Record<string, unknown>).ng) {
                return true;
            }

            // Check for ng-version attribute (Angular apps set this on root)
            const hasAngularApp = document.querySelector('[ng-version]') !== null;
            if (hasAngularApp) {
                return true;
            }

            // Check for Angular-specific attributes
            const hasAngularBindings = document.querySelector('[_nghost], [_ngcontent]') !== null;
            if (hasAngularBindings) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get attribute prefix
     */
    getAttributePrefix(): string {
        return 'ng-';
    }

    /**
     * Get event prefix
     */
    getEventPrefix(): string {
        return 'ng-on-';
    }

    /**
     * Get script selector
     */
    getScriptSelector(): string {
        return 'script[ng-script], accelade-script, accelade\\:script';
    }

    /**
     * Get binding attributes
     */
    getBindingAttributes(): BindingAttributeMap {
        return ANGULAR_BINDING_ATTRIBUTES;
    }
}

export default AngularAdapter;
