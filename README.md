# Accelade

**Accelerate your Blade templates with reactive components.**

Accelade brings reactivity to Laravel Blade without the complexity of a full SPA framework. Write reactive components using familiar Blade syntax with your choice of frontend framework (Vanilla JS, Vue, React, Svelte, or Angular).

[![Tests](https://github.com/your-org/accelade/actions/workflows/tests.yml/badge.svg)](https://github.com/your-org/accelade/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## Features

- **Reactive Blade Components** - Add reactivity to Blade templates with simple directives
- **Multi-Framework Support** - Use Vanilla JS, Vue, React, Svelte, or Angular
- **SPA Navigation** - Client-side navigation with progress bar (no full page reloads)
- **Server State Sync** - Seamlessly sync component state with Laravel backend
- **Filament-Style Notifications** - Beautiful toast notifications from PHP or JavaScript
- **Zero Build Step** - Works out of the box, no npm required for basic usage
- **Blade-First** - Keep your templates in Blade, add reactivity where needed

---

## Quick Start

### Installation

```bash
composer require accelade/accelade
```

### Basic Usage

Add the scripts and styles to your layout:

```blade
<!DOCTYPE html>
<html>
<head>
    @acceladeStyles
</head>
<body>
    @yield('content')

    @acceladeScripts
    @acceladeNotifications
</body>
</html>
```

Create a reactive component:

```blade
@accelade(['count' => 0])
    <div class="counter">
        <p>Count: <span a-text="count">0</span></p>
        <button a-on:click="$set('count', count + 1)">+1</button>
        <button a-on:click="$set('count', count - 1)">-1</button>
    </div>
@endaccelade
```

That's it! Your counter is now reactive.

---

## Documentation

| Topic | Description |
|-------|-------------|
| [Installation](docs/installation.md) | Setup and configuration |
| [Components](docs/components.md) | Creating reactive Blade components |
| [Notifications](docs/notifications.md) | Toast notifications system |
| [SPA Navigation](docs/spa-navigation.md) | Client-side routing with progress bar |
| [Frameworks](docs/frameworks.md) | Using with Vue, React, Svelte, Angular |
| [Architecture](docs/architecture.md) | Package internals and design |
| [Configuration](docs/configuration.md) | All configuration options |
| [Testing](docs/testing.md) | Running tests and contributing |
| [API Reference](docs/api-reference.md) | Complete API documentation |

---

## Examples

### Reactive Counter with Server Sync

```blade
@accelade(['count' => 0])
    <div a-sync="count">
        <span a-text="count">0</span>
        <button a-on:click="$set('count', count + 1)">Increment</button>
    </div>
@endaccelade
```

### Notifications from PHP

```php
use Accelade\Facades\Notify;

// Quick notifications
Notify::success('Saved!')->body('Your changes have been saved.');
Notify::warning('Warning')->body('Please review your input.');

// Advanced usage
Notification::make()
    ->title('New Message')
    ->body('You have a new message from John.')
    ->info()
    ->actions([
        ['label' => 'View', 'url' => '/messages/1'],
        ['label' => 'Dismiss', 'close' => true],
    ])
    ->send();
```

### Notifications from JavaScript

```javascript
window.Accelade.notify.success('Success!', 'Operation completed.');
window.Accelade.notify.info('Info', 'Here is some information.');
window.Accelade.notify.warning('Warning', 'Please be careful!');
window.Accelade.notify.danger('Error', 'Something went wrong.');
```

### SPA Navigation

```blade
<x-accelade::link href="/dashboard" class="nav-link">
    Dashboard
</x-accelade::link>

<x-accelade::link
    href="/settings"
    :preserveScroll="true"
    :preserveState="true"
>
    Settings
</x-accelade::link>
```

---

## Binding Syntax

| Directive | Description | Example |
|-----------|-------------|---------|
| `a-text` | Bind text content | `<span a-text="name">Default</span>` |
| `a-show` | Toggle visibility | `<div a-show="isVisible">...</div>` |
| `a-if` | Conditional render | `<div a-if="hasItems">...</div>` |
| `a-model` | Two-way binding | `<input a-model="email">` |
| `a-on:event` | Event handlers | `<button a-on:click="handleClick()">` |
| `a-class` | Dynamic classes | `<div a-class="{ active: isActive }">` |
| `a-sync` | Server sync | `<div a-sync="count">` |

---

## Framework-Specific Bindings

Accelade adapts to your preferred framework syntax:

```blade
{{-- Vanilla (default) --}}
<span a-text="name"></span>

{{-- Vue --}}
<span v-text="name"></span>

{{-- React --}}
<span state:text="name"></span>

{{-- Svelte --}}
<span bind:text="name"></span>

{{-- Angular --}}
<span ng-text="name"></span>
```

---

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- Node.js 18+ (for development only)

---

## Development

```bash
# Install dependencies
composer install
npm install

# Build JavaScript
npm run build

# Run tests
composer test

# Run full CI (format + lint + test)
composer ci

# Format code
composer format

# Static analysis
composer mago
```

---

## License

Accelade is open-source software licensed under the [MIT license](LICENSE).

---

## Credits

- Inspired by [Laravel Livewire](https://livewire.laravel.com)
- Notification API inspired by [Filament Notifications](https://filamentphp.com/docs/notifications)
- SPA navigation inspired by [Inertia.js](https://inertiajs.com)
