# Configuration

All Accelade configuration is done through `config/accelade.php`.

## Publish Configuration

```bash
php artisan vendor:publish --tag=accelade-config
```

## Configuration Options

### Framework

```php
'framework' => env('ACCELADE_FRAMEWORK', 'vanilla'),
```

Choose the frontend framework adapter:

| Value | Description |
|-------|-------------|
| `vanilla` | Vanilla JavaScript (default, no dependencies) |
| `vue` | Vue 3 with Composition API |
| `react` | React 18 with hooks |
| `svelte` | Svelte 4 with stores |
| `angular` | Angular 17+ with signals |

### Asset Serving Mode

```php
'asset_mode' => env('ACCELADE_ASSET_MODE', 'route'),
```

| Value | Description |
|-------|-------------|
| `route` | Serve assets via Laravel route (default) |
| `published` | Serve from `public/vendor/accelade` |

### Route Prefix

```php
'prefix' => 'accelade',
```

URL prefix for Accelade routes. Default routes:

- `GET /accelade/accelade-v2.js` - JavaScript bundle
- `POST /accelade/update` - State sync endpoint
- `POST /accelade/batch-update` - Batch state sync

### Middleware

```php
'middleware' => ['web'],
```

Middleware applied to Accelade routes.

### State TTL

```php
'state_ttl' => env('ACCELADE_STATE_TTL', 3600),
```

Time-to-live in seconds for server-synced component state. Default: 1 hour.

### Sync Debounce

```php
'sync_debounce' => env('ACCELADE_SYNC_DEBOUNCE', 300),
```

Debounce time in milliseconds for server sync requests. Prevents excessive server calls during rapid state changes.

### Progress Bar

```php
'progress' => [
    'color' => '#6366f1',           // Primary color
    'gradientColor' => '#8b5cf6',   // Gradient color 1
    'gradientColor2' => '#a855f7',  // Gradient color 2
    'height' => 3,                   // Bar height in pixels
    'showBar' => true,               // Show progress bar
    'includeSpinner' => true,        // Show spinner
    'spinnerPosition' => 'top-right', // Spinner position
    'position' => 'top',             // Bar position (top/bottom)
],
```

Available spinner positions:
- `top-left`
- `top-right`
- `bottom-left`
- `bottom-right`

### Demo Mode

```php
'demo' => [
    'enabled' => env('ACCELADE_DEMO_ENABLED', env('APP_ENV') !== 'production'),
    'prefix' => env('ACCELADE_DEMO_PREFIX', 'demo'),
    'middleware' => ['web'],
],
```

Demo routes showcase all Accelade features. Disabled in production by default.

Demo routes when enabled:
- `/demo/vanilla` - Vanilla JS demo
- `/demo/vue` - Vue demo
- `/demo/react` - React demo
- `/demo/svelte` - Svelte demo
- `/demo/angular` - Angular demo
- `/demo/notify/{type}` - Backend notification demos

### Testing

```php
'testing' => [
    'base_url' => env('ACCELADE_TEST_URL', env('APP_URL', 'http://localhost')),
],
```

Configuration for E2E tests.

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `ACCELADE_FRAMEWORK` | `vanilla` | Frontend framework |
| `ACCELADE_ASSET_MODE` | `route` | Asset serving mode |
| `ACCELADE_STATE_TTL` | `3600` | State cache TTL |
| `ACCELADE_SYNC_DEBOUNCE` | `300` | Sync debounce (ms) |
| `ACCELADE_DEMO_ENABLED` | auto | Enable demo routes |
| `ACCELADE_DEMO_PREFIX` | `demo` | Demo routes prefix |
| `ACCELADE_TEST_URL` | `APP_URL` | E2E test base URL |

## JavaScript Configuration

Accelade exposes configuration to JavaScript via `window.AcceladeConfig`:

```javascript
window.AcceladeConfig = {
    framework: 'vanilla',
    csrfToken: '...',
    updateUrl: '/accelade/update',
    batchUpdateUrl: '/accelade/batch-update',
    syncDebounce: 300,
    progress: { /* progress bar options */ },
    notifications: [ /* pending notifications */ ],
};
```

You can override settings before Accelade initializes:

```html
<script>
    window.AcceladeConfig = {
        ...window.AcceladeConfig,
        syncDebounce: 500,
        progress: { color: '#ff0000' }
    };
</script>
@acceladeScripts
```

## Next Steps

- [Components](components.md) - Create reactive components
- [Architecture](architecture.md) - Understanding the internals
