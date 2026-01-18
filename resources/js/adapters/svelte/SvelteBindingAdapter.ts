/**
 * SvelteBindingAdapter - DOM bindings for Svelte-style reactivity
 *
 * Uses store subscriptions for reactive updates.
 */

import type { IBindingAdapter, IStateAdapter, CleanupFn } from '../types';
import type { AcceladeActions } from '../../core/types';
import type { CustomMethods } from '../../core/factories/ScriptExecutor';
import { ScriptExecutor } from '../../core/factories/ScriptExecutor';
import {
    evaluateExpression,
    evaluateBooleanExpression,
    evaluateStringExpression,
    parseClassObject,
} from '../../core/expressions';

/**
 * Binding record
 */
interface BindingRecord {
    element: HTMLElement;
    type: string;
    expression: string;
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
 * Show binding with animation support
 */
interface ShowBinding {
    element: HTMLElement;
    expression: string;
    originalDisplay: string;
    animation?: AnimationConfig;
}

/**
 * SvelteBindingAdapter - Handles DOM bindings with store subscriptions
 */
export class SvelteBindingAdapter implements IBindingAdapter {
    private element: HTMLElement | null = null;
    private stateAdapter: IStateAdapter | null = null;
    private bindings: BindingRecord[] = [];
    private eventListeners: Array<{ element: HTMLElement; event: string; handler: EventListener }> = [];
    private ifPlaceholders: Map<HTMLElement, Comment> = new Map();
    private modelBound: WeakSet<Element> = new WeakSet();
    private unsubscribe: CleanupFn | null = null;
    private showBindings: ShowBinding[] = [];

    /**
     * Initialize the binding adapter
     */
    init(element: HTMLElement, stateAdapter: IStateAdapter): void {
        this.element = element;
        this.stateAdapter = stateAdapter;

        // Subscribe to state changes for reactive updates
        this.unsubscribe = stateAdapter.subscribe(() => {
            this.update();
        });
    }

    /**
     * Bind text content
     */
    bindText(element: HTMLElement, expression: string): void {
        this.bindings.push({
            element,
            type: 'text',
            expression,
            cleanup: () => {},
        });
        this.updateTextBinding(element, expression);
    }

    /**
     * Bind HTML content
     */
    bindHtml(element: HTMLElement, expression: string): void {
        this.bindings.push({
            element,
            type: 'html',
            expression,
            cleanup: () => {},
        });
        this.updateHtmlBinding(element, expression);
    }

    /**
     * Bind visibility with animation support
     */
    bindShow(element: HTMLElement, expression: string): void {
        // Get computed display style
        const originalDisplay = element.style.display || '';

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

        // Store binding for animation support
        this.showBindings.push({ element, expression, originalDisplay, animation });

        this.bindings.push({
            element,
            type: 'show',
            expression,
            cleanup: () => {},
        });

        // Initial update (no animation on first render)
        if (this.stateAdapter) {
            const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
            if (visible) {
                element.style.display = originalDisplay;
                if (animation) {
                    this.addClasses(element, animation.enterTo);
                } else {
                    element.classList.add('accelade-visible');
                }
            } else {
                element.style.display = 'none';
            }
        }
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
     * Bind conditional rendering
     */
    bindIf(element: HTMLElement, expression: string): void {
        const placeholder = document.createComment('svelte-if');
        this.ifPlaceholders.set(element, placeholder);

        this.bindings.push({
            element,
            type: 'if',
            expression,
            cleanup: () => {
                this.ifPlaceholders.delete(element);
            },
        });
        this.updateIfBinding(element, expression);
    }

    /**
     * Bind two-way input
     */
    bindModel(
        element: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement,
        property: string
    ): void {
        if (this.modelBound.has(element)) return;
        this.modelBound.add(element);

        const inputElement = element as HTMLInputElement;
        const isCheckbox = inputElement.type === 'checkbox';
        const isRadio = inputElement.type === 'radio';
        const isNumber = inputElement.type === 'number' || inputElement.type === 'range';
        const eventType = isCheckbox || isRadio ? 'change' : 'input';

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

        this.bindings.push({
            element,
            type: 'model',
            expression: property,
            cleanup: () => {},
        });

        // Initial update
        this.updateModelBinding(element, property);
    }

    /**
     * Bind attribute
     */
    bindAttribute(element: HTMLElement, attr: string, expression: string): void {
        this.bindings.push({
            element,
            type: `attr:${attr}`,
            expression,
            cleanup: () => {},
        });
        this.updateAttributeBinding(element, attr, expression);
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
        // Parse event modifiers (e.g., click.prevent.stop -> click with prevent and stop modifiers)
        const parts = event.split('.');
        const eventName = parts[0];
        const modifiers = new Set(parts.slice(1));

        const listener = (e: Event) => {
            if (!this.stateAdapter) return;

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

            ScriptExecutor.executeAction(
                handler,
                this.stateAdapter.getState(),
                actions,
                customMethods,
                e
            );
        };

        element.addEventListener(eventName, listener);
        this.eventListeners.push({ element, event: eventName, handler: listener });
    }

    /**
     * Bind class object
     */
    bindClass(element: HTMLElement, expression: string): void {
        this.bindings.push({
            element,
            type: 'class',
            expression,
            cleanup: () => {},
        });
        this.updateClassBinding(element, expression);
    }

    /**
     * Bind style object
     */
    bindStyle(element: HTMLElement, expression: string): void {
        this.bindings.push({
            element,
            type: 'style',
            expression,
            cleanup: () => {},
        });
        this.updateStyleBinding(element, expression);
    }

    /**
     * Update all bindings
     */
    update(): void {
        for (const binding of this.bindings) {
            switch (binding.type) {
                case 'text':
                    this.updateTextBinding(binding.element, binding.expression);
                    break;
                case 'html':
                    this.updateHtmlBinding(binding.element, binding.expression);
                    break;
                case 'show':
                    this.updateShowBinding(binding.element, binding.expression);
                    break;
                case 'if':
                    this.updateIfBinding(binding.element, binding.expression);
                    break;
                case 'model':
                    this.updateModelBinding(
                        binding.element as HTMLInputElement,
                        binding.expression
                    );
                    break;
                case 'class':
                    this.updateClassBinding(binding.element, binding.expression);
                    break;
                case 'style':
                    this.updateStyleBinding(binding.element, binding.expression);
                    break;
                default:
                    if (binding.type.startsWith('attr:')) {
                        const attr = binding.type.slice(5);
                        this.updateAttributeBinding(binding.element, attr, binding.expression);
                    }
            }
        }
    }

    /**
     * Update text binding
     */
    private updateTextBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;
        const value = evaluateStringExpression(expression, this.stateAdapter.getState());
        element.textContent = value;
    }

    /**
     * Update HTML binding
     */
    private updateHtmlBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;
        const value = evaluateStringExpression(expression, this.stateAdapter.getState());
        element.innerHTML = value;
    }

    /**
     * Update show binding with animation support
     */
    private updateShowBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;

        // Find the show binding for this element
        const showBinding = this.showBindings.find(b => b.element === element);
        if (!showBinding) {
            // Fallback to simple show/hide
            const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
            element.style.display = visible ? '' : 'none';
            return;
        }

        const { originalDisplay, animation } = showBinding;
        const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
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
            }
        } else {
            // Default simple animation
            if (visible) {
                if (isCurrentlyHidden) {
                    element.style.display = originalDisplay;
                    void element.offsetHeight;
                }
                element.classList.remove('accelade-hiding');
                element.classList.add('accelade-visible');
            } else {
                if (!isCurrentlyHidden) {
                    element.classList.add('accelade-hiding');
                    element.classList.remove('accelade-visible');
                    setTimeout(() => {
                        if (element.classList.contains('accelade-hiding')) {
                            element.style.display = 'none';
                        }
                    }, 100);
                }
            }
        }
    }

    /**
     * Update if binding
     */
    private updateIfBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;

        const placeholder = this.ifPlaceholders.get(element);
        if (!placeholder) return;

        const result = evaluateBooleanExpression(expression, this.stateAdapter.getState());
        const isInserted = element.parentNode !== null;

        if (result && !isInserted) {
            placeholder.replaceWith(element);
        } else if (!result && isInserted) {
            element.replaceWith(placeholder);
        }
    }

    /**
     * Update model binding
     */
    private updateModelBinding(
        element: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement,
        property: string
    ): void {
        if (!this.stateAdapter) return;

        const value = this.stateAdapter.get(property);
        const inputElement = element as HTMLInputElement;

        if (inputElement.type === 'checkbox') {
            inputElement.checked = Boolean(value);
        } else if (inputElement.type === 'radio') {
            inputElement.checked = inputElement.value === value;
        } else if (element.value !== String(value ?? '')) {
            element.value = value !== undefined ? String(value) : '';
        }
    }

    /**
     * Update attribute binding
     */
    private updateAttributeBinding(
        element: HTMLElement,
        attr: string,
        expression: string
    ): void {
        if (!this.stateAdapter) return;

        const value = evaluateExpression(expression, this.stateAdapter.getState());

        if (attr === 'class' && typeof value === 'object' && value !== null) {
            Object.entries(value as Record<string, boolean>).forEach(([className, condition]) => {
                element.classList.toggle(className, Boolean(condition));
            });
        } else if (value === false || value === null || value === undefined) {
            element.removeAttribute(attr);
        } else if (value === true) {
            element.setAttribute(attr, '');
        } else {
            element.setAttribute(attr, String(value));
        }
    }

    /**
     * Update class binding
     */
    private updateClassBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;

        const classObj = parseClassObject(expression, this.stateAdapter.getState());
        Object.entries(classObj).forEach(([className, condition]) => {
            element.classList.toggle(className, Boolean(condition));
        });
    }

    /**
     * Update style binding
     */
    private updateStyleBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;

        const value = evaluateExpression(expression, this.stateAdapter.getState());

        if (typeof value === 'object' && value !== null) {
            Object.entries(value as Record<string, string>).forEach(([prop, val]) => {
                element.style.setProperty(prop, val);
            });
        }
    }

    /**
     * Dispose all bindings
     */
    dispose(): void {
        // Unsubscribe from state changes
        if (this.unsubscribe) {
            this.unsubscribe();
            this.unsubscribe = null;
        }

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

        // Clear show bindings
        this.showBindings = [];

        // Clear placeholders
        this.ifPlaceholders.clear();

        // Clear references
        this.element = null;
        this.stateAdapter = null;
    }
}

export default SvelteBindingAdapter;
