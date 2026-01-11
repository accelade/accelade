/**
 * NetworkInspector - Network request tracking for Accelade
 *
 * Tracks all sync requests and allows replay for debugging.
 */

/**
 * Network request record
 */
export interface NetworkRecord {
    id: number;
    timestamp: number;
    url: string;
    method: string;
    headers: Record<string, string>;
    payload: unknown;
    status: number | null;
    statusText: string | null;
    response: unknown;
    duration: number | null;
    error: string | null;
    componentId: string | null;
    property: string | null;
}

/**
 * Network inspector configuration
 */
export interface NetworkInspectorConfig {
    maxRecords: number;
    enabled: boolean;
    captureResponse: boolean;
}

/**
 * Request start data
 */
interface PendingRequest {
    startTime: number;
    record: NetworkRecord;
}

/**
 * NetworkInspector - Tracks network requests for debugging
 */
export class NetworkInspector {
    private static instance: NetworkInspector | null = null;
    private records: NetworkRecord[] = [];
    private pendingRequests: Map<number, PendingRequest> = new Map();
    private recordCounter = 0;
    private config: NetworkInspectorConfig = {
        maxRecords: 100,
        enabled: false,
        captureResponse: true,
    };
    private listeners: Set<(records: NetworkRecord[]) => void> = new Set();

    /**
     * Get singleton instance
     */
    static getInstance(): NetworkInspector {
        if (!NetworkInspector.instance) {
            NetworkInspector.instance = new NetworkInspector();
        }
        return NetworkInspector.instance;
    }

    /**
     * Configure inspector settings
     */
    configure(config: Partial<NetworkInspectorConfig>): void {
        this.config = { ...this.config, ...config };

        // Trim records if max reduced
        if (this.records.length > this.config.maxRecords) {
            const trimCount = this.records.length - this.config.maxRecords;
            this.records = this.records.slice(trimCount);
        }
    }

    /**
     * Enable/disable inspector
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
     * Start tracking a request
     */
    startRequest(
        url: string,
        method: string,
        headers: Record<string, string>,
        payload: unknown,
        componentId?: string,
        property?: string
    ): number {
        const id = ++this.recordCounter;

        const record: NetworkRecord = {
            id,
            timestamp: Date.now(),
            url,
            method,
            headers: { ...headers },
            payload,
            status: null,
            statusText: null,
            response: null,
            duration: null,
            error: null,
            componentId: componentId ?? null,
            property: property ?? null,
        };

        this.pendingRequests.set(id, {
            startTime: performance.now(),
            record,
        });

        return id;
    }

    /**
     * Complete a request successfully
     */
    completeRequest(
        requestId: number,
        status: number,
        statusText: string,
        response: unknown
    ): void {
        const pending = this.pendingRequests.get(requestId);
        if (!pending) return;

        const { startTime, record } = pending;

        record.status = status;
        record.statusText = statusText;
        record.response = this.config.captureResponse ? response : '[response not captured]';
        record.duration = performance.now() - startTime;

        this.addRecord(record);
        this.pendingRequests.delete(requestId);
    }

    /**
     * Fail a request
     */
    failRequest(requestId: number, error: string): void {
        const pending = this.pendingRequests.get(requestId);
        if (!pending) return;

        const { startTime, record } = pending;

        record.status = 0;
        record.error = error;
        record.duration = performance.now() - startTime;

        this.addRecord(record);
        this.pendingRequests.delete(requestId);
    }

    /**
     * Add a completed record
     */
    private addRecord(record: NetworkRecord): void {
        if (!this.config.enabled) return;

        this.records.push(record);

        // Enforce max records
        if (this.records.length > this.config.maxRecords) {
            this.records.shift();
        }

        this.notifyListeners();
    }

    /**
     * Replay a request
     */
    async replay(requestId: number): Promise<Response> {
        const record = this.records.find((r) => r.id === requestId);
        if (!record) {
            throw new Error(`Network record ${requestId} not found`);
        }

        const newRequestId = this.startRequest(
            record.url,
            record.method,
            record.headers,
            record.payload,
            record.componentId ?? undefined,
            record.property ?? undefined
        );

        try {
            const response = await fetch(record.url, {
                method: record.method,
                headers: record.headers,
                body: record.payload ? JSON.stringify(record.payload) : undefined,
            });

            const responseData = await response.clone().json().catch(() => null);

            this.completeRequest(
                newRequestId,
                response.status,
                response.statusText,
                responseData
            );

            return response;
        } catch (e) {
            this.failRequest(newRequestId, String(e));
            throw e;
        }
    }

    /**
     * Get all records
     */
    getRecords(): NetworkRecord[] {
        return [...this.records];
    }

    /**
     * Get record by ID
     */
    getRecord(id: number): NetworkRecord | null {
        return this.records.find((r) => r.id === id) ?? null;
    }

    /**
     * Get records for a component
     */
    getComponentRecords(componentId: string): NetworkRecord[] {
        return this.records.filter((r) => r.componentId === componentId);
    }

    /**
     * Get failed requests
     */
    getFailedRequests(): NetworkRecord[] {
        return this.records.filter((r) => r.error !== null || (r.status && r.status >= 400));
    }

    /**
     * Get pending requests
     */
    getPendingRequests(): NetworkRecord[] {
        return Array.from(this.pendingRequests.values()).map((p) => p.record);
    }

    /**
     * Clear all records
     */
    clear(): void {
        this.records = [];
        this.notifyListeners();
    }

    /**
     * Subscribe to record changes
     */
    subscribe(callback: (records: NetworkRecord[]) => void): () => void {
        this.listeners.add(callback);
        return () => {
            this.listeners.delete(callback);
        };
    }

    /**
     * Notify listeners of changes
     */
    private notifyListeners(): void {
        const records = this.getRecords();
        for (const listener of this.listeners) {
            listener(records);
        }
    }

    /**
     * Get statistics
     */
    getStats(): {
        total: number;
        successful: number;
        failed: number;
        pending: number;
        averageDuration: number;
    } {
        const completed = this.records.filter((r) => r.duration !== null);
        const successful = this.records.filter(
            (r) => r.status !== null && r.status >= 200 && r.status < 400
        );
        const failed = this.records.filter(
            (r) => r.error !== null || (r.status !== null && r.status >= 400)
        );

        const totalDuration = completed.reduce((sum, r) => sum + (r.duration ?? 0), 0);
        const averageDuration = completed.length > 0 ? totalDuration / completed.length : 0;

        return {
            total: this.records.length,
            successful: successful.length,
            failed: failed.length,
            pending: this.pendingRequests.size,
            averageDuration: Math.round(averageDuration * 100) / 100,
        };
    }

    /**
     * Export records for debugging
     */
    export(): string {
        return JSON.stringify(
            {
                records: this.records,
                stats: this.getStats(),
                config: this.config,
            },
            null,
            2
        );
    }
}

export default NetworkInspector;
