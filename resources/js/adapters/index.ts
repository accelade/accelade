/**
 * Adapters - Export all adapter modules
 */

// Types
export type {
    FrameworkType,
    ComponentInstance,
    IStateAdapter,
    IBindingAdapter,
    IFrameworkAdapter,
    BindingAttributeMap,
    AdapterFactory,
    AdapterRegistration,
    AdapterConfig,
    EventBinding,
    CleanupFn,
} from './types';

// Base adapter
export { BaseAdapter } from './BaseAdapter';

// Vanilla adapter
export { VanillaAdapter, VanillaStateAdapter, VanillaBindingAdapter } from './vanilla';

// Vue adapter
export { VueAdapter, VueStateAdapter, VueBindingAdapter } from './vue';

// React adapter
export {
    ReactAdapter,
    ReactStateAdapter,
    ReactBindingAdapter,
    useAccelade,
    useAcceladeSync,
    AcceladeProvider,
    useAcceladeContext,
    AcceladeLink,
    Show,
    For,
    Switch,
    Match,
} from './react';

// Svelte adapter
export { SvelteAdapter, SvelteStateAdapter, SvelteBindingAdapter } from './svelte';

// Angular adapter
export { AngularAdapter, AngularStateAdapter, AngularBindingAdapter } from './angular';
