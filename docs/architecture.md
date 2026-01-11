# Architecture

This document describes the internal architecture and design decisions of Accelade.

## Overview

Accelade follows a hybrid architecture that combines server-rendered Blade templates with client-side reactivity. The key principle is **progressive enhancement** - your application works without JavaScript, and Accelade adds reactivity on top.

```
┌─────────────────────────────────────────────────────────────────┐
│                         Laravel Backend                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Blade     │  │  Service    │  │     Notification        │  │
│  │  Compiler   │  │  Provider   │  │       Manager           │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
├─────────────────────────────────────────────────────────────────┤
│                      Accelade Core (JS)                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Router    │  │  Progress   │  │     Notification        │  │
│  │  (SPA Nav)  │  │    Bar      │  │       System            │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
├─────────────────────────────────────────────────────────────────┤
│                    Framework Adapters                            │
├──────────┬──────────┬──────────┬──────────┬────────────────────┤
│  Vanilla │   Vue    │  React   │  Svelte  │      Angular       │
└──────────┴──────────┴──────────┴──────────┴────────────────────┘
```

## Directory Structure

```
packages/accelade/
├── config/
│   └── accelade.php           # Configuration file
├── dist/                       # Compiled JavaScript bundles
├── docs/                       # Documentation
├── resources/
│   ├── js/                    # TypeScript source
│   │   ├── core/              # Core functionality
│   │   │   ├── router.ts      # SPA navigation
│   │   │   ├── progress.ts    # Progress bar
│   │   │   ├── notification/  # Notification system
│   │   │   ├── state.ts       # State management
│   │   │   ├── types.ts       # TypeScript types
│   │   │   └── utils.ts       # Utilities
│   │   ├── vanilla/           # Vanilla JS adapter
│   │   ├── vue/               # Vue adapter
│   │   └── react/             # React adapter
│   └── views/
│       ├── components/        # Blade components
│       └── demo/              # Demo views
├── routes/
│   ├── web.php                # Core routes
│   └── demo.php               # Demo routes
├── src/
│   ├── Accelade.php           # Main class
│   ├── AcceladeServiceProvider.php
│   ├── Compilers/
│   │   └── AcceladeTagCompiler.php  # Tag compiler
│   ├── Components/            # Blade components
│   ├── Console/               # Artisan commands
│   ├── Facades/               # Laravel facades
│   ├── Http/Controllers/      # HTTP controllers
│   ├── Notification/          # Notification system
│   └── Support/               # Support classes
└── tests/
    ├── Feature/               # Feature tests
    └── Unit/                  # Unit tests
```

## PHP Components

### AcceladeServiceProvider

Registers all package services:

```php
// Singletons
$this->app->singleton('accelade', fn($app) => new Accelade($app));
$this->app->singleton('accelade.notify', fn($app) => new NotificationManager());

// Blade directives
Blade::directive('acceladeScripts', ...);
Blade::directive('acceladeStyles', ...);
Blade::directive('accelade', ...);
Blade::directive('endaccelade', ...);

// Component namespace
Blade::componentNamespace('Accelade\\Components', 'accelade');
```

### Accelade Class

The main class handles:

1. **Script/Style Rendering** - Generates JavaScript config and includes
2. **Component Stack** - Manages nested `@accelade` blocks
3. **State Serialization** - Converts PHP state to JSON for client

### AcceladeTagCompiler

Pre-processes Blade templates to transform `<x-accelade:*>` tags:

```blade
{{-- Input --}}
<x-accelade:counter :initial-count="5" />

{{-- Output (after compilation) --}}
@acceladeComponent('counter', ['initial-count' => 5])
    {{-- Component content --}}
@endacceladeComponent
```

### Notification System

```
┌──────────────────┐     ┌───────────────────┐
│   Notification   │────▶│ NotificationManager│
│    (Entity)      │     │   (Collection)     │
└──────────────────┘     └─────────┬─────────┘
                                   │
                         ┌─────────▼─────────┐
                         │  Session Store    │
                         │ (Flash Messages)  │
                         └───────────────────┘
```

Notifications persist across redirects using Laravel's session flash.

## JavaScript Components

### Core Modules

| Module | Purpose |
|--------|---------|
| `router.ts` | SPA navigation, intercepts links |
| `progress.ts` | Progress bar during navigation |
| `notification/` | Toast notification rendering |
| `state.ts` | Reactive state management |
| `utils.ts` | DOM parsing, expression evaluation |

### Framework Adapters

Each adapter implements reactivity for its framework:

```typescript
// Vanilla: Proxy-based reactivity
const state = new Proxy(initialState, {
    set(target, key, value) {
        target[key] = value;
        updateDOM();
        return true;
    }
});

// Vue: reactive()
const state = reactive(initialState);
watch(state, () => updateDOM());

// React: useState hooks
const [state, setState] = useState(initialState);
```

### Build Process

```
resources/js/
├── vanilla/accelade.ts  ──┐
├── vue/accelade.ts      ──┼──▶ dist/accelade-{framework}.js
└── react/accelade.tsx   ──┘
```

Vite bundles each framework separately as IIFE modules.

## Request/Response Flow

### Initial Page Load

```
1. Browser requests page
2. Laravel renders Blade template
3. @accelade directive creates component wrapper
4. State is serialized to data-accelade-state attribute
5. @acceladeScripts injects JavaScript
6. Client-side: Accelade.init() finds components
7. Components become reactive
```

### State Sync

```
1. User changes state (e.g., clicks button)
2. State updates locally (immediate feedback)
3. Debounced: sync request sent to /accelade/update
4. Server validates and persists state
5. Response confirms or provides new state
```

### SPA Navigation

```
1. User clicks <x-accelade::link>
2. Router intercepts click event
3. Progress bar starts
4. Fetch request for new page
5. Response HTML parsed
6. DOM diffed and updated (no full reload)
7. New components initialized
8. Progress bar completes
```

## State Management

### Client-Side State

Each component has isolated state stored in a Proxy object:

```javascript
// Component state structure
{
    componentId: 'accelade-abc123',
    state: { count: 0, name: '' },
    syncedProperties: ['count'],
    actions: { /* custom functions */ }
}
```

### Server-Side State

Server state uses Laravel's Cache:

```php
// Cache key format
"accelade.state.{$componentId}"

// Data stored
['count' => 5, 'name' => 'John']
```

## Security Considerations

1. **CSRF Protection** - All sync requests include CSRF token
2. **State Validation** - Server validates incoming state changes
3. **XSS Prevention** - State values are escaped when rendered
4. **Rate Limiting** - Debounce prevents request flooding

## Performance Optimizations

1. **Debounced Sync** - Batches rapid state changes
2. **Selective DOM Updates** - Only updates changed elements
3. **Lazy Loading** - Components initialize on visibility
4. **Minimal Bundle** - Tree-shaking removes unused code

## Extending Accelade

### Custom Components

```php
// app/View/Components/CustomCounter.php
class CustomCounter extends AcceladeComponent
{
    public int $count = 0;

    public function render()
    {
        return view('components.custom-counter');
    }
}
```

### Custom Directives

```php
// In a service provider
Blade::directive('customDirective', function ($expression) {
    return "<?php /* custom logic */ ?>";
});
```

### JavaScript Extensions

```javascript
// Extend Accelade globally
window.Accelade.extend({
    customMethod() {
        // Custom functionality
    }
});
```

## Next Steps

- [API Reference](api-reference.md) - Complete API documentation
- [Testing](testing.md) - Running and writing tests
