# Changelog

All notable changes to Accelade will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Animation System** - CSS class-based enter/leave animations
  - `Animation` facade for registering custom animation presets
  - Built-in presets: `default`, `fade`, `opacity`, `scale`, `collapse`, `slide-up`, `slide-down`, `slide-left`, `slide-right`
  - `AnimationManager` class for managing animation presets
  - `AnimationPreset` value object for animation configuration
  - Animation utility CSS classes included in `@acceladeStyles`

- **Transition Component** - Standalone CSS transition wrapper
  - `<x-accelade::transition>` Blade component for animated show/hide
  - `show` attribute for reactive visibility expression
  - `animation` attribute for preset selection
  - Custom class props: `enter`, `enter-from`, `enter-to`, `leave`, `leave-from`, `leave-to`
  - Works with any animation preset or custom Tailwind classes

- **Toggle Animation** - Animation prop for toggle component
  - `animation` attribute on `<x-accelade::toggle>` component
  - Automatically animates all `a-show` elements inside the toggle
  - Simplest usage: `<x-accelade::toggle animation="fade">`
  - All built-in presets available: fade, scale, collapse, slide-*

- **Shared Data** - Share data from Laravel backend to JavaScript frontend
  - `Accelade::share()` method to share data globally from PHP
  - `Accelade::getShared()` and `Accelade::allShared()` helper methods
  - `Accelade::shared()` to access the underlying SharedData instance
  - Support for lazy-loaded data via closures
  - JavaScript API via `window.Accelade.shared`
    - `get(key, default)` - Get shared value with dot notation support
    - `has(key)` - Check if key exists
    - `all()` - Get all shared data
    - `set(key, value)` - Set value client-side
    - `merge(data)` - Merge multiple values
    - `subscribe(key, callback)` - Subscribe to specific key changes
    - `subscribeAll(callback)` - Subscribe to all changes
  - `ShareAcceladeData` middleware for sharing data on every request
  - Demo page at `/demo/shared-data`
  - Full documentation in `docs/shared-data.md`

- **Text Interpolation** - Use `@{{ expression }}` syntax in Blade templates
  - Automatic `{{ }}` text node interpolation in components
  - Access component state: `@{{ count }}`, `@{{ user.name }}`
  - Access shared data: `@{{ shared.appName }}`, `@{{ $shared.settings.theme }}`
  - Reactive updates when state or shared data changes
  - `TextInterpolator` class for programmatic use

- **SEO Engine** - Fluent API for managing page metadata
  - `SEO` facade with chainable methods for title, description, keywords
  - OpenGraph support: type, site_name, title, description, url, image, locale
  - Twitter Cards support: card, site, creator, title, description, image
  - Custom meta tags via `metaByName()`, `metaByProperty()`, and `meta()`
  - Blade directives:
    - `@seoTitle()`, `@seoDescription()`, `@seoKeywords()`
    - `@seoCanonical()`, `@seoRobots()`, `@seoAuthor()`
    - `@seoOpenGraph()`, `@seoTwitter()`
    - `@seoMeta()` for custom meta tags
    - `@seo` to output all meta tags
  - Auto-fill OpenGraph/Twitter from main SEO values
  - Auto-canonical URL generation from current request
  - Configurable title separator and suffix
  - Macroable for custom extensions
  - Full documentation in `docs/seo.md`

- **Event Binding Syntax** - Unified `@` prefix for events across all frameworks
  - All event handlers now use `@click`, `@submit`, `@input`, etc.
  - Works consistently across Vanilla, Vue, React, Svelte, and Angular
  - Legacy syntax (`a-on:`, `v-on:`, etc.) still supported for backward compatibility

- **Lazy Loading** - Defer content rendering with beautiful placeholders
  - `<x-accelade::lazy>` Blade component for lazy loading content
  - Shimmer placeholder with single `shimmer` attribute
  - Customizable shimmer lines, height, width, rounded, and circle shapes
  - Custom placeholder slot for advanced loading UI
  - URL mode for loading content from endpoints
  - Conditional loading with `show` attribute
  - Delay option for timed loading
  - JavaScript API: `Accelade.lazy.load()`, `Accelade.lazy.reload()`, `Accelade.lazy.hide()`
  - Events: `load`, `loaded`, `error`
  - Full documentation in `docs/lazy-loading.md`

- **Content Component** - Render pre-rendered HTML without interpolation
  - `<x-accelade::content>` Blade component for static HTML content
  - Customizable wrapper element with `as` attribute (div, article, section, etc.)
  - Perfect for Markdown, CMS content, or syntax-highlighted code
  - Security: Only use with trusted content (bypasses sanitization)
  - Full documentation in `docs/content.md`

- **Data Component** - Reactive data containers with storage persistence
  - `<x-accelade::data>` Blade component for reactive data management
  - `default` attribute for initial state (supports arrays, collections, Eloquent models)
  - `remember` attribute for session storage persistence (survives page refreshes)
  - `local-storage` attribute for localStorage persistence (survives browser close)
  - `store` attribute for global shared state across components
  - JavaScript object notation support for initial state
  - Reserved store names validation (data, form, toggle, state, store)
  - JavaScript API via `window.Accelade.stores`
    - `get(name)` - Get a store by name
    - `has(name)` - Check if store exists
    - `names()` - Get all store names
    - `all()` - Get all stores
  - `$store(name)` helper function in components
  - Full documentation in `docs/data.md`

- **Event Component** - Laravel Echo broadcast event integration
  - `<x-accelade::event>` Blade component for real-time event listening
  - Support for public, private, and presence channels
  - Comma-separated event names with `listen` attribute
  - Exposed reactive state: `subscribed` (boolean), `events` (array)
  - Automatic action handling from broadcast events:
    - `Accelade::redirectOnEvent($url)` - Navigate to URL
    - `Accelade::refreshOnEvent()` - Refresh page (with optional scroll preservation)
    - `Accelade::toastOnEvent($message, $type)` - Show toast notification
  - `EventResponse` class for building broadcast payloads
  - Custom event dispatch: `accelade:echo` for advanced usage
  - Graceful degradation when Laravel Echo is not configured
  - Full documentation in `docs/event.md`

- **Navigation Keep-Alive** - Page state caching for SPA navigation
  - Configurable via `config/accelade.php` navigation settings
  - `max_keep_alive` - Maximum pages to cache (default: 10, 0 to disable)
  - `transition_duration` - Page transition animation duration in ms
  - `preserve_scroll` - Default scroll behavior after navigation
  - Automatic state restoration when navigating back/forward
  - Component states preserved across navigation
  - Cache automatically clears oldest entries when limit reached
  - JavaScript API: `router.clearCache()`, `router.getCacheSize()`

- **Persistent Layout** - Keep elements active during SPA navigation
  - `<x-accelade::persistent>` Blade component for persistent regions
  - `PersistentComponent` PHP class for custom persistent layouts
  - Media players continue playing across navigation
  - Elements matched by `id` attribute between pages
  - Preserves DOM state (form inputs, playback position, etc.)
  - Automatic save/restore during SPA transitions
  - Full documentation in `docs/persistent-layout.md`

## [0.2.0] - 2024-01-11

### Added
- Initial release with core features:
  - Reactive Blade components with `@accelade` directive
  - Multi-framework support (Vanilla, Vue, React, Svelte, Angular)
  - SPA navigation with `<x-accelade::link>` component
  - Progress bar during navigation
  - Toast notifications (PHP and JavaScript)
  - Server state sync
  - Custom script functions with `<accelade:script>`

### Changed

### Deprecated

### Removed

### Fixed

### Security
