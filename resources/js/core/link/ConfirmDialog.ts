/**
 * ConfirmDialog - Modal confirmation dialog for link actions
 *
 * Provides a customizable confirmation dialog that appears before
 * navigation or destructive actions.
 */

import type { ConfirmDialogOptions, ConfirmDialogResult } from './types';

/**
 * Default dialog options
 */
const defaults: ConfirmDialogOptions = {
    text: 'Are you sure you want to continue?',
    confirmButton: 'Confirm',
    cancelButton: 'Cancel',
    danger: false,
};

/**
 * CSS for the confirmation dialog
 */
const dialogStyles = `
.accelade-confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    opacity: 0;
    transition: opacity 0.15s ease-out;
}
.accelade-confirm-overlay.show {
    opacity: 1;
}
.accelade-confirm-dialog {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    max-width: 28rem;
    width: calc(100% - 2rem);
    padding: 1.5rem;
    transform: scale(0.95);
    transition: transform 0.15s ease-out;
}
.accelade-confirm-overlay.show .accelade-confirm-dialog {
    transform: scale(1);
}
.accelade-confirm-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}
.accelade-confirm-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0 0 1.5rem 0;
    line-height: 1.5;
}
.accelade-confirm-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}
.accelade-confirm-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    border: none;
}
.accelade-confirm-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px white, 0 0 0 4px #6366f1;
}
.accelade-confirm-btn-cancel {
    background: #f3f4f6;
    color: #374151;
}
.accelade-confirm-btn-cancel:hover {
    background: #e5e7eb;
}
.accelade-confirm-btn-confirm {
    background: #6366f1;
    color: white;
}
.accelade-confirm-btn-confirm:hover {
    background: #4f46e5;
}
.accelade-confirm-btn-danger {
    background: #ef4444;
    color: white;
}
.accelade-confirm-btn-danger:hover {
    background: #dc2626;
}
`;

/**
 * Inject dialog styles into document
 */
function ensureStyles(): void {
    if (document.getElementById('accelade-confirm-styles')) return;

    const style = document.createElement('style');
    style.id = 'accelade-confirm-styles';
    style.textContent = dialogStyles;
    document.head.appendChild(style);
}

/**
 * Show a confirmation dialog
 */
export function showConfirmDialog(options: Partial<ConfirmDialogOptions> = {}): Promise<ConfirmDialogResult> {
    ensureStyles();

    const opts: ConfirmDialogOptions = { ...defaults, ...options };

    return new Promise((resolve) => {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'accelade-confirm-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');

        // Create dialog
        const dialog = document.createElement('div');
        dialog.className = 'accelade-confirm-dialog';

        // Title (optional)
        if (opts.title) {
            const title = document.createElement('h3');
            title.className = 'accelade-confirm-title';
            title.textContent = opts.title;
            dialog.appendChild(title);
        }

        // Message
        const text = document.createElement('p');
        text.className = 'accelade-confirm-text';
        text.textContent = opts.text;
        dialog.appendChild(text);

        // Buttons container
        const buttons = document.createElement('div');
        buttons.className = 'accelade-confirm-buttons';

        // Cancel button
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'accelade-confirm-btn accelade-confirm-btn-cancel';
        cancelBtn.textContent = opts.cancelButton;
        cancelBtn.type = 'button';

        // Confirm button
        const confirmBtn = document.createElement('button');
        confirmBtn.className = `accelade-confirm-btn ${opts.danger ? 'accelade-confirm-btn-danger' : 'accelade-confirm-btn-confirm'}`;
        confirmBtn.textContent = opts.confirmButton;
        confirmBtn.type = 'button';

        buttons.appendChild(cancelBtn);
        buttons.appendChild(confirmBtn);
        dialog.appendChild(buttons);
        overlay.appendChild(dialog);

        // Close and resolve
        const close = (confirmed: boolean) => {
            overlay.classList.remove('show');
            setTimeout(() => {
                overlay.remove();
                resolve({ confirmed });
            }, 150);
        };

        // Event handlers
        cancelBtn.addEventListener('click', () => close(false));
        confirmBtn.addEventListener('click', () => close(true));

        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                close(false);
            }
        });

        // Close on Escape
        const handleKeydown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                close(false);
                document.removeEventListener('keydown', handleKeydown);
            } else if (e.key === 'Enter') {
                close(true);
                document.removeEventListener('keydown', handleKeydown);
            }
        };
        document.addEventListener('keydown', handleKeydown);

        // Add to DOM and animate in
        document.body.appendChild(overlay);
        requestAnimationFrame(() => {
            overlay.classList.add('show');
            confirmBtn.focus();
        });
    });
}

/**
 * Quick confirm with just a message
 */
export function confirm(message: string): Promise<boolean> {
    return showConfirmDialog({ text: message }).then(r => r.confirmed);
}

/**
 * Danger confirm (red button)
 */
export function confirmDanger(message: string, confirmLabel = 'Delete'): Promise<boolean> {
    return showConfirmDialog({
        text: message,
        confirmButton: confirmLabel,
        danger: true,
    }).then(r => r.confirmed);
}

export default {
    show: showConfirmDialog,
    confirm,
    confirmDanger,
};
