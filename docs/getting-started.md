# Getting Started

Get up and running with Accelade in minutes.

## Installation

```bash
composer require accelade/accelade
```

## Setup

Add the Accelade directives to your layout file:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @acceladeStyles
</head>
<body>
    {{ $slot }}

    @acceladeScripts
    @acceladeNotifications
</body>
</html>
```

**Important:** The CSRF meta tag is required for server sync functionality.

## Your First Component

Create a reactive counter in any Blade view:

```blade
@accelade(['count' => 0])
    <div class="counter">
        <p>Count: <span a-text="count">0</span></p>
        <button a-on:click="$set('count', count + 1)">Increment</button>
        <button a-on:click="$set('count', count - 1)">Decrement</button>
        <button a-on:click="$set('count', 0)">Reset</button>
    </div>
@endaccelade
```

That's it! The counter is now reactive without writing any JavaScript.

## Understanding the Basics

### The @accelade Directive

The `@accelade` directive creates a reactive component. Pass an array of initial state:

```blade
@accelade(['name' => '', 'email' => '', 'agreed' => false])
    {{-- Your reactive content --}}
@endaccelade
```

### Binding Directives

| Directive | Purpose | Example |
|-----------|---------|---------|
| `a-text` | Display text | `<span a-text="name">default</span>` |
| `a-html` | Display HTML | `<div a-html="content"></div>` |
| `a-show` | Toggle visibility | `<div a-show="isVisible">...</div>` |
| `a-if` | Conditional render | `<div a-if="hasItems">...</div>` |
| `a-model` | Two-way binding | `<input a-model="email">` |
| `a-on:event` | Event handler | `<button a-on:click="save()">` |
| `a-class` | Dynamic classes | `<div a-class="{ active: isActive }">` |
| `a-sync` | Server sync | `<div a-sync="preferences">` |

### State Actions

Inside event handlers, you have access to these functions:

```blade
{{-- Set a value --}}
<button a-on:click="$set('count', count + 1)">+1</button>

{{-- Toggle a boolean --}}
<button a-on:click="$toggle('isOpen')">Toggle</button>

{{-- Reset to initial state --}}
<button a-on:click="$reset()">Reset All</button>
<button a-on:click="$reset('count')">Reset Count</button>
```

## Common Patterns

### Form with Validation Feedback

```blade
@accelade(['email' => '', 'submitted' => false])
    <form a-on:submit.prevent="$set('submitted', true)">
        <input
            a-model="email"
            type="email"
            placeholder="Enter email"
        >

        <p a-show="submitted && !email" class="error">
            Email is required
        </p>

        <button type="submit">Subscribe</button>
    </form>
@endaccelade
```

### Toggle Panel

```blade
@accelade(['isOpen' => false])
    <button a-on:click="$toggle('isOpen')">
        <span a-show="!isOpen">Show Details</span>
        <span a-show="isOpen">Hide Details</span>
    </button>

    <div a-show="isOpen" class="panel">
        <p>Panel content goes here...</p>
    </div>
@endaccelade
```

### Tabs

```blade
@accelade(['activeTab' => 'home'])
    <div class="tabs">
        <button
            a-on:click="$set('activeTab', 'home')"
            a-class="{ active: activeTab === 'home' }"
        >Home</button>
        <button
            a-on:click="$set('activeTab', 'profile')"
            a-class="{ active: activeTab === 'profile' }"
        >Profile</button>
        <button
            a-on:click="$set('activeTab', 'settings')"
            a-class="{ active: activeTab === 'settings' }"
        >Settings</button>
    </div>

    <div a-show="activeTab === 'home'">Home content</div>
    <div a-show="activeTab === 'profile'">Profile content</div>
    <div a-show="activeTab === 'settings'">Settings content</div>
@endaccelade
```

### Search Filter

```blade
@accelade(['search' => '', 'items' => ['Apple', 'Banana', 'Cherry', 'Date']])
    <input a-model="search" placeholder="Search...">

    <ul>
        <template a-for="item in items">
            <li a-show="item.toLowerCase().includes(search.toLowerCase())" a-text="item"></li>
        </template>
    </ul>
@endaccelade
```

## Adding Notifications

Send toast notifications from your controllers:

```php
use Accelade\Facades\Notify;

class UserController extends Controller
{
    public function update(Request $request)
    {
        // Update user...

        Notify::success('Profile Updated')
            ->body('Your changes have been saved.')
            ->send();

        return back();
    }
}
```

Or from JavaScript:

```javascript
window.Accelade.notify.success('Saved!', 'Your changes have been saved.');
```

## SPA Navigation

Use the link component for client-side navigation:

```blade
<nav>
    <x-accelade::link href="/">Home</x-accelade::link>
    <x-accelade::link href="/about">About</x-accelade::link>
    <x-accelade::link href="/contact">Contact</x-accelade::link>
</nav>
```

Links automatically:
- Intercept clicks for SPA navigation
- Show a progress bar during loading
- Update the browser URL
- Preserve component state (optional)

## Server State Sync

Persist component state to the server with `a-sync`:

```blade
@accelade(['theme' => 'light', 'fontSize' => 16])
    <div a-sync="theme,fontSize">
        <select a-model="theme">
            <option value="light">Light</option>
            <option value="dark">Dark</option>
        </select>

        <input type="range" a-model="fontSize" min="12" max="24">
    </div>
@endaccelade
```

Changes are automatically debounced and synced to the server.

## Configuration

Publish the config file to customize Accelade:

```bash
php artisan vendor:publish --tag=accelade-config
```

Key options:

```php
// config/accelade.php
return [
    'framework' => 'vanilla',     // vanilla, vue, react, svelte, angular
    'sync_debounce' => 300,       // ms before syncing to server
    'progress' => [
        'color' => '#6366f1',     // progress bar color
        'showBar' => true,
        'includeSpinner' => true,
    ],
];
```

## Demo Pages

Enable the built-in demo to explore all features:

```env
ACCELADE_DEMO_ENABLED=true
```

Then visit `/demo/vanilla` (or `/demo/vue`, `/demo/react`, etc.).

## Next Steps

- [Components](components.md) — Advanced component patterns
- [Notifications](notifications.md) — Full notification API
- [SPA Navigation](spa-navigation.md) — Router configuration
- [Frameworks](frameworks.md) — Using with Vue, React, etc.
- [Configuration](configuration.md) — All config options
- [API Reference](api-reference.md) — Complete API documentation
