# Shared Data

Share data from your Laravel backend to the JavaScript frontend, making it globally accessible across your entire application.

## Overview

Shared data allows you to pass information from PHP to JavaScript without explicitly passing props to every component. This is useful for:

- User authentication state
- Application settings
- Flash messages
- Global configuration
- Any cross-cutting data needed throughout your app

## Text Interpolation

Use `@{{ expression }}` in Blade templates to display JavaScript variables directly in your HTML. The `@` escapes Blade's own syntax, outputting `{{ expression }}` which Accelade evaluates.

```blade
@accelade(['count' => 0, 'name' => 'World'])
    <p>Hello, @{{ name }}!</p>
    <p>Count: @{{ count }}</p>

    {{-- Access shared data --}}
    <p>User: @{{ shared.user.name }}</p>
    <p>Theme: @{{ shared.settings.theme }}</p>

    <button @click="$set('count', count + 1)">Increment</button>
@endaccelade
```

### Available Context

Inside `{{ }}` expressions, you have access to:

- **Component state** - All state variables defined in `@accelade(['key' => value])`
- **`shared`** - All shared data from `Accelade::share()`
- **`$shared`** - Alias for shared data

```blade
@{{ count }}              {{-- Component state --}}
@{{ shared.user.name }}   {{-- Shared data --}}
@{{ $shared.settings }}   {{-- Shared data alias --}}
```

## Basic Usage

### Sharing Data from PHP

Use the `Accelade` facade to share data from anywhere in your application:

```php
use Accelade\Facades\Accelade;

// Share a single value
Accelade::share('appName', config('app.name'));

// Share multiple values at once
Accelade::share([
    'user' => auth()->user()?->only('id', 'name', 'email'),
    'settings' => [
        'theme' => 'dark',
        'language' => 'en',
    ],
]);

// Lazy-loaded data (closure is evaluated only when needed)
Accelade::share('stats', fn () => [
    'users' => User::count(),
    'orders' => Order::count(),
]);
```

### Accessing Data in JavaScript

Access shared data via `window.Accelade.shared`:

```javascript
// Get a value
const appName = window.Accelade.shared.get('appName');

// Get nested values with dot notation
const userName = window.Accelade.shared.get('user.name');
const theme = window.Accelade.shared.get('settings.theme');

// Get with default value
const locale = window.Accelade.shared.get('locale', 'en');

// Check if key exists
if (window.Accelade.shared.has('user')) {
    // User is authenticated
}

// Get all shared data
const allData = window.Accelade.shared.all();
```

## Sharing in Middleware

For data that should be available on every request, create a middleware:

```php
<?php

namespace App\Http\Middleware;

use Accelade\Facades\Accelade;
use Closure;
use Illuminate\Http\Request;

class ShareAcceladeData
{
    public function handle(Request $request, Closure $next)
    {
        Accelade::share([
            'auth' => [
                'user' => $request->user()?->only('id', 'name', 'email', 'avatar'),
                'guest' => !$request->user(),
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'warning' => session('warning'),
            ],
            'app' => [
                'name' => config('app.name'),
                'environment' => app()->environment(),
            ],
        ]);

        return $next($request);
    }
}
```

Register the middleware in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\ShareAcceladeData::class,
    ]);
})
```

## Sharing in Controllers

Share data specific to certain routes:

```php
class DashboardController extends Controller
{
    public function index()
    {
        Accelade::share('dashboard', [
            'widgets' => $this->getWidgets(),
            'notifications' => auth()->user()->unreadNotifications->take(5),
        ]);

        return view('dashboard');
    }
}
```

## Sharing in Service Providers

For truly global data, share in your `AppServiceProvider`:

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Accelade::share('version', config('app.version', '1.0.0'));
        Accelade::share('features', config('features', []));
    }
}
```

## Client-Side Modifications

You can modify shared data on the client side (useful for optimistic updates):

```javascript
// Set a value
window.Accelade.shared.set('theme', 'light');

// Set a nested value
window.Accelade.shared.set('settings.sidebar', 'collapsed');

// Merge multiple values
window.Accelade.shared.merge({
    theme: 'light',
    fontSize: 'large',
});
```

> **Note:** Client-side modifications are not persisted to the server. They only affect the current page session.

## Reactive Subscriptions

Subscribe to changes in shared data:

```javascript
// Subscribe to a specific key
const unsubscribe = window.Accelade.shared.subscribe('theme', (key, newValue, oldValue) => {
    console.log(`Theme changed from ${oldValue} to ${newValue}`);
    document.body.classList.toggle('dark-mode', newValue === 'dark');
});

// Later, unsubscribe when no longer needed
unsubscribe();

// Subscribe to all changes
window.Accelade.shared.subscribeAll((key, newValue, oldValue) => {
    console.log(`Shared data changed: ${key}`);
});
```

## Using in Blade Components

Access shared data in your Blade templates via JavaScript:

```blade
@accelade(['localState' => 'value'])
    <div class="user-greeting">
        Welcome, <span id="user-name">Loading...</span>!
    </div>

    <script>
        document.getElementById('user-name').textContent =
            window.Accelade.shared.get('user.name', 'Guest');
    </script>
@endaccelade
```

## API Reference

### PHP API

#### `Accelade::share(string|array $key, mixed $value = null): self`

Share data globally. Accepts either a key-value pair or an array of key-value pairs.

```php
Accelade::share('key', 'value');
Accelade::share(['key1' => 'value1', 'key2' => 'value2']);
```

#### `Accelade::getShared(string $key, mixed $default = null): mixed`

Get a shared value by key.

```php
$value = Accelade::getShared('key', 'default');
```

#### `Accelade::allShared(): array`

Get all shared data as an array.

```php
$all = Accelade::allShared();
```

#### `Accelade::shared(): SharedData`

Get the underlying SharedData instance for advanced operations.

```php
$shared = Accelade::shared();
$shared->has('key');
$shared->forget('key');
$shared->flush();
```

### JavaScript API

#### `window.Accelade.shared.get<T>(key: string, defaultValue?: T): T`

Get a shared value. Supports dot notation for nested values.

```javascript
const name = Accelade.shared.get('user.name');
const theme = Accelade.shared.get('settings.theme', 'light');
```

#### `window.Accelade.shared.has(key: string): boolean`

Check if a shared key exists.

```javascript
if (Accelade.shared.has('user')) {
    // authenticated
}
```

#### `window.Accelade.shared.all(): object`

Get all shared data.

```javascript
const data = Accelade.shared.all();
```

#### `window.Accelade.shared.set(key: string, value: unknown): void`

Set a shared value (client-side only).

```javascript
Accelade.shared.set('theme', 'dark');
```

#### `window.Accelade.shared.merge(data: object): void`

Merge data into shared data.

```javascript
Accelade.shared.merge({ key1: 'value1', key2: 'value2' });
```

#### `window.Accelade.shared.subscribe(key, callback): () => void`

Subscribe to changes for a specific key. Returns an unsubscribe function.

```javascript
const unsubscribe = Accelade.shared.subscribe('theme', (key, newVal, oldVal) => {
    console.log(`${key}: ${oldVal} -> ${newVal}`);
});
```

#### `window.Accelade.shared.subscribeAll(callback): () => void`

Subscribe to all shared data changes.

```javascript
const unsubscribe = Accelade.shared.subscribeAll((key, newVal, oldVal) => {
    console.log(`Changed: ${key}`);
});
```

## Best Practices

1. **Keep shared data minimal** - Only share data that's truly needed globally
2. **Use lazy loading for expensive data** - Pass closures to defer computation
3. **Organize with namespaces** - Group related data under keys like `auth`, `settings`, `flash`
4. **Don't share sensitive data** - Never share passwords, tokens, or other secrets
5. **Use TypeScript declarations** - Define types for better IDE support

## Demo

Visit `/demo/shared-data` (when demo mode is enabled) to see shared data in action.

## See Also

- [Getting Started](getting-started.md)
- [Components](components.md)
- [API Reference](api-reference.md)
