/**
 * StateHistory - Time-travel debugging for Accelade
 *
 * Records state snapshots and allows navigating back/forward through history.
 */

/**
 * State snapshot
 */
export interface StateSnapshot {
    id: number;
    timestamp: number;
    componentId: string;
    key: string;
    oldValue: unknown;
    newValue: unknown;
    state: Record<string, unknown>;
}

/**
 * State history configuration
 */
export interface StateHistoryConfig {
    maxSnapshots: number;
    enabled: boolean;
}

/**
 * StateHistory - Manages state snapshots for time-travel debugging
 */
export class StateHistory {
    private static instance: StateHistory | null = null;
    private snapshots: StateSnapshot[] = [];
    private currentIndex = -1;
    private snapshotCounter = 0;
    private config: StateHistoryConfig = {
        maxSnapshots: 50,
        enabled: false,
    };
    private listeners: Set<(snapshots: StateSnapshot[], currentIndex: number) => void> = new Set();
    private stateSetters: Map<string, (state: Record<string, unknown>) => void> = new Map();

    /**
     * Get singleton instance
     */
    static getInstance(): StateHistory {
        if (!StateHistory.instance) {
            StateHistory.instance = new StateHistory();
        }
        return StateHistory.instance;
    }

    /**
     * Configure history settings
     */
    configure(config: Partial<StateHistoryConfig>): void {
        this.config = { ...this.config, ...config };

        // Trim snapshots if max reduced
        if (this.snapshots.length > this.config.maxSnapshots) {
            const trimCount = this.snapshots.length - this.config.maxSnapshots;
            this.snapshots = this.snapshots.slice(trimCount);
            this.currentIndex = Math.max(0, this.currentIndex - trimCount);
        }
    }

    /**
     * Enable/disable history recording
     */
    setEnabled(enabled: boolean): void {
        this.config.enabled = enabled;
    }

    /**
     * Check if enabled
     */
    isEnabled(): boolean {
        return this.config.enabled;
    }

    /**
     * Register a state setter for a component
     */
    registerStateSetter(
        componentId: string,
        setter: (state: Record<string, unknown>) => void
    ): () => void {
        this.stateSetters.set(componentId, setter);
        return () => {
            this.stateSetters.delete(componentId);
        };
    }

    /**
     * Record a state change
     */
    record(
        componentId: string,
        key: string,
        oldValue: unknown,
        newValue: unknown,
        currentState: Record<string, unknown>
    ): void {
        if (!this.config.enabled) return;

        // If we're not at the end, truncate future history
        if (this.currentIndex < this.snapshots.length - 1) {
            this.snapshots = this.snapshots.slice(0, this.currentIndex + 1);
        }

        const snapshot: StateSnapshot = {
            id: ++this.snapshotCounter,
            timestamp: Date.now(),
            componentId,
            key,
            oldValue,
            newValue,
            state: { ...currentState },
        };

        this.snapshots.push(snapshot);
        this.currentIndex = this.snapshots.length - 1;

        // Enforce max snapshots
        if (this.snapshots.length > this.config.maxSnapshots) {
            this.snapshots.shift();
            this.currentIndex--;
        }

        this.notifyListeners();
    }

    /**
     * Go to a specific snapshot
     */
    goto(snapshotId: number): boolean {
        const index = this.snapshots.findIndex((s) => s.id === snapshotId);
        if (index === -1) return false;

        return this.gotoIndex(index);
    }

    /**
     * Go to a specific index
     */
    gotoIndex(index: number): boolean {
        if (index < 0 || index >= this.snapshots.length) return false;

        const snapshot = this.snapshots[index];
        const setter = this.stateSetters.get(snapshot.componentId);

        if (setter) {
            setter(snapshot.state);
            this.currentIndex = index;
            this.notifyListeners();
            return true;
        }

        return false;
    }

    /**
     * Go back one step
     */
    back(): boolean {
        if (this.currentIndex <= 0) return false;

        const prevIndex = this.currentIndex - 1;
        const snapshot = this.snapshots[prevIndex];
        const setter = this.stateSetters.get(snapshot.componentId);

        if (setter) {
            setter(snapshot.state);
            this.currentIndex = prevIndex;
            this.notifyListeners();
            return true;
        }

        return false;
    }

    /**
     * Go forward one step
     */
    forward(): boolean {
        if (this.currentIndex >= this.snapshots.length - 1) return false;

        const nextIndex = this.currentIndex + 1;
        const snapshot = this.snapshots[nextIndex];
        const setter = this.stateSetters.get(snapshot.componentId);

        if (setter) {
            setter(snapshot.state);
            this.currentIndex = nextIndex;
            this.notifyListeners();
            return true;
        }

        return false;
    }

    /**
     * Get all snapshots
     */
    getSnapshots(): StateSnapshot[] {
        return [...this.snapshots];
    }

    /**
     * Get current index
     */
    getCurrentIndex(): number {
        return this.currentIndex;
    }

    /**
     * Get current snapshot
     */
    getCurrentSnapshot(): StateSnapshot | null {
        return this.snapshots[this.currentIndex] ?? null;
    }

    /**
     * Check if can go back
     */
    canGoBack(): boolean {
        return this.currentIndex > 0;
    }

    /**
     * Check if can go forward
     */
    canGoForward(): boolean {
        return this.currentIndex < this.snapshots.length - 1;
    }

    /**
     * Clear all history
     */
    clear(): void {
        this.snapshots = [];
        this.currentIndex = -1;
        this.notifyListeners();
    }

    /**
     * Subscribe to history changes
     */
    subscribe(
        callback: (snapshots: StateSnapshot[], currentIndex: number) => void
    ): () => void {
        this.listeners.add(callback);
        return () => {
            this.listeners.delete(callback);
        };
    }

    /**
     * Notify listeners of changes
     */
    private notifyListeners(): void {
        const snapshots = this.getSnapshots();
        const currentIndex = this.currentIndex;

        for (const listener of this.listeners) {
            listener(snapshots, currentIndex);
        }
    }

    /**
     * Export history for debugging
     */
    export(): string {
        return JSON.stringify(
            {
                snapshots: this.snapshots,
                currentIndex: this.currentIndex,
                config: this.config,
            },
            null,
            2
        );
    }

    /**
     * Import history from export
     */
    import(data: string): void {
        try {
            const parsed = JSON.parse(data) as {
                snapshots: StateSnapshot[];
                currentIndex: number;
                config: StateHistoryConfig;
            };

            this.snapshots = parsed.snapshots;
            this.currentIndex = parsed.currentIndex;
            this.config = { ...this.config, ...parsed.config };
            this.notifyListeners();
        } catch (e) {
            console.error('StateHistory: Failed to import data', e);
        }
    }
}

export default StateHistory;
