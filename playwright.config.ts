import { defineConfig, devices } from '@playwright/test';

/**
 * Accelade E2E Test Configuration
 *
 * Environment Variables:
 * - ACCELADE_TEST_URL: Base URL for tests (default: http://localhost:8000)
 * - CI: Set to true in CI environments for different retry/worker settings
 *
 * Usage:
 * - Local: npm run test:e2e
 * - CI: ACCELADE_TEST_URL=http://localhost:8000 npm run test:e2e
 */
export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: process.env.CI ? 'github' : 'html',
    timeout: 30000,
    expect: {
        timeout: 5000,
    },
    use: {
        baseURL: process.env.ACCELADE_TEST_URL || 'http://localhost:8000',
        trace: 'on-first-retry',
        ignoreHTTPSErrors: true,
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
        // Uncomment for cross-browser testing
        // {
        //     name: 'firefox',
        //     use: { ...devices['Desktop Firefox'] },
        // },
        // {
        //     name: 'webkit',
        //     use: { ...devices['Desktop Safari'] },
        // },
    ],
    // Web server configuration for CI
    webServer: process.env.CI ? {
        command: 'cd ../.. && php artisan serve --port=8000',
        url: 'http://localhost:8000',
        reuseExistingServer: !process.env.CI,
        timeout: 120000,
    } : undefined,
});
