/**
 * Accelade Progress Bar
 * Customizable loading indicator inspired by Inertia.js/NProgress
 */

export interface ProgressConfig {
    /** Delay in ms before showing progress bar (default: 250) */
    delay?: number;
    /** Progress bar color (default: '#6366f1') */
    color?: string;
    /** Include spinner indicator (default: true) */
    includeSpinner?: boolean;
    /** Show progress bar (default: true) */
    showBar?: boolean;
    /** Progress bar height in pixels (default: 3) */
    height?: number;
    /** Minimum progress percentage on start (default: 8) */
    minimum?: number;
    /** Animation easing function (default: 'ease-out') */
    easing?: string;
    /** Animation speed in ms (default: 200) */
    speed?: number;
    /** Trickle speed - how fast it trickles (default: 200) */
    trickleSpeed?: number;
    /** Z-index for the progress bar (default: 99999) */
    zIndex?: number;
    /** Position: 'top' or 'bottom' (default: 'top') */
    position?: 'top' | 'bottom';
    /** Spinner position (default: 'top-right') */
    spinnerPosition?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    /** Custom spinner size in pixels (default: 18) */
    spinnerSize?: number;
    /** Use gradient colors (default: true) */
    useGradient?: boolean;
    /** Secondary gradient color (default: '#8b5cf6') */
    gradientColor?: string;
    /** Third gradient color (default: '#a855f7') */
    gradientColor2?: string;
}

const defaultConfig: Required<ProgressConfig> = {
    delay: 0,
    color: '#6366f1',
    includeSpinner: true,
    showBar: true,
    height: 3,
    minimum: 8,
    easing: 'ease-out',
    speed: 200,
    trickleSpeed: 200,
    zIndex: 99999,
    position: 'top',
    spinnerPosition: 'top-right',
    spinnerSize: 18,
    useGradient: true,
    gradientColor: '#8b5cf6',
    gradientColor2: '#a855f7',
};

class AcceladeProgress {
    private config: Required<ProgressConfig>;
    private element: HTMLElement | null = null;
    private barElement: HTMLElement | null = null;
    private spinnerElement: HTMLElement | null = null;
    private progress: number = 0;
    private isRunning: boolean = false;
    private trickleInterval: ReturnType<typeof setInterval> | null = null;
    private delayTimeout: ReturnType<typeof setTimeout> | null = null;
    private isVisible: boolean = false;
    private static stylesInjected: boolean = false;

    /**
     * Check if document is in RTL mode
     */
    private isRTL(): boolean {
        return document.documentElement.dir === 'rtl' || document.body.dir === 'rtl';
    }

    constructor(config: ProgressConfig = {}) {
        this.config = { ...defaultConfig, ...config };
        this.injectStyles();
    }

    /**
     * Inject CSS keyframes for spinner animation
     */
    private injectStyles(): void {
        if (AcceladeProgress.stylesInjected) return;
        if (typeof document === 'undefined') return;

        const style = document.createElement('style');
        style.id = 'accelade-progress-styles';
        style.textContent = `
            @keyframes accelade-spinner {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
        AcceladeProgress.stylesInjected = true;
    }

    /**
     * Configure the progress bar
     */
    configure(config: ProgressConfig): void {
        this.config = { ...this.config, ...config };
        this.updateStyles();
    }

    /**
     * Create progress bar DOM elements
     */
    private createElements(): void {
        if (this.element) {
            return;
        }

        // Create container
        this.element = document.createElement('div');
        this.element.id = 'accelade-progress';
        this.applyContainerStyles();

        // Create bar
        if (this.config.showBar) {
            this.barElement = document.createElement('div');
            this.barElement.className = 'bar';
            this.applyBarStyles();
            this.element.appendChild(this.barElement);
        }

        // Create spinner
        if (this.config.includeSpinner) {
            this.spinnerElement = document.createElement('div');
            this.spinnerElement.className = 'spinner';
            this.applySpinnerStyles();
            this.element.appendChild(this.spinnerElement);
        }

        document.body.appendChild(this.element);
    }

    /**
     * Apply container styles
     */
    private applyContainerStyles(): void {
        if (!this.element) return;

        const { position, zIndex, height } = this.config;
        Object.assign(this.element.style, {
            position: 'fixed',
            [position]: '0',
            left: '0',
            right: '0',
            height: `${height}px`,
            zIndex: String(zIndex),
            pointerEvents: 'none',
            background: 'transparent',
            overflow: 'hidden',
            opacity: '0',
            transition: 'opacity 0.2s ease',
        });
    }

    /**
     * Apply bar styles
     */
    private applyBarStyles(): void {
        if (!this.barElement) return;

        const { color, height, easing, speed, useGradient, gradientColor, gradientColor2 } = this.config;
        const background = useGradient
            ? `linear-gradient(90deg, ${color}, ${gradientColor}, ${gradientColor2})`
            : color;

        Object.assign(this.barElement.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            height: '100%',
            width: '0%',
            background,
            boxShadow: `0 0 10px ${this.hexToRgba(color, 0.7)}, 0 0 5px ${this.hexToRgba(gradientColor, 0.5)}`,
            transition: `width ${speed}ms ${easing}`,
            borderRadius: '0 2px 2px 0',
        });
    }

    /**
     * Apply spinner styles
     */
    private applySpinnerStyles(): void {
        if (!this.spinnerElement) return;

        const { color, gradientColor, spinnerSize, spinnerPosition } = this.config;
        const isRTL = this.isRTL();

        // Position mapping - RTL-aware (swaps left/right in RTL mode)
        const positions: Record<string, { top?: string; bottom?: string; left?: string; right?: string }> = {
            'top-left': { top: '15px', [isRTL ? 'right' : 'left']: '15px' },
            'top-right': { top: '15px', [isRTL ? 'left' : 'right']: '15px' },
            'bottom-left': { bottom: '15px', [isRTL ? 'right' : 'left']: '15px' },
            'bottom-right': { bottom: '15px', [isRTL ? 'left' : 'right']: '15px' },
        };

        const pos = positions[spinnerPosition] || positions['top-right'];

        // Clear existing position styles
        this.spinnerElement.style.left = '';
        this.spinnerElement.style.right = '';

        Object.assign(this.spinnerElement.style, {
            position: 'fixed',
            ...pos,
            width: `${spinnerSize}px`,
            height: `${spinnerSize}px`,
            border: '2px solid transparent',
            borderTopColor: color,
            [isRTL ? 'borderRightColor' : 'borderLeftColor']: gradientColor,
            borderRadius: '50%',
            animation: 'accelade-spinner 0.6s linear infinite',
            opacity: '0',
            transition: 'opacity 0.2s ease',
        });
    }

    /**
     * Update styles dynamically
     */
    private updateStyles(): void {
        this.applyContainerStyles();
        this.applyBarStyles();
        this.applySpinnerStyles();
    }

    /**
     * Convert hex to rgba
     */
    private hexToRgba(hex: string, alpha: number): string {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    /**
     * Start the progress bar
     */
    start(): void {
        if (this.isRunning) return;

        this.createElements();
        this.isRunning = true;
        this.progress = 0;

        // Add navigating class to document
        document.documentElement.classList.add('accelade-navigating');

        // Delay showing the progress bar
        this.delayTimeout = setTimeout(() => {
            this.show();
            this.set(this.config.minimum);
            this.startTrickle();
        }, this.config.delay);
    }

    /**
     * Show the progress bar
     */
    private show(): void {
        if (this.isVisible) return;
        this.isVisible = true;

        if (this.element) {
            this.element.style.opacity = '1';
        }
        if (this.spinnerElement) {
            this.spinnerElement.classList.add('visible');
            this.spinnerElement.style.opacity = '1';
        }
    }

    /**
     * Hide the progress bar
     */
    private hide(): void {
        this.isVisible = false;

        if (this.element) {
            this.element.style.opacity = '0';
        }
        if (this.spinnerElement) {
            this.spinnerElement.classList.remove('visible');
            this.spinnerElement.style.opacity = '0';
        }
    }

    /**
     * Set progress to a specific value (0-100)
     */
    set(value: number): void {
        this.progress = Math.max(0, Math.min(100, value));

        if (this.barElement) {
            this.barElement.style.width = `${this.progress}%`;
        }
    }

    /**
     * Increment progress by a random amount
     */
    inc(amount?: number): void {
        if (this.progress >= 100) return;

        if (amount === undefined) {
            // Smart increment - slower as we get closer to 100%
            if (this.progress < 25) {
                amount = Math.random() * 10 + 3;
            } else if (this.progress < 50) {
                amount = Math.random() * 5 + 2;
            } else if (this.progress < 80) {
                amount = Math.random() * 3 + 1;
            } else if (this.progress < 95) {
                amount = Math.random() * 2 + 0.5;
            } else {
                amount = 0.1;
            }
        }

        this.set(this.progress + amount);
    }

    /**
     * Start trickling (automatic progress)
     */
    private startTrickle(): void {
        this.stopTrickle();
        this.trickleInterval = setInterval(() => {
            this.inc();
        }, this.config.trickleSpeed);
    }

    /**
     * Stop trickling
     */
    private stopTrickle(): void {
        if (this.trickleInterval) {
            clearInterval(this.trickleInterval);
            this.trickleInterval = null;
        }
    }

    /**
     * Complete the progress bar
     */
    done(force: boolean = false): void {
        if (!this.isRunning && !force) return;

        // Clear delay timeout if progress hasn't shown yet
        if (this.delayTimeout) {
            clearTimeout(this.delayTimeout);
            this.delayTimeout = null;
        }

        this.stopTrickle();

        // If not visible yet, just clean up
        if (!this.isVisible) {
            this.isRunning = false;
            document.documentElement.classList.remove('accelade-navigating');
            return;
        }

        // Complete to 100%
        this.set(100);

        // Hide after animation completes
        setTimeout(() => {
            this.hide();

            // Reset after fade out
            setTimeout(() => {
                this.set(0);
                this.isRunning = false;
                document.documentElement.classList.remove('accelade-navigating');
            }, 200);
        }, this.config.speed);
    }

    /**
     * Check if progress bar is running
     */
    isActive(): boolean {
        return this.isRunning;
    }

    /**
     * Remove the progress bar from DOM
     */
    remove(): void {
        this.done(true);
        if (this.element) {
            this.element.remove();
            this.element = null;
            this.barElement = null;
            this.spinnerElement = null;
        }
    }
}

// Singleton instance
let progressInstance: AcceladeProgress | null = null;

/**
 * Get or create the progress instance
 */
export function getProgress(config?: ProgressConfig): AcceladeProgress {
    if (!progressInstance) {
        progressInstance = new AcceladeProgress(config);
    } else if (config) {
        progressInstance.configure(config);
    }
    return progressInstance;
}

/**
 * Configure the progress bar
 */
export function configureProgress(config: ProgressConfig): void {
    getProgress().configure(config);
}

/**
 * Start the progress bar
 */
export function startProgress(): void {
    getProgress().start();
}

/**
 * Set progress value
 */
export function setProgress(value: number): void {
    getProgress().set(value);
}

/**
 * Increment progress
 */
export function incProgress(amount?: number): void {
    getProgress().inc(amount);
}

/**
 * Complete the progress bar
 */
export function doneProgress(force?: boolean): void {
    getProgress().done(force);
}

// Export the class for type usage
export { AcceladeProgress };

export default {
    AcceladeProgress,
    getProgress,
    configureProgress,
    startProgress,
    setProgress,
    incProgress,
    doneProgress,
};
