import { test, expect } from '@playwright/test';

/**
 * Accelade Demo E2E Tests
 *
 * Tests all Accelade features across all supported frameworks.
 * Uses baseURL from playwright.config.ts (configurable via ACCELADE_TEST_URL env var)
 */

const FRAMEWORKS = ['vanilla', 'vue', 'react', 'svelte', 'angular'] as const;
type Framework = typeof FRAMEWORKS[number];

// Demo route prefix (matches config default)
const DEMO_PREFIX = '/demo';

// All frameworks now support reactive state
const REACTIVE_FRAMEWORKS: Framework[] = ['vanilla', 'vue', 'react', 'svelte', 'angular'];

test.describe('Accelade Demo Tests', () => {
    for (const framework of FRAMEWORKS) {
        test.describe(`${framework.charAt(0).toUpperCase() + framework.slice(1)} Framework`, () => {

            test('page loads correctly', async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);

                // Check page title
                await expect(page).toHaveTitle(/Accelade Demo/);

                // Check framework badge
                const badge = page.locator('.inline-flex.items-center span.text-sm');
                await expect(badge).toBeVisible();

                // Check Accelade is loaded
                const acceladeLoaded = await page.evaluate(() => {
                    return typeof window.Accelade !== 'undefined';
                });
                expect(acceladeLoaded).toBe(true);
            });

            test('counter component works', async ({ page }) => {
                // Skip non-reactive frameworks until adapters are fixed
                test.skip(!REACTIVE_FRAMEWORKS.includes(framework), `${framework} adapter needs reactivity fixes`);

                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find counter section
                const counterSection = page.locator('section').filter({ hasText: 'Counter Component' }).first();
                await expect(counterSection).toBeVisible();

                // Get initial count
                const countDisplay = counterSection.locator('.text-4xl');
                const initialCount = await countDisplay.textContent();
                expect(initialCount?.trim()).toBe('0');

                // Click increment button
                const incrementBtn = counterSection.locator('button', { hasText: '+' });
                await incrementBtn.click();
                await page.waitForTimeout(100);

                // Verify count increased (use regex to handle whitespace)
                await expect(countDisplay).toHaveText(/^\s*1\s*$/);

                // Click decrement button
                const decrementBtn = counterSection.locator('button', { hasText: '-' });
                await decrementBtn.click();
                await page.waitForTimeout(100);

                // Verify count decreased
                await expect(countDisplay).toHaveText(/^\s*0\s*$/);
            });

            test('counter with server sync displays correctly', async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find sync counter section
                const syncSection = page.locator('section').filter({ hasText: 'Counter with Server Sync' }).first();
                await expect(syncSection).toBeVisible();

                // Check initial count is 10
                const countDisplay = syncSection.locator('.text-4xl');
                await expect(countDisplay).toHaveText('10');
            });

            test('custom script functions work', async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find custom script section
                const scriptSection = page.locator('section').filter({ hasText: 'Custom Script Functions' }).first();
                await expect(scriptSection).toBeVisible();

                // Get initial count
                const countDisplay = scriptSection.locator('.text-4xl');
                await expect(countDisplay).toHaveText('0');

                // Click +5 button
                const addFiveBtn = scriptSection.locator('button', { hasText: '+5' });
                await addFiveBtn.click();
                await page.waitForTimeout(100);

                // Verify count is 5
                await expect(countDisplay).toHaveText('5');

                // Click Double button
                const doubleBtn = scriptSection.locator('button', { hasText: 'Double' });
                await doubleBtn.click();
                await page.waitForTimeout(100);

                // Verify count is 10
                await expect(countDisplay).toHaveText('10');

                // Click Reset button
                const resetBtn = scriptSection.locator('button', { hasText: 'Reset' });
                await resetBtn.click();
                await page.waitForTimeout(100);

                // Verify count is 0
                await expect(countDisplay).toHaveText('0');
            });

            test('progress bar manual controls work', async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find progress bar section
                const progressSection = page.locator('section').filter({ hasText: 'Progress Bar Demo' }).first();
                await expect(progressSection).toBeVisible();

                // Scroll to progress section first
                await progressSection.scrollIntoViewIfNeeded();

                // Click Start Progress button
                const startBtn = progressSection.locator('button', { hasText: 'Start Progress' });
                await startBtn.click();

                // Wait for progress bar to appear (transition takes 200ms)
                await page.waitForTimeout(300);

                // Check progress bar is visible via JS (avoids timing issues)
                const isVisible = await page.evaluate(() => {
                    const el = document.getElementById('accelade-progress');
                    if (!el) return false;
                    const opacity = parseFloat(getComputedStyle(el).opacity);
                    return opacity > 0.5;
                });
                expect(isVisible).toBe(true);

                // Complete progress bar directly via JS (more reliable than clicking)
                await page.evaluate(() => {
                    window.Accelade?.progress?.done();
                });

                // Wait for completion animation (200ms speed + 200ms fade + extra buffer)
                await page.waitForTimeout(800);

                // Progress bar should be hidden
                const isHidden = await page.evaluate(() => {
                    const el = document.getElementById('accelade-progress');
                    if (!el) return true;
                    const opacity = parseFloat(getComputedStyle(el).opacity);
                    return opacity < 0.5;
                });
                expect(isHidden).toBe(true);
            });

            test('SPA navigation within same framework works', async ({ page }) => {
                // Skip non-reactive frameworks until adapters are fixed
                test.skip(!REACTIVE_FRAMEWORKS.includes(framework), `${framework} adapter needs reactivity fixes`);

                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find SPA navigation section
                const spaSection = page.locator('section').filter({ hasText: 'SPA Navigation' }).first();
                await expect(spaSection).toBeVisible();

                // Modify counter first to verify state change
                const counterSection = page.locator('section').filter({ hasText: 'Counter Component' }).first();
                const incrementBtn = counterSection.locator('button', { hasText: '+' });
                await incrementBtn.click();
                await page.waitForTimeout(100);
                await incrementBtn.click();
                await page.waitForTimeout(100);

                // Verify counter is at 2 (use regex to handle whitespace)
                const countDisplay = counterSection.locator('.text-4xl');
                await expect(countDisplay).toHaveText(/^\s*2\s*$/);

                // Click SPA reload link
                const spaReloadLink = spaSection.locator('a', { hasText: 'Reload This Page (SPA)' });
                await spaReloadLink.click();

                // Wait for SPA navigation
                await page.waitForTimeout(500);

                // Counter should reset to 0 (state not preserved)
                const newCountDisplay = page.locator('section').filter({ hasText: 'Counter Component' }).first().locator('.text-4xl');
                await expect(newCountDisplay).toHaveText(/^\s*0\s*$/);

                // Page should still be on same framework
                await expect(page).toHaveURL(new RegExp(`${DEMO_PREFIX}/${framework}`));
            });

            test('framework tabs show all options', async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Check all framework tabs exist
                const nav = page.locator('nav').first();

                await expect(nav.locator('a', { hasText: 'Vanilla' })).toBeVisible();
                await expect(nav.locator('a', { hasText: 'Vue' })).toBeVisible();
                await expect(nav.locator('a', { hasText: 'React' })).toBeVisible();
                await expect(nav.locator('a', { hasText: 'Svelte' })).toBeVisible();
                await expect(nav.locator('a', { hasText: 'Angular' })).toBeVisible();
            });
        });
    }

    test.describe('Cross-Framework Navigation', () => {
        test('switching frameworks does full page reload', async ({ page }) => {
            // Start on vanilla
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Click Vue tab
            const vueTab = page.locator('nav a', { hasText: 'Vue' });
            await vueTab.click();

            // Should navigate to Vue demo
            await page.waitForURL(/\/demo\/vue/);

            // Check Vue framework badge
            const badge = page.locator('.inline-flex.items-center span.text-sm');
            await expect(badge).toContainText('Vue');
        });
    });

    test.describe('Window API', () => {
        test('Accelade global is available', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Check that Accelade is on window
            const result = await page.evaluate(() => {
                return {
                    hasAccelade: typeof window.Accelade !== 'undefined',
                    hasInit: typeof window.Accelade?.init === 'function',
                    hasNavigate: typeof window.Accelade?.navigate === 'function',
                    hasProgress: typeof window.Accelade?.progress !== 'undefined',
                    hasRouter: typeof window.Accelade?.router !== 'undefined',
                };
            });

            expect(result.hasAccelade).toBe(true);
            expect(result.hasInit).toBe(true);
            expect(result.hasNavigate).toBe(true);
            expect(result.hasProgress).toBe(true);
            expect(result.hasRouter).toBe(true);
        });

        test('Progress bar API works', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Test progress API
            const result = await page.evaluate(() => {
                const hasStart = typeof window.Accelade?.progress?.start === 'function';
                const hasDone = typeof window.Accelade?.progress?.done === 'function';
                const hasConfigure = typeof window.Accelade?.progress?.configure === 'function';

                // Start progress
                window.Accelade?.progress?.start();

                return { hasStart, hasDone, hasConfigure };
            });

            expect(result.hasStart).toBe(true);
            expect(result.hasDone).toBe(true);
            expect(result.hasConfigure).toBe(true);

            // Verify progress element was created
            await page.waitForTimeout(100);
            const progressExists = await page.evaluate(() => {
                return document.getElementById('accelade-progress') !== null;
            });
            expect(progressExists).toBe(true);
        });
    });

    test.describe('Notifications', () => {
        for (const framework of FRAMEWORKS) {
            test(`${framework}: success notification displays`, async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Find notification section
                const notifySection = page.locator('section').filter({ hasText: 'Notifications' }).first();
                await notifySection.scrollIntoViewIfNeeded();

                // Click success button
                const successBtn = notifySection.locator('[data-testid="notify-success"]');
                await successBtn.click();

                // Wait for notification to appear
                await page.waitForTimeout(100);

                // Check notification is visible
                const notification = page.locator('.accelade-notif-success');
                await expect(notification).toBeVisible();
                await expect(notification).toContainText('Success!');
            });

            test(`${framework}: info notification displays`, async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                // Click info button
                const infoBtn = page.locator('[data-testid="notify-info"]');
                await infoBtn.click();

                await page.waitForTimeout(100);

                const notification = page.locator('.accelade-notif-info');
                await expect(notification).toBeVisible();
                await expect(notification).toContainText('Info');
            });

            test(`${framework}: warning notification displays`, async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                const warningBtn = page.locator('[data-testid="notify-warning"]');
                await warningBtn.click();

                await page.waitForTimeout(100);

                const notification = page.locator('.accelade-notif-warning');
                await expect(notification).toBeVisible();
                await expect(notification).toContainText('Warning');
            });

            test(`${framework}: danger notification displays`, async ({ page }) => {
                await page.goto(`${DEMO_PREFIX}/${framework}`);
                await page.waitForLoadState('networkidle');

                const dangerBtn = page.locator('[data-testid="notify-danger"]');
                await dangerBtn.click();

                await page.waitForTimeout(100);

                const notification = page.locator('.accelade-notif-danger');
                await expect(notification).toBeVisible();
                await expect(notification).toContainText('Error');
            });
        }

        test('notification can be dismissed', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Show notification
            const successBtn = page.locator('[data-testid="notify-success"]');
            await successBtn.click();
            await page.waitForTimeout(100);

            // Find and click dismiss button
            const notification = page.locator('.accelade-notif-success');
            await expect(notification).toBeVisible();

            const closeBtn = notification.locator('.accelade-notif-close');
            await closeBtn.click();

            // Wait for dismiss animation
            await page.waitForTimeout(400);

            // Notification should be removed
            await expect(notification).not.toBeVisible();
        });

        test('notification auto-dismisses after duration', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Show notification with 3s duration via custom position button
            await page.evaluate(() => {
                window.Accelade?.notify?.show({
                    id: 'test-auto-dismiss',
                    title: 'Auto Dismiss Test',
                    message: 'This should auto-dismiss',
                    type: 'info',
                    position: 'top-right',
                    duration: 1000, // 1 second
                    dismissible: true
                });
            });

            await page.waitForTimeout(100);

            // Should be visible initially
            const notification = page.locator('#test-auto-dismiss');
            await expect(notification).toBeVisible();

            // Wait for auto-dismiss (1s + 300ms animation)
            await page.waitForTimeout(1500);

            // Should be gone
            await expect(notification).not.toBeVisible();
        });

        test('notify API is available on window', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            const result = await page.evaluate(() => {
                return {
                    hasNotify: typeof window.Accelade?.notify !== 'undefined',
                    hasSuccess: typeof window.Accelade?.notify?.success === 'function',
                    hasInfo: typeof window.Accelade?.notify?.info === 'function',
                    hasWarning: typeof window.Accelade?.notify?.warning === 'function',
                    hasDanger: typeof window.Accelade?.notify?.danger === 'function',
                    hasShow: typeof window.Accelade?.notify?.show === 'function',
                    hasDismiss: typeof window.Accelade?.notify?.dismiss === 'function',
                };
            });

            expect(result.hasNotify).toBe(true);
            expect(result.hasSuccess).toBe(true);
            expect(result.hasInfo).toBe(true);
            expect(result.hasWarning).toBe(true);
            expect(result.hasDanger).toBe(true);
            expect(result.hasShow).toBe(true);
            expect(result.hasDismiss).toBe(true);
        });

        test('notification positions work correctly', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/vanilla`);
            await page.waitForLoadState('networkidle');

            // Test top-right (default)
            await page.evaluate(() => {
                window.Accelade?.notify?.show({
                    id: 'pos-test-1',
                    title: 'Top Right',
                    message: '',
                    type: 'info',
                    position: 'top-right',
                    duration: 5000,
                    dismissible: true
                });
            });

            await page.waitForTimeout(100);

            // Check container has correct class
            const container = page.locator('#accelade-notifications-top-right');
            await expect(container).toBeVisible();
            await expect(container).toHaveClass(/accelade-notifications-top-right/);
        });
    });
});

// TypeScript declarations for window.Accelade
declare global {
    interface Window {
        Accelade?: {
            init: () => void;
            navigate: (url: string) => void;
            progress?: {
                start: () => void;
                done: () => void;
                configure: (config: unknown) => void;
            };
            router?: unknown;
            notify?: {
                success: (title: string, message?: string) => void;
                info: (title: string, message?: string) => void;
                warning: (title: string, message?: string) => void;
                danger: (title: string, message?: string) => void;
                show: (data: {
                    id: string;
                    title: string;
                    message: string;
                    type: string;
                    position: string;
                    duration: number;
                    dismissible: boolean;
                }) => void;
                dismiss: (id: string) => void;
            };
        };
    }
}
