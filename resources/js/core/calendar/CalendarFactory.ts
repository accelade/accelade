/**
 * Calendar Factory
 *
 * Creates Calendar instances using @event-calendar/core.
 * Provides a declarative way to create full-featured event calendars
 * with multiple views, drag & drop, and resource support.
 */

// Event Calendar v5 - all plugins bundled in core
import {
    createCalendar,
    destroyCalendar,
    DayGrid,
    TimeGrid,
    List,
    ResourceTimeGrid,
    ResourceTimeline,
    Interaction,
} from '@event-calendar/core';

// Type for the Calendar instance (opaque object from mount())
type CalendarType = ReturnType<typeof createCalendar>;

import type { IStateAdapter } from '../../adapters/types';
import type {
    CalendarConfig,
    CalendarInstance,
    CalendarMethods,
    CalendarEvent,
    CalendarResource,
    CalendarView,
    CalendarHeaderToolbar,
    CalendarTheme,
    CalendarDuration,
    CalendarValidRange,
    CalendarEventSource,
    CalendarCustomButton,
    CalendarDateClickInfo,
    CalendarSelectInfo,
    CalendarEventDropInfo,
    CalendarEventResizeInfo,
    CalendarEventInfo,
} from './types';

// Import CSS
import '@event-calendar/core/index.css';

/**
 * Default calendar configuration
 */
const DEFAULT_CONFIG: Omit<CalendarConfig, 'id'> = {
    view: 'dayGridMonth',
    date: new Date(),
    height: 'auto',
    events: [],
    eventSources: [],
    resources: [],
    headerToolbar: {
        start: 'prev,next today',
        center: 'title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay',
    },
    editable: false,
    selectable: false,
    nowIndicator: true,
    allDaySlot: true,
    firstDay: 0,
    locale: 'en',
    slotDuration: '00:30:00',
    slotMinTime: '00:00:00',
    slotMaxTime: '24:00:00',
    scrollTime: '06:00:00',
    slotHeight: 24,
    validRange: null,
    hiddenDays: [],
    duration: null,
    eventBackgroundColor: '#3b82f6',
    eventTextColor: '#ffffff',
    theme: {},
    customButtons: {},
    buttonText: {},
    darkMode: false,
    pointer: true,
    lazyFetching: true,
};

/**
 * Active calendar instances
 */
const instances: Map<string, CalendarInstance> = new Map();

/**
 * Parse calendar configuration from element
 */
function parseConfig(element: HTMLElement): CalendarConfig {
    const id = element.dataset.calendarId ||
        `calendar-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    const configAttr = element.dataset.calendarConfig;
    let parsedConfig: Partial<CalendarConfig> = {};

    if (configAttr) {
        try {
            parsedConfig = JSON.parse(configAttr);
        } catch {
            console.warn('Calendar: Invalid config JSON');
        }
    }

    // Parse events from data attribute
    const eventsAttr = element.dataset.calendarEvents;
    let events: CalendarEvent[] = [];
    if (eventsAttr) {
        try {
            events = JSON.parse(eventsAttr);
        } catch {
            console.warn('Calendar: Invalid events JSON');
        }
    }

    // Parse resources from data attribute
    const resourcesAttr = element.dataset.calendarResources;
    let resources: CalendarResource[] = [];
    if (resourcesAttr) {
        try {
            resources = JSON.parse(resourcesAttr);
        } catch {
            console.warn('Calendar: Invalid resources JSON');
        }
    }

    // Parse date (handle string or Date)
    let date = parsedConfig.date ?? DEFAULT_CONFIG.date;
    if (typeof date === 'string') {
        date = new Date(date);
    }

    return {
        id,
        view: (parsedConfig.view as CalendarView) ?? DEFAULT_CONFIG.view,
        date,
        height: parsedConfig.height ?? DEFAULT_CONFIG.height,
        events: events.length > 0 ? events : (parsedConfig.events ?? DEFAULT_CONFIG.events),
        eventSources: parsedConfig.eventSources ?? DEFAULT_CONFIG.eventSources,
        resources: resources.length > 0 ? resources : (parsedConfig.resources ?? DEFAULT_CONFIG.resources),
        headerToolbar: parsedConfig.headerToolbar ?? DEFAULT_CONFIG.headerToolbar,
        editable: parsedConfig.editable ?? DEFAULT_CONFIG.editable,
        selectable: parsedConfig.selectable ?? DEFAULT_CONFIG.selectable,
        nowIndicator: parsedConfig.nowIndicator ?? DEFAULT_CONFIG.nowIndicator,
        allDaySlot: parsedConfig.allDaySlot ?? DEFAULT_CONFIG.allDaySlot,
        firstDay: parsedConfig.firstDay ?? DEFAULT_CONFIG.firstDay,
        locale: parsedConfig.locale ?? DEFAULT_CONFIG.locale,
        slotDuration: parsedConfig.slotDuration ?? DEFAULT_CONFIG.slotDuration,
        slotMinTime: parsedConfig.slotMinTime ?? DEFAULT_CONFIG.slotMinTime,
        slotMaxTime: parsedConfig.slotMaxTime ?? DEFAULT_CONFIG.slotMaxTime,
        scrollTime: parsedConfig.scrollTime ?? DEFAULT_CONFIG.scrollTime,
        slotHeight: parsedConfig.slotHeight ?? DEFAULT_CONFIG.slotHeight,
        validRange: parsedConfig.validRange ?? DEFAULT_CONFIG.validRange,
        hiddenDays: parsedConfig.hiddenDays ?? DEFAULT_CONFIG.hiddenDays,
        duration: parsedConfig.duration ?? DEFAULT_CONFIG.duration,
        eventBackgroundColor: parsedConfig.eventBackgroundColor ?? DEFAULT_CONFIG.eventBackgroundColor,
        eventTextColor: parsedConfig.eventTextColor ?? DEFAULT_CONFIG.eventTextColor,
        theme: parsedConfig.theme ?? DEFAULT_CONFIG.theme,
        customButtons: parsedConfig.customButtons ?? DEFAULT_CONFIG.customButtons,
        buttonText: parsedConfig.buttonText ?? DEFAULT_CONFIG.buttonText,
        darkMode: parsedConfig.darkMode ?? DEFAULT_CONFIG.darkMode,
        pointer: parsedConfig.pointer ?? DEFAULT_CONFIG.pointer,
        lazyFetching: parsedConfig.lazyFetching ?? DEFAULT_CONFIG.lazyFetching,
    };
}

/**
 * Get plugins based on configured views
 */
function getPlugins(config: CalendarConfig): unknown[] {
    const plugins: unknown[] = [];
    const view = config.view;

    // Add plugins based on view type
    if (view.includes('dayGrid')) {
        plugins.push(DayGrid);
    }
    if (view.includes('timeGrid')) {
        plugins.push(TimeGrid);
    }
    if (view.includes('list')) {
        plugins.push(List);
    }
    if (view.includes('resourceTimeGrid')) {
        plugins.push(ResourceTimeGrid);
    }
    if (view.includes('resourceTimeline')) {
        plugins.push(ResourceTimeline);
    }

    // Add interaction plugin if editable or selectable
    if (config.editable || config.selectable) {
        plugins.push(Interaction);
    }

    // Ensure at least DayGrid and TimeGrid are available for header toolbar views
    const toolbar = config.headerToolbar;
    const toolbarViews = `${toolbar.start || ''} ${toolbar.center || ''} ${toolbar.end || ''}`;

    if (toolbarViews.includes('dayGrid') && !plugins.includes(DayGrid)) {
        plugins.push(DayGrid);
    }
    if (toolbarViews.includes('timeGrid') && !plugins.includes(TimeGrid)) {
        plugins.push(TimeGrid);
    }
    if (toolbarViews.includes('list') && !plugins.includes(List)) {
        plugins.push(List);
    }

    return plugins;
}

/**
 * Detect if the page is in dark mode
 * Only returns true if the page explicitly has dark mode enabled via class or data attribute.
 * Does NOT fall back to system preference - that's handled separately.
 */
function isPageInDarkMode(): boolean {
    const htmlElement = document.documentElement;
    const bodyElement = document.body;

    // Check for class-based dark mode (Tailwind CSS / Filament style)
    if (htmlElement.classList.contains('dark') || bodyElement?.classList.contains('dark')) {
        return true;
    }

    // Check data attribute based dark mode
    if (htmlElement.dataset.theme === 'dark' || htmlElement.dataset.mode === 'dark') {
        return true;
    }

    // If no explicit dark class/attribute, assume light mode
    // Don't fall back to system preference - let the page control the theme
    return false;
}

/**
 * Light mode CSS variable values (from @event-calendar/core defaults)
 */
const LIGHT_MODE_CSS_VARS = {
    '--ec-color-400': 'oklch(70.4% 0 0)',
    '--ec-color-300': 'oklch(86.9% 0 0)',
    '--ec-color-200': 'oklch(92.8% 0 0)',
    '--ec-color-100': 'oklch(97% 0 0)',
    '--ec-color-50': 'oklch(98.5% 0 0)',
    '--ec-bg-color': '#fff',
    '--ec-today-bg-color': 'oklch(97.4% 0.014 103.054)',
    '--ec-highlight-color': 'oklch(93.1% 0.032 255.508)',
    '--ec-bg-event-opacity': '1',
};

/**
 * Dark mode CSS variable values (from @event-calendar/core)
 */
const DARK_MODE_CSS_VARS = {
    '--ec-color-400': 'oklch(43.9% 0 0)',
    '--ec-color-300': 'oklch(37.1% 0 0)',
    '--ec-color-200': 'oklch(26.9% 0 0)',
    '--ec-color-100': 'oklch(20.5% 0 0)',
    '--ec-color-50': 'oklch(14.5% 0 0)',
    '--ec-bg-color': 'oklch(20.5% 0 0)',
    '--ec-today-bg-color': 'oklch(28.6% 0.066 53.813)',
    '--ec-highlight-color': 'oklch(30.2% 0.056 229.695)',
    '--ec-bg-event-opacity': '0.5',
};

/**
 * Apply theme to calendar element
 */
function applyTheme(element: HTMLElement, theme: CalendarTheme, darkMode: boolean | 'auto'): void {
    // Determine if we should use dark mode
    let useDarkMode = false;

    if (darkMode === true) {
        useDarkMode = true;
    } else if (darkMode === 'auto') {
        useDarkMode = isPageInDarkMode();
    }
    // darkMode === false means light mode, useDarkMode stays false

    // Remove any existing dark mode classes
    element.classList.remove('ec-dark');
    element.classList.remove('ec-auto-dark');

    // Apply the appropriate CSS variables to override media query styles
    // This is necessary because the library uses @media (prefers-color-scheme: dark)
    // which would apply dark styles even when the page is in light mode
    const cssVars = useDarkMode ? DARK_MODE_CSS_VARS : LIGHT_MODE_CSS_VARS;
    for (const [key, value] of Object.entries(cssVars)) {
        element.style.setProperty(key, value);
    }

    // Apply dark mode class if in dark mode
    if (useDarkMode) {
        element.classList.add('ec-dark');
    }

    // Apply custom theme CSS variables (these override the defaults above)
    if (theme.textColor) {
        element.style.setProperty('--ec-text-color', theme.textColor);
    }
    if (theme.backgroundColor) {
        element.style.setProperty('--ec-bg-color', theme.backgroundColor);
    }
    if (theme.primaryColor) {
        element.style.setProperty('--ec-highlight-color', theme.primaryColor);
    }
    if (theme.borderColor) {
        element.style.setProperty('--ec-border-color', theme.borderColor);
    }
    if (theme.todayColor) {
        element.style.setProperty('--ec-today-bg-color', theme.todayColor);
    }
    if (theme.eventBackgroundColor) {
        element.style.setProperty('--ec-event-bg-color', theme.eventBackgroundColor);
    }
    if (theme.eventTextColor) {
        element.style.setProperty('--ec-event-text-color', theme.eventTextColor);
    }

    // Apply custom class
    if (theme.className) {
        element.classList.add(...theme.className.split(' '));
    }
}

/**
 * Create a Calendar instance
 */
export function createCalendarInstance(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): CalendarInstance | undefined {
    const config = parseConfig(element);

    // Initialize state
    if (stateAdapter.get('currentView') === undefined) {
        stateAdapter.set('currentView', config.view);
    }
    if (stateAdapter.get('currentDate') === undefined) {
        stateAdapter.set('currentDate', config.date);
    }
    if (stateAdapter.get('events') === undefined) {
        stateAdapter.set('events', config.events);
    }
    if (stateAdapter.get('isLoading') === undefined) {
        stateAdapter.set('isLoading', false);
    }
    if (stateAdapter.get('selectedRange') === undefined) {
        stateAdapter.set('selectedRange', null);
    }

    // Get plugins
    const plugins = getPlugins(config);

    // Apply theme
    applyTheme(element, config.theme, config.darkMode);

    // Build calendar options
    const calendarOptions: Record<string, unknown> = {
        view: config.view,
        date: config.date,
        height: config.height,
        events: config.events,
        eventSources: config.eventSources,
        resources: config.resources,
        headerToolbar: config.headerToolbar,
        editable: config.editable,
        selectable: config.selectable,
        nowIndicator: config.nowIndicator,
        allDaySlot: config.allDaySlot,
        firstDay: config.firstDay,
        locale: config.locale,
        slotDuration: config.slotDuration,
        slotMinTime: config.slotMinTime,
        slotMaxTime: config.slotMaxTime,
        scrollTime: config.scrollTime,
        slotHeight: config.slotHeight,
        hiddenDays: config.hiddenDays,
        eventBackgroundColor: config.eventBackgroundColor,
        eventTextColor: config.eventTextColor,
        pointer: config.pointer,
        lazyFetching: config.lazyFetching,
    };

    // Add optional configs
    if (config.validRange) {
        calendarOptions.validRange = config.validRange;
    }
    if (config.duration) {
        calendarOptions.duration = config.duration;
    }
    if (Object.keys(config.customButtons).length > 0) {
        calendarOptions.customButtons = config.customButtons;
    }
    if (Object.keys(config.buttonText).length > 0) {
        calendarOptions.buttonText = config.buttonText;
    }

    // Event callbacks
    calendarOptions.datesSet = (info: { view: { type: string; currentStart: Date; currentEnd: Date } }) => {
        stateAdapter.set('currentView', info.view.type);
        stateAdapter.set('currentDate', info.view.currentStart);

        dispatchCalendarEvent(element, 'datesSet', {
            id: config.id,
            view: info.view.type,
            start: info.view.currentStart,
            end: info.view.currentEnd,
        });
    };

    calendarOptions.dateClick = (info: CalendarDateClickInfo) => {
        dispatchCalendarEvent(element, 'dateClick', {
            id: config.id,
            date: info.date,
            dateStr: info.dateStr,
            allDay: info.allDay,
            resource: info.resource,
        });
    };

    calendarOptions.eventClick = (info: CalendarEventInfo) => {
        dispatchCalendarEvent(element, 'eventClick', {
            id: config.id,
            event: info.event,
        });
    };

    calendarOptions.select = (info: CalendarSelectInfo) => {
        stateAdapter.set('selectedRange', { start: info.start, end: info.end });

        dispatchCalendarEvent(element, 'select', {
            id: config.id,
            start: info.start,
            end: info.end,
            startStr: info.startStr,
            endStr: info.endStr,
            allDay: info.allDay,
            resource: info.resource,
        });
    };

    calendarOptions.unselect = () => {
        stateAdapter.set('selectedRange', null);

        dispatchCalendarEvent(element, 'unselect', {
            id: config.id,
        });
    };

    calendarOptions.eventDrop = (info: CalendarEventDropInfo) => {
        const events = stateAdapter.get('events') as CalendarEvent[];
        const updatedEvents = events.map(e =>
            e.id === info.event.id ? info.event : e
        );
        stateAdapter.set('events', updatedEvents);

        dispatchCalendarEvent(element, 'eventDrop', {
            id: config.id,
            event: info.event,
            oldEvent: info.oldEvent,
            delta: info.delta,
        });
    };

    calendarOptions.eventResize = (info: CalendarEventResizeInfo) => {
        const events = stateAdapter.get('events') as CalendarEvent[];
        const updatedEvents = events.map(e =>
            e.id === info.event.id ? info.event : e
        );
        stateAdapter.set('events', updatedEvents);

        dispatchCalendarEvent(element, 'eventResize', {
            id: config.id,
            event: info.event,
            oldEvent: info.oldEvent,
            startDelta: info.startDelta,
            endDelta: info.endDelta,
        });
    };

    calendarOptions.eventMouseEnter = (info: CalendarEventInfo) => {
        dispatchCalendarEvent(element, 'eventMouseEnter', {
            id: config.id,
            event: info.event,
        });
    };

    calendarOptions.eventMouseLeave = (info: CalendarEventInfo) => {
        dispatchCalendarEvent(element, 'eventMouseLeave', {
            id: config.id,
            event: info.event,
        });
    };

    calendarOptions.loading = (isLoading: boolean) => {
        stateAdapter.set('isLoading', isLoading);

        dispatchCalendarEvent(element, 'loading', {
            id: config.id,
            isLoading,
        });
    };

    // Create the calendar using the v5 API
    // createCalendar(target, plugins, options)
    let ec: CalendarType;
    try {
        ec = createCalendar(element, plugins, calendarOptions);
    } catch (error) {
        console.error('Calendar: Failed to create calendar', error);
        return undefined;
    }

    /**
     * Dispatch calendar event
     */
    function dispatchCalendarEvent(el: HTMLElement, type: string, detail: Record<string, unknown>): void {
        el.dispatchEvent(new CustomEvent(`calendar${type}`, { detail, bubbles: true }));
        document.dispatchEvent(new CustomEvent(`accelade:calendar:${type}`, { detail }));
    }

    /**
     * Get current view
     */
    const getView = (): CalendarView => {
        const view = ec.getOption('view');
        return view as CalendarView;
    };

    /**
     * Set view
     */
    const setView = (view: CalendarView): void => {
        ec.setOption('view', view);
    };

    /**
     * Get current date
     */
    const getDate = (): Date => {
        return ec.getOption('date') as Date;
    };

    /**
     * Go to date
     */
    const gotoDate = (date: Date | string): void => {
        ec.setOption('date', typeof date === 'string' ? new Date(date) : date);
    };

    /**
     * Go to previous period
     */
    const prev = (): void => {
        ec.prev();
    };

    /**
     * Go to next period
     */
    const next = (): void => {
        ec.next();
    };

    /**
     * Go to today
     */
    const today = (): void => {
        ec.setOption('date', new Date());
    };

    /**
     * Get all events
     */
    const getEvents = (): CalendarEvent[] => {
        return ec.getEvents() as CalendarEvent[];
    };

    /**
     * Get event by ID
     */
    const getEventById = (id: string | number): CalendarEvent | null => {
        return ec.getEventById(String(id)) as CalendarEvent | null;
    };

    /**
     * Add event
     */
    const addEvent = (event: CalendarEvent): void => {
        ec.addEvent(event as unknown as { [key: string]: unknown });
        const events = stateAdapter.get('events') as CalendarEvent[];
        stateAdapter.set('events', [...events, event]);
    };

    /**
     * Update event
     */
    const updateEvent = (event: CalendarEvent): void => {
        ec.updateEvent(event as unknown as { [key: string]: unknown });
        const events = stateAdapter.get('events') as CalendarEvent[];
        const updatedEvents = events.map(e => e.id === event.id ? event : e);
        stateAdapter.set('events', updatedEvents);
    };

    /**
     * Remove event by ID
     */
    const removeEventById = (id: string | number): void => {
        ec.removeEventById(String(id));
        const events = stateAdapter.get('events') as CalendarEvent[];
        stateAdapter.set('events', events.filter(e => e.id !== id));
    };

    /**
     * Refetch events
     */
    const refetchEvents = (): void => {
        ec.refetchEvents();
    };

    /**
     * Get option
     */
    const getOption = (name: string): unknown => {
        return ec.getOption(name);
    };

    /**
     * Set option
     */
    const setOption = (name: string, value: unknown): void => {
        ec.setOption(name, value);
    };

    /**
     * Clear selection
     */
    const unselect = (): void => {
        ec.unselect();
    };

    /**
     * Get date from point
     */
    const dateFromPoint = (x: number, y: number): { date: Date; allDay: boolean; resource?: CalendarResource } | null => {
        const result = ec.dateFromPoint(x, y);
        return result ? { date: result, allDay: false } : null;
    };

    /**
     * Dispose
     */
    const dispose = (): void => {
        destroyCalendar(ec);
        instances.delete(config.id);
    };

    const instance: CalendarInstance = {
        id: config.id,
        config,
        element,
        getView,
        setView,
        getDate,
        gotoDate,
        prev,
        next,
        today,
        getEvents,
        getEventById,
        addEvent,
        updateEvent,
        removeEventById,
        refetchEvents,
        getOption,
        setOption,
        unselect,
        dateFromPoint,
        dispose,
    };

    instances.set(config.id, instance);

    return instance;
}

/**
 * Create calendar methods for template usage
 */
export function createCalendarMethods(instance: CalendarInstance): CalendarMethods {
    return {
        getCalendarView: instance.getView,
        setCalendarView: instance.setView,
        getCalendarDate: instance.getDate,
        gotoCalendarDate: instance.gotoDate,
        calendarPrev: instance.prev,
        calendarNext: instance.next,
        calendarToday: instance.today,
        getCalendarEvents: instance.getEvents,
        addCalendarEvent: instance.addEvent,
        updateCalendarEvent: instance.updateEvent,
        removeCalendarEvent: instance.removeEventById,
        refetchCalendarEvents: instance.refetchEvents,
    };
}

/**
 * Get a calendar instance by ID
 */
export function getCalendarInstance(id: string): CalendarInstance | undefined {
    return instances.get(id);
}

/**
 * CalendarFactory namespace for module exports
 */
export const CalendarFactory = {
    parseConfig,
    create: createCalendarInstance,
    createMethods: createCalendarMethods,
    getInstance: getCalendarInstance,
};
