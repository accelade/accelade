# State Component

The Accelade State component provides unified access to validation errors, flash messages, and shared data within a reactive scope. It wraps your content and exposes helper methods for accessing these common data sources.

## Basic Usage

### Validation Errors

Display validation errors from Laravel's error bag:

```blade
<x-accelade::state>
    {{-- Show general error message if any errors exist --}}
    <div a-show="state.hasErrors" class="alert alert-danger">
        Please fix the errors below.
    </div>

    {{-- Email field with error --}}
    <div>
        <label>Email</label>
        <input type="email" name="email">
        <span a-show="hasError('email')" class="text-red-500" a-text="getError('email')"></span>
    </div>

    {{-- Password field with error --}}
    <div>
        <label>Password</label>
        <input type="password" name="password">
        <span a-show="hasError('password')" class="text-red-500" a-text="getError('password')"></span>
    </div>
</x-accelade::state>
```

### Flash Messages

Display session flash data:

```blade
<x-accelade::state>
    {{-- Success message --}}
    <div a-show="hasFlash('success')" class="alert alert-success">
        <span a-text="getFlash('success')"></span>
    </div>

    {{-- Info message --}}
    <div a-show="hasFlash('info')" class="alert alert-info">
        <span a-text="getFlash('info')"></span>
    </div>

    {{-- Warning message --}}
    <div a-show="hasFlash('warning')" class="alert alert-warning">
        <span a-text="getFlash('warning')"></span>
    </div>
</x-accelade::state>
```

### Shared Data

Access globally shared data:

```blade
<x-accelade::state>
    {{-- Display user info --}}
    <div a-show="hasShared('user')">
        Welcome, <span a-text="getShared('user.name')"></span>!
        Your role: <span a-text="getShared('user.role')"></span>
    </div>

    {{-- Access app settings --}}
    <p>Current theme: <span a-text="getShared('settings.theme')"></span></p>
</x-accelade::state>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `errors` | array | error bag | Custom validation errors (uses Laravel error bag by default) |
| `flash` | array | session flash | Custom flash data (uses session flash by default) |
| `shared` | array | Accelade shared | Custom shared data (uses Accelade shared data by default) |

## State Object

The `state` object is available within the component scope and contains:

| Property | Type | Description |
|----------|------|-------------|
| `state.errors` | object | First error message for each field |
| `state.rawErrors` | object | All error messages per field (arrays) |
| `state.hasErrors` | boolean | Whether any errors exist |
| `state.flash` | object | All flash data |
| `state.shared` | object | All shared data |

## Helper Methods

### Error Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `hasError(key)` | boolean | Check if a field has an error |
| `getError(key)` | string\|null | Get the first error message for a field |
| `getErrors(key)` | string[] | Get all error messages for a field |

### Flash Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `hasFlash(key)` | boolean | Check if flash data exists for key |
| `getFlash(key)` | any\|null | Get flash value |

### Shared Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `hasShared(key)` | boolean | Check if shared data exists (supports dot notation) |
| `getShared(key)` | any\|null | Get shared value (supports dot notation) |

## Custom Data

Override the default data sources with custom arrays:

```blade
<x-accelade::state
    :errors="['email' => ['Invalid email format']]"
    :flash="['success' => 'Changes saved!']"
    :shared="['customKey' => 'customValue']"
>
    <div a-show="hasError('email')" a-text="getError('email')"></div>
    <div a-show="hasFlash('success')" a-text="getFlash('success')"></div>
    <p a-text="getShared('customKey')"></p>
</x-accelade::state>
```

## Direct State Access

You can also access the state object directly:

```blade
<x-accelade::state>
    {{-- Access errors directly --}}
    <span a-text="state.errors.email"></span>

    {{-- Access flash directly --}}
    <span a-text="state.flash.message"></span>

    {{-- Access nested shared data --}}
    <span a-text="state.shared.user?.name"></span>

    {{-- Conditional with hasErrors --}}
    <div a-show="state.hasErrors" class="error-summary">
        You have errors in your form.
    </div>
</x-accelade::state>
```

## Dot Notation for Shared Data

The `hasShared()` and `getShared()` methods support dot notation for nested data:

```php
// In your controller or service provider
Accelade::share('user', [
    'profile' => [
        'name' => 'John Doe',
        'settings' => [
            'theme' => 'dark',
            'notifications' => true
        ]
    ]
]);
```

```blade
<x-accelade::state>
    {{-- Access deeply nested values --}}
    <span a-text="getShared('user.profile.name')"></span>
    <span a-text="getShared('user.profile.settings.theme')"></span>

    {{-- Check if nested path exists --}}
    <div a-show="hasShared('user.profile.settings')">
        Settings configured!
    </div>
</x-accelade::state>
```

## Form Validation Example

A complete form with validation error display:

```blade
<x-accelade::state>
    {{-- Global error message --}}
    <div a-show="state.hasErrors" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <p class="font-bold">Validation Error</p>
        <p>Please correct the errors below.</p>
    </div>

    <form method="POST" action="/register">
        @csrf

        {{-- Name field --}}
        <div class="mb-4">
            <label class="block font-medium">Name</label>
            <input type="text" name="name" class="border rounded p-2 w-full"
                :class="{ 'border-red-500': hasError('name') }">
            <p a-show="hasError('name')" class="text-red-500 text-sm mt-1" a-text="getError('name')"></p>
        </div>

        {{-- Email field --}}
        <div class="mb-4">
            <label class="block font-medium">Email</label>
            <input type="email" name="email" class="border rounded p-2 w-full"
                :class="{ 'border-red-500': hasError('email') }">
            <p a-show="hasError('email')" class="text-red-500 text-sm mt-1" a-text="getError('email')"></p>
        </div>

        {{-- Password field with multiple errors --}}
        <div class="mb-4">
            <label class="block font-medium">Password</label>
            <input type="password" name="password" class="border rounded p-2 w-full"
                :class="{ 'border-red-500': hasError('password') }">
            <p a-show="hasError('password')" class="text-red-500 text-sm mt-1" a-text="getError('password')"></p>
        </div>

        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">
            Register
        </button>
    </form>
</x-accelade::state>
```

## Flash Messages Example

Display various types of flash messages:

```blade
<x-accelade::state>
    {{-- Success notification --}}
    <div a-show="hasFlash('success')" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <span class="font-bold">Success!</span>
        <span a-text="getFlash('success')"></span>
    </div>

    {{-- Error notification --}}
    <div a-show="hasFlash('error')" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <span class="font-bold">Error!</span>
        <span a-text="getFlash('error')"></span>
    </div>

    {{-- Info notification --}}
    <div a-show="hasFlash('info')" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
        <span class="font-bold">Info:</span>
        <span a-text="getFlash('info')"></span>
    </div>

    {{-- Warning notification --}}
    <div a-show="hasFlash('warning')" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        <span class="font-bold">Warning:</span>
        <span a-text="getFlash('warning')"></span>
    </div>
</x-accelade::state>
```

## User Profile Example

Display user information from shared data:

```blade
<x-accelade::state>
    <div a-show="hasShared('user')" class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                <span class="text-2xl" a-text="getShared('user.name')?.charAt(0)"></span>
            </div>

            {{-- User info --}}
            <div>
                <h2 class="text-xl font-bold" a-text="getShared('user.name')"></h2>
                <p class="text-gray-600" a-text="getShared('user.email')"></p>
                <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded"
                    a-text="getShared('user.role')"></span>
            </div>
        </div>
    </div>

    <div a-show="!hasShared('user')" class="text-gray-500">
        Please log in to view your profile.
    </div>
</x-accelade::state>
```

## Sharing Data in Laravel

Use the Accelade facade to share data globally:

```php
// In a service provider or middleware
use Accelade\Facades\Accelade;

Accelade::share('user', [
    'id' => auth()->id(),
    'name' => auth()->user()->name,
    'email' => auth()->user()->email,
    'role' => auth()->user()->role,
]);

Accelade::share('settings', [
    'theme' => 'dark',
    'language' => 'en',
]);

// Or share multiple at once
Accelade::share([
    'appName' => config('app.name'),
    'currentYear' => date('Y'),
]);
```

## Framework Attributes

The State component works with all framework attribute prefixes:

| Framework | Attribute Prefix | Example |
|-----------|-----------------|---------|
| Vanilla | `a-` | `a-show="hasError('email')"` |
| Vue | `v-` | `v-show="hasError('email')"` |
| React | `data-state-` | `data-state-show="hasError('email')"` |
| Svelte | `s-` | `s-show="hasError('email')"` |
| Angular | `ng-` | `ng-show="hasError('email')"` |

## Next Steps

- [Components](components.md) - Reactive components
- [Errors Component](errors.md) - Dedicated error display
- [Flash Component](flash.md) - Flash message helpers
- [Shared Data](shared-data.md) - Global data sharing
