/**
 * VanillaBindingAdapter - DOM bindings for vanilla JavaScript
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
import { TextInterpolator } from '../../core/interpolation';

/**
 * Binding cleanup record
 */
interface BindingRecord {
    element: HTMLElement;
    type: string;
    cleanup: CleanupFn;
}

/**
 * Element binding with expression for reactive updates
 * This allows tracking elements regardless of DOM position (for teleport support)
 */
interface ElementBinding {
    element: HTMLElement;
    expression: string;
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
 * Show binding with optional animation
 */
interface ShowBinding extends ElementBinding {
    originalDisplay: string;
    animation?: AnimationConfig;
}

/**
 * VanillaBindingAdapter - Handles DOM bindings with manual updates
 */
export class VanillaBindingAdapter implements IBindingAdapter {
    private element: HTMLElement | null = null;
    private stateAdapter: IStateAdapter | null = null;
    private bindings: BindingRecord[] = [];
    private eventListeners: Array<{ element: HTMLElement; event: string; handler: EventListener }> = [];
    private ifPlaceholders: Map<HTMLElement, Comment> = new Map();
    private modelBound: WeakSet<Element> = new WeakSet();
    private textInterpolator: TextInterpolator | null = null;

    // Element bindings storage for teleport support (elements tracked regardless of DOM position)
    private textBindings: ElementBinding[] = [];
    private showBindings: ShowBinding[] = [];
    private classBindings: ElementBinding[] = [];
    private styleBindings: ElementBinding[] = [];
    private attrBindings: Array<{ element: HTMLElement; attr: string; expression: string }> = [];
    private modelBindings: Array<{ element: HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement; property: string }> = [];

    /**
     * Initialize the binding adapter
     */
    init(element: HTMLElement, stateAdapter: IStateAdapter): void {
        this.element = element;
        this.stateAdapter = stateAdapter;

        // Initialize text interpolation for {{ }} syntax
        this.textInterpolator = new TextInterpolator(element, stateAdapter.getState());
        this.textInterpolator.init();

        // Subscribe to state changes for reactive updates
        const unsubscribe = stateAdapter.subscribe(() => {
            this.update();
        });

        this.bindings.push({
            element,
            type: 'subscription',
            cleanup: unsubscribe,
        });
    }

    /**
     * Bind text content
     */
    bindText(element: HTMLElement, expression: string): void {
        // Store binding for teleport support
        this.textBindings.push({ element, expression });

        const update = () => {
            if (!this.stateAdapter) return;
            const value = evaluateStringExpression(expression, this.stateAdapter.getState());
            element.textContent = value;
        };

        update();
        this.bindings.push({ element, type: 'text', cleanup: () => {} });
    }

    /**
     * Bind HTML content
     */
    bindHtml(element: HTMLElement, expression: string): void {
        const update = () => {
            if (!this.stateAdapter) return;
            const value = evaluateStringExpression(expression, this.stateAdapter.getState());
            element.innerHTML = value;
        };

        update();
        this.bindings.push({ element, type: 'html', cleanup: () => {} });
    }

    /**
     * Bind visibility with smooth transitions
     */
    bindShow(element: HTMLElement, expression: string): void {
        // Get computed display style if no inline style is set
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

        // Store binding for teleport support
        this.showBindings.push({ element, expression, originalDisplay, animation });

        // Initial update (no animation on first render)
        if (this.stateAdapter) {
            const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
            if (visible) {
                element.style.display = originalDisplay;
                if (animation) {
                    // Apply enterTo classes immediately for initial visible state
                    this.addClasses(element, animation.enterTo);
                } else {
                    element.classList.add('accelade-visible');
                }
            } else {
                // Hide immediately on initial render (no animation)
                element.style.display = 'none';
            }
        }

        this.bindings.push({ element, type: 'show', cleanup: () => {} });
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
        const placeholder = document.createComment('a-if');
        this.ifPlaceholders.set(element, placeholder);
        let isInserted = true;

        const update = () => {
            if (!this.stateAdapter) return;
            const result = evaluateBooleanExpression(expression, this.stateAdapter.getState());

            if (result && !isInserted) {
                placeholder.replaceWith(element);
                isInserted = true;
            } else if (!result && isInserted && element.parentNode) {
                element.replaceWith(placeholder);
                isInserted = false;
            }
        };

        update();
        this.bindings.push({
            element,
            type: 'if',
            cleanup: () => {
                this.ifPlaceholders.delete(element);
            },
        });
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

        // Store binding for teleport support
        this.modelBindings.push({ element, property });

        const inputElement = element as HTMLInputElement;
        const isCheckbox = inputElement.type === 'checkbox';
        const isRadio = inputElement.type === 'radio';
        const isNumber = inputElement.type === 'number' || inputElement.type === 'range';
        const eventType = isCheckbox || isRadio ? 'change' : 'input';

        // Update DOM from state
        const updateDOM = () => {
            if (!this.stateAdapter) return;
            const value = this.stateAdapter.get(property);

            if (isCheckbox) {
                inputElement.checked = Boolean(value);
            } else if (isRadio) {
                inputElement.checked = inputElement.value === value;
            } else if (element.value !== String(value ?? '')) {
                element.value = value !== undefined ? String(value) : '';
            }
        };

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

        // Initial update
        updateDOM();

        // Bind event
        element.addEventListener(eventType, updateState);
        this.eventListeners.push({ element, event: eventType, handler: updateState });

        this.bindings.push({ element, type: 'model', cleanup: () => {} });
    }

    /**
     * Bind attribute
     */
    bindAttribute(element: HTMLElement, attr: string, expression: string): void {
        // Store binding for teleport support
        this.attrBindings.push({ element, attr, expression });

        const update = () => {
            if (!this.stateAdapter) return;
            const value = evaluateExpression(expression, this.stateAdapter.getState());

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
        };

        update();
        this.bindings.push({ element, type: `attr:${attr}`, cleanup: () => {} });
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
     * Bind class object
     */
    bindClass(element: HTMLElement, expression: string): void {
        // Store binding for teleport support
        this.classBindings.push({ element, expression });

        const update = () => {
            if (!this.stateAdapter) return;
            const classObj = parseClassObject(expression, this.stateAdapter.getState());
            Object.entries(classObj).forEach(([className, condition]) => {
                element.classList.toggle(className, Boolean(condition));
            });
        };

        update();
        this.bindings.push({ element, type: 'class', cleanup: () => {} });
    }

    /**
     * Bind style object
     */
    bindStyle(element: HTMLElement, expression: string): void {
        // Store binding for teleport support
        this.styleBindings.push({ element, expression });

        const update = () => {
            if (!this.stateAdapter) return;
            const value = evaluateExpression(expression, this.stateAdapter.getState());

            if (typeof value === 'object' && value !== null) {
                Object.entries(value as Record<string, string>).forEach(([prop, val]) => {
                    element.style.setProperty(prop, val);
                });
            }
        };

        update();
        this.bindings.push({ element, type: 'style', cleanup: () => {} });
    }

    /**
     * Update all bindings
     * Uses stored element references to support teleported content (elements moved outside container)
     */
    update(): void {
        if (!this.stateAdapter) return;

        const state = this.stateAdapter.getState();

        // Update text interpolation
        if (this.textInterpolator) {
            this.textInterpolator.setState(state);
        }

        // Update text bindings using stored references (supports teleported elements)
        for (const { element, expression } of this.textBindings) {
            element.textContent = evaluateStringExpression(expression, state);
        }

        // Update show bindings using stored references with smooth transitions
        for (const { element, expression, originalDisplay, animation } of this.showBindings) {
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
                    // Hide: animate out, then set display:none after transition
                    if (!isCurrentlyHidden) {
                        element.classList.add('accelade-hiding');
                        element.classList.remove('accelade-visible');
                        // Set display:none after transition completes
                        setTimeout(() => {
                            // Only hide if still supposed to be hidden
                            if (element.classList.contains('accelade-hiding')) {
                                element.style.display = 'none';
                            }
                        }, 200); // Match CSS transition duration
                    }
                }
            }
        }

        // Update model bindings using stored references
        for (const { element, property } of this.modelBindings) {
            const value = state[property];
            const inputElement = element as HTMLInputElement;

            if (inputElement.type === 'checkbox') {
                inputElement.checked = Boolean(value);
            } else if (inputElement.type === 'radio') {
                inputElement.checked = inputElement.value === value;
            } else if (element.value !== String(value ?? '')) {
                element.value = value !== undefined ? String(value) : '';
            }
        }

        // Update class bindings using stored references
        for (const { element, expression } of this.classBindings) {
            const classObj = parseClassObject(expression, state);
            Object.entries(classObj).forEach(([className, condition]) => {
                element.classList.toggle(className, Boolean(condition));
            });
        }

        // Update style bindings using stored references
        for (const { element, expression } of this.styleBindings) {
            const value = evaluateExpression(expression, state);
            if (typeof value === 'object' && value !== null) {
                Object.entries(value as Record<string, string>).forEach(([prop, val]) => {
                    element.style.setProperty(prop, val);
                });
            }
        }

        // Update attribute bindings using stored references
        for (const { element, attr, expression } of this.attrBindings) {
            const value = evaluateExpression(expression, state);

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
    }

    /**
     * Dispose all bindings
     */
    dispose(): void {
        // Destroy text interpolator
        if (this.textInterpolator) {
            this.textInterpolator.destroy();
            this.textInterpolator = null;
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

        // Clear element binding arrays (teleport support)
        this.textBindings = [];
        this.showBindings = [];
        this.classBindings = [];
        this.styleBindings = [];
        this.attrBindings = [];
        this.modelBindings = [];

        // Clear placeholders
        this.ifPlaceholders.clear();

        // Clear references
        this.element = null;
        this.stateAdapter = null;
    }
}

export default VanillaBindingAdapter;
