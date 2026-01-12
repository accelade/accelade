/**
 * Transition Component Types
 *
 * Types for the Transition component which provides CSS class-based
 * enter/leave animations.
 */

/**
 * Transition configuration parsed from element attributes
 */
export interface TransitionConfig {
    /** Unique identifier for the transition instance */
    id: string;
    /** Expression to evaluate for show/hide state */
    showExpression: string;
    /** Classes applied during enter transition */
    enter: string;
    /** Classes applied at the start of enter transition */
    enterFrom: string;
    /** Classes applied at the end of enter transition */
    enterTo: string;
    /** Classes applied during leave transition */
    leave: string;
    /** Classes applied at the start of leave transition */
    leaveFrom: string;
    /** Classes applied at the end of leave transition */
    leaveTo: string;
}

/**
 * Transition instance returned by the factory
 */
export interface TransitionInstance {
    /** Unique identifier */
    id: string;
    /** Configuration */
    config: TransitionConfig;
    /** The transition element */
    element: HTMLElement;
    /** Current visibility state */
    isVisible: boolean;
    /** Update visibility based on expression */
    update: (state: Record<string, unknown>) => void;
    /** Force show with animation */
    show: () => void;
    /** Force hide with animation */
    hide: () => void;
    /** Dispose the transition instance */
    dispose: () => void;
}
