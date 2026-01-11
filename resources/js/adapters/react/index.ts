/**
 * React Adapter Exports
 */

// Core adapter
export { ReactStateAdapter } from './ReactStateAdapter';
export { ReactBindingAdapter } from './ReactBindingAdapter';
export { ReactAdapter } from './ReactAdapter';
export { ReactAdapter as default } from './ReactAdapter';

// Hooks
export {
    useAccelade,
    useAcceladeSync,
    type UseAcceladeResult,
    type UseAcceladeOptions,
} from './hooks';

// Components
export {
    AcceladeProvider,
    useAcceladeContext,
    AcceladeLink,
    Show,
    For,
    Switch,
    Match,
    type AcceladeProviderProps,
    type AcceladeLinkProps,
    type ShowProps,
    type ForProps,
    type SwitchProps,
    type MatchProps,
} from './components';
