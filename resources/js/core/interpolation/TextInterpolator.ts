/**
 * TextInterpolator - Handles {{ }} text interpolation in the DOM
 *
 * Automatically finds and processes text nodes containing {{ expression }}
 * syntax, evaluating expressions against component state and shared data.
 */

import { getNestedValue } from '../expressions';
import { SharedDataManager } from '../shared';

/**
 * Interpolation binding info
 */
interface InterpolationBinding {
    node: Text;
    template: string;
    expressions: string[];
}

/**
 * Regex for matching {{ expression }} patterns
 * Note: We use two versions - one for testing (no 'g' flag) and one for extracting (with 'g' flag)
 */
const INTERPOLATION_REGEX = /\{\{\s*([^}]+?)\s*\}\}/g;
const INTERPOLATION_TEST_REGEX = /\{\{\s*[^}]+?\s*\}\}/;

/**
 * TextInterpolator class
 */
export class TextInterpolator {
    private bindings: InterpolationBinding[] = [];

    private state: Record<string, unknown> = {};

    private element: HTMLElement;

    private sharedUnsubscribe: (() => void) | null = null;

    constructor(element: HTMLElement, initialState: Record<string, unknown> = {}) {
        this.element = element;
        this.state = initialState;
    }

    /**
     * Initialize interpolation - find and process all text nodes
     */
    init(): void {
        this.processElement(this.element);
        this.subscribeToSharedChanges();
        this.update();
    }

    /**
     * Process an element and its children for interpolation
     */
    private processElement(element: Element): void {
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: (node: Text) => {
                    // Skip script and style elements
                    const parent = node.parentElement;
                    if (parent && (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE')) {
                        return NodeFilter.FILTER_REJECT;
                    }

                    // Check if text contains interpolation (use non-global regex for testing)
                    if (node.textContent && INTERPOLATION_TEST_REGEX.test(node.textContent)) {
                        return NodeFilter.FILTER_ACCEPT;
                    }

                    return NodeFilter.FILTER_SKIP;
                },
            }
        );

        const textNodes: Text[] = [];
        let node: Text | null;
        while ((node = walker.nextNode() as Text | null)) {
            textNodes.push(node);
        }

        for (const textNode of textNodes) {
            this.processTextNode(textNode);
        }
    }

    /**
     * Process a single text node
     */
    private processTextNode(node: Text): void {
        const template = node.textContent || '';

        // Extract all expressions from the template
        const expressions: string[] = [];
        let match;
        const regex = new RegExp(INTERPOLATION_REGEX.source, 'g');
        while ((match = regex.exec(template)) !== null) {
            expressions.push(match[1].trim());
        }

        if (expressions.length > 0) {
            this.bindings.push({
                node,
                template,
                expressions,
            });
        }
    }

    /**
     * Subscribe to shared data changes
     */
    private subscribeToSharedChanges(): void {
        this.sharedUnsubscribe = SharedDataManager.getInstance().subscribeAll(() => {
            this.update();
        });
    }

    /**
     * Update state and re-render all bindings
     */
    setState(newState: Record<string, unknown>): void {
        this.state = newState;
        this.update();
    }

    /**
     * Update all interpolation bindings
     */
    update(): void {
        const context = this.buildContext();

        for (const binding of this.bindings) {
            const newText = this.interpolate(binding.template, context);
            if (binding.node.textContent !== newText) {
                binding.node.textContent = newText;
            }
        }
    }

    /**
     * Build evaluation context from state and shared data
     */
    private buildContext(): Record<string, unknown> {
        const shared = SharedDataManager.getInstance().all();
        return {
            ...this.state,
            $shared: shared,
            shared,  // Alias for convenience
        };
    }

    /**
     * Interpolate a template string with context
     */
    private interpolate(template: string, context: Record<string, unknown>): string {
        return template.replace(INTERPOLATION_REGEX, (_, expr: string) => {
            const trimmedExpr = expr.trim();
            const value = this.evaluateExpression(trimmedExpr, context);
            return value !== undefined && value !== null ? String(value) : '';
        });
    }

    /**
     * Evaluate an expression against context
     */
    private evaluateExpression(expr: string, context: Record<string, unknown>): unknown {
        try {
            // Simple property access (e.g., "count", "shared.user.name")
            if (/^[\w.$]+$/.test(expr)) {
                return getNestedValue(context, expr);
            }

            // Complex expression - use Function constructor
            const keys = Object.keys(context);
            const values = Object.values(context);
            const fn = new Function(...keys, `return ${expr}`) as (...args: unknown[]) => unknown;
            return fn(...values);
        } catch (e) {
            console.warn(`Accelade: Failed to evaluate expression "${expr}"`, e);
            return undefined;
        }
    }

    /**
     * Clean up bindings and subscriptions
     */
    destroy(): void {
        this.bindings = [];
        if (this.sharedUnsubscribe) {
            this.sharedUnsubscribe();
            this.sharedUnsubscribe = null;
        }
    }

    /**
     * Get the number of bindings
     */
    getBindingCount(): number {
        return this.bindings.length;
    }
}

/**
 * Create and initialize a text interpolator for an element
 */
export function createTextInterpolator(
    element: HTMLElement,
    state: Record<string, unknown> = {}
): TextInterpolator {
    const interpolator = new TextInterpolator(element, state);
    interpolator.init();
    return interpolator;
}

export default TextInterpolator;
