/**
 * ConfigFactory - Parse and manage component configuration
 */

import type { AcceladeComponentConfig, AcceladeConfig, SharedData } from '../types';

/**
 * Full config type with required properties (for internal use)
 */
interface FullAcceladeConfig {
    framework: AcceladeConfig['framework'];
    syncDebounce: number;
    csrfToken: string;
    updateUrl: string;
    batchUpdateUrl: string;
    debug: boolean;
    progress?: AcceladeConfig['progress'];
    shared?: SharedData;
}

/**
 * Default Accelade configuration
 */
const defaultConfig: FullAcceladeConfig = {
    framework: 'vanilla',
    syncDebounce: 150,
    csrfToken: '',
    updateUrl: '/accelade/update',
    batchUpdateUrl: '/accelade/batch-update',
    debug: false,
};

/**
 * ConfigFactory - Handles configuration parsing and merging
 */
export class ConfigFactory {
    private static globalConfig: FullAcceladeConfig | null = null;

    /**
     * Parse component configuration from an HTML element
     */
    static parseElement(element: HTMLElement): AcceladeComponentConfig {
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

        const syncStr = element.dataset.acceladeSync ?? '';
        const sync = syncStr ? syncStr.split(',').map(s => s.trim()).filter(Boolean) : [];

        return { id, state, sync, props };
    }

    /**
     * Get global configuration (from window.AcceladeConfig or defaults)
     */
    static getGlobalConfig(): FullAcceladeConfig {
        if (this.globalConfig) {
            return this.globalConfig;
        }

        const windowConfig = typeof window !== 'undefined' ? window.AcceladeConfig : undefined;
        this.globalConfig = { ...defaultConfig, ...windowConfig };

        return this.globalConfig;
    }

    /**
     * Set global configuration
     */
    static setGlobalConfig(config: Partial<AcceladeConfig>): void {
        this.globalConfig = { ...this.getGlobalConfig(), ...config };
    }

    /**
     * Merge configurations
     */
    static merge<T extends Record<string, unknown>>(base: T, override: Partial<T>): T {
        const result = { ...base };

        for (const key of Object.keys(override) as Array<keyof T>) {
            const value = override[key];
            if (value !== undefined) {
                if (this.isObject(value) && this.isObject(base[key])) {
                    result[key] = this.merge(
                        base[key] as Record<string, unknown>,
                        value as Record<string, unknown>
                    ) as T[keyof T];
                } else {
                    result[key] = value as T[keyof T];
                }
            }
        }

        return result;
    }

    /**
     * Check if value is a plain object
     */
    private static isObject(val: unknown): val is Record<string, unknown> {
        return val !== null && typeof val === 'object' && !Array.isArray(val);
    }

    /**
     * Get CSRF token
     */
    static getCsrfToken(): string {
        const config = this.getGlobalConfig();
        if (config.csrfToken) {
            return config.csrfToken;
        }

        // Try to get from meta tag
        if (typeof document !== 'undefined') {
            const meta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
            if (meta) {
                return meta.content;
            }
        }

        return '';
    }

    /**
     * Get update URL
     */
    static getUpdateUrl(): string {
        return this.getGlobalConfig().updateUrl;
    }

    /**
     * Get batch update URL
     */
    static getBatchUpdateUrl(): string {
        return this.getGlobalConfig().batchUpdateUrl;
    }

    /**
     * Get sync debounce time
     */
    static getSyncDebounce(): number {
        return this.getGlobalConfig().syncDebounce;
    }

    /**
     * Reset configuration (useful for testing)
     */
    static reset(): void {
        this.globalConfig = null;
    }
}

export default ConfigFactory;
