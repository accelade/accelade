/**
 * React Components for Accelade
 */

import React, {
    createContext,
    useContext,
    type ReactNode,
    type ReactElement,
} from 'react';
import { useAccelade, type UseAcceladeResult, type UseAcceladeOptions } from './hooks';
import { navigate } from '../../core/router';

/**
 * State type
 */
type StateRecord = Record<string, unknown>;

/**
 * Context for Accelade state
 */
const AcceladeContext = createContext<UseAcceladeResult<StateRecord> | null>(null);

/**
 * AcceladeProvider props
 */
export interface AcceladeProviderProps<T extends StateRecord> {
    initialState: T;
    componentId?: string;
    syncProperties?: string[];
    scripts?: string[];
    children: ReactNode | ((accelade: UseAcceladeResult<T>) => ReactNode);
}

/**
 * AcceladeProvider - Context provider for Accelade state
 *
 * @example
 * ```tsx
 * <AcceladeProvider initialState={{ count: 0 }}>
 *   {({ state, actions }) => (
 *     <div>
 *       <span>{state.count}</span>
 *       <button onClick={() => actions.increment()}>+</button>
 *     </div>
 *   )}
 * </AcceladeProvider>
 * ```
 */
export function AcceladeProvider<T extends StateRecord>({
    initialState,
    componentId,
    syncProperties,
    scripts,
    children,
}: AcceladeProviderProps<T>): ReactElement {
    const options: UseAcceladeOptions = {
        componentId,
        syncProperties,
        scripts,
    };

    const accelade = useAccelade(initialState, options);

    return (
        <AcceladeContext.Provider value={accelade as UseAcceladeResult<StateRecord>}>
            {typeof children === 'function' ? children(accelade) : children}
        </AcceladeContext.Provider>
    );
}

/**
 * useAcceladeContext - Use Accelade state from context
 *
 * @example
 * ```tsx
 * function Counter() {
 *   const { state, actions } = useAcceladeContext();
 *   return <span>{state.count}</span>;
 * }
 * ```
 */
export function useAcceladeContext<T extends StateRecord = StateRecord>(): UseAcceladeResult<T> {
    const context = useContext(AcceladeContext);
    if (!context) {
        throw new Error('useAcceladeContext must be used within AcceladeProvider');
    }
    return context as UseAcceladeResult<T>;
}

/**
 * AcceladeLink props
 */
export interface AcceladeLinkProps {
    href: string;
    children: ReactNode;
    className?: string;
    activeClassName?: string;
    prefetch?: boolean;
    replace?: boolean;
    [key: string]: unknown;
}

/**
 * AcceladeLink - SPA navigation link component
 *
 * @example
 * ```tsx
 * <AcceladeLink href="/dashboard" activeClassName="text-blue-500">
 *   Dashboard
 * </AcceladeLink>
 * ```
 */
export function AcceladeLink({
    href,
    children,
    className = '',
    activeClassName = 'active',
    prefetch = false,
    replace = false,
    ...props
}: AcceladeLinkProps): ReactElement {
    const isActive =
        typeof window !== 'undefined' &&
        window.location.pathname === new URL(href, window.location.origin).pathname;

    const handleClick = (e: React.MouseEvent<HTMLAnchorElement>) => {
        // Allow ctrl/cmd click for new tab
        if (e.ctrlKey || e.metaKey || e.shiftKey) return;

        e.preventDefault();
        void navigate(href, { pushState: !replace });
    };

    // Prefetch on hover (optional enhancement)
    const handleMouseEnter = () => {
        if (prefetch && typeof window !== 'undefined') {
            // Could prefetch the page here
            // For now, just a placeholder for future enhancement
        }
    };

    return (
        <a
            href={href}
            className={`${className} ${isActive ? activeClassName : ''}`.trim()}
            onClick={handleClick}
            onMouseEnter={handleMouseEnter}
            {...props}
        >
            {children}
        </a>
    );
}

/**
 * Show component props - Conditional rendering based on state
 */
export interface ShowProps {
    when: unknown;
    fallback?: ReactNode;
    children: ReactNode;
}

/**
 * Show - Conditional rendering component
 *
 * @example
 * ```tsx
 * <Show when={state.isLoading} fallback={<Content />}>
 *   <Spinner />
 * </Show>
 * ```
 */
export function Show({ when, fallback = null, children }: ShowProps): ReactElement | null {
    return <>{when ? children : fallback}</>;
}

/**
 * For component props - List rendering
 */
export interface ForProps<T> {
    each: T[];
    fallback?: ReactNode;
    children: (item: T, index: number) => ReactNode;
}

/**
 * For - List rendering component
 *
 * @example
 * ```tsx
 * <For each={state.items} fallback={<p>No items</p>}>
 *   {(item, i) => <li key={i}>{item.name}</li>}
 * </For>
 * ```
 */
export function For<T>({ each, fallback = null, children }: ForProps<T>): ReactElement {
    if (!each || each.length === 0) {
        return <>{fallback}</>;
    }

    return <>{each.map((item, index) => children(item, index))}</>;
}

/**
 * Switch/Match components for conditional rendering
 */
export interface SwitchProps {
    children: ReactNode;
    fallback?: ReactNode;
}

export interface MatchProps {
    when: unknown;
    children: ReactNode;
}

/**
 * Match - Used inside Switch for pattern matching
 */
export function Match({ when, children }: MatchProps): ReactElement | null {
    // Match is handled by Switch, this just provides the structure
    return <>{when ? children : null}</>;
}

/**
 * Switch - Pattern matching component
 *
 * @example
 * ```tsx
 * <Switch fallback={<p>Unknown</p>}>
 *   <Match when={state.status === 'loading'}>Loading...</Match>
 *   <Match when={state.status === 'success'}>Done!</Match>
 *   <Match when={state.status === 'error'}>Error!</Match>
 * </Switch>
 * ```
 */
export function Switch({ children, fallback = null }: SwitchProps): ReactElement {
    const childArray = React.Children.toArray(children);

    for (const child of childArray) {
        if (React.isValidElement(child) && child.type === Match) {
            const matchProps = child.props as MatchProps;
            if (matchProps.when) {
                return <>{matchProps.children}</>;
            }
        }
    }

    return <>{fallback}</>;
}

export default {
    AcceladeProvider,
    useAcceladeContext,
    AcceladeLink,
    Show,
    For,
    Switch,
    Match,
};
