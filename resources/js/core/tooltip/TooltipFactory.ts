/**
 * Tooltip Factory
 *
 * Creates tooltip instances from the a-tooltip directive.
 * Supports Filament-like API with themes (light/dark/auto), RTL, and width control.
 *
 * Usage:
 *   <button a-tooltip="Hello World">Hover me</button>
 *   <button a-tooltip='{"content": "Welcome", "theme": "light", "position": "bottom"}'>Click me</button>
 */

import type {
    TooltipConfig,
    TooltipInstance,
    TooltipMethods,
    TooltipPosition,
    TooltipTrigger,
    TooltipTheme,
    TooltipGlobalSettings,
} from './types';

/**
 * Storage key for global tooltip settings
 */
const STORAGE_KEY = 'accelade:tooltip:settings';

/**
 * Default tooltip configuration (used only when no stored setting exists)
 */
const DEFAULT_CONFIG: Omit<TooltipConfig, 'id' | 'content'> = {
    position: 'top',
    trigger: 'hover',
    theme: 'dark', // Default when nothing is stored
    delay: 0,
    hideDelay: 0,
    arrow: true,
    interactive: false,
    offset: 8,
    maxWidth: '320px',
    rtl: false,
};

/**
 * Active tooltip instances
 */
const instances: Map<string, TooltipInstance> = new Map();

/**
 * Timers for delayed show/hide
 */
const showTimers: Map<string, number> = new Map();
const hideTimers: Map<string, number> = new Map();

/**
 * Get global settings from storage
 */
function getGlobalSettings(): Partial<TooltipGlobalSettings> {
    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            return JSON.parse(stored);
        }
    } catch {
        // Ignore storage errors
    }
    return {};
}

/**
 * Save global settings to storage
 */
function saveGlobalSettings(settings: Partial<TooltipGlobalSettings>): void {
    try {
        const current = getGlobalSettings();
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ ...current, ...settings }));
    } catch {
        // Ignore storage errors
    }
}

/**
 * Detect RTL from document
 */
function detectRTL(): boolean {
    return document.documentElement.dir === 'rtl' ||
        document.body.dir === 'rtl' ||
        getComputedStyle(document.body).direction === 'rtl';
}

/**
 * Detect theme from system preference
 */
function detectTheme(): 'light' | 'dark' {
    if (typeof window !== 'undefined' && window.matchMedia) {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return 'light';
}

/**
 * Get effective theme (resolve 'auto' to actual theme)
 */
function getEffectiveTheme(theme: TooltipTheme): 'light' | 'dark' {
    if (theme === 'auto') {
        // Check if page has dark mode class
        if (document.documentElement.classList.contains('dark')) {
            return 'dark';
        }
        return detectTheme();
    }
    return theme;
}

/**
 * Mirror position for RTL
 */
function mirrorPositionForRTL(position: TooltipPosition): TooltipPosition {
    const mirrorMap: Record<string, TooltipPosition> = {
        'left': 'right',
        'left-start': 'right-start',
        'left-end': 'right-end',
        'right': 'left',
        'right-start': 'left-start',
        'right-end': 'left-end',
        'top-start': 'top-end',
        'top-end': 'top-start',
        'bottom-start': 'bottom-end',
        'bottom-end': 'bottom-start',
    };
    return mirrorMap[position] || position;
}

/**
 * Parse tooltip attribute value
 * Supports: "Simple text" or '{"content": "text", "theme": "light"}'
 */
function parseAttributeValue(value: string): Partial<TooltipConfig> {
    const trimmed = value.trim();

    // Try parsing as JSON first
    if (trimmed.startsWith('{')) {
        try {
            return JSON.parse(trimmed);
        } catch {
            // Not valid JSON, treat as plain text
        }
    }

    // Plain text content
    return { content: trimmed };
}

/**
 * Parse tooltip configuration from element
 *
 * Priority order:
 * 1. Explicit attribute value (a-tooltip='{"theme": "light"}')
 * 2. Stored global settings from localStorage
 * 3. Default configuration
 */
function parseConfig(element: HTMLElement): TooltipConfig {
    const id = element.id || `tooltip-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    // Get attribute value
    const attrValue = element.getAttribute('a-tooltip') || '';
    const parsed = parseAttributeValue(attrValue);

    // Get global settings from storage (these override defaults)
    const globalSettings = getGlobalSettings();

    // Detect RTL if not explicitly set
    const rtl = parsed.rtl ?? detectRTL();

    // Theme priority: explicit > stored > default
    // If theme is not set in attribute, use stored theme from localStorage
    const theme = parsed.theme ?? globalSettings.theme ?? DEFAULT_CONFIG.theme;

    return {
        id,
        content: parsed.content || '',
        position: parsed.position ?? globalSettings.position ?? DEFAULT_CONFIG.position,
        trigger: parsed.trigger ?? DEFAULT_CONFIG.trigger,
        theme,
        delay: parsed.delay ?? globalSettings.delay ?? DEFAULT_CONFIG.delay,
        hideDelay: parsed.hideDelay ?? DEFAULT_CONFIG.hideDelay,
        arrow: parsed.arrow ?? DEFAULT_CONFIG.arrow,
        interactive: parsed.interactive ?? DEFAULT_CONFIG.interactive,
        offset: parsed.offset ?? DEFAULT_CONFIG.offset,
        maxWidth: parsed.maxWidth ?? globalSettings.maxWidth ?? DEFAULT_CONFIG.maxWidth,
        rtl,
        storageKey: parsed.storageKey,
    };
}

/**
 * Get theme classes for tooltip
 */
function getThemeClasses(theme: 'light' | 'dark'): string {
    if (theme === 'light') {
        return 'bg-white text-gray-900 border border-gray-200 shadow-lg dark:bg-gray-100 dark:text-gray-900';
    }
    return 'bg-gray-900 text-white shadow-lg dark:bg-gray-800';
}

/**
 * Get arrow classes for theme
 */
function getArrowThemeClasses(theme: 'light' | 'dark', position: TooltipPosition): string {
    const isLight = theme === 'light';

    // Determine which side the arrow points to
    const isTop = position.startsWith('top');
    const isBottom = position.startsWith('bottom');
    const isLeft = position.startsWith('left');
    const isRight = position.startsWith('right');

    if (isLight) {
        if (isTop) return 'border-t-white dark:border-t-gray-100';
        if (isBottom) return 'border-b-white dark:border-b-gray-100';
        if (isLeft) return 'border-l-white dark:border-l-gray-100';
        if (isRight) return 'border-r-white dark:border-r-gray-100';
    } else {
        if (isTop) return 'border-t-gray-900 dark:border-t-gray-800';
        if (isBottom) return 'border-b-gray-900 dark:border-b-gray-800';
        if (isLeft) return 'border-l-gray-900 dark:border-l-gray-800';
        if (isRight) return 'border-r-gray-900 dark:border-r-gray-800';
    }

    return '';
}

/**
 * Get position classes for tooltip
 */
function getPositionClasses(position: TooltipPosition, rtl: boolean): string {
    const effectivePosition = rtl ? mirrorPositionForRTL(position) : position;

    const classes: Record<TooltipPosition, string> = {
        'top': 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'top-start': 'bottom-full left-0 mb-2',
        'top-end': 'bottom-full right-0 mb-2',
        'bottom': 'top-full left-1/2 -translate-x-1/2 mt-2',
        'bottom-start': 'top-full left-0 mt-2',
        'bottom-end': 'top-full right-0 mt-2',
        'left': 'right-full top-1/2 -translate-y-1/2 mr-2',
        'left-start': 'right-full top-0 mr-2',
        'left-end': 'right-full bottom-0 mr-2',
        'right': 'left-full top-1/2 -translate-y-1/2 ml-2',
        'right-start': 'left-full top-0 ml-2',
        'right-end': 'left-full bottom-0 ml-2',
    };

    return classes[effectivePosition] || classes['top'];
}

/**
 * Get arrow position classes
 */
function getArrowPositionClasses(position: TooltipPosition, rtl: boolean): string {
    const effectivePosition = rtl ? mirrorPositionForRTL(position) : position;

    const classes: Record<TooltipPosition, string> = {
        'top': 'top-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-b-transparent',
        'top-start': 'top-full left-3 border-l-transparent border-r-transparent border-b-transparent',
        'top-end': 'top-full right-3 border-l-transparent border-r-transparent border-b-transparent',
        'bottom': 'bottom-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-t-transparent',
        'bottom-start': 'bottom-full left-3 border-l-transparent border-r-transparent border-t-transparent',
        'bottom-end': 'bottom-full right-3 border-l-transparent border-r-transparent border-t-transparent',
        'left': 'left-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-r-transparent',
        'left-start': 'left-full top-2 border-t-transparent border-b-transparent border-r-transparent',
        'left-end': 'left-full bottom-2 border-t-transparent border-b-transparent border-r-transparent',
        'right': 'right-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-l-transparent',
        'right-start': 'right-full top-2 border-t-transparent border-b-transparent border-l-transparent',
        'right-end': 'right-full bottom-2 border-t-transparent border-b-transparent border-l-transparent',
    };

    return classes[effectivePosition] || classes['top'];
}

/**
 * Create tooltip element
 */
function createTooltipElement(config: TooltipConfig): HTMLElement {
    const effectiveTheme = getEffectiveTheme(config.theme);

    const tooltip = document.createElement('div');
    tooltip.className = `absolute z-50 px-3 py-2 text-sm font-medium rounded-lg transition-opacity duration-150 ${getThemeClasses(effectiveTheme)} ${getPositionClasses(config.position, config.rtl)}`;
    tooltip.style.opacity = '0';
    tooltip.setAttribute('role', 'tooltip');
    tooltip.setAttribute('dir', config.rtl ? 'rtl' : 'ltr');

    // Handle width
    if (config.maxWidth === 'none' || config.maxWidth === 'full') {
        tooltip.style.maxWidth = 'none';
        tooltip.style.width = 'max-content';
    } else if (config.maxWidth) {
        tooltip.style.maxWidth = config.maxWidth;
    }

    // Add content
    const content = document.createElement('span');
    content.textContent = config.content;
    tooltip.appendChild(content);

    // Add arrow if enabled
    if (config.arrow) {
        const arrow = document.createElement('div');
        arrow.className = `absolute w-0 h-0 border-4 ${getArrowPositionClasses(config.position, config.rtl)} ${getArrowThemeClasses(effectiveTheme, config.position)}`;
        tooltip.appendChild(arrow);
    }

    // Make interactive tooltips hoverable
    if (config.interactive) {
        tooltip.classList.add('pointer-events-auto');
    } else {
        tooltip.classList.add('pointer-events-none');
    }

    return tooltip;
}

/**
 * Clear timers for an instance
 */
function clearTimers(id: string): void {
    const showTimer = showTimers.get(id);
    const hideTimer = hideTimers.get(id);

    if (showTimer) {
        clearTimeout(showTimer);
        showTimers.delete(id);
    }

    if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimers.delete(id);
    }
}

/**
 * Dispatch tooltip event
 */
function dispatchTooltipEvent(element: HTMLElement, type: string, id: string): void {
    const detail = { type, id };
    element.dispatchEvent(new CustomEvent('tooltip', { detail, bubbles: true }));
    document.dispatchEvent(new CustomEvent(`accelade:tooltip:${type}`, { detail }));
}

/**
 * Initialize tooltip from a-tooltip attribute
 */
export function initTooltip(element: HTMLElement): TooltipInstance | undefined {
    // Don't re-initialize
    if (element.hasAttribute('data-tooltip-initialized')) {
        const existingId = element.getAttribute('data-tooltip-id');
        if (existingId) {
            return instances.get(existingId);
        }
    }

    const config = parseConfig(element);

    // Mark as initialized
    element.setAttribute('data-tooltip-initialized', 'true');
    element.setAttribute('data-tooltip-id', config.id);

    // Ensure element has relative positioning for tooltip placement
    const computedStyle = getComputedStyle(element);
    if (computedStyle.position === 'static') {
        element.style.position = 'relative';
    }

    let tooltipElement: HTMLElement | null = null;
    let isShowing = false;
    let currentTheme = config.theme;
    let currentPosition = config.position;

    /**
     * Show the tooltip
     */
    const show = (): void => {
        if (isShowing || !config.content) return;

        clearTimers(config.id);

        const doShow = (): void => {
            isShowing = true;

            // Create tooltip element if not exists
            if (!tooltipElement) {
                tooltipElement = createTooltipElement({
                    ...config,
                    theme: currentTheme,
                    position: currentPosition,
                });
                element.appendChild(tooltipElement);
            }

            // Show with animation
            requestAnimationFrame(() => {
                if (tooltipElement) {
                    tooltipElement.style.opacity = '1';
                }
            });

            // Dispatch event
            dispatchTooltipEvent(element, 'show', config.id);
        };

        if (config.delay > 0) {
            const timer = window.setTimeout(doShow, config.delay);
            showTimers.set(config.id, timer);
        } else {
            doShow();
        }
    };

    /**
     * Hide the tooltip
     */
    const hide = (): void => {
        if (!isShowing) return;

        clearTimers(config.id);

        const doHide = (): void => {
            if (tooltipElement) {
                tooltipElement.style.opacity = '0';

                setTimeout(() => {
                    if (tooltipElement && tooltipElement.parentNode) {
                        tooltipElement.parentNode.removeChild(tooltipElement);
                        tooltipElement = null;
                    }
                }, 150);
            }

            isShowing = false;

            // Dispatch event
            dispatchTooltipEvent(element, 'hide', config.id);
        };

        if (config.hideDelay > 0) {
            const timer = window.setTimeout(doHide, config.hideDelay);
            hideTimers.set(config.id, timer);
        } else {
            doHide();
        }
    };

    /**
     * Toggle the tooltip
     */
    const toggle = (): void => {
        if (isShowing) {
            hide();
        } else {
            show();
        }
    };

    /**
     * Set tooltip content
     */
    const setContent = (content: string): void => {
        config.content = content;

        if (tooltipElement) {
            const contentEl = tooltipElement.querySelector('span');
            if (contentEl) {
                contentEl.textContent = content;
            }
        }
    };

    /**
     * Set tooltip theme
     */
    const setTheme = (theme: TooltipTheme): void => {
        currentTheme = theme;

        // Persist to storage if using storage key
        if (config.storageKey) {
            saveGlobalSettings({ theme });
        }

        // Recreate tooltip if visible
        if (isShowing && tooltipElement) {
            const parent = tooltipElement.parentNode;
            if (parent) {
                parent.removeChild(tooltipElement);
                tooltipElement = createTooltipElement({
                    ...config,
                    theme: currentTheme,
                    position: currentPosition,
                });
                tooltipElement.style.opacity = '1';
                element.appendChild(tooltipElement);
            }
        }
    };

    /**
     * Set tooltip position
     */
    const setPosition = (position: TooltipPosition): void => {
        currentPosition = position;

        // Recreate tooltip if visible
        if (isShowing && tooltipElement) {
            const parent = tooltipElement.parentNode;
            if (parent) {
                parent.removeChild(tooltipElement);
                tooltipElement = createTooltipElement({
                    ...config,
                    theme: currentTheme,
                    position: currentPosition,
                });
                tooltipElement.style.opacity = '1';
                element.appendChild(tooltipElement);
            }
        }
    };

    /**
     * Check if visible
     */
    const isVisible = (): boolean => isShowing;

    /**
     * Setup event listeners based on trigger
     */
    const setupTriggers = (): (() => void) => {
        const cleanups: (() => void)[] = [];

        if (config.trigger === 'hover') {
            const handleMouseEnter = (): void => show();
            const handleMouseLeave = (): void => {
                if (config.interactive && tooltipElement) {
                    // Delay hide to allow hovering tooltip
                    setTimeout(() => {
                        if (!tooltipElement?.matches(':hover') && !element.matches(':hover')) {
                            hide();
                        }
                    }, 100);
                } else {
                    hide();
                }
            };

            element.addEventListener('mouseenter', handleMouseEnter);
            element.addEventListener('mouseleave', handleMouseLeave);
            cleanups.push(() => {
                element.removeEventListener('mouseenter', handleMouseEnter);
                element.removeEventListener('mouseleave', handleMouseLeave);
            });
        }

        if (config.trigger === 'focus') {
            const handleFocus = (): void => show();
            const handleBlur = (): void => hide();

            element.addEventListener('focus', handleFocus);
            element.addEventListener('blur', handleBlur);
            cleanups.push(() => {
                element.removeEventListener('focus', handleFocus);
                element.removeEventListener('blur', handleBlur);
            });
        }

        if (config.trigger === 'click') {
            const handleClick = (e: MouseEvent): void => {
                e.stopPropagation();
                toggle();
            };

            const handleClickOutside = (e: MouseEvent): void => {
                if (isShowing && !element.contains(e.target as Node)) {
                    hide();
                }
            };

            element.addEventListener('click', handleClick);
            document.addEventListener('click', handleClickOutside);
            cleanups.push(() => {
                element.removeEventListener('click', handleClick);
                document.removeEventListener('click', handleClickOutside);
            });
        }

        // Listen for theme changes
        if (config.theme === 'auto') {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const handleThemeChange = (): void => {
                if (isShowing) {
                    setTheme('auto'); // This will recreate with new theme
                }
            };
            mediaQuery.addEventListener('change', handleThemeChange);
            cleanups.push(() => mediaQuery.removeEventListener('change', handleThemeChange));
        }

        return () => {
            cleanups.forEach(fn => fn());
        };
    };

    // Setup triggers and store cleanup
    const cleanupTriggers = setupTriggers();

    /**
     * Dispose
     */
    const dispose = (): void => {
        clearTimers(config.id);
        cleanupTriggers();

        if (tooltipElement && tooltipElement.parentNode) {
            tooltipElement.parentNode.removeChild(tooltipElement);
        }

        element.removeAttribute('data-tooltip-initialized');
        element.removeAttribute('data-tooltip-id');
        instances.delete(config.id);
    };

    const instance: TooltipInstance = {
        id: config.id,
        config,
        element,
        get tooltipElement() { return tooltipElement; },
        show,
        hide,
        toggle,
        setContent,
        setTheme,
        setPosition,
        isVisible,
        dispose,
    };

    instances.set(config.id, instance);

    return instance;
}

/**
 * Initialize all tooltips on the page
 */
export function initAllTooltips(): void {
    const elements = document.querySelectorAll<HTMLElement>('[a-tooltip]');
    elements.forEach(el => initTooltip(el));
}

/**
 * Create tooltip methods for template usage (when used with accelade component)
 */
export function createTooltipMethods(instance: TooltipInstance): TooltipMethods {
    return {
        showTooltip: instance.show,
        hideTooltip: instance.hide,
        toggleTooltip: instance.toggle,
        setTooltipContent: instance.setContent,
        setTooltipTheme: instance.setTheme,
        setTooltipPosition: instance.setPosition,
    };
}

/**
 * Get a tooltip instance by ID
 */
export function getTooltipInstance(id: string): TooltipInstance | undefined {
    return instances.get(id);
}

/**
 * Get global tooltip settings
 */
export function getTooltipSettings(): Partial<TooltipGlobalSettings> {
    return getGlobalSettings();
}

/**
 * Set global tooltip settings
 */
export function setTooltipSettings(settings: Partial<TooltipGlobalSettings>): void {
    saveGlobalSettings(settings);
}

// Legacy exports for backward compatibility
export const createTooltip = initTooltip;

/**
 * TooltipFactory namespace for module exports
 */
export const TooltipFactory = {
    init: initTooltip,
    initAll: initAllTooltips,
    create: initTooltip,
    createMethods: createTooltipMethods,
    getInstance: getTooltipInstance,
    getSettings: getTooltipSettings,
    setSettings: setTooltipSettings,
};
