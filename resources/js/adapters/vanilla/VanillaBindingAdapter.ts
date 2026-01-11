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
     * Bind visibility
     */
    bindShow(element: HTMLElement, expression: string): void {
        const originalDisplay = element.style.display;

        const update = () => {
            if (!this.stateAdapter) return;
            const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
            element.style.display = visible ? originalDisplay : 'none';
        };

        update();
        this.bindings.push({ element, type: 'show', cleanup: () => {} });
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
        const listener = (e: Event) => {
            if (!this.stateAdapter) return;
            ScriptExecutor.executeAction(
                handler,
                this.stateAdapter.getState(),
                actions,
                customMethods,
                e
            );
        };

        element.addEventListener(event, listener);
        this.eventListeners.push({ element, event, handler: listener });
    }

    /**
     * Bind class object
     */
    bindClass(element: HTMLElement, expression: string): void {
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
     */
    update(): void {
        if (!this.stateAdapter || !this.element) return;

        const state = this.stateAdapter.getState();

        // Update text interpolation
        if (this.textInterpolator) {
            this.textInterpolator.setState(state);
        }
        const attrs = {
            text: 'a-text',
            html: 'a-html',
            show: 'a-show',
            model: 'a-model',
            class: 'a-class',
            style: 'a-style',
        };

        // Update text bindings
        this.element.querySelectorAll<HTMLElement>(`[${attrs.text}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.text);
            if (expr) {
                el.textContent = evaluateStringExpression(expr, state);
            }
        });

        // Update show bindings
        this.element.querySelectorAll<HTMLElement>(`[${attrs.show}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.show);
            if (expr) {
                const visible = evaluateBooleanExpression(expr, state);
                el.style.display = visible ? '' : 'none';
            }
        });

        // Update model bindings
        this.element.querySelectorAll<HTMLInputElement>(`[${attrs.model}]`).forEach((el) => {
            const prop = el.getAttribute(attrs.model);
            if (!prop) return;

            const value = state[prop];
            if (el.type === 'checkbox') {
                el.checked = Boolean(value);
            } else if (el.type === 'radio') {
                el.checked = el.value === value;
            } else if (el.value !== String(value ?? '')) {
                el.value = value !== undefined ? String(value) : '';
            }
        });

        // Update class bindings
        this.element.querySelectorAll<HTMLElement>(`[${attrs.class}]`).forEach((el) => {
            const expr = el.getAttribute(attrs.class);
            if (expr) {
                const classObj = parseClassObject(expr, state);
                Object.entries(classObj).forEach(([className, condition]) => {
                    el.classList.toggle(className, Boolean(condition));
                });
            }
        });

        // Update attribute bindings
        this.element.querySelectorAll<HTMLElement>('*').forEach((el) => {
            Array.from(el.attributes).forEach((attr) => {
                if (attr.name.startsWith('a-bind:') || (attr.name.startsWith(':') && !attr.name.startsWith('::'))) {
                    const attrName = attr.name.startsWith(':')
                        ? attr.name.slice(1)
                        : attr.name.slice(7);
                    const value = evaluateExpression(attr.value, state);

                    if (attrName === 'class' && typeof value === 'object' && value !== null) {
                        Object.entries(value as Record<string, boolean>).forEach(([className, condition]) => {
                            el.classList.toggle(className, Boolean(condition));
                        });
                    } else if (value === false || value === null || value === undefined) {
                        el.removeAttribute(attrName);
                    } else if (value === true) {
                        el.setAttribute(attrName, '');
                    } else {
                        el.setAttribute(attrName, String(value));
                    }
                }
            });
        });
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

        // Clear placeholders
        this.ifPlaceholders.clear();

        // Clear references
        this.element = null;
        this.stateAdapter = null;
    }
}

export default VanillaBindingAdapter;
