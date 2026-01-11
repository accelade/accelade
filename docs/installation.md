# Installation

## Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x or 12.x
- **Node.js**: 18+ (for development only)

## Install via Composer

```bash
composer require accelade/accelade
```

The package auto-registers its service provider and facades.

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

    @acceladeStyles
</head>
<body>
    {{ $slot }}

    @acceladeScripts
    @acceladeNotifications
</body>
</html>
```

**Note:** The CSRF token meta tag is required for server sync functionality.

## Verify Installation

Create a quick test:

```blade
@accelade(['count' => 0])
    <button @click="$set('count', count + 1)">
        Clicked: <span a-text="count">0</span>
    </button>
@endaccelade
```

If the counter increments on click, you're ready to go!

## Optional: Publish Config

```bash
php artisan vendor:publish --tag=accelade-config
```

Creates `config/accelade.php` for customization.

## Optional: Publish Assets

For production, serve assets from your public directory:

```bash
php artisan vendor:publish --tag=accelade-assets
```

Then update config:

```php
'asset_mode' => 'published',
```

## Optional: Publish Views

Customize notification templates:

```bash
php artisan vendor:publish --tag=accelade-views
```

## Enable Demo

For development, enable the built-in demo pages:

```env
ACCELADE_DEMO_ENABLED=true
```

Visit `/demo/vanilla` to explore all features.

## Troubleshooting

### Scripts Not Loading

Ensure the CSRF token meta tag is present:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Reactivity Not Working

1. Check browser console for JavaScript errors
2. Ensure `@acceladeScripts` is before `</body>`
3. Verify no JavaScript conflicts

### Assets 404

If using published mode, run:

```bash
php artisan vendor:publish --tag=accelade-assets
```

## Next Steps

- [Getting Started](getting-started.md) — First steps and examples
- [Configuration](configuration.md) — All configuration options
- [Components](components.md) — Building reactive components
