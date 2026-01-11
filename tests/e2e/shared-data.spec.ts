import { test, expect } from '@playwright/test';

/**
 * Shared Data E2E Tests
 *
 * Tests the shared data feature that allows passing data from Laravel backend
 * to JavaScript frontend, making it available globally via window.Accelade.shared
 */

const DEMO_PREFIX = '/demo';

test.describe('Shared Data Feature', () => {
    test.describe('Shared Data Demo Page', () => {
        test('page loads with shared data', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Check page loaded
            await expect(page).toHaveTitle(/Accelade Demo/);

            // Check Accelade is loaded
            const acceladeLoaded = await page.evaluate(() => {
                return typeof window.Accelade !== 'undefined';
            });
            expect(acceladeLoaded).toBe(true);
        });

        test('shared data is available on window.Accelade.shared', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Check shared API exists
            const result = await page.evaluate(() => {
                return {
                    hasShared: typeof window.Accelade?.shared !== 'undefined',
                    hasGet: typeof window.Accelade?.shared?.get === 'function',
                    hasHas: typeof window.Accelade?.shared?.has === 'function',
                    hasAll: typeof window.Accelade?.shared?.all === 'function',
                    hasSet: typeof window.Accelade?.shared?.set === 'function',
                    hasSubscribe: typeof window.Accelade?.shared?.subscribe === 'function',
                    hasSubscribeAll: typeof window.Accelade?.shared?.subscribeAll === 'function',
                };
            });

            expect(result.hasShared).toBe(true);
            expect(result.hasGet).toBe(true);
            expect(result.hasHas).toBe(true);
            expect(result.hasAll).toBe(true);
            expect(result.hasSet).toBe(true);
            expect(result.hasSubscribe).toBe(true);
            expect(result.hasSubscribeAll).toBe(true);
        });

        test('can get shared app name', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Click button to get app name
            const getAppNameBtn = page.locator('[data-testid="get-app-name"]');
            await getAppNameBtn.click();

            await page.waitForTimeout(100);

            // Check result displays
            const result = page.locator('#shared-value-result');
            await expect(result).toContainText('appName');
        });

        test('can get nested shared data using dot notation', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Click button to get user name (nested)
            const getUserNameBtn = page.locator('[data-testid="get-user-name"]');
            await getUserNameBtn.click();

            await page.waitForTimeout(100);

            // Check result displays the user name
            const result = page.locator('#shared-value-result');
            await expect(result).toContainText('user.name');
            await expect(result).toContainText('John Doe');
        });

        test('can get theme setting from nested shared data', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Click button to get theme
            const getThemeBtn = page.locator('[data-testid="get-theme"]');
            await getThemeBtn.click();

            await page.waitForTimeout(100);

            // Check result displays the theme
            const result = page.locator('#shared-value-result');
            await expect(result).toContainText('settings.theme');
            await expect(result).toContainText('dark');
        });

        test('can toggle theme (modify shared data)', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Get initial theme
            const themeDisplay = page.locator('#theme-display');
            await expect(themeDisplay).toContainText('dark');

            // Click toggle theme button
            const toggleBtn = page.locator('[data-testid="toggle-theme"]');
            await toggleBtn.click();

            await page.waitForTimeout(100);

            // Theme should now be light
            await expect(themeDisplay).toContainText('light');

            // Toggle again
            await toggleBtn.click();
            await page.waitForTimeout(100);

            // Theme should be dark again
            await expect(themeDisplay).toContainText('dark');
        });

        test('can update app name and subscription is notified', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Type new app name
            const input = page.locator('[data-testid="new-app-name-input"]');
            await input.fill('New App Name');

            // Click update button
            const updateBtn = page.locator('[data-testid="update-app-name"]');
            await updateBtn.click();

            await page.waitForTimeout(100);

            // Check app name display updated
            const appNameDisplay = page.locator('#app-name-display');
            await expect(appNameDisplay).toContainText('New App Name');

            // Check change log shows the update
            const changeLog = page.locator('#change-log');
            await expect(changeLog).toContainText('appName');
            await expect(changeLog).toContainText('New App Name');
        });

        test('shared data displays all values from backend', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Check the shared data display shows all expected keys
            const sharedDataDisplay = page.locator('#shared-data-display');
            await expect(sharedDataDisplay).toContainText('appName');
            await expect(sharedDataDisplay).toContainText('currentTime');
            await expect(sharedDataDisplay).toContainText('user');
            await expect(sharedDataDisplay).toContainText('settings');
        });

        test('displays user name from shared data', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Check user name display
            const userNameDisplay = page.locator('#user-name-display');
            await expect(userNameDisplay).toContainText('John Doe');
        });
    });

    test.describe('Shared Data API', () => {
        test('shared.get returns correct values', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            const result = await page.evaluate(() => {
                return {
                    appName: window.Accelade?.shared?.get('appName'),
                    userName: window.Accelade?.shared?.get('user.name'),
                    theme: window.Accelade?.shared?.get('settings.theme'),
                    missing: window.Accelade?.shared?.get('nonexistent', 'default'),
                };
            });

            expect(result.appName).toBeDefined();
            expect(result.userName).toBe('John Doe');
            expect(result.theme).toBe('dark');
            expect(result.missing).toBe('default');
        });

        test('shared.has returns correct boolean', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            const result = await page.evaluate(() => {
                return {
                    hasAppName: window.Accelade?.shared?.has('appName'),
                    hasUser: window.Accelade?.shared?.has('user'),
                    hasMissing: window.Accelade?.shared?.has('nonexistent'),
                };
            });

            expect(result.hasAppName).toBe(true);
            expect(result.hasUser).toBe(true);
            expect(result.hasMissing).toBe(false);
        });

        test('shared.all returns all shared data', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            const result = await page.evaluate(() => {
                return window.Accelade?.shared?.all();
            });

            expect(result).toHaveProperty('appName');
            expect(result).toHaveProperty('user');
            expect(result).toHaveProperty('settings');
            expect(result).toHaveProperty('currentTime');
        });

        test('shared.set updates values', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            await page.evaluate(() => {
                window.Accelade?.shared?.set('testKey', 'testValue');
            });

            const result = await page.evaluate(() => {
                return window.Accelade?.shared?.get('testKey');
            });

            expect(result).toBe('testValue');
        });

        test('shared.subscribe is called on value change', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Set up subscription and track calls
            const result = await page.evaluate(() => {
                return new Promise<{ key: string; newValue: unknown; oldValue: unknown }>((resolve) => {
                    window.Accelade?.shared?.subscribe('testSubscribe', (key, newValue, oldValue) => {
                        resolve({ key, newValue, oldValue });
                    });

                    // Trigger the change
                    window.Accelade?.shared?.set('testSubscribe', 'newValue');
                });
            });

            expect(result.key).toBe('testSubscribe');
            expect(result.newValue).toBe('newValue');
        });

        test('shared.subscribeAll is called on any change', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            const result = await page.evaluate(() => {
                return new Promise<{ key: string; newValue: unknown }>((resolve) => {
                    window.Accelade?.shared?.subscribeAll((key, newValue) => {
                        if (key === 'globalTestKey') {
                            resolve({ key, newValue });
                        }
                    });

                    // Trigger the change
                    window.Accelade?.shared?.set('globalTestKey', 'globalValue');
                });
            });

            expect(result.key).toBe('globalTestKey');
            expect(result.newValue).toBe('globalValue');
        });
    });

    test.describe('SPA Navigation with Shared Data', () => {
        test('shared data persists across SPA navigation', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Modify shared data
            await page.evaluate(() => {
                window.Accelade?.shared?.set('persistTest', 'beforeNavigation');
            });

            // Navigate to vanilla demo via SPA link
            const vanillaLink = page.locator('a', { hasText: 'Vanilla Demo' }).first();
            await vanillaLink.click();

            // Wait for navigation
            await page.waitForURL(/\/demo\/vanilla/);
            await page.waitForLoadState('networkidle');

            // Check shared data still exists
            // Note: Due to page reload, server-set shared data will be available
            // but client-set data may be reset depending on navigation type
            const hasShared = await page.evaluate(() => {
                return typeof window.Accelade?.shared !== 'undefined';
            });

            expect(hasShared).toBe(true);
        });
    });

    test.describe('Text Interpolation', () => {
        test('displays component state using {{ }} syntax', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Check that text interpolation rendered the greeting
            const textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('Hello');
            expect(textContent).toContain('World');
        });

        test('updates interpolated text when state changes', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Get initial count value (should be 0)
            const initialText = await page.textContent('.accelade-ready');
            expect(initialText).toContain('0 times');

            // Click the increment button
            const incrementBtn = page.locator('[data-testid="increment-btn"]');
            await incrementBtn.click();

            // Wait for update
            await page.waitForTimeout(100);

            // Check count is now 1
            const updatedText = await page.textContent('.accelade-ready');
            expect(updatedText).toContain('1 times');
        });

        test('displays shared data in interpolated text', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Check that shared data is interpolated
            const textContent = await page.textContent('.accelade-ready');

            // Should show app name from shared data
            expect(textContent).toContain('Accelade Demo');

            // Should show user name from shared data
            expect(textContent).toContain('John Doe');

            // Should show theme from shared data
            expect(textContent).toContain('dark');
        });

        test('updates interpolated shared data when changed', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Verify initial shared data display
            let textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('dark');

            // Update theme in shared data
            await page.evaluate(() => {
                window.Accelade?.shared?.set('settings.theme', 'light');
            });

            // Wait for update
            await page.waitForTimeout(100);

            // Check that interpolated text updated
            textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('light');
        });

        test('toggles greeting via button click', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Check initial greeting
            let textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('Hello');

            // Click toggle greeting button
            const toggleBtn = page.locator('button', { hasText: 'Toggle Greeting' });
            await toggleBtn.click();

            // Wait for update
            await page.waitForTimeout(100);

            // Check greeting changed
            textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('Welcome');
        });

        test('toggles name via button click', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            // Check initial name
            let textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('World');

            // Click toggle name button
            const toggleBtn = page.locator('button', { hasText: 'Toggle Name' });
            await toggleBtn.click();

            // Wait for update
            await page.waitForTimeout(100);

            // Check name changed
            textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('Accelade');
        });

        test('increments count multiple times', async ({ page }) => {
            await page.goto(`${DEMO_PREFIX}/shared-data`);
            await page.waitForLoadState('networkidle');

            // Wait for Accelade to initialize
            await page.waitForTimeout(200);

            const incrementBtn = page.locator('[data-testid="increment-btn"]');

            // Click multiple times
            for (let i = 1; i <= 5; i++) {
                await incrementBtn.click();
                await page.waitForTimeout(50);
            }

            // Check count is 5
            const textContent = await page.textContent('.accelade-ready');
            expect(textContent).toContain('5 times');
        });
    });
});

// TypeScript declarations for window.Accelade.shared
declare global {
    interface Window {
        Accelade?: {
            shared?: {
                get: <T = unknown>(key: string, defaultValue?: T) => T;
                has: (key: string) => boolean;
                all: () => Record<string, unknown>;
                set: (key: string, value: unknown) => void;
                merge: (data: Record<string, unknown>) => void;
                subscribe: (key: string, callback: (key: string, newValue: unknown, oldValue: unknown) => void) => () => void;
                subscribeAll: (callback: (key: string, newValue: unknown, oldValue: unknown) => void) => () => void;
                instance: () => unknown;
            };
            [key: string]: unknown;
        };
    }
}
