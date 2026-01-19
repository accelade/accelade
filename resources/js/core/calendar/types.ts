/**
 * Calendar Component Types
 *
 * Types for the Calendar component which wraps @event-calendar/core
 * to provide a full-featured event calendar with multiple views.
 */

/**
 * Calendar view types
 */
export type CalendarView =
    | 'dayGridDay'
    | 'dayGridWeek'
    | 'dayGridMonth'
    | 'timeGridDay'
    | 'timeGridWeek'
    | 'listDay'
    | 'listWeek'
    | 'listMonth'
    | 'listYear'
    | 'resourceTimeGridDay'
    | 'resourceTimeGridWeek'
    | 'resourceTimelineDay'
    | 'resourceTimelineWeek'
    | 'resourceTimelineMonth';

/**
 * Duration object for calendar configuration
 */
export interface CalendarDuration {
    years?: number;
    months?: number;
    weeks?: number;
    days?: number;
    hours?: number;
    minutes?: number;
    seconds?: number;
}

/**
 * Calendar event object
 */
export interface CalendarEvent {
    /** Unique identifier */
    id: string | number;
    /** Event title */
    title: string;
    /** Start date/time (Date object or ISO string) */
    start: Date | string;
    /** End date/time (Date object or ISO string) */
    end?: Date | string;
    /** Whether the event is all-day */
    allDay?: boolean;
    /** Resource IDs for resource views */
    resourceIds?: (string | number)[];
    /** Single resource ID */
    resourceId?: string | number;
    /** Display mode: 'auto', 'background', 'none' */
    display?: 'auto' | 'background' | 'none';
    /** Whether the event is editable (drag/resize) */
    editable?: boolean;
    /** Whether the event start is editable */
    startEditable?: boolean;
    /** Whether the event duration is editable */
    durationEditable?: boolean;
    /** Background color */
    backgroundColor?: string;
    /** Text color */
    textColor?: string;
    /** Shorthand for both background and text color */
    color?: string;
    /** CSS class names */
    classNames?: string | string[];
    /** CSS styles */
    styles?: Record<string, string>;
    /** Custom properties */
    extendedProps?: Record<string, unknown>;
}

/**
 * Calendar resource object
 */
export interface CalendarResource {
    /** Unique identifier */
    id: string | number;
    /** Resource title/name */
    title: string;
    /** Default event background color for this resource */
    eventBackgroundColor?: string;
    /** Default event text color for this resource */
    eventTextColor?: string;
    /** Custom properties */
    extendedProps?: Record<string, unknown>;
    /** Child resources (for hierarchical resources) */
    children?: CalendarResource[];
}

/**
 * Event source for dynamic event loading
 */
export interface CalendarEventSource {
    /** URL to fetch events from */
    url?: string;
    /** HTTP method */
    method?: 'GET' | 'POST';
    /** Extra parameters to send */
    extraParams?: Record<string, string | number | boolean> | (() => Record<string, string | number | boolean>);
    /** Function to fetch events */
    events?: (info: { start: Date; end: Date; startStr: string; endStr: string }) => CalendarEvent[] | Promise<CalendarEvent[]>;
}

/**
 * Header toolbar configuration
 */
export interface CalendarHeaderToolbar {
    start?: string;
    center?: string;
    end?: string;
}

/**
 * Valid date range constraint
 */
export interface CalendarValidRange {
    start?: Date | string;
    end?: Date | string;
}

/**
 * Custom button configuration
 */
export interface CalendarCustomButton {
    text: string;
    click?: () => void;
    active?: boolean;
}

/**
 * Theme configuration with CSS variables
 */
export interface CalendarTheme {
    /** Calendar text color */
    textColor?: string;
    /** Calendar background color */
    backgroundColor?: string;
    /** Primary color for highlights */
    primaryColor?: string;
    /** Border color */
    borderColor?: string;
    /** Today highlight color */
    todayColor?: string;
    /** Event default background */
    eventBackgroundColor?: string;
    /** Event default text color */
    eventTextColor?: string;
    /** Custom CSS class for the calendar */
    className?: string;
}

/**
 * Calendar configuration parsed from element attributes
 */
export interface CalendarConfig {
    /** Unique identifier for the calendar instance */
    id: string;
    /** Initial view to display */
    view: CalendarView;
    /** Initial date to display */
    date: Date | string;
    /** Calendar height (CSS value or 'auto') */
    height: string;
    /** Array of events */
    events: CalendarEvent[];
    /** Event sources for dynamic loading */
    eventSources: CalendarEventSource[];
    /** Resources for resource views */
    resources: CalendarResource[];
    /** Header toolbar configuration */
    headerToolbar: CalendarHeaderToolbar;
    /** Whether events are editable (drag/resize) */
    editable: boolean;
    /** Whether date/time selection is enabled */
    selectable: boolean;
    /** Show current time indicator */
    nowIndicator: boolean;
    /** Show all-day section */
    allDaySlot: boolean;
    /** First day of week (0=Sunday, 1=Monday) */
    firstDay: number;
    /** Locale for date formatting */
    locale: string;
    /** Time slot duration */
    slotDuration: string;
    /** Earliest time slot */
    slotMinTime: string;
    /** Latest time slot */
    slotMaxTime: string;
    /** Initial scroll position */
    scrollTime: string;
    /** Slot height in pixels */
    slotHeight: number;
    /** Navigation range limits */
    validRange: CalendarValidRange | null;
    /** Days to hide (0=Sunday, 1=Monday, etc.) */
    hiddenDays: number[];
    /** View duration */
    duration: CalendarDuration | null;
    /** Default event background color */
    eventBackgroundColor: string;
    /** Default event text color */
    eventTextColor: string;
    /** Theme configuration */
    theme: CalendarTheme;
    /** Custom button definitions */
    customButtons: Record<string, CalendarCustomButton>;
    /** Button text overrides */
    buttonText: Record<string, string>;
    /** Enable dark mode */
    darkMode: boolean | 'auto';
    /** Show pointer cursor on events */
    pointer: boolean;
    /** Enable lazy fetching of events */
    lazyFetching: boolean;
}

/**
 * Calendar state
 */
export interface CalendarState {
    /** Currently displayed view */
    currentView: CalendarView;
    /** Currently displayed date */
    currentDate: Date;
    /** Currently selected date range */
    selectedRange: { start: Date; end: Date } | null;
    /** Events array */
    events: CalendarEvent[];
    /** Whether calendar is loading events */
    isLoading: boolean;
}

/**
 * Event info passed to callbacks
 */
export interface CalendarEventInfo {
    event: CalendarEvent;
    el: HTMLElement;
    view: {
        type: CalendarView;
        title: string;
        currentStart: Date;
        currentEnd: Date;
    };
}

/**
 * Date click info
 */
export interface CalendarDateClickInfo {
    date: Date;
    dateStr: string;
    allDay: boolean;
    dayEl: HTMLElement;
    resource?: CalendarResource;
}

/**
 * Selection info
 */
export interface CalendarSelectInfo {
    start: Date;
    end: Date;
    startStr: string;
    endStr: string;
    allDay: boolean;
    resource?: CalendarResource;
}

/**
 * Event drop info
 */
export interface CalendarEventDropInfo {
    event: CalendarEvent;
    oldEvent: CalendarEvent;
    delta: CalendarDuration;
    revert: () => void;
}

/**
 * Event resize info
 */
export interface CalendarEventResizeInfo {
    event: CalendarEvent;
    oldEvent: CalendarEvent;
    startDelta: CalendarDuration;
    endDelta: CalendarDuration;
    revert: () => void;
}

/**
 * Calendar instance returned by the factory
 */
export interface CalendarInstance {
    /** Unique identifier */
    id: string;
    /** Configuration */
    config: CalendarConfig;
    /** The DOM element */
    element: HTMLElement;
    /** Get current view type */
    getView: () => CalendarView;
    /** Change view */
    setView: (view: CalendarView) => void;
    /** Get current date */
    getDate: () => Date;
    /** Go to specific date */
    gotoDate: (date: Date | string) => void;
    /** Go to previous period */
    prev: () => void;
    /** Go to next period */
    next: () => void;
    /** Go to today */
    today: () => void;
    /** Get all events */
    getEvents: () => CalendarEvent[];
    /** Get event by ID */
    getEventById: (id: string | number) => CalendarEvent | null;
    /** Add event */
    addEvent: (event: CalendarEvent) => void;
    /** Update event */
    updateEvent: (event: CalendarEvent) => void;
    /** Remove event by ID */
    removeEventById: (id: string | number) => void;
    /** Refetch events from sources */
    refetchEvents: () => void;
    /** Get option value */
    getOption: (name: string) => unknown;
    /** Set option value */
    setOption: (name: string, value: unknown) => void;
    /** Clear selection */
    unselect: () => void;
    /** Get date from screen coordinates */
    dateFromPoint: (x: number, y: number) => { date: Date; allDay: boolean; resource?: CalendarResource } | null;
    /** Dispose the calendar instance */
    dispose: () => void;
}

/**
 * Calendar methods exposed to templates
 */
export interface CalendarMethods {
    /** Get current view */
    getCalendarView: () => CalendarView;
    /** Set view */
    setCalendarView: (view: CalendarView) => void;
    /** Get current date */
    getCalendarDate: () => Date;
    /** Go to date */
    gotoCalendarDate: (date: Date | string) => void;
    /** Navigate previous */
    calendarPrev: () => void;
    /** Navigate next */
    calendarNext: () => void;
    /** Go to today */
    calendarToday: () => void;
    /** Get events */
    getCalendarEvents: () => CalendarEvent[];
    /** Add event */
    addCalendarEvent: (event: CalendarEvent) => void;
    /** Update event */
    updateCalendarEvent: (event: CalendarEvent) => void;
    /** Remove event */
    removeCalendarEvent: (id: string | number) => void;
    /** Refetch events */
    refetchCalendarEvents: () => void;
}
