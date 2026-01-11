/**
 * Accelade React Integration
 * React-style state management with hooks
 */

import React, {
    useState,
    useEffect,
    useCallback,
    createContext,
    useContext,
    useMemo,
    useRef,
    type ReactNode,
    type Dispatch,
    type SetStateAction,
} from 'react';
import { createRoot, type Root } from 'react-dom/client';
import type { AcceladeComponentConfig, AcceladeActions } from '../core/types';
import { initRouter, navigate, getRouter, type NavigationOptions } from '../core/router';
import { getProgress, configureProgress, startProgress, doneProgress, type ProgressConfig } from '../core/progress';

// State type
type StateRecord = Record<string, unknown>;

// Custom methods type
type CustomMethods = Record<string, (...args: unknown[]) => unknown>;

// Hook result type
interface UseAcceladeResult<T extends StateRecord> {
    state: T;
    setState: Dispatch<SetStateAction<T>>;
    actions: AcceladeActions;
    customMethods: CustomMethods;
}

// Context type
const AcceladeContext = createContext<UseAcceladeResult<StateRecord> | null>(null);

/**
 * Parse Accelade element data attributes
 */
function parseAcceladeElement(el: HTMLElement): AcceladeComponentConfig {
    const id = el.dataset.acceladeId ?? `accelade-${Math.random().toString(36).slice(2, 10)}`;
    const stateStr = el.dataset.acceladeState ?? '{}';
    const syncStr = el.dataset.acceladeSync ?? '';

    let state: StateRecord = {};
    try {
        state = JSON.parse(stateStr) as StateRecord;
    } catch {
        console.error('Accelade React: Invalid state JSON', stateStr);
    }

    const sync = syncStr ? syncStr.split(',').filter(Boolean) : [];

    return { id, state, sync };
}

/**
 * Sync state to server
 */
function syncToServer(componentId: string, property: string, value: unknown): void {
    const config = window.AcceladeConfig;
    const url = config?.updateUrl ?? '/accelade/update';
    const csrfToken = config?.csrfToken ?? '';

    // Show progress bar during sync
    startProgress();

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            component: componentId,
            property: property,
            value: value
        })
    })
    .then(() => {
        doneProgress();
    })
    .catch((err: unknown) => {
        console.error('Accelade React: Sync failed', err);
        doneProgress();
    });
}

/**
 * Options for useAccelade hook
 */
interface UseAcceladeOptions {
    componentId?: string;
    syncProperties?: string[];
    scripts?: string[];
}

/**
 * useAccelade hook - React-style state management
 */
export function useAccelade<T extends StateRecord>(
    initialState: T,
    options: UseAcceladeOptions = {}
): UseAcceladeResult<T> {
    const [state, setState] = useState<T>(initialState);
    const { componentId, syncProperties = [], scripts = [] } = options;
    const customMethodsRef = useRef<CustomMethods>({});

    // Sync effect
    useEffect(() => {
        if (componentId && syncProperties.length > 0) {
            syncProperties.forEach(prop => {
                syncToServer(componentId, prop, state[prop]);
            });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, syncProperties.map(prop => state[prop]));

    // Actions
    const actions = useMemo<AcceladeActions>(() => ({
        increment: (key = 'count', amount = 1): void => {
            setState(prev => ({
                ...prev,
                [key]: (parseInt(String(prev[key]), 10) || 0) + amount
            }));
        },

        decrement: (key = 'count', amount = 1): void => {
            setState(prev => ({
                ...prev,
                [key]: (parseInt(String(prev[key]), 10) || 0) - amount
            }));
        },

        set: (key: string, value: unknown): void => {
            setState(prev => ({ ...prev, [key]: value }));
        },

        toggle: (key: string): void => {
            setState(prev => ({ ...prev, [key]: !prev[key] }));
        },

        reset: (key: string): void => {
            setState(prev => ({
                ...prev,
                [key]: typeof initialState[key] === 'number' ? 0 :
                       typeof initialState[key] === 'boolean' ? false : ''
            }));
        }
    }), [initialState]);

    // Process scripts on mount
    useEffect(() => {
        scripts.forEach(code => {
            if (!code.trim()) return;

            try {
                const scriptFn = new Function(
                    'state',
                    'setState',
                    'actions',
                    '$set',
                    '$get',
                    '$toggle',
                    '$navigate',
                    'initialState',
                    code
                ) as (
                    state: T,
                    setState: Dispatch<SetStateAction<T>>,
                    actions: AcceladeActions,
                    $set: (key: string, value: unknown) => void,
                    $get: (key: string) => unknown,
                    $toggle: (key: string) => void,
                    $navigate: (url: string, options?: NavigationOptions) => Promise<boolean>,
                    initialState: T
                ) => CustomMethods | void;

                const $set = (key: string, value: unknown): void => {
                    setState(prev => ({ ...prev, [key]: value }));
                };
                const $get = (key: string): unknown => state[key];
                const $toggle = (key: string): void => {
                    setState(prev => ({ ...prev, [key]: !prev[key] }));
                };

                const result = scriptFn(
                    state,
                    setState,
                    actions,
                    $set,
                    $get,
                    $toggle,
                    navigate,
                    initialState
                );

                if (result && typeof result === 'object') {
                    Object.assign(customMethodsRef.current, result);
                }
            } catch (e) {
                console.error('Accelade React: Error executing script:', e);
            }
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return { state, setState, actions, customMethods: customMethodsRef.current };
}

/**
 * AcceladeProvider props
 */
interface AcceladeProviderProps<T extends StateRecord> {
    initialState: T;
    componentId?: string;
    syncProperties?: string[];
    children: ReactNode | ((accelade: UseAcceladeResult<T>) => ReactNode);
}

/**
 * AcceladeProvider - Context provider for Accelade state
 */
export function AcceladeProvider<T extends StateRecord>({
    initialState,
    componentId,
    syncProperties,
    children
}: AcceladeProviderProps<T>): React.ReactElement {
    const accelade = useAccelade(initialState, { componentId, syncProperties });

    return (
        <AcceladeContext.Provider value={accelade as UseAcceladeResult<StateRecord>}>
            {typeof children === 'function' ? children(accelade) : children}
        </AcceladeContext.Provider>
    );
}

/**
 * useAcceladeContext - Use Accelade from context
 */
export function useAcceladeContext<T extends StateRecord = StateRecord>(): UseAcceladeResult<T> {
    const context = useContext(AcceladeContext);
    if (!context) {
        throw new Error('useAcceladeContext must be used within AcceladeProvider');
    }
    return context as UseAcceladeResult<T>;
}

/**
 * Link component props
 */
interface AcceladeLinkProps {
    href: string;
    children: ReactNode;
    className?: string;
    activeClassName?: string;
    [key: string]: unknown;
}

/**
 * AcceladeLink - SPA navigation link component
 */
export function AcceladeLink({
    href,
    children,
    className = '',
    activeClassName = 'active',
    ...props
}: AcceladeLinkProps): React.ReactElement {
    const isActive = window.location.pathname === new URL(href, window.location.origin).pathname;

    const handleClick = (e: React.MouseEvent<HTMLAnchorElement>) => {
        e.preventDefault();
        void navigate(href);
    };

    return (
        <a
            href={href}
            className={`${className} ${isActive ? activeClassName : ''}`.trim()}
            onClick={handleClick}
            {...props}
        >
            {children}
        </a>
    );
}

/**
 * AcceladeComponent props
 */
interface AcceladeComponentProps {
    template: string;
    config: AcceladeComponentConfig;
    scripts: string[];
}

/**
 * Generic Accelade Component - Renders children with state
 */
function AcceladeComponent({ template, config, scripts }: AcceladeComponentProps): React.ReactElement {
    const { state, actions, customMethods } = useAccelade(config.state, {
        componentId: config.id,
        syncProperties: config.sync,
        scripts
    });

    // Parse and render template with state bindings
    const renderTemplate = useCallback((): ReactNode[] => {
        const container = document.createElement('div');
        container.innerHTML = template;

        // Process the template and convert to React elements
        const processNode = (node: Node, key: number = 0): ReactNode => {
            if (node.nodeType === Node.TEXT_NODE) {
                // Replace {{ property }} with state values
                let text = node.textContent ?? '';
                Object.keys(state).forEach(stateKey => {
                    text = text.replace(
                        new RegExp(`\\{\\{\\s*${stateKey}\\s*\\}\\}`, 'g'),
                        String(state[stateKey])
                    );
                });
                return text;
            }

            if (node.nodeType !== Node.ELEMENT_NODE) return null;

            const element = node as HTMLElement;
            const props: Record<string, unknown> = { key };
            const tagName = element.tagName.toLowerCase();

            // Process attributes
            Array.from(element.attributes).forEach(attr => {
                let name = attr.name;
                const value = attr.value;

                // Handle state: bindings (React-style)
                if (name.startsWith('state:')) {
                    const bindType = name.slice(6);
                    if (bindType === 'text') {
                        // Will handle in children
                        return;
                    }
                    // state:disabled="isDisabled" -> disabled={state.isDisabled}
                    const stateValue = state[value];
                    if (stateValue !== undefined) {
                        if (bindType === 'class' || bindType === 'className') {
                            props.className = stateValue;
                        } else {
                            props[bindType] = stateValue;
                        }
                    }
                    return;
                }

                // Handle onClick actions
                if (name === 'onclick' || name === 'data-onclick') {
                    const actionStr = value;
                    props.onClick = (e: React.MouseEvent) => {
                        e.preventDefault();

                        // Check custom methods first
                        if (customMethods[actionStr]) {
                            (customMethods[actionStr] as (event: React.MouseEvent) => void)(e);
                            return;
                        }

                        if (actionStr.includes(':')) {
                            const [action, actionKey] = actionStr.split(':');

                            // Check custom methods
                            if (customMethods[action]) {
                                (customMethods[action] as (key: string, event: React.MouseEvent) => void)(actionKey, e);
                                return;
                            }

                            const actionMethod = actions[action as keyof AcceladeActions];
                            if (typeof actionMethod === 'function') {
                                (actionMethod as (key: string) => void)(actionKey);
                            }
                        } else {
                            const actionMethod = actions[actionStr as keyof AcceladeActions];
                            if (typeof actionMethod === 'function') {
                                (actionMethod as () => void)();
                            }
                        }
                    };
                    return;
                }

                // Convert to React-style props
                if (name === 'class') name = 'className';
                if (name === 'for') name = 'htmlFor';
                if (name === 'tabindex') name = 'tabIndex';
                if (name === 'readonly') name = 'readOnly';

                props[name] = value;
            });

            // Process children
            const children = Array.from(element.childNodes)
                .map((child, i) => processNode(child, i))
                .filter((child): child is ReactNode => child !== null);

            // Check for state:text binding
            const stateText = element.getAttribute('state:text');
            if (stateText && state[stateText] !== undefined) {
                return React.createElement(tagName, props, String(state[stateText]));
            }

            return React.createElement(tagName, props, ...children);
        };

        return Array.from(container.childNodes)
            .map((node, i) => processNode(node, i))
            .filter((node): node is ReactNode => node !== null);
    }, [template, state, actions, customMethods]);

    return <div>{renderTemplate()}</div>;
}

/**
 * Extended HTMLElement with initialization flag
 */
interface AcceladeHTMLElement extends HTMLElement {
    __accelade_react_initialized?: boolean;
    __accelade_react_root?: Root;
}

// Module-level initialization guard
let acceladeInitialized = false;

/**
 * Initialize Accelade React components
 */
export function init(): void {
    const elements = document.querySelectorAll<AcceladeHTMLElement>('[data-accelade]');

    elements.forEach((el) => {
        if (el.__accelade_react_initialized) return;

        const config = parseAcceladeElement(el);
        const template = el.innerHTML;

        // Extract scripts - look for state-script attribute, accelade-script tag, or accelade:script tag
        // Note: CSS selector needs escaped colon for accelade:script
        const scripts: string[] = [];
        const scriptElements = el.querySelectorAll<HTMLScriptElement>('script[state-script], accelade-script, accelade\\:script');
        scriptElements.forEach((scriptEl) => {
            const code = scriptEl.textContent ?? '';
            if (code.trim()) {
                scripts.push(code);
            }
            scriptEl.remove();
        });

        try {
            // Clear and create mount point
            el.innerHTML = '';
            const root = createRoot(el);

            // Render the component
            root.render(
                <AcceladeComponent template={template} config={config} scripts={scripts} />
            );

            // Remove cloak and add ready class for smooth reveal
            el.removeAttribute('data-accelade-cloak');
            el.classList.add('accelade-ready');

            el.__accelade_react_initialized = true;
            el.__accelade_react_root = root;
        } catch (e) {
            console.error('Accelade React: Failed to init component', config.id, e);
        }
    });

    // Initialize the router only once
    if (!acceladeInitialized) {
        initRouter();
        acceladeInitialized = true;
    }
}

// Export types
export type {
    UseAcceladeResult,
    UseAcceladeOptions,
    AcceladeProviderProps,
    AcceladeLinkProps,
    AcceladeComponentProps,
};

// Progress API object
const progress = {
    configure: configureProgress,
    start: startProgress,
    done: doneProgress,
    instance: getProgress,
};

// Export for window
if (typeof window !== 'undefined') {
    // Configure progress from AcceladeConfig if available
    const progressConfig = window.AcceladeConfig?.progress;
    if (progressConfig && Object.keys(progressConfig).length > 0) {
        configureProgress(progressConfig as ProgressConfig);
    }

    // Auto-init on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => init());
    } else {
        init();
    }
}

// Export everything including progress
export { navigate, getRouter, initRouter, configureProgress, startProgress, doneProgress, progress };
export default { init, navigate, getRouter, initRouter, useAccelade, AcceladeProvider, AcceladeLink, AcceladeComponent, configureProgress, startProgress, doneProgress, progress };
