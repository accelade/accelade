# Installation

This guide covers installing and setting up Accelade in your Laravel application.

## Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x or 12.x
- **Node.js**: 18+ (for development/building only)

## Install via Composer

```bash
composer require accelade/accelade
```

The package will auto-register its service provider and facades.

## Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=accelade-config
```

This publishes `config/accelade.php` for customization.

## Publish Assets (Optional)

If you prefer serving assets from your public directory instead of via route:

```bash
php artisan vendor:publish --tag=accelade-assets
```

Then update your config:

```php
// config/accelade.php
'asset_mode' => 'published',
```

## Add to Layout

Include Accelade in your main layout file:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'My App' }}</title>

    {{-- Accelade styles (notifications, progress bar) --}}
    @acceladeStyles
</head>
<body>
    {{ $slot }}

    {{-- Accelade scripts --}}
    @acceladeScripts

    {{-- Notifications container --}}
    @acceladeNotifications
</body>
</html>
```

## Verify Installation

Create a test route:

```php
// routes/web.php
Route::get('/accelade-test', function () {
    return view('accelade-test');
});
```

Create the view:

```blade
{{-- resources/views/accelade-test.blade.php --}}
@extends('layouts.app')

@section('content')
    @accelade(['count' => 0])
        <div style="text-align: center; padding: 2rem;">
            <h1>Accelade Test</h1>
            <p>Count: <span a-text="count" style="font-size: 2rem; font-weight: bold;">0</span></p>
            <button a-on:click="$set('count', count + 1)">Increment</button>
            <button a-on:click="$set('count', count - 1)">Decrement</button>
        </div>
    @endaccelade
@endsection
```

Visit `/accelade-test` - you should see a working counter.

## Using the Install Command

For a guided setup with framework selection:

```bash
php artisan accelade:install --framework=vue
```

Options:
- `--framework=vue` - Use Vue.js adapter
- `--framework=react` - Use React adapter

## Troubleshooting

### Scripts Not Loading

Ensure you have the CSRF token meta tag:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Reactivity Not Working

Check browser console for JavaScript errors. Ensure:

1. `@acceladeScripts` is placed before `</body>`
2. No JavaScript conflicts with other libraries
3. The `web` middleware is applied to your routes

### Assets 404

If using published assets mode, ensure you've published them:

```bash
php artisan vendor:publish --tag=accelade-assets
```

## Next Steps

- [Configuration](configuration.md) - Customize Accelade settings
- [Components](components.md) - Create reactive components
- [Frameworks](frameworks.md) - Use Vue, React, or other frameworks
