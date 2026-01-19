/**
 * Type declarations for @event-calendar/core v5
 * All plugins are bundled in the core package
 */

declare module '@event-calendar/core' {
    export interface CalendarOptions {
        view?: string;
        date?: Date;
        events?: CalendarEvent[];
        plugins?: unknown[];
        [key: string]: unknown;
    }

    export interface CalendarEvent {
        id?: string | number;
        start: Date | string;
        end?: Date | string;
        title?: string;
        allDay?: boolean;
        [key: string]: unknown;
    }

    export interface CalendarInstance {
        setOption(name: string, value: unknown): void;
        getOption(name: string): unknown;
        getEvents(): CalendarEvent[];
        getEventById(id: string | number): CalendarEvent | null;
        addEvent(event: { [key: string]: unknown }): void;
        updateEvent(event: { [key: string]: unknown }): void;
        removeEventById(id: string | number): void;
        refetchEvents(): void;
        unselect(): void;
        dateFromPoint(x: number, y: number): Date | null;
        getView(): { type: string; title: string };
        prev(): void;
        next(): void;
    }

    // v5 API - functions instead of class
    export function createCalendar(
        target: HTMLElement,
        plugins: unknown[],
        options: Record<string, unknown>
    ): CalendarInstance;

    export function destroyCalendar(calendar: CalendarInstance): void;

    // Plugins (all bundled in core for v5)
    export const DayGrid: unknown;
    export const TimeGrid: unknown;
    export const List: unknown;
    export const ResourceTimeGrid: unknown;
    export const ResourceTimeline: unknown;
    export const Interaction: unknown;
}

// Keep the old module declarations for backwards compatibility
// (in case someone still imports from separate packages)
declare module '@event-calendar/day-grid' {
    const DayGrid: unknown;
    export default DayGrid;
}

declare module '@event-calendar/time-grid' {
    const TimeGrid: unknown;
    export default TimeGrid;
}

declare module '@event-calendar/list' {
    const List: unknown;
    export default List;
}

declare module '@event-calendar/resource-time-grid' {
    const ResourceTimeGrid: unknown;
    export default ResourceTimeGrid;
}

declare module '@event-calendar/resource-timeline' {
    const ResourceTimeline: unknown;
    export default ResourceTimeline;
}

declare module '@event-calendar/interaction' {
    const Interaction: unknown;
    export default Interaction;
}
