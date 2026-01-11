# Changelog

All notable changes to Accelade will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

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
