# Accelade

<p align="center">
<strong>Reactive Blade. Zero Complexity.</strong>
</p>

<p align="center">
<a href="https://github.com/accelade/accelade/actions/workflows/tests.yml"><img src="https://github.com/accelade/accelade/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
<a href="https://packagist.org/packages/accelade/accelade"><img src="https://img.shields.io/packagist/v/accelade/accelade" alt="Latest Version"></a>
<a href="https://packagist.org/packages/accelade/accelade"><img src="https://img.shields.io/packagist/dt/accelade/accelade" alt="Total Downloads"></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

---

Add reactivity to your Laravel Blade templates without the overhead of a full SPA framework. Keep your server-rendered templates and sprinkle in reactive components exactly where you need them.

```blade
@accelade(['count' => 0])
    <button @click="$set('count', count + 1)">
        Clicked <span a-text="count">0</span> times
    </button>
@endaccelade
```

**That's it.** No build step required. No JavaScript to write. Just Blade.

---

## Why Accelade?

- **Blade-First** — Your templates stay in Blade. Add reactivity where needed.
- **No Build Required** — Works out of the box. Zero configuration.
- **Multi-Framework** — Use Vanilla JS, Vue, React, Svelte, or Angular syntax.
- **SPA Navigation** — Client-side routing with automatic progress bar.
- **Server Sync** — Seamlessly persist state to Laravel backend.
- **Shared Data** — Pass data from PHP to JavaScript globally across your app.
- **Animations** — Built-in animation presets for smooth transitions.
- **Lazy Loading** — Defer content with beautiful shimmer placeholders.
- **Persistent Layout** — Keep media players and widgets active during navigation.
- **SEO Engine** — Fluent API for managing meta tags, OpenGraph, and Twitter Cards.
- **Toast Notifications** — Beautiful Filament-style notifications from PHP or JS.
- **Lightweight** — ~28KB gzipped. No heavy dependencies.

---

## Quick Start

```bash
composer require accelade/accelade
```

Add to your layout:

```blade
<head>
    @acceladeStyles
</head>
<body>
    {{ $slot }}

    @acceladeScripts
    @acceladeNotifications
</body>
```

Start building:

```blade
@accelade(['name' => ''])
    <input a-model="name" placeholder="Your name">
    <p a-show="name">Hello, <span a-text="name"></span>!</p>
@endaccelade
```

---

## Features at a Glance

### Reactive Components
```blade
@accelade(['items' => [], 'newItem' => ''])
    <input a-model="newItem">
    <button @click="$set('items', [...items, newItem]); $set('newItem', '')">Add</button>
    <ul>
        <template a-for="item in items">
            <li a-text="item"></li>
        </template>
    </ul>
@endaccelade
```

### Server State Sync
```blade
@accelade(['preferences' => $userPreferences])
    <div a-sync="preferences">
        <select a-model="preferences.theme">
            <option value="light">Light</option>
            <option value="dark">Dark</option>
        </select>
    </div>
@endaccelade
```

### Toast Notifications
```php
// From PHP
Notify::success('Saved!')->body('Your changes have been saved.');

// From JavaScript
window.Accelade.notify.success('Success!', 'Operation completed.');
```

### Shared Data
```php
// Share data from PHP (controller, middleware, etc.)
Accelade::share('user', auth()->user()->only('id', 'name'));
Accelade::share('settings', ['theme' => 'dark']);
```

```javascript
// Access in JavaScript anywhere
const userName = window.Accelade.shared.get('user.name');
const theme = window.Accelade.shared.get('settings.theme');
```

### Text Interpolation
```blade
@accelade(['count' => 0, 'name' => 'World'])
    <p>Hello, @{{ name }}!</p>
    <p>Count: @{{ count }}</p>
    <p>User: @{{ shared.user.name }}</p>
    <button @click="$set('count', count + 1)">Click</button>
@endaccelade
```

### Animations & Transitions
```blade
{{-- Simple toggle with animation --}}
<x-accelade::toggle animation="fade">
    <button @click="toggle()">Toggle</button>
    <div a-show="toggled">Fades in and out!</div>
</x-accelade::toggle>

{{-- Accordion with collapse animation --}}
<x-accelade::toggle animation="collapse">
    <button @click="toggle()">FAQ Question</button>
    <div a-show="toggled">Answer content...</div>
</x-accelade::toggle>

{{-- Available presets: fade, scale, collapse, slide-up, slide-down, slide-left, slide-right --}}
```

```php
// Register custom animation preset
Animation::new(
    name: 'bounce',
    enter: 'transition ease-bounce duration-300',
    enterFrom: 'opacity-0 scale-50',
    enterTo: 'opacity-100 scale-100',
    leave: 'transition ease-in duration-200',
    leaveFrom: 'opacity-100 scale-100',
    leaveTo: 'opacity-0 scale-50',
);
```

### Lazy Loading with Shimmer
```blade
<x-accelade::lazy :shimmer="true">
    @foreach($items as $item)
        <div class="card">{{ $item->name }}</div>
    @endforeach
</x-accelade::lazy>

{{-- Circle shimmer for avatars --}}
<x-accelade::lazy :shimmer="true" :shimmer-circle="true">
    <img src="{{ $user->avatar }}" alt="Avatar">
</x-accelade::lazy>
```

### SEO Management
```blade
@seoTitle('My Page Title')
@seoDescription('Page description for search engines')
@seoKeywords('laravel, blade, reactive')
@seoOpenGraph(['type' => 'article', 'image' => '/og-image.jpg'])

{{-- In layout <head> --}}
@seo
```

```php
// From PHP controller
SEO::title($post->title)
    ->description($post->excerpt)
    ->openGraphImage($post->featured_image);
```

### SPA Navigation
```blade
<x-accelade::link href="/dashboard">Dashboard</x-accelade::link>
```

### Content Component
```blade
{{-- Render pre-rendered HTML (Markdown, CMS content, etc.) --}}
<x-accelade::content :html="$renderedMarkdown" />

{{-- With custom wrapper and styling --}}
<x-accelade::content as="article" class="prose dark:prose-invert" :html="$html" />
```

---

## Choose Your Framework

Accelade adapts to your preferred syntax. Events use `@click`, `@submit`, etc. across all frameworks:

| Framework | Prefix | Example |
|-----------|--------|---------|
| Vanilla JS | `a-` | `<span a-text="name">`, `<button @click="...">` |
| Vue | `v-` | `<span v-text="name">`, `<button @click="...">` |
| React | `data-state-` | `<span data-state-text="name">`, `<button @click="...">` |
| Svelte | `s-` | `<span s-text="name">`, `<button @click="...">` |
| Angular | `ng-` | `<span ng-text="name">`, `<button @click="...">` |

```env
ACCELADE_FRAMEWORK=vue
```

---

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x

---

## Documentation

### Getting Started

| Guide | Description |
|-------|-------------|
| [Installation](docs/installation.md) | Install and configure Accelade |
| [Getting Started](docs/getting-started.md) | First steps and basic concepts |
| [Configuration](docs/configuration.md) | All config options |
| [Architecture](docs/architecture.md) | How Accelade works under the hood |

### Core Components

| Component | Description |
|-----------|-------------|
| [State](docs/state.md) | Reactive state management |
| [Data](docs/data.md) | Reactive data with storage persistence |
| [Toggle](docs/toggle.md) | Toggle visibility with animations |
| [Modal](docs/modal.md) | Modal dialogs and overlays |
| [Link](docs/link.md) | SPA navigation links |
| [Content](docs/content.md) | Render pre-rendered HTML (Markdown, CMS) |
| [Flash](docs/flash.md) | Flash message component |
| [Event](docs/event.md) | Event handling component |
| [Teleport](docs/teleport.md) | Teleport content to other DOM locations |
| [Rehydrate](docs/rehydrate.md) | Rehydrate server-rendered content |

### UI Components

| Component | Description |
|-----------|-------------|
| [Code Block](docs/code-block.md) | Syntax highlighted code blocks |
| [Icon](docs/icon.md) | Icon component with Blade Icons |
| [Tooltip](docs/tooltip.md) | Tooltip component |
| [Draggable](docs/draggable.md) | Drag and drop functionality |
| [Chart](docs/chart.md) | Chart.js and ApexCharts integration |
| [Calendar](docs/calendar.md) | Calendar component |

### Features

| Guide | Description |
|-------|-------------|
| [SPA Navigation](docs/spa-navigation.md) | Client-side routing |
| [Animations](docs/animations.md) | Animation presets and transitions |
| [Lazy Loading](docs/lazy-loading.md) | Deferred content with shimmer |
| [Persistent Layout](docs/persistent-layout.md) | Keep elements active during navigation |
| [Shared Data](docs/shared-data.md) | Share data from PHP to JavaScript |
| [Event Bus](docs/event-bus.md) | Decoupled component communication |
| [Bridge](docs/bridge.md) | Two-way PHP/JavaScript binding (Beta) |
| [Scripts](docs/scripts.md) | Script management |
| [Notifications](docs/notifications.md) | Toast notification system |
| [SEO](docs/seo.md) | Meta tags, OpenGraph, Twitter Cards |
| [Exception Handling](docs/exception-handling.md) | Error handling and display |

### Advanced

| Guide | Description |
|-------|-------------|
| [Frameworks](docs/frameworks.md) | Vue, React, Svelte, Angular adapters |
| [MCP Server](docs/mcp-server.md) | AI-assisted development with Claude |
| [Testing](docs/testing.md) | Testing your Accelade components |
| [API Reference](docs/api-reference.md) | Complete API docs |
| [Contributing](docs/contributing.md) | How to contribute |

---

## Accelade Ecosystem

Accelade is part of a larger ecosystem of packages designed to work together seamlessly:

| Package | Description |
|---------|-------------|
| **[accelade/schemas](https://github.com/accelade/schemas)** | Schema-based layouts with sections, tabs, grids, wizards, and more |
| **[accelade/forms](https://github.com/accelade/forms)** | Form builder with validation, file uploads, and rich inputs |
| **[accelade/infolists](https://github.com/accelade/infolists)** | Display read-only data with Filament-compatible API |
| **[accelade/tables](https://github.com/accelade/tables)** | Data tables with sorting, filtering, and pagination |
| **[accelade/actions](https://github.com/accelade/actions)** | Action buttons with modals, confirmations, and bulk operations |
| **[accelade/widgets](https://github.com/accelade/widgets)** | Dashboard widgets including stats, charts, and tables |
| **[accelade/filters](https://github.com/accelade/filters)** | Advanced filtering components |
| **[accelade/grids](https://github.com/accelade/grids)** | Grid and card layouts |
| **[accelade/query-builder](https://github.com/accelade/query-builder)** | Visual query builder component |
| **[accelade/ai](https://github.com/accelade/ai)** | AI-powered features and integrations |

All packages follow the same Blade-first philosophy and work together without requiring a full SPA framework.

---

## Credits

Built with inspiration from [Livewire](https://livewire.laravel.com), [Filament](https://filamentphp.com), and [Inertia.js](https://inertiajs.com).

---

## Sponsors

If you find Accelade useful, please consider [sponsoring](docs/sponsor.md) the project. Your support helps maintain and improve the ecosystem.

---

## License

MIT License. See [LICENSE](LICENSE) for details.
