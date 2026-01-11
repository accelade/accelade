/**
 * Accelade Utilities
 */

import type { AcceladeComponentConfig } from './types';

/**
 * Parse data attributes from an element
 */
export function parseAcceladeElement(element: HTMLElement): AcceladeComponentConfig {
    const id = element.dataset.acceladeId ?? `accelade-${Math.random().toString(36).slice(2, 10)}`;

    let state: Record<string, unknown> = {};
    try {
        state = JSON.parse(element.dataset.acceladeState ?? '{}') as Record<string, unknown>;
    } catch {
        console.error('Accelade: Invalid state JSON', element.dataset.acceladeState);
    }

    let props: Record<string, unknown> = {};
    try {
        props = JSON.parse(element.dataset.acceladeProps ?? '{}') as Record<string, unknown>;
    } catch {
        console.error('Accelade: Invalid props JSON', element.dataset.acceladeProps);
    }

    return {
        id,
        state,
        sync: (element.dataset.acceladeSync ?? '').split(',').filter(Boolean),
        props,
    };
}

/**
 * Find all Accelade components in the DOM
 */
export function findAcceladeComponents(root: Document | Element = document): HTMLElement[] {
    return Array.from(root.querySelectorAll<HTMLElement>('[data-accelade]'));
}

/**
 * Debounce a function
 */
export function debounce<T extends (...args: unknown[]) => unknown>(
    fn: T,
    delay: number
): (...args: Parameters<T>) => void {
    let timer: ReturnType<typeof setTimeout> | null = null;
    return function (this: unknown, ...args: Parameters<T>): void {
        if (timer !== null) {
            clearTimeout(timer);
        }
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/**
 * Deep clone an object
 */
export function deepClone<T>(obj: T): T {
    return JSON.parse(JSON.stringify(obj)) as T;
}

/**
 * Check if a value is a plain object
 */
export function isObject(val: unknown): val is Record<string, unknown> {
    return val !== null && typeof val === 'object' && !Array.isArray(val);
}

/**
 * Merge objects deeply
 */
export function deepMerge<T extends Record<string, unknown>>(
    target: T,
    source: Partial<T>
): T {
    const result = { ...target } as T;

    for (const key of Object.keys(source) as Array<keyof T>) {
        const sourceValue = source[key];
        const targetValue = target[key];

        if (isObject(sourceValue) && isObject(targetValue)) {
            result[key] = deepMerge(
                targetValue as Record<string, unknown>,
                sourceValue as Record<string, unknown>
            ) as T[keyof T];
        } else if (sourceValue !== undefined) {
            result[key] = sourceValue as T[keyof T];
        }
    }

    return result;
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

export default {
    parseAcceladeElement,
    findAcceladeComponents,
    debounce,
    deepClone,
    isObject,
    deepMerge,
    getNestedValue,
    setNestedValue,
};
