# Contributing to Accelade

Thank you for considering contributing to Accelade! This document outlines the process for contributing to this project.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for everyone.

## How Can I Contribute?

### Reporting Bugs

Before creating a bug report, please check existing issues to avoid duplicates.

When creating a bug report, include:

- **Clear title** describing the issue
- **Steps to reproduce** the behavior
- **Expected behavior** vs actual behavior
- **Screenshots** if applicable
- **Environment details**:
  - PHP version
  - Laravel version
  - Accelade version
  - Browser (for frontend issues)

### Suggesting Features

Feature requests are welcome! Please:

1. Check if the feature has already been suggested
2. Provide a clear use case
3. Explain why this would benefit other users

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Run tests and code quality checks
5. Commit with a descriptive message
6. Push and open a Pull Request

## Development Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/accelade.git
cd accelade

# Install dependencies
composer install
npm install

# Build assets
npm run build
```

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run specific test file
./vendor/bin/pest tests/Unit/NotificationTest.php

# Run specific test
./vendor/bin/pest --filter="notification can set title"
```

### Code Quality

```bash
# Format code with Laravel Pint
composer format

# Check formatting (no changes)
composer format:test

# Run Mago static analysis
composer mago

# Fix Mago issues automatically
composer mago:fix

# Run all checks
composer analyse

# Full CI pipeline (analyse + test)
composer ci
```

### E2E Tests

```bash
# Install Playwright browsers
npx playwright install chromium

# Run E2E tests
npm run test:e2e

# Run with headed browser
npm run test:e2e -- --headed

# Debug mode
npx playwright test --debug
```

## Coding Standards

### PHP

- Follow PSR-12 coding standards
- Use `declare(strict_types=1)` in all PHP files
- Add type hints for parameters and return types
- Use Laravel Pint for formatting

### TypeScript/JavaScript

- Use TypeScript for all new code
- Follow existing code style
- Add JSDoc comments for public APIs

### Commits

- Use clear, descriptive commit messages
- Reference issues when applicable: `Fix #123`
- Keep commits focused and atomic

### Pull Request Guidelines

1. **Title**: Clear and descriptive (e.g., "Add support for custom notification icons")
2. **Description**: Explain what and why, not just how
3. **Tests**: Include tests for new features and bug fixes
4. **Documentation**: Update docs if needed
5. **Breaking Changes**: Clearly document any breaking changes

## Project Structure

```
accelade/
├── src/                    # PHP source code
│   ├── Facades/           # Laravel facades
│   ├── Http/              # Controllers
│   ├── Notification/      # Notification system
│   └── View/              # Blade components
├── resources/
│   ├── js/                # TypeScript source
│   │   ├── core/         # Shared utilities
│   │   ├── vanilla/      # Vanilla JS adapter
│   │   ├── vue/          # Vue adapter
│   │   └── react/        # React adapter
│   └── views/            # Blade templates
├── tests/
│   ├── Unit/             # Unit tests
│   ├── Feature/          # Feature tests
│   └── e2e/              # Playwright E2E tests
├── config/               # Package config
├── routes/               # Route definitions
└── docs/                 # Documentation
```

## Testing Guidelines

### Unit Tests

Test individual classes in isolation:

```php
it('can create a notification with title', function () {
    $notification = Notification::make()->title('Test');

    expect($notification->title)->toBe('Test');
});
```

### Feature Tests

Test integration with Laravel:

```php
it('stores notifications in session', function () {
    Notify::success('Test')->body('Body');

    $notifications = Notify::flush();

    expect($notifications)->toHaveCount(1);
});
```

### E2E Tests

Test browser functionality:

```typescript
test('shows success notification', async ({ page }) => {
    await page.goto('/demo/vanilla');
    await page.click('[data-testid="notify-success"]');

    const notification = page.locator('.accelade-notification');
    await expect(notification).toBeVisible();
});
```

## Release Process

1. Update CHANGELOG.md
2. Update version in composer.json
3. Create a GitHub release with tag
4. Package is automatically published to Packagist

## Getting Help

- **Documentation**: [docs/](./docs/)
- **Issues**: [GitHub Issues](https://github.com/accelade/accelade/issues)
- **Discussions**: [GitHub Discussions](https://github.com/accelade/accelade/discussions)

## Recognition

Contributors will be recognized in:

- The README.md contributors section
- Release notes for significant contributions

Thank you for contributing!
