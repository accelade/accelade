/**
 * ReactBindingAdapter - React-style DOM bindings
 *
 * Note: React's paradigm is fundamentally different from Vue/Vanilla.
 * Instead of directly manipulating DOM, React re-renders components.
 * This adapter provides binding metadata that React components can use.
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
 * Binding record for tracking
 */
interface BindingRecord {
    element: HTMLElement;
    type: string;
    expression: string;
    cleanup: CleanupFn;
}

/**
 * Event binding record
 */
interface EventBinding {
    element: HTMLElement;
    event: string;
    handler: string;
    listener: EventListener;
}

/**
 * ReactBindingAdapter - Handles DOM bindings for React integration
 *
 * This adapter is used when rendering server-side templates with React.
 * For pure React components, use the useAccelade hook instead.
 */
export class ReactBindingAdapter implements IBindingAdapter {
    private element: HTMLElement | null = null;
    private stateAdapter: IStateAdapter | null = null;
    private bindings: BindingRecord[] = [];
    private eventBindings: EventBinding[] = [];
    private ifPlaceholders: Map<HTMLElement, Comment> = new Map();
    private modelBound: WeakSet<Element> = new WeakSet();
    private unsubscribe: CleanupFn | null = null;

    /**
     * Initialize the binding adapter
     */
    init(element: HTMLElement, stateAdapter: IStateAdapter): void {
        this.element = element;
        this.stateAdapter = stateAdapter;

        // Subscribe to state changes for updates
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
     * Bind visibility
     */
    bindShow(element: HTMLElement, expression: string): void {
        this.bindings.push({
            element,
            type: 'show',
            expression,
            cleanup: () => {},
        });
        this.updateShowBinding(element, expression);
    }

    /**
     * Bind conditional rendering
     */
    bindIf(element: HTMLElement, expression: string): void {
        const placeholder = document.createComment('state-if');
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

        this.bindings.push({
            element,
            type: 'model',
            expression: property,
            cleanup: () => {
                element.removeEventListener(eventType, updateState);
            },
        });

        // Initial DOM update
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
        const state = this.stateAdapter?.getState() ?? {};

        const listener = (e: Event) => {
            const currentState = this.stateAdapter?.getState() ?? {};
            ScriptExecutor.executeAction(handler, currentState, actions, customMethods, e);
        };

        element.addEventListener(event, listener);
        this.eventBindings.push({ element, event, handler, listener });
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
     * Update show binding
     */
    private updateShowBinding(element: HTMLElement, expression: string): void {
        if (!this.stateAdapter) return;
        const visible = evaluateBooleanExpression(expression, this.stateAdapter.getState());
        element.style.display = visible ? '' : 'none';
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
        for (const { element, event, listener } of this.eventBindings) {
            element.removeEventListener(event, listener);
        }
        this.eventBindings = [];

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

export default ReactBindingAdapter;
