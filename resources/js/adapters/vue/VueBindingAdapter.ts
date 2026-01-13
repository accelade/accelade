/**
 * VueBindingAdapter - Vue reactive DOM bindings using effect()
 */

import { effect, type ReactiveEffectRunner } from 'vue';
import type { IBindingAdapter, IStateAdapter, CleanupFn } from '../types';
import type { AcceladeActions } from '../../core/types';
import type { CustomMethods } from '../../core/factories/ScriptExecutor';
import { ScriptExecutor } from '../../core/factories/ScriptExecutor';
import {
    evaluateExpression,
    evaluateBooleanExpression,
    evaluateStringExpression,
    parseClassObject,
    getNestedValue,
} from '../../core/expressions';

/**
 * Binding cleanup record
 */
interface BindingRecord {
    element: HTMLElement;
    type: string;
    cleanup: CleanupFn;
}

/**
 * Animation configuration for show bindings
 */
interface AnimationConfig {
    enter: string;
    enterFrom: string;
    enterTo: string;
    leave: string;
    leaveFrom: string;
    leaveTo: string;
}

/**
 * VueBindingAdapter - Uses Vue's effect() for reactive bindings
 */
export class VueBindingAdapter implements IBindingAdapter {
    private element: HTMLElement | null = null;
    private stateAdapter: IStateAdapter | null = null;
    private bindings: BindingRecord[] = [];
    private effects: ReactiveEffectRunner[] = [];
    private eventListeners: Array<{ element: HTMLElement; event: string; handler: EventListener }> = [];
    private ifPlaceholders: Map<HTMLElement, Comment> = new Map();
    private modelBound: WeakSet<Element> = new WeakSet();

    /**
     * Initialize the binding adapter
     */
    init(element: HTMLElement, stateAdapter: IStateAdapter): void {
        this.element = element;
        this.stateAdapter = stateAdapter;
    }

    /**
     * Get reactive state from Vue adapter
     */
    private getReactiveState(): Record<string, unknown> {
        return this.stateAdapter?.getReactiveState() as Record<string, unknown> ?? {};
    }

    /**
     * Bind text content with Vue effect
     */
    bindText(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();

        const runner = effect(() => {
            // Use evaluateStringExpression to support expressions like "name || 'default'"
            const value = evaluateStringExpression(expression, state);
            element.textContent = value;
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'text',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Bind HTML content with Vue effect
     */
    bindHtml(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();

        const runner = effect(() => {
            // Use evaluateStringExpression to support expressions
            const value = evaluateStringExpression(expression, state);
            element.innerHTML = value;
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'html',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Bind visibility with Vue effect and smooth transitions
     */
    bindShow(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();
        const originalDisplay = element.style.display || '';
        let isFirstRun = true;

        // Check for animation config from parent toggle
        let animation: AnimationConfig | undefined;
        const toggleParent = element.closest('[data-toggle-animation]');
        if (toggleParent) {
            try {
                const animData = toggleParent.getAttribute('data-toggle-animation');
                if (animData) {
                    animation = JSON.parse(animData);
                }
            } catch {
                // Invalid JSON, skip animation
            }
        }

        const runner = effect(() => {
            const visible = evaluateBooleanExpression(expression, state);
            const isCurrentlyHidden = element.style.display === 'none';

            if (animation) {
                // Use custom animation classes
                if (visible && isCurrentlyHidden) {
                    // Enter animation
                    element.style.display = originalDisplay;
                    this.addClasses(element, animation.enter);
                    this.addClasses(element, animation.enterFrom);
                    // Force reflow
                    void element.offsetHeight;
                    // Transition to enterTo
                    this.removeClasses(element, animation.enterFrom);
                    this.addClasses(element, animation.enterTo);
                    // Cleanup after animation
                    const duration = this.getAnimationDuration(animation.enter);
                    setTimeout(() => {
                        this.removeClasses(element, animation.enter);
                    }, duration);
                } else if (!visible && !isCurrentlyHidden) {
                    // Leave animation
                    this.removeClasses(element, animation.enterTo);
                    this.addClasses(element, animation.leave);
                    this.addClasses(element, animation.leaveFrom);
                    // Force reflow
                    void element.offsetHeight;
                    // Transition to leaveTo
                    this.removeClasses(element, animation.leaveFrom);
                    this.addClasses(element, animation.leaveTo);
                    // Hide after animation
                    const duration = this.getAnimationDuration(animation.leave);
                    setTimeout(() => {
                        if (element.classList.contains(animation.leaveTo.split(/\s+/)[0])) {
                            element.style.display = 'none';
                            this.removeClasses(element, animation.leave);
                            this.removeClasses(element, animation.leaveTo);
                        }
                    }, duration);
                } else if (isFirstRun && !visible) {
                    // Initial hide without animation
                    element.style.display = 'none';
                } else if (isFirstRun && visible) {
                    // Initial show without animation
                    this.addClasses(element, animation.enterTo);
                }
            } else {
                // Default simple animation
                if (visible) {
                    // Show: first set display, then animate in
                    if (isCurrentlyHidden) {
                        element.style.display = originalDisplay;
                        // Force reflow to ensure transition works
                        void element.offsetHeight;
                    }
                    element.classList.remove('accelade-hiding');
                    element.classList.add('accelade-visible');
                } else {
                    if (isFirstRun) {
                        // Skip transition on initial hide - just hide immediately
                        element.style.display = 'none';
                    } else if (!isCurrentlyHidden) {
                        // Animate out, then set display:none after transition
                        element.classList.add('accelade-hiding');
                        element.classList.remove('accelade-visible');
                        setTimeout(() => {
                            // Only hide if still supposed to be hidden
                            if (element.classList.contains('accelade-hiding')) {
                                element.style.display = 'none';
                            }
                        }, 200); // Match CSS transition duration
                    }
                }
            }
            isFirstRun = false;
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'show',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Helper to add space-separated classes
     */
    private addClasses(element: HTMLElement, classes: string): void {
        classes.split(/\s+/).filter(c => c).forEach(c => element.classList.add(c));
    }

    /**
     * Helper to remove space-separated classes
     */
    private removeClasses(element: HTMLElement, classes: string): void {
        classes.split(/\s+/).filter(c => c).forEach(c => element.classList.remove(c));
    }

    /**
     * Extract duration from Tailwind duration class
     */
    private getAnimationDuration(classes: string): number {
        const match = classes.match(/duration-(\d+)/);
        if (match) {
            return parseInt(match[1], 10);
        }
        return 200; // Default fallback
    }

    /**
     * Bind conditional rendering with Vue effect
     */
    bindIf(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();
        const placeholder = document.createComment('v-if');
        this.ifPlaceholders.set(element, placeholder);
        let isInserted = true;

        const runner = effect(() => {
            const result = evaluateBooleanExpression(expression, state);

            if (result && !isInserted) {
                placeholder.replaceWith(element);
                isInserted = true;
            } else if (!result && isInserted && element.parentNode) {
                element.replaceWith(placeholder);
                isInserted = false;
            }
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'if',
            cleanup: () => {
                runner.effect.stop();
                this.ifPlaceholders.delete(element);
            },
        });
    }

    /**
     * Bind two-way input with Vue effect
     */
    bindModel(
        element: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement,
        property: string
    ): void {
        if (this.modelBound.has(element)) return;
        this.modelBound.add(element);

        const state = this.getReactiveState();
        const inputElement = element as HTMLInputElement;
        const isCheckbox = inputElement.type === 'checkbox';
        const isRadio = inputElement.type === 'radio';
        const isNumber = inputElement.type === 'number' || inputElement.type === 'range';
        const eventType = isCheckbox || isRadio ? 'change' : 'input';

        // Update DOM from state using Vue effect
        const runner = effect(() => {
            // Use getNestedValue to support nested paths like "props.count"
            const value = getNestedValue(state as Record<string, unknown>, property);

            if (isCheckbox) {
                inputElement.checked = Boolean(value);
            } else if (isRadio) {
                inputElement.checked = inputElement.value === value;
            } else if (element.value !== String(value ?? '')) {
                element.value = value !== undefined ? String(value) : '';
            }
        });

        // Update state from DOM
        const updateState = () => {
            if (!this.stateAdapter) return;

            if (isCheckbox) {
                this.stateAdapter.set(property, inputElement.checked);
            } else if (isRadio) {
                this.stateAdapter.set(property, inputElement.value);
            } else if (isNumber) {
                this.stateAdapter.set(property, parseFloat(element.value) || 0);
            } else {
                this.stateAdapter.set(property, element.value);
            }
        };

        element.addEventListener(eventType, updateState);
        this.eventListeners.push({ element, event: eventType, handler: updateState });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'model',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Bind attribute with Vue effect
     */
    bindAttribute(element: HTMLElement, attr: string, expression: string): void {
        const state = this.getReactiveState();

        const runner = effect(() => {
            const value = evaluateExpression(expression, state);

            if (attr === 'class') {
                if (typeof value === 'object' && value !== null) {
                    Object.entries(value as Record<string, boolean>).forEach(([className, condition]) => {
                        element.classList.toggle(className, Boolean(condition));
                    });
                } else {
                    element.setAttribute('class', String(value));
                }
            } else if (value === false || value === null || value === undefined) {
                element.removeAttribute(attr);
            } else if (value === true) {
                element.setAttribute(attr, '');
            } else {
                element.setAttribute(attr, String(value));
            }
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: `attr:${attr}`,
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Bind event handler
     */
    bindEvent(
        element: HTMLElement,
        event: string,
        handler: string,
        actions: AcceladeActions,
        customMethods: CustomMethods
    ): void {
        const state = this.getReactiveState();

        // Parse event modifiers (e.g., click.prevent.stop -> click with prevent and stop modifiers)
        const parts = event.split('.');
        const eventName = parts[0];
        const modifiers = new Set(parts.slice(1));

        const listener = (e: Event) => {
            // Apply modifiers
            if (modifiers.has('prevent')) {
                e.preventDefault();
            }
            if (modifiers.has('stop')) {
                e.stopPropagation();
            }

            // Handle keyboard modifiers for keydown/keyup events
            if (e instanceof KeyboardEvent) {
                if (modifiers.has('enter') && e.key !== 'Enter') return;
                if (modifiers.has('escape') && e.key !== 'Escape') return;
                if (modifiers.has('tab') && e.key !== 'Tab') return;
                if (modifiers.has('space') && e.key !== ' ') return;
                if (modifiers.has('up') && e.key !== 'ArrowUp') return;
                if (modifiers.has('down') && e.key !== 'ArrowDown') return;
                if (modifiers.has('left') && e.key !== 'ArrowLeft') return;
                if (modifiers.has('right') && e.key !== 'ArrowRight') return;
            }

            // Handle mouse button modifiers
            if (e instanceof MouseEvent) {
                if (modifiers.has('left') && e.button !== 0) return;
                if (modifiers.has('middle') && e.button !== 1) return;
                if (modifiers.has('right') && e.button !== 2) return;
            }

            // Handle modifier key requirements
            if (e instanceof KeyboardEvent || e instanceof MouseEvent) {
                if (modifiers.has('ctrl') && !e.ctrlKey) return;
                if (modifiers.has('alt') && !e.altKey) return;
                if (modifiers.has('shift') && !e.shiftKey) return;
                if (modifiers.has('meta') && !e.metaKey) return;
            }

            ScriptExecutor.executeAction(handler, state, actions, customMethods, e);
        };

        // Handle 'once' modifier
        const options: AddEventListenerOptions = {};
        if (modifiers.has('once')) {
            options.once = true;
        }
        if (modifiers.has('passive')) {
            options.passive = true;
        }
        if (modifiers.has('capture')) {
            options.capture = true;
        }

        element.addEventListener(eventName, listener, options);
        this.eventListeners.push({ element, event: eventName, handler: listener });
    }

    /**
     * Bind class object with Vue effect
     */
    bindClass(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();

        const runner = effect(() => {
            const classObj = parseClassObject(expression, state);
            Object.entries(classObj).forEach(([className, condition]) => {
                element.classList.toggle(className, Boolean(condition));
            });
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'class',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Bind style object with Vue effect
     */
    bindStyle(element: HTMLElement, expression: string): void {
        const state = this.getReactiveState();

        const runner = effect(() => {
            const value = evaluateExpression(expression, state);

            if (typeof value === 'object' && value !== null) {
                Object.entries(value as Record<string, string>).forEach(([prop, val]) => {
                    element.style.setProperty(prop, val);
                });
            }
        });

        this.effects.push(runner);
        this.bindings.push({
            element,
            type: 'style',
            cleanup: () => runner.effect.stop(),
        });
    }

    /**
     * Update all bindings (no-op for Vue - effects handle updates automatically)
     */
    update(): void {
        // Vue's effect() automatically tracks dependencies and re-runs when state changes
        // No manual update needed
    }

    /**
     * Dispose all bindings and effects
     */
    dispose(): void {
        // Stop all Vue effects
        for (const runner of this.effects) {
            runner.effect.stop();
        }
        this.effects = [];

        // Remove event listeners
        for (const { element, event, handler } of this.eventListeners) {
            element.removeEventListener(event, handler);
        }
        this.eventListeners = [];

        // Run cleanup functions
        for (const binding of this.bindings) {
            binding.cleanup();
        }
        this.bindings = [];

        // Clear placeholders
        this.ifPlaceholders.clear();

        // Clear references
        this.element = null;
        this.stateAdapter = null;
    }
}

export default VueBindingAdapter;
