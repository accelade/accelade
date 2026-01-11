/**
 * FrameworkRegistry - Runtime framework detection and adapter management
 */

import type { IFrameworkAdapter, FrameworkType, AdapterRegistration, AdapterFactory } from '../adapters/types';
import { ConfigFactory } from '../core/factories/ConfigFactory';

/**
 * Detection strategies for each framework
 */
const DETECTION_STRATEGIES: Record<FrameworkType, () => boolean> = {
    vanilla: () => true, // Always available as fallback

    vue: () => {
        if (typeof window === 'undefined') return false;
        // Check for Vue global
        if ('Vue' in window) return true;
        // Check for Vue-compiled templates
        if (document.querySelector('[data-v-]')) return true;
        if (document.querySelector('[v-cloak]')) return true;
        return false;
    },

    react: () => {
        if (typeof window === 'undefined') return false;
        // Check for React global
        if ('React' in window) return true;
        // Check for React root
        if (document.querySelector('[data-reactroot]')) return true;
        // Check for React 18+ root
        if (document.querySelector('#__next')) return true;
        // Check for React fiber
        const root = document.getElementById('root');
        if (root && '_reactRootContainer' in root) return true;
        return false;
    },

    svelte: () => {
        if (typeof window === 'undefined') return false;
        // Check for Svelte-compiled class names
        if (document.querySelector('[class*="svelte-"]')) return true;
        // Check for SvelteKit
        if (document.querySelector('#svelte')) return true;
        return false;
    },

    angular: () => {
        if (typeof window === 'undefined') return false;
        // Check for Angular globals
        if ('ng' in window) return true;
        // Check for Angular version attribute
        if (document.querySelector('[ng-version]')) return true;
        // Check for Angular app
        if (document.querySelector('app-root')) return true;
        return false;
    },
};

/**
 * Framework priority (higher = preferred)
 */
const FRAMEWORK_PRIORITY: Record<FrameworkType, number> = {
    react: 100,
    vue: 90,
    svelte: 80,
    angular: 70,
    vanilla: 0, // Fallback
};

/**
 * FrameworkRegistry - Manages framework adapters and detection
 */
export class FrameworkRegistry {
    private static registrations: Map<FrameworkType, AdapterRegistration> = new Map();
    private static instances: Map<FrameworkType, IFrameworkAdapter> = new Map();
    private static detectedFramework: FrameworkType | null = null;
    private static initialized = false;

    /**
     * Register a framework adapter
     */
    static register(
        type: FrameworkType,
        factory: AdapterFactory,
        options: { priority?: number; detectFn?: () => boolean } = {}
    ): void {
        const registration: AdapterRegistration = {
            type,
            factory,
            priority: options.priority ?? FRAMEWORK_PRIORITY[type] ?? 50,
            detectFn: options.detectFn ?? DETECTION_STRATEGIES[type],
        };

        this.registrations.set(type, registration);
    }

    /**
     * Unregister a framework adapter
     */
    static unregister(type: FrameworkType): void {
        this.registrations.delete(type);
        this.instances.delete(type);

        if (this.detectedFramework === type) {
            this.detectedFramework = null;
        }
    }

    /**
     * Detect the framework to use
     */
    static detect(): FrameworkType {
        if (this.detectedFramework) {
            return this.detectedFramework;
        }

        // 1. Check explicit configuration (highest priority - don't validate with detectFn)
        const config = ConfigFactory.getGlobalConfig();
        if (config.framework && this.registrations.has(config.framework)) {
            this.detectedFramework = config.framework;
            return config.framework;
        }

        // 2. Check meta tag (explicit configuration - don't validate with detectFn)
        if (typeof document !== 'undefined') {
            const meta = document.querySelector<HTMLMetaElement>('meta[name="accelade-framework"]');
            if (meta?.content) {
                const framework = meta.content as FrameworkType;
                if (this.registrations.has(framework)) {
                    this.detectedFramework = framework;
                    return framework;
                }
            }
        }

        // 3. Auto-detect by checking registrations in priority order
        const sortedRegistrations = Array.from(this.registrations.values())
            .filter(reg => reg.type !== 'vanilla') // Exclude vanilla from auto-detect
            .sort((a, b) => b.priority - a.priority);

        for (const reg of sortedRegistrations) {
            if (reg.detectFn && reg.detectFn()) {
                this.detectedFramework = reg.type;
                return reg.type;
            }
        }

        // 4. Fallback to vanilla
        this.detectedFramework = 'vanilla';
        return 'vanilla';
    }

    /**
     * Get adapter for a specific framework
     */
    static getAdapter(type?: FrameworkType): IFrameworkAdapter {
        const framework = type ?? this.detect();

        // Return cached instance if available
        if (this.instances.has(framework)) {
            return this.instances.get(framework)!;
        }

        // Create new instance
        const registration = this.registrations.get(framework);
        if (!registration) {
            // Fallback to vanilla if framework not registered
            if (framework !== 'vanilla') {
                console.warn(`Accelade: Framework "${framework}" not registered, falling back to vanilla`);
                return this.getAdapter('vanilla');
            }
            throw new Error('Accelade: No adapters registered. Call FrameworkRegistry.register() first.');
        }

        const adapter = registration.factory();
        this.instances.set(framework, adapter);

        return adapter;
    }

    /**
     * Check if a framework is registered
     */
    static isRegistered(type: FrameworkType): boolean {
        return this.registrations.has(type);
    }

    /**
     * Check if a framework is available (registered and detected)
     */
    static isAvailable(type: FrameworkType): boolean {
        const registration = this.registrations.get(type);
        if (!registration) return false;
        if (!registration.detectFn) return true;
        return registration.detectFn();
    }

    /**
     * Get all registered framework types
     */
    static getRegistered(): FrameworkType[] {
        return Array.from(this.registrations.keys());
    }

    /**
     * Get all available frameworks (registered and detected)
     */
    static getAvailable(): FrameworkType[] {
        return this.getRegistered().filter(type => this.isAvailable(type));
    }

    /**
     * Get the currently detected framework
     */
    static getDetected(): FrameworkType | null {
        return this.detectedFramework;
    }

    /**
     * Force set the framework (bypasses detection)
     */
    static setFramework(type: FrameworkType): void {
        if (!this.registrations.has(type)) {
            throw new Error(`Accelade: Framework "${type}" is not registered`);
        }
        this.detectedFramework = type;
    }

    /**
     * Reset the registry (useful for testing)
     */
    static reset(): void {
        this.registrations.clear();
        this.instances.clear();
        this.detectedFramework = null;
        this.initialized = false;
    }

    /**
     * Initialize the registry with default adapters
     */
    static initialize(): void {
        if (this.initialized) return;
        this.initialized = true;
    }

    /**
     * Get registration info for a framework
     */
    static getRegistration(type: FrameworkType): AdapterRegistration | undefined {
        return this.registrations.get(type);
    }

    /**
     * Debug: Get detection status for all frameworks
     */
    static getDetectionStatus(): Record<FrameworkType, { registered: boolean; available: boolean; detected: boolean }> {
        const detected = this.detect();
        const status: Record<string, { registered: boolean; available: boolean; detected: boolean }> = {};

        for (const type of ['vanilla', 'vue', 'react', 'svelte', 'angular'] as FrameworkType[]) {
            status[type] = {
                registered: this.isRegistered(type),
                available: this.isAvailable(type),
                detected: type === detected,
            };
        }

        return status as Record<FrameworkType, { registered: boolean; available: boolean; detected: boolean }>;
    }
}

export default FrameworkRegistry;
