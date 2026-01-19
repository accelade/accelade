/**
 * Type declarations for @event-calendar packages
 * These packages don't have built-in TypeScript support
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

    export default class Calendar {
        constructor(options: CalendarOptions);
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
        today(): void;
        destroy(): void;
    }
}

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
