/**
 * Notification types (Filament-compatible).
 */

export type NotificationStatus = 'success' | 'info' | 'warning' | 'danger';

export type NotificationPosition =
    | 'top-left'
    | 'top-center'
    | 'top-right'
    | 'bottom-left'
    | 'bottom-center'
    | 'bottom-right';

export interface NotificationAction {
    name: string;
    label?: string;
    url?: string;
    openInNewTab?: boolean;
    close?: boolean;
    dispatch?: string;
    dispatchTo?: string;
}

export interface NotificationData {
    id: string;
    title: string;
    body: string;
    status: NotificationStatus;
    icon?: string;
    iconColor?: string;
    color?: string;
    position: NotificationPosition;
    duration: number;
    persistent: boolean;
    actions?: NotificationAction[];
    // Backward compatibility
    message?: string;
    type?: NotificationStatus;
}

export interface NotificationConfig {
    position: NotificationPosition;
    duration: number;
    maxVisible: number;
    pauseOnHover: boolean;
}

export const DEFAULT_CONFIG: NotificationConfig = {
    position: 'top-right',
    duration: 5000,
    maxVisible: 5,
    pauseOnHover: true,
};

export const STATUS_ICONS: Record<NotificationStatus, string> = {
    success: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
    info: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    warning: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>',
    danger: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
};
