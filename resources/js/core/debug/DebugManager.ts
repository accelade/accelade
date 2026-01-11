/**
 * DebugManager - Main debug controller for Accelade
 *
 * Provides a unified API for debugging Accelade applications.
 * Enables state inspection, time-travel debugging, and network monitoring.
 */

import { StateHistory, type StateSnapshot, type StateHistoryConfig } from './StateHistory';
import { NetworkInspector, type NetworkRecord, type NetworkInspectorConfig } from './NetworkInspector';
import type { ComponentInstance } from '../../adapters/types';

/**
 * Debug configuration
 */
export interface DebugConfig {
    enabled: boolean;
    stateHistory: Partial<StateHistoryConfig>;
    networkInspector: Partial<NetworkInspectorConfig>;
    logLevel: 'none' | 'error' | 'warn' | 'info' | 'debug';
}

/**
 * Devtools API exposed on window.Accelade.devtools
 */
export interface AcceladeDevtools {
    // State inspection
    getState(componentId?: string): Record<string, unknown> | null;
    setState(componentId: string, key: string, value: unknown): boolean;
    getComponents(): Map<string, ComponentInstance>;
    getComponent(componentId: string): ComponentInstance | undefined;

    // Time-travel debugging
    history: StateSnapshot[];
    currentHistoryIndex: number;
    goto(snapshotId: number): boolean;
    back(): boolean;
    forward(): boolean;
    canGoBack(): boolean;
    canGoForward(): boolean;
    clearHistory(): void;

    // Network inspection
    network: NetworkRecord[];
    replay(requestId: number): Promise<Response>;
    getNetworkStats(): ReturnType<NetworkInspector['getStats']>;
    clearNetwork(): void;

    // Debug utilities
    log(...args: unknown[]): void;
    warn(...args: unknown[]): void;
    error(...args: unknown[]): void;
    export(): string;
}

/**
 * DebugManager - Orchestrates all debug functionality
 */
export class DebugManager {
    private static instance: DebugManager | null = null;
    private config: DebugConfig = {
        enabled: false,
        stateHistory: { maxSnapshots: 50 },
        networkInspector: { maxRecords: 100, captureResponse: true },
        logLevel: 'warn',
    };
    private components: Map<string, ComponentInstance> = new Map();
    private stateHistory: StateHistory;
    private networkInspector: NetworkInspector;

    private constructor() {
        this.stateHistory = StateHistory.getInstance();
        this.networkInspector = NetworkInspector.getInstance();
    }

    /**
     * Get singleton instance
     */
    static getInstance(): DebugManager {
        if (!DebugManager.instance) {
            DebugManager.instance = new DebugManager();
        }
        return DebugManager.instance;
    }

    /**
     * Initialize debug manager
     */
    init(config?: Partial<DebugConfig>): void {
        if (config) {
            this.configure(config);
        }

        // Setup window.Accelade.devtools if enabled
        if (this.config.enabled && typeof window !== 'undefined') {
            this.exposeDevtools();
        }
    }

    /**
     * Configure debug settings
     */
    configure(config: Partial<DebugConfig>): void {
        this.config = { ...this.config, ...config };

        // Apply to sub-systems
        this.stateHistory.configure({
            ...this.config.stateHistory,
            enabled: this.config.enabled,
        });

        this.networkInspector.configure({
            ...this.config.networkInspector,
            enabled: this.config.enabled,
        });
    }

    /**
     * Enable/disable debug mode
     */
    setEnabled(enabled: boolean): void {
        this.config.enabled = enabled;
        this.stateHistory.setEnabled(enabled);
        this.networkInspector.setEnabled(enabled);

        if (enabled && typeof window !== 'undefined') {
            this.exposeDevtools();
            this.log('Debug mode enabled');
        } else {
            this.log('Debug mode disabled');
        }
    }

    /**
     * Check if debug mode is enabled
     */
    isEnabled(): boolean {
        return this.config.enabled;
    }

    /**
     * Register a component
     */
    registerComponent(instance: ComponentInstance): void {
        this.components.set(instance.id, instance);

        // Register state setter for time-travel
        if (instance.stateAdapter) {
            this.stateHistory.registerStateSetter(instance.id, (state) => {
                if (instance.stateAdapter) {
                    for (const [key, value] of Object.entries(state)) {
                        instance.stateAdapter.set(key, value);
                    }
                }
            });
        }
    }

    /**
     * Unregister a component
     */
    unregisterComponent(componentId: string): void {
        this.components.delete(componentId);
    }

    /**
     * Get all registered components
     */
    getComponents(): Map<string, ComponentInstance> {
        return new Map(this.components);
    }

    /**
     * Get a specific component
     */
    getComponent(componentId: string): ComponentInstance | undefined {
        return this.components.get(componentId);
    }

    /**
     * Get state from a component
     */
    getState(componentId?: string): Record<string, unknown> | null {
        if (componentId) {
            const component = this.components.get(componentId);
            return component?.stateAdapter?.getState() ?? null;
        }

        // Return all component states
        const allStates: Record<string, Record<string, unknown>> = {};
        for (const [id, component] of this.components) {
            const state = component.stateAdapter?.getState();
            if (state) {
                allStates[id] = state;
            }
        }
        return allStates;
    }

    /**
     * Set state on a component
     */
    setState(componentId: string, key: string, value: unknown): boolean {
        const component = this.components.get(componentId);
        if (!component?.stateAdapter) return false;

        component.stateAdapter.set(key, value);
        return true;
    }

    /**
     * Record a state change (called by adapters)
     */
    recordStateChange(
        componentId: string,
        key: string,
        oldValue: unknown,
        newValue: unknown,
        currentState: Record<string, unknown>
    ): void {
        this.stateHistory.record(componentId, key, oldValue, newValue, currentState);
    }

    /**
     * Get state history instance
     */
    getStateHistory(): StateHistory {
        return this.stateHistory;
    }

    /**
     * Get network inspector instance
     */
    getNetworkInspector(): NetworkInspector {
        return this.networkInspector;
    }

    /**
     * Log a debug message
     */
    log(...args: unknown[]): void {
        if (this.config.logLevel === 'debug' || this.config.logLevel === 'info') {
            console.log('[Accelade]', ...args);
        }
    }

    /**
     * Log a warning
     */
    warn(...args: unknown[]): void {
        if (this.config.logLevel !== 'none' && this.config.logLevel !== 'error') {
            console.warn('[Accelade]', ...args);
        }
    }

    /**
     * Log an error
     */
    error(...args: unknown[]): void {
        if (this.config.logLevel !== 'none') {
            console.error('[Accelade]', ...args);
        }
    }

    /**
     * Export all debug data
     */
    export(): string {
        const data = {
            components: Array.from(this.components.entries()).map(([id, c]) => ({
                id,
                state: c.stateAdapter?.getState() ?? {},
            })),
            stateHistory: JSON.parse(this.stateHistory.export()),
            networkInspector: JSON.parse(this.networkInspector.export()),
            config: this.config,
            timestamp: new Date().toISOString(),
        };

        return JSON.stringify(data, null, 2);
    }

    /**
     * Expose devtools on window.Accelade
     */
    private exposeDevtools(): void {
        const manager = this;

        const devtools: AcceladeDevtools = {
            // State inspection
            getState: (componentId?: string) => manager.getState(componentId),
            setState: (componentId: string, key: string, value: unknown) =>
                manager.setState(componentId, key, value),
            getComponents: () => manager.getComponents(),
            getComponent: (componentId: string) => manager.getComponent(componentId),

            // Time-travel (getters for reactive properties)
            get history() {
                return manager.stateHistory.getSnapshots();
            },
            get currentHistoryIndex() {
                return manager.stateHistory.getCurrentIndex();
            },
            goto: (snapshotId: number) => manager.stateHistory.goto(snapshotId),
            back: () => manager.stateHistory.back(),
            forward: () => manager.stateHistory.forward(),
            canGoBack: () => manager.stateHistory.canGoBack(),
            canGoForward: () => manager.stateHistory.canGoForward(),
            clearHistory: () => manager.stateHistory.clear(),

            // Network inspection
            get network() {
                return manager.networkInspector.getRecords();
            },
            replay: (requestId: number) => manager.networkInspector.replay(requestId),
            getNetworkStats: () => manager.networkInspector.getStats(),
            clearNetwork: () => manager.networkInspector.clear(),

            // Debug utilities
            log: (...args: unknown[]) => manager.log(...args),
            warn: (...args: unknown[]) => manager.warn(...args),
            error: (...args: unknown[]) => manager.error(...args),
            export: () => manager.export(),
        };

        // Attach to window.Accelade
        if (typeof window !== 'undefined') {
            const accelade = (window as unknown as Record<string, unknown>).Accelade as
                | Record<string, unknown>
                | undefined;

            if (accelade) {
                accelade.devtools = devtools;
                accelade.debug = true;
            }
        }
    }
}

export default DebugManager;
