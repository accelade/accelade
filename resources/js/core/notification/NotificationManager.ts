/**
 * NotificationManager - Filament-style notification display.
 */

import type {
    NotificationData,
    NotificationConfig,
    NotificationStatus,
    NotificationPosition,
    NotificationAction,
} from './types';
import { DEFAULT_CONFIG, STATUS_ICONS } from './types';

export class NotificationManager {
    private config: NotificationConfig;
    private containers: Map<string, HTMLElement> = new Map();
    private notifications: Map<string, HTMLElement> = new Map();
    private timers: Map<string, number> = new Map();

    constructor(config: Partial<NotificationConfig> = {}) {
        this.config = { ...DEFAULT_CONFIG, ...config };
    }

    configure(config: Partial<NotificationConfig>): void {
        this.config = { ...this.config, ...config };
    }

    show(data: Partial<NotificationData> & { id?: string; title: string }): void {
        const normalized = this.normalizeData(data);
        const container = this.ensureContainer(normalized.position);
        const el = this.createElement(normalized);

        container.appendChild(el);
        this.notifications.set(normalized.id, el);

        requestAnimationFrame(() => el.classList.add('accelade-notif-show'));

        if (!normalized.persistent && normalized.duration > 0) {
            this.scheduleRemove(normalized.id, normalized.duration);
        }
    }

    dismiss(id: string): void {
        const el = this.notifications.get(id);
        if (!el) return;

        this.clearTimer(id);
        el.classList.remove('accelade-notif-show');
        el.classList.add('accelade-notif-hide');

        setTimeout(() => {
            el.remove();
            this.notifications.delete(id);
            this.cleanupContainers();
        }, 300);
    }

    success(title: string, body = ''): void {
        this.show({ id: `notif-${Date.now()}`, title, body, status: 'success' });
    }

    info(title: string, body = ''): void {
        this.show({ id: `notif-${Date.now()}`, title, body, status: 'info' });
    }

    warning(title: string, body = ''): void {
        this.show({ id: `notif-${Date.now()}`, title, body, status: 'warning' });
    }

    danger(title: string, body = ''): void {
        this.show({ id: `notif-${Date.now()}`, title, body, status: 'danger' });
    }

    private normalizeData(data: Partial<NotificationData> & { title: string }): NotificationData {
        return {
            id: data.id || `notif-${Date.now()}`,
            title: data.title,
            body: data.body || data.message || '',
            status: data.status || data.type || 'success',
            icon: data.icon,
            iconColor: data.iconColor,
            color: data.color,
            position: data.position || this.config.position,
            duration: data.duration ?? this.config.duration,
            persistent: data.persistent ?? false,
            actions: data.actions || [],
        };
    }

    private ensureContainer(position: NotificationPosition): HTMLElement {
        const id = `accelade-notifications-${position}`;
        let container = this.containers.get(position);

        if (!container) {
            container = document.getElementById(id) as HTMLElement | null;
            if (!container) {
                container = document.createElement('div');
                container.id = id;
                container.className = `accelade-notifications accelade-notifications-${position}`;
                document.body.appendChild(container);
            }
            this.containers.set(position, container);
        }

        return container;
    }

    private createElement(data: NotificationData): HTMLElement {
        const el = document.createElement('div');
        el.id = data.id;
        el.className = `accelade-notif accelade-notif-${data.status}`;
        if (data.color) el.style.setProperty('--accelade-notif-color', data.color);
        el.innerHTML = this.getTemplate(data);

        el.querySelector('.accelade-notif-close')?.addEventListener('click', () => {
            this.dismiss(data.id);
        });

        data.actions?.forEach((action) => {
            const btn = el.querySelector(`[data-action="${action.name}"]`);
            btn?.addEventListener('click', () => this.handleAction(data.id, action));
        });

        if (this.config.pauseOnHover) {
            el.addEventListener('mouseenter', () => this.clearTimer(data.id));
            el.addEventListener('mouseleave', () => {
                if (!data.persistent && data.duration > 0) {
                    this.scheduleRemove(data.id, data.duration);
                }
            });
        }

        return el;
    }

    private getTemplate(data: NotificationData): string {
        const icon = data.icon || STATUS_ICONS[data.status];
        const iconStyle = data.iconColor ? `color: ${data.iconColor}` : '';

        return `
            <div class="accelade-notif-icon" style="${iconStyle}">${icon}</div>
            <div class="accelade-notif-content">
                <div class="accelade-notif-title">${data.title}</div>
                ${data.body ? `<div class="accelade-notif-body">${data.body}</div>` : ''}
                ${this.renderActions(data.actions || [])}
            </div>
            <button type="button" class="accelade-notif-close" aria-label="Dismiss">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
    }

    private renderActions(actions: NotificationAction[]): string {
        if (!actions.length) return '';

        const buttons = actions.map((a) => {
            const label = a.label || a.name;
            if (a.url) {
                const target = a.openInNewTab ? ' target="_blank" rel="noopener"' : '';
                return `<a href="${a.url}"${target} data-action="${a.name}" class="accelade-notif-action">${label}</a>`;
            }
            return `<button type="button" data-action="${a.name}" class="accelade-notif-action">${label}</button>`;
        });

        return `<div class="accelade-notif-actions">${buttons.join('')}</div>`;
    }

    private handleAction(notifId: string, action: NotificationAction): void {
        if (action.dispatch) {
            window.dispatchEvent(new CustomEvent(action.dispatch, { detail: { notificationId: notifId } }));
        }
        if (action.close !== false && !action.url) {
            this.dismiss(notifId);
        }
    }

    private scheduleRemove(id: string, duration: number): void {
        this.clearTimer(id);
        const timer = window.setTimeout(() => this.dismiss(id), duration);
        this.timers.set(id, timer);
    }

    private clearTimer(id: string): void {
        const timer = this.timers.get(id);
        if (timer) {
            clearTimeout(timer);
            this.timers.delete(id);
        }
    }

    private cleanupContainers(): void {
        this.containers.forEach((container, position) => {
            if (container.children.length === 0) {
                container.remove();
                this.containers.delete(position);
            }
        });
    }
}

export default NotificationManager;
