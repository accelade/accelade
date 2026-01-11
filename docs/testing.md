# Testing

This guide covers running tests and contributing to Accelade.

## Test Stack

- **PHP Tests**: [PEST](https://pestphp.com/) with Laravel plugin
- **E2E Tests**: [Playwright](https://playwright.dev/)
- **Static Analysis**: [Mago](https://mago.carthage.software/)
- **Code Formatting**: [Laravel Pint](https://laravel.com/docs/pint)

## Running Tests

### All Tests

```bash
# PHP tests only
composer test

# Full CI (format check + lint + tests)
composer ci
```

### PHP Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run specific test file
./vendor/bin/pest tests/Unit/NotificationTest.php

# Run specific test
./vendor/bin/pest --filter="notification can set title"

# Run by group
./vendor/bin/pest --group=notifications
```

### E2E Tests

```bash
# Install Playwright browsers
npx playwright install chromium

# Run all E2E tests
npm run test:e2e

# Run with headed browser
npm run test:e2e -- --headed

# Run specific test file
npx playwright test tests/e2e/notifications.spec.ts

# Debug mode
npx playwright test --debug
```

### Code Quality

```bash
# Format code with Pint
composer format

# Check formatting (no changes)
composer format:test

# Run Mago static analysis
composer mago

# Auto-fix Mago issues
composer mago:fix

# Run all analysis
composer analyse
```

## Test Structure

```
tests/
├── Feature/                    # Feature/integration tests
│   ├── NotifyFacadeTest.php   # Notification facade tests
│   ├── RouteRegistrationTest.php
│   └── ServiceProviderTest.php
├── Unit/                       # Unit tests
│   ├── AcceladeTest.php       # Core class tests
│   ├── NotificationTest.php   # Notification entity tests
│   └── NotificationManagerTest.php
├── Pest.php                   # PEST configuration
└── TestCase.php               # Base test case

tests/e2e/                      # Playwright E2E tests
├── navigation.spec.ts         # SPA navigation tests
├── notifications.spec.ts      # Notification UI tests
└── components.spec.ts         # Component tests
```

## Writing PHP Tests

### Basic Test

```php
<?php

declare(strict_types=1);

it('can create a notification', function () {
    $notification = Notification::make()
        ->title('Test')
        ->body('Test body');

    expect($notification->title)->toBe('Test');
    expect($notification->body)->toBe('Test body');
});
```

### Feature Test

```php
<?php

declare(strict_types=1);

use Accelade\Facades\Notify;

it('stores notifications in session', function () {
    Notify::success('Test')->body('Body');

    $notifications = Notify::flush();

    expect($notifications)->toHaveCount(1);
    expect($notifications->first()->title)->toBe('Test');
});
```

### Grouped Tests

```php
<?php

declare(strict_types=1);

describe('Notification Status', function () {
    it('can set success status', function () {
        $notification = Notification::make()->success();
        expect($notification->status)->toBe('success');
    });

    it('can set danger status', function () {
        $notification = Notification::make()->danger();
        expect($notification->status)->toBe('danger');
    });
});
```

### Using Test Helpers

```php
<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('registers core routes', function () {
    expect(Route::has('accelade.script'))->toBeTrue();
    expect(Route::has('accelade.update'))->toBeTrue();
});

it('serves javascript file', function () {
    get(route('accelade.script'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8');
});
```

## Writing E2E Tests

### Basic E2E Test

```typescript
import { test, expect } from '@playwright/test';

test.describe('Notifications', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/demo/vanilla');
    });

    test('shows success notification', async ({ page }) => {
        await page.click('[data-testid="notify-success"]');

        const notification = page.locator('.accelade-notification');
        await expect(notification).toBeVisible();
        await expect(notification).toContainText('Success');
    });

    test('notification auto-dismisses', async ({ page }) => {
        await page.click('[data-testid="notify-info"]');

        const notification = page.locator('.accelade-notification');
        await expect(notification).toBeVisible();

        // Wait for auto-dismiss (default 5s)
        await expect(notification).toBeHidden({ timeout: 6000 });
    });
});
```

### Testing SPA Navigation

```typescript
test('navigates without full reload', async ({ page }) => {
    await page.goto('/demo/vanilla');

    // Store reference to check for reload
    await page.evaluate(() => {
        window.__navigationTest = true;
    });

    // Click SPA link
    await page.click('a[data-accelade-link]');

    // Verify no reload occurred
    const preserved = await page.evaluate(() => window.__navigationTest);
    expect(preserved).toBe(true);
});
```

### Testing Components

```typescript
test('counter increments on click', async ({ page }) => {
    await page.goto('/demo/vanilla');

    const count = page.locator('[a-text="count"]');
    await expect(count).toHaveText('0');

    await page.click('button:has-text("+1")');
    await expect(count).toHaveText('1');

    await page.click('button:has-text("+1")');
    await expect(count).toHaveText('2');
});
```

## Test Configuration

### PEST Configuration

```php
// tests/Pest.php
<?php

declare(strict_types=1);

use Accelade\Tests\TestCase;

pest()->extend(TestCase::class)->in('Unit', 'Feature');
```

### Base Test Case

```php
// tests/TestCase.php
<?php

declare(strict_types=1);

namespace Accelade\Tests;

use Accelade\AcceladeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [AcceladeServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Accelade' => \Accelade\Facades\Accelade::class,
            'Notify' => \Accelade\Facades\Notify::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('accelade.framework', 'vanilla');
    }
}
```

### Playwright Configuration

```typescript
// playwright.config.ts
import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    timeout: 30000,
    use: {
        baseURL: process.env.ACCELADE_TEST_URL || 'http://localhost:8000',
        trace: 'on-first-retry',
    },
    projects: [
        { name: 'chromium', use: { browserName: 'chromium' } },
    ],
});
```

## CI/CD Integration

### GitHub Actions

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  php-tests:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['8.2', '8.3', '8.4']

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}

      - name: Install dependencies
        run: composer install

      - name: Run tests
        run: composer ci

  e2e-tests:
    runs-on: ubuntu-latest
    needs: php-tests

    steps:
      - uses: actions/checkout@v4

      - name: Setup
        run: |
          composer install
          npm ci
          npm run build

      - name: Install Playwright
        run: npx playwright install chromium --with-deps

      - name: Start server
        run: php artisan serve &

      - name: Run E2E tests
        run: npm run test:e2e
```

## Debugging Tests

### PHP Test Debugging

```php
// Dump and die
it('debugs state', function () {
    $notification = Notification::make()->success();
    dd($notification);  // Stops execution
    ray($notification); // Ray app (if installed)
});

// Output during test
it('shows output', function () {
    dump('Debug info');
    expect(true)->toBeTrue();
});
```

### E2E Test Debugging

```bash
# Run with headed browser
npx playwright test --headed

# Debug mode (step through)
npx playwright test --debug

# Generate trace
npx playwright test --trace on
```

### Playwright Inspector

```typescript
test('debug test', async ({ page }) => {
    await page.goto('/demo');
    await page.pause(); // Opens inspector
    await page.click('button');
});
```

## Contributing

1. Fork the repository
2. Create feature branch: `git checkout -b feature/my-feature`
3. Write tests for your changes
4. Run `composer ci` to verify
5. Commit: `git commit -m "Add feature"`
6. Push: `git push origin feature/my-feature`
7. Open Pull Request

### Code Standards

- Follow PSR-12 for PHP
- Use TypeScript for JavaScript
- Write tests for all new features
- Update documentation as needed

## Next Steps

- [Architecture](architecture.md) - Understanding internals
- [API Reference](api-reference.md) - Complete API
