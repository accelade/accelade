/**
 * Expression Evaluator - Shared expression evaluation logic
 */

/**
 * Evaluate an expression against state
 */
export function evaluateExpression(
    expr: string,
    state: Record<string, unknown>
): unknown {
    try {
        // Simple property access (e.g., "count", "user.name")
        if (/^[\w.]+$/.test(expr)) {
            return getNestedValue(state, expr);
        }

        // Create evaluation function with state variables in scope
        const keys = Object.keys(state);
        const values = Object.values(state);
        const fn = new Function(...keys, 'return ' + expr) as (...args: unknown[]) => unknown;
        return fn(...values);
    } catch {
        return undefined;
    }
}

/**
 * Evaluate a boolean expression
 */
export function evaluateBooleanExpression(
    expr: string,
    state: Record<string, unknown>
): boolean {
    const result = evaluateExpression(expr, state);
    return Boolean(result);
}

/**
 * Evaluate a string expression (with template interpolation)
 */
export function evaluateStringExpression(
    expr: string,
    state: Record<string, unknown>
): string {
    const result = evaluateExpression(expr, state);
    return result !== undefined ? String(result) : '';
}

/**
 * Get nested value from object using dot notation
 */
export function getNestedValue(obj: Record<string, unknown>, path: string): unknown {
    return path.split('.').reduce<unknown>(
        (current, key) => {
            if (current !== null && typeof current === 'object') {
                return (current as Record<string, unknown>)[key];
            }
            return undefined;
        },
        obj
    );
}

/**
 * Set nested value in object using dot notation
 */
export function setNestedValue(
    obj: Record<string, unknown>,
    path: string,
    value: unknown
): void {
    const keys = path.split('.');
    const lastKey = keys.pop();

    if (!lastKey) return;

    let current: Record<string, unknown> = obj;
    for (const key of keys) {
        if (!(key in current) || typeof current[key] !== 'object') {
            current[key] = {};
        }
        current = current[key] as Record<string, unknown>;
    }

    current[lastKey] = value;
}

/**
 * Interpolate mustache-style templates
 * e.g., "Hello {{ name }}" -> "Hello John"
 */
export function interpolate(
    template: string,
    state: Record<string, unknown>
): string {
    return template.replace(/\{\{\s*([\w.]+)\s*\}\}/g, (_, key: string) => {
        const value = getNestedValue(state, key);
        return value !== undefined ? String(value) : '';
    });
}

/**
 * Parse a class binding object expression
 * e.g., "{ active: isActive, 'text-red': hasError }" -> { active: true, 'text-red': false }
 */
export function parseClassObject(
    expr: string,
    state: Record<string, unknown>
): Record<string, boolean> {
    try {
        // If it's a simple property reference
        if (/^[\w.]+$/.test(expr)) {
            const value = getNestedValue(state, expr);
            if (typeof value === 'object' && value !== null) {
                return value as Record<string, boolean>;
            }
            return {};
        }

        // Parse as object expression
        const result = evaluateExpression(expr, state);
        if (typeof result === 'object' && result !== null) {
            return result as Record<string, boolean>;
        }
        return {};
    } catch {
        return {};
    }
}

/**
 * Parse a style binding object expression
 */
export function parseStyleObject(
    expr: string,
    state: Record<string, unknown>
): Record<string, string> {
    try {
        const result = evaluateExpression(expr, state);
        if (typeof result === 'object' && result !== null) {
            const styles: Record<string, string> = {};
            for (const [key, value] of Object.entries(result)) {
                if (value !== null && value !== undefined) {
                    styles[key] = String(value);
                }
            }
            return styles;
        }
        return {};
    } catch {
        return {};
    }
}

/**
 * Check if expression is a simple property reference
 */
export function isSimpleProperty(expr: string): boolean {
    return /^[\w.]+$/.test(expr);
}

/**
 * Check if expression contains operators
 */
export function hasOperators(expr: string): boolean {
    return /[+\-*/%<>=!&|?:]/.test(expr);
}

/**
 * Safe expression evaluation with error handling
 */
export function safeEvaluate<T>(
    expr: string,
    state: Record<string, unknown>,
    defaultValue: T
): T {
    try {
        const result = evaluateExpression(expr, state);
        return (result as T) ?? defaultValue;
    } catch {
        return defaultValue;
    }
}

export default {
    evaluateExpression,
    evaluateBooleanExpression,
    evaluateStringExpression,
    getNestedValue,
    setNestedValue,
    interpolate,
    parseClassObject,
    parseStyleObject,
    isSimpleProperty,
    hasOperators,
    safeEvaluate,
};
