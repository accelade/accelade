# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Accelade is a Laravel package that adds reactivity to Blade templates without requiring a full SPA framework. It provides a hybrid architecture combining server-rendered Blade with client-side reactivity, supporting multiple frontend frameworks (Vanilla JS, Vue, React, Svelte, Angular) through a unified adapter system.

## Common Commands

### Development & Building
```bash
npm run dev              # Start Vite dev server
npm run build            # Build unified JS bundle (all frameworks)
npm run build:all        # Build all framework-specific bundles
npm run typecheck        # Run TypeScript type checking
```

### Testing
```bash
# PHP tests (Pest)
composer test                                    # Run all tests
vendor/bin/pest tests/Unit/AcceladeTest.php     # Run specific test file
vendor/bin/pest --filter=testName               # Filter by test name

# E2E tests (Playwright)
npm run test                                     # Run all E2E tests
npm run test:e2e:ui                             # Run with interactive UI
npm run test:e2e:headed                         # Run in headed browser
npm run test:install                            # Install Playwright browsers
```

### Code Quality
```bash
composer format              # Format PHP with Pint
composer format:test         # Check formatting without fixing
composer mago                # Run Mago linter on src/
composer analyse             # Run format check + Mago
composer ci                  # Full CI: analyse + test
```

## Architecture

### Hybrid Server-Client Design
- **PHP Backend**: Blade directives (`@accelade`, `@endaccelade`) compile reactive components with state serialized to `data-accelade-*` attributes
- **JS Frontend**: Detects framework at runtime, initializes components, binds reactivity

### Key PHP Components
- `AcceladeServiceProvider` - Registers singletons (`accelade`, `accelade.notify`), Blade directives, and component namespace
- `Accelade` - Main class managing component stack, script/style generation, state serialization
- `AcceladeTagCompiler` - Blade precompiler transforming `<x-accelade:*>` tags
- `NotificationManager` - Session-based toast notifications persisting across redirects

### Key TypeScript Components (resources/js/)
- `index.ts` - Unified entry point, exports all framework adapters
- `adapters/` - Framework-specific implementations (Vanilla, Vue, React, Svelte, Angular)
- `core/router.ts` - SPA navigation intercepting links
- `core/progress.ts` - Progress bar during navigation
- `core/notification/` - Client-side toast rendering

### Build System
Vite builds framework bundles in multiple modes:
- `--mode unified` (default): Single bundle including all frameworks
- `--mode vanilla|vue|react`: Framework-specific bundles

Output goes to `dist/` as IIFE and ESM formats.

### Test Structure
- `tests/Unit/` - Unit tests for core classes
- `tests/Feature/` - Feature tests for service provider, routes, facades
- `tests/e2e/` - Playwright E2E tests (require `ACCELADE_TEST_URL`)

Tests use Orchestra Testbench via `tests/TestCase.php`.

## Framework Adapter Pattern

Each framework adapter implements `IFrameworkAdapter` with:
- `IStateAdapter` - Framework-specific reactive state (Proxy, Vue reactive, React useState)
- `IBindingAdapter` - DOM binding for directives (`a-text`, `a-model`, `@click`, etc.)

Adapters are registered with priority in `FrameworkRegistry`; detection happens at runtime.

## Blade Directive Syntax

```blade
@accelade(['count' => 0])
    <button @click="$set('count', count + 1)">
        Clicked <span a-text="count">0</span> times
    </button>
@endaccelade
```

Framework-specific prefixes: `a-` (Vanilla), `v-` (Vue), `data-state-` (React), `s-` (Svelte), `ng-` (Angular).
Event binding uses `@event` syntax across all frameworks (e.g., `@click`, `@submit`, `@input`).

## Configuration

Key config options in `config/accelade.php`:
- `framework` - Default framework (vanilla, vue, react, svelte, angular)
- `asset_mode` - 'route' (serve via Laravel) or 'published' (public/vendor)
- `progress` - SPA navigation progress bar styling
- `demo.enabled` - Enable demo routes (disabled in production by default)
