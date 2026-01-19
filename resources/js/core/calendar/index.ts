/**
 * Calendar Module
 *
 * Exports the Calendar factory and types for creating full-featured
 * event calendars using @event-calendar/core.
 */

export { CalendarFactory, getCalendarInstance } from './CalendarFactory';
export type {
    CalendarView,
    CalendarDuration,
    CalendarEvent,
    CalendarResource,
    CalendarEventSource,
    CalendarHeaderToolbar,
    CalendarValidRange,
    CalendarCustomButton,
    CalendarTheme,
    CalendarConfig,
    CalendarState,
    CalendarEventInfo,
    CalendarDateClickInfo,
    CalendarSelectInfo,
    CalendarEventDropInfo,
    CalendarEventResizeInfo,
    CalendarInstance,
    CalendarMethods,
} from './types';
