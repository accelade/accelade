# Contributing

Thank you for considering contributing to Accelade! We welcome contributions from everyone, whether it's fixing bugs, improving documentation, or adding new features.

---

## Getting Started

### Fork the Repository

1. Fork the [Accelade repository](https://github.com/accelade/accelade) on GitHub
2. Clone your fork locally:

```bash
git clone https://github.com/YOUR-USERNAME/accelade.git
cd accelade
```

3. Add the upstream remote:

```bash
git remote add upstream https://github.com/accelade/accelade.git
```

### Set Up Development Environment

1. Install PHP dependencies:

```bash
composer install
```

2. Install JavaScript dependencies:

```bash
npm install
```

3. Build assets for development:

```bash
npm run dev
```

4. Run tests to ensure everything is working:

```bash
composer test
```

---

## Development Workflow

### Creating a Branch

Create a feature branch for your changes:

```bash
git checkout -b feature/your-feature-name
```

Use meaningful branch names:
- `feature/add-tooltip-component` - For new features
- `fix/notification-position` - For bug fixes
- `docs/improve-modal-docs` - For documentation
- `refactor/optimize-state-sync` - For refactoring

### Making Changes

1. Write your code following our coding standards
2. Add tests for new functionality
3. Update documentation if needed
4. Run the test suite to ensure nothing is broken

### Commit Messages

Write clear, descriptive commit messages:

```bash
# Good examples
git commit -m "Add tooltip component with hover and click triggers"
git commit -m "Fix notification RTL positioning issue"
git commit -m "Update modal documentation with new examples"

# Bad examples
git commit -m "fix stuff"
git commit -m "update"
```

### Pull Requests

1. Push your branch to your fork:

```bash
git push origin feature/your-feature-name
```

2. Open a Pull Request from your fork to the main repository
3. Fill out the PR template with:
   - Description of changes
   - Related issue numbers
   - Screenshots (if applicable)
   - Testing instructions

---

## Coding Standards

### PHP

We follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards. Run Laravel Pint to format your code:

```bash
vendor/bin/pint
```

Key guidelines:
- Use strict types: `declare(strict_types=1);`
- Add return type declarations to all methods
- Use descriptive variable and method names
- Document public methods with PHPDoc

```php
<?php

declare(strict_types=1);

namespace Accelade\Components;

class MyComponent
{
    /**
     * Create a new component instance.
     *
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected array $options = []
    ) {}

    /**
     * Render the component.
     */
    public function render(): string
    {
        // Implementation
    }
}
```

### JavaScript/TypeScript

- Use TypeScript for new code
- Follow ESLint configuration
- Use meaningful variable names
- Document complex functions

```typescript
/**
 * Synchronize state with the server.
 *
 * @param key - The state key to sync
 * @param value - The value to send
 * @param options - Sync options
 */
export function syncState(
    key: string,
    value: unknown,
    options: SyncOptions = {}
): Promise<void> {
    // Implementation
}
```

### Blade Templates

- Use semantic HTML
- Include dark mode support (`dark:` prefixed classes)
- Support RTL layouts with logical properties
- Test across all supported frameworks

---

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
vendor/bin/pest tests/Feature/NotificationTest.php

# Run with coverage
composer test-coverage
```

### Writing Tests

Use Pest for PHP tests:

```php
<?php

use Accelade\Notification\Notification;

it('creates a notification with title', function () {
    $notification = Notification::make('Hello World');

    expect($notification->getTitle())->toBe('Hello World');
});

it('sets the correct status type', function () {
    $notification = Notification::make()->success();

    expect($notification->getStatus())->toBe('success');
});
```

### Testing Guidelines

- Write tests for all new functionality
- Cover edge cases and error scenarios
- Test across different framework configurations
- Use meaningful test descriptions

---

## Documentation

### Writing Documentation

- Use clear, concise language
- Include code examples
- Add live demos when possible
- Keep examples up-to-date with the codebase

### Documentation Structure

```markdown
# Component Name

Brief description of what the component does.

---

## Basic Usage

Show the simplest possible example.

## Props/Options

Document all available options.

## Advanced Usage

Show more complex scenarios.

## JavaScript API

Document any JS methods.

---

## Related

Link to related components or concepts.
```

---

## Reporting Issues

### Bug Reports

Include:
- Accelade version
- Laravel version
- PHP version
- Browser (for frontend issues)
- Steps to reproduce
- Expected vs actual behavior
- Error messages or screenshots

### Feature Requests

Describe:
- The problem you're trying to solve
- Your proposed solution
- Alternative solutions considered
- Any related components or features

---

## Code of Conduct

We are committed to providing a welcoming and inclusive environment. Please:

- Be respectful and considerate
- Use inclusive language
- Focus on constructive feedback
- Help others learn and grow
- Report unacceptable behavior

---

## Recognition

All contributors are recognized in our [Thanks](/docs/thanks) page. We appreciate every contribution, no matter how small!

---

## Questions?

If you have questions:

1. Check the [documentation](/docs/getting-started)
2. Search existing [GitHub issues](https://github.com/accelade/accelade/issues)
3. Open a new issue with your question
4. Join our community discussions

Thank you for contributing to Accelade!
