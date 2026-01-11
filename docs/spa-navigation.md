# SPA Navigation

Accelade includes a built-in router for single-page application (SPA) style navigation without full page reloads.

## Overview

When you click an `<x-accelade::link>` component, Accelade:

1. Intercepts the click event
2. Shows a progress bar
3. Fetches the new page via AJAX
4. Updates the DOM without a full reload
5. Updates the browser history
6. Initializes new components

## Link Component

### Basic Usage

```blade
<x-accelade::link href="/dashboard">
    Dashboard
</x-accelade::link>
```

Renders as:

```html
<a href="/dashboard" data-accelade-link>Dashboard</a>
```

### With Attributes

```blade
<x-accelade::link
    href="/settings"
    class="nav-link active"
    id="settings-link"
>
    Settings
</x-accelade::link>
```

### Preserve Scroll Position

Maintain scroll position after navigation:

```blade
<x-accelade::link href="/products" :preserveScroll="true">
    Products
</x-accelade::link>
```

### Preserve Component State

Keep component state across navigation:

```blade
<x-accelade::link href="/page-2" :preserveState="true">
    Next Page
</x-accelade::link>
```

### External Links

Links to other domains are not intercepted:

```blade
{{-- Opens normally (not SPA) --}}
<x-accelade::link href="https://external-site.com">
    External
</x-accelade::link>
```

## Programmatic Navigation

Navigate using JavaScript:

```javascript
// Basic navigation
window.Accelade.navigate('/dashboard');

// With options
window.Accelade.navigate('/settings', {
    preserveScroll: true,
    preserveState: true
});

// Replace history (no back button)
window.Accelade.router.replace('/login');
```

## Progress Bar

The progress bar shows during navigation.

### Configuration

```php
// config/accelade.php
'progress' => [
    'delay' => 250,              // Delay before showing (ms)
    'color' => '#6366f1',        // Primary color
    'gradientColor' => '#8b5cf6', // Gradient start
    'gradientColor2' => '#a855f7', // Gradient end
    'useGradient' => true,       // Use gradient
    'height' => 3,               // Height in pixels
    'showBar' => true,           // Show the bar
    'includeSpinner' => true,    // Show spinner
    'spinnerSize' => 18,         // Spinner size (px)
    'spinnerPosition' => 'top-right',
    'position' => 'top',         // top or bottom
    'minimum' => 8,              // Starting percentage
    'easing' => 'ease-out',
    'speed' => 200,              // Animation speed (ms)
    'trickleSpeed' => 200,       // Trickle speed (ms)
    'zIndex' => 99999,
],
```

### Manual Control

```javascript
// Start progress
window.Accelade.progress.start();

// Complete progress
window.Accelade.progress.done();

// Force complete (immediate)
window.Accelade.progress.done(true);

// Set specific percentage
window.Accelade.progress.set(50);

// Increment
window.Accelade.progress.inc();
```

### Customizing via CSS

```css
/* Progress bar */
#accelade-progress-bar {
    background: linear-gradient(to right, #ff0000, #ff6600) !important;
}

/* Spinner */
#accelade-spinner {
    border-top-color: #ff0000 !important;
}
```

## Navigation Events

Listen to navigation events:

```javascript
// Before navigation starts
document.addEventListener('accelade:navigate-start', (event) => {
    console.log('Navigating to:', event.detail.url);
});

// After navigation completes
document.addEventListener('accelade:navigate-end', (event) => {
    console.log('Navigated to:', event.detail.url);
});

// Navigation error
document.addEventListener('accelade:navigate-error', (event) => {
    console.error('Navigation failed:', event.detail.error);
});
```

## Handling Non-SPA Links

### Exclude Specific Links

Add `data-no-spa` to skip SPA handling:

```html
<a href="/download" data-no-spa>Download PDF</a>
```

### Links with Target

Links with `target="_blank"` are not intercepted:

```blade
<x-accelade::link href="/document" target="_blank">
    Open in New Tab
</x-accelade::link>
```

### Form Submissions

Form submissions trigger full page loads by default:

```html
<form action="/submit" method="POST">
    {{-- Normal form submission --}}
</form>
```

For AJAX form handling, use JavaScript:

```javascript
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    // Handle with fetch/axios
});
```

## History Management

### Back/Forward Navigation

Accelade handles browser back/forward buttons automatically.

### Replace vs Push

```javascript
// Push (adds to history)
window.Accelade.navigate('/new-page');

// Replace (replaces current entry)
window.Accelade.router.replace('/new-page');
```

### Current URL

```javascript
const currentUrl = window.Accelade.router.currentUrl;
```

## Prefetching

Prefetch pages on hover for faster navigation:

```blade
<x-accelade::link href="/heavy-page" prefetch>
    Heavy Page
</x-accelade::link>
```

Or manually:

```javascript
window.Accelade.router.prefetch('/heavy-page');
```

## Loading States

Show loading indicators during navigation:

```blade
@accelade(['loading' => false])
    <div a-show="loading" class="loading-overlay">
        Loading...
    </div>

    <div a-show="!loading">
        {{-- Page content --}}
    </div>

    <accelade:script>
        return {
            init() {
                document.addEventListener('accelade:navigate-start', () => {
                    $set('loading', true);
                });
                document.addEventListener('accelade:navigate-end', () => {
                    $set('loading', false);
                });
            }
        };
    </accelade:script>
@endaccelade
```

## Framework Switching

When navigating between pages with different frameworks, a full page reload occurs automatically to load the correct adapter.

```blade
{{-- On /demo/vue (Vue adapter) --}}
<x-accelade::link href="/demo/react">
    Go to React
</x-accelade::link>
{{-- Full reload happens because framework changes --}}
```

## Performance Tips

1. **Use preserveState** for wizard-like flows
2. **Prefetch common pages** on hover
3. **Keep page sizes small** for faster transitions
4. **Use skeleton loaders** for perceived performance
5. **Cache repeated requests** on the server

## Troubleshooting

### Navigation Not Working

1. Ensure `@acceladeScripts` is included
2. Check for JavaScript errors in console
3. Verify links use `<x-accelade::link>` component

### Progress Bar Not Showing

1. Check progress config: `showBar => true`
2. Verify CSS isn't hiding `#accelade-progress-bar`
3. Check `delay` setting (default 250ms)

### State Lost on Navigation

Use `preserveState`:

```blade
<x-accelade::link href="/page" :preserveState="true">
```

Or store state on server with `a-sync`.

## Next Steps

- [Components](components.md) - Reactive components
- [Notifications](notifications.md) - Toast notifications
- [Configuration](configuration.md) - All options
