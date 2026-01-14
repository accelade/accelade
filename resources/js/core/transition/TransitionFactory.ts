/**
 * Transition Factory
 *
 * Creates Transition instances for CSS class-based enter/leave animations.
 * Similar to Vue/Alpine transitions with customizable classes.
 */

import type { IStateAdapter } from '../../adapters/types';
import type { TransitionConfig, TransitionInstance } from './types';
import { evaluateBooleanExpression } from '../expressions';

/**
 * Parse transition configuration from element
 */
function parseConfig(element: HTMLElement): TransitionConfig {
    const id = element.dataset.transitionId ||
        `transition-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    return {
        id,
        showExpression: element.dataset.transitionShow || 'true',
        enter: element.dataset.transitionEnter || 'transition ease-out duration-200',
        enterFrom: element.dataset.transitionEnterFrom || 'opacity-0',
        enterTo: element.dataset.transitionEnterTo || 'opacity-100',
        leave: element.dataset.transitionLeave || 'transition ease-in duration-150',
        leaveFrom: element.dataset.transitionLeaveFrom || 'opacity-100',
        leaveTo: element.dataset.transitionLeaveTo || 'opacity-0',
    };
}

/**
 * Parse classes string into array
 */
function parseClasses(classString: string): string[] {
    return classString.split(/\s+/).filter(c => c.length > 0);
}

/**
 * Add classes to element
 */
function addClasses(element: HTMLElement, classes: string[]): void {
    classes.forEach(c => element.classList.add(c));
}

/**
 * Remove classes from element
 */
function removeClasses(element: HTMLElement, classes: string[]): void {
    classes.forEach(c => element.classList.remove(c));
}

/**
 * Get transition duration from element's computed style
 */
function getTransitionDuration(element: HTMLElement): number {
    const style = window.getComputedStyle(element);
    const duration = style.transitionDuration || '0s';

    // Parse duration (can be "0.2s" or "200ms")
    const match = duration.match(/^([\d.]+)(s|ms)$/);
    if (match) {
        const value = parseFloat(match[1]);
        const unit = match[2];
        return unit === 's' ? value * 1000 : value;
    }

    return 200; // Default fallback
}

/**
 * Wait for next frame
 */
function nextFrame(): Promise<void> {
    return new Promise(resolve => {
        requestAnimationFrame(() => {
            requestAnimationFrame(() => resolve());
        });
    });
}

/**
 * Create a Transition instance
 */
export function createTransition(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): TransitionInstance {
    const config = parseConfig(element);
    let isVisible = true;
    let animationId = 0; // Used to cancel stale animations

    // Parse all class sets
    const enterClasses = parseClasses(config.enter);
    const enterFromClasses = parseClasses(config.enterFrom);
    const enterToClasses = parseClasses(config.enterTo);
    const leaveClasses = parseClasses(config.leave);
    const leaveFromClasses = parseClasses(config.leaveFrom);
    const leaveToClasses = parseClasses(config.leaveTo);

    /**
     * Clear all transition classes immediately
     */
    const clearAllClasses = (): void => {
        removeClasses(element, enterClasses);
        removeClasses(element, enterFromClasses);
        removeClasses(element, enterToClasses);
        removeClasses(element, leaveClasses);
        removeClasses(element, leaveFromClasses);
        removeClasses(element, leaveToClasses);
    };

    /**
     * Perform enter animation
     */
    const enter = async (): Promise<void> => {
        // Cancel any ongoing animation by incrementing the ID
        const currentAnimationId = ++animationId;

        // Clear any previous transition classes
        clearAllClasses();

        // Make visible
        element.style.display = '';

        // Add enter + enterFrom classes
        addClasses(element, enterClasses);
        addClasses(element, enterFromClasses);

        // Wait for next frame to ensure classes are applied
        await nextFrame();

        // Check if this animation was cancelled
        if (currentAnimationId !== animationId) return;

        // Remove enterFrom, add enterTo
        removeClasses(element, enterFromClasses);
        addClasses(element, enterToClasses);

        // Get duration and wait for transition
        const duration = getTransitionDuration(element);

        await new Promise(resolve => setTimeout(resolve, duration));

        // Check if this animation was cancelled
        if (currentAnimationId !== animationId) return;

        // Cleanup - remove transition classes
        removeClasses(element, enterClasses);
        removeClasses(element, enterToClasses);

        isVisible = true;
    };

    /**
     * Perform leave animation
     */
    const leave = async (): Promise<void> => {
        // Cancel any ongoing animation by incrementing the ID
        const currentAnimationId = ++animationId;

        // Clear any previous transition classes
        clearAllClasses();

        // Add leave + leaveFrom classes
        addClasses(element, leaveClasses);
        addClasses(element, leaveFromClasses);

        // Wait for next frame
        await nextFrame();

        // Check if this animation was cancelled
        if (currentAnimationId !== animationId) return;

        // Remove leaveFrom, add leaveTo
        removeClasses(element, leaveFromClasses);
        addClasses(element, leaveToClasses);

        // Get duration and wait for transition
        const duration = getTransitionDuration(element);

        await new Promise(resolve => setTimeout(resolve, duration));

        // Check if this animation was cancelled
        if (currentAnimationId !== animationId) return;

        // Cleanup - remove transition classes and hide
        removeClasses(element, leaveClasses);
        removeClasses(element, leaveToClasses);
        element.style.display = 'none';

        isVisible = false;
    };

    /**
     * Update visibility based on state
     */
    const update = (state: Record<string, unknown>): void => {
        const shouldShow = evaluateBooleanExpression(config.showExpression, state);

        if (shouldShow && !isVisible) {
            enter();
        } else if (!shouldShow && isVisible) {
            leave();
        }
    };

    /**
     * Force show with animation
     */
    const show = (): void => {
        if (!isVisible) {
            enter();
        }
    };

    /**
     * Force hide with animation
     */
    const hide = (): void => {
        if (isVisible) {
            leave();
        }
    };

    /**
     * Dispose
     */
    const dispose = (): void => {
        // Cancel any ongoing animation
        animationId++;
        // Cleanup all possible classes
        clearAllClasses();
    };

    // Initial state check - hide if show expression is false
    const initialState = stateAdapter.getState();
    const initialShow = evaluateBooleanExpression(config.showExpression, initialState);

    if (!initialShow) {
        element.style.display = 'none';
        isVisible = false;
    }

    // Subscribe to state changes
    const unsubscribe = stateAdapter.subscribe(() => {
        update(stateAdapter.getState());
    });

    return {
        id: config.id,
        config,
        element,
        get isVisible() {
            return isVisible;
        },
        update,
        show,
        hide,
        dispose: () => {
            unsubscribe();
            dispose();
        },
    };
}

/**
 * TransitionFactory namespace for module exports
 */
export const TransitionFactory = {
    parseConfig,
    create: createTransition,
};
