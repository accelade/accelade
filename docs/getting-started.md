<p align="center">
  <img src="/vendor/accelade/logo-dark.png" alt="Accelade" style="height: 36px; width: auto;" class="dark:hidden">
  <img src="/vendor/accelade/logo-light.png" alt="Accelade" style="height: 36px; width: auto;" class="hidden dark:block">
</p>

<p align="center">
  <strong>Reactive Blade Components for Laravel</strong><br>
  Build dynamic, interactive UIs without leaving your Blade templates.
</p>

<p align="center">
  <a href="https://github.com/accelade/accelade">GitHub</a> &bull;
  <a href="/docs/installation">Installation</a> &bull;
  <a href="/docs/api-reference">API Reference</a> &bull;
  <a href="/docs/sponsor">Sponsor</a>
</p>

---

# Getting Started

Welcome to Accelade — the reactive Blade template library that brings modern frontend interactivity to Laravel without the complexity of JavaScript frameworks.

## What is Accelade?

Accelade is a lightweight, powerful library that transforms your Blade templates into reactive components. It bridges the gap between traditional server-rendered Laravel applications and modern interactive UIs, letting you build dynamic interfaces using familiar Blade syntax.

**Think of it as:** Alpine.js simplicity + Livewire's server integration + Inertia's SPA navigation — all unified in a single, cohesive package.

---

## Key Features

### Reactive Components
Create interactive UI components directly in Blade without writing JavaScript:

```blade
@accelade(['count' => 0])
    <button @click="$set('count', count + 1)">
        Clicked <span a-text="count">0</span> times
    </button>
@endaccelade
```

### Multi-Framework Support
Works with your preferred JavaScript framework or standalone:

- **Vanilla JS** — Zero dependencies, works everywhere
- **Vue.js** — Native Vue reactivity and directives
- **React** — JSX-compatible state bindings
- **Svelte** — Svelte-style reactive declarations
- **Angular** — Angular template syntax support

### SPA Navigation
Full single-page application navigation with a progress bar, prefetching, and state preservation:

```blade
<x-accelade::link href="/dashboard" :prefetch="true">
    Dashboard
</x-accelade::link>
```

### Server Integration
Seamlessly sync state between frontend and backend:

- **Bridge** — Call PHP methods directly from JavaScript
- **Server Sync** — Persist component state to the server
- **Flash Messages** — Display session flash data reactively
- **Real-time Events** — Laravel Echo integration for WebSockets

### Toast Notifications
Beautiful, customizable notifications from PHP or JavaScript:

```php
Notify::success('Saved!')->body('Your changes have been saved.')->send();
```

### Modals & Dialogs
Pre-built modal and slideover components with async loading:

```blade
<x-accelade::link href="/users/create" :modal="true">
    Create User
</x-accelade::link>
```

---

## Why Accelade?

| Traditional Approach | With Accelade |
|---------------------|---------------|
| Write separate JavaScript files | Keep logic in Blade templates |
| Manage complex build pipelines | Works with or without build tools |
| Learn new templating languages | Use familiar Blade syntax |
| Handle state synchronization manually | Automatic server sync |
| Build navigation from scratch | Built-in SPA routing |

### Perfect For

- **Laravel developers** who want interactivity without learning React/Vue
- **Teams** maintaining existing Blade applications
- **Projects** needing progressive enhancement
- **Rapid prototyping** with instant reactivity
- **Full-stack developers** who prefer server-side rendering

---

## Quick Start

### 1. Install via Composer

```bash
composer require accelade/accelade
```

### 2. Add Directives to Layout

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

> **Important:** The CSRF meta tag is required for server sync functionality.

### 3. Create Your First Component

```blade
@accelade(['count' => 0])
    <div class="counter">
        <p>Count: <span a-text="count">0</span></p>
        <button @click="$set('count', count + 1)">Increment</button>
        <button @click="$set('count', count - 1)">Decrement</button>
        <button @click="$set('count', 0)">Reset</button>
    </div>
@endaccelade
```

That's it! The counter is now fully reactive without writing any JavaScript.

---

## Core Concepts

### The @accelade Directive

The `@accelade` directive creates a reactive component scope. Pass an array of initial state:

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
| `a-for` | Loop rendering | `<template a-for="item in items">` |
| `a-class` | Dynamic classes | `<div a-class="{ active: isActive }">` |
| `a-sync` | Server sync | `<div a-sync="preferences">` |
| `@event` | Event handler | `<button @click="save()">` |

### State Actions

Built-in functions available in event handlers:

```blade
{{-- Set a value --}}
<button @click="$set('count', count + 1)">+1</button>

{{-- Toggle a boolean --}}
<button @click="$toggle('isOpen')">Toggle</button>

{{-- Reset to initial state --}}
<button @click="$reset()">Reset All</button>
<button @click="$reset('count')">Reset Count</button>

{{-- Navigate programmatically --}}
<button @click="$navigate('/dashboard')">Go to Dashboard</button>
```

---

## Common Patterns

### Form with Validation

```blade
@accelade(['email' => '', 'submitted' => false])
    <form @submit.prevent="$set('submitted', true)">
        <input a-model="email" type="email" placeholder="Enter email">

        <p a-show="submitted && !email" class="text-red-500">
            Email is required
        </p>

        <button type="submit">Subscribe</button>
    </form>
@endaccelade
```

### Toggle Panel

```blade
@accelade(['isOpen' => false])
    <button @click="$toggle('isOpen')">
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
        <button @click="$set('activeTab', 'home')" a-class="{ active: activeTab === 'home' }">
            Home
        </button>
        <button @click="$set('activeTab', 'profile')" a-class="{ active: activeTab === 'profile' }">
            Profile
        </button>
    </div>

    <div a-show="activeTab === 'home'">Home content</div>
    <div a-show="activeTab === 'profile'">Profile content</div>
@endaccelade
```

---

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

---

## Documentation

Enable the built-in documentation to explore all features with interactive demos:

```env
ACCELADE_DOCS_ENABLED=true
```

Then visit `/docs/getting-started` to browse the documentation.

---

## Next Steps

- [Installation](installation.md) — Detailed installation guide
- [Components](components.md) — Advanced component patterns
- [Custom Scripts](scripts.md) — Add custom JavaScript methods
- [Notifications](notifications.md) — Full notification API
- [SPA Navigation](spa-navigation.md) — Router configuration
- [Bridge](bridge.md) — Call PHP from JavaScript
- [Frameworks](frameworks.md) — Using with Vue, React, etc.
- [API Reference](api-reference.md) — Complete API documentation
