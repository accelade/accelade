# API Reference

Complete API documentation for Accelade.

---

## PHP API

### Accelade Facade

```php
use Accelade\Facades\Accelade;
```

| Method | Description |
|--------|-------------|
| `scripts()` | Returns script tags for inclusion |
| `styles()` | Returns style tags for inclusion |
| `startComponent(array $state)` | Start a reactive block |
| `endComponent()` | End reactive block, returns HTML |

### Notify Facade

```php
use Accelade\Facades\Notify;
```

| Method | Returns | Description |
|--------|---------|-------------|
| `make()` | Notification | Create new notification |
| `title(string)` | Notification | Create with title |
| `success(string)` | Notification | Create success notification |
| `info(string)` | Notification | Create info notification |
| `warning(string)` | Notification | Create warning notification |
| `danger(string)` | Notification | Create danger notification |
| `push(Notification)` | void | Add to queue |
| `flush()` | Collection | Get and clear all |
| `close(string $id)` | void | Remove by ID |
| `defaultPosition(string)` | self | Set default position |
| `defaultDuration(int)` | self | Set default duration (ms) |

### Notification Class

```php
use Accelade\Notification\Notification;
```

#### Static Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `make(?string $title)` | Notification | Create new instance |

#### Instance Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `title(string)` | self | Set title |
| `body(string)` | self | Set body text |
| `message(string)` | self | Alias for body() |
| `status(string)` | self | Set status type |
| `success()` | self | Set success status |
| `info()` | self | Set info status |
| `warning()` | self | Set warning status |
| `danger()` | self | Set danger status |
| `icon(string)` | self | Set custom icon HTML |
| `iconColor(string)` | self | Set icon color |
| `color(string)` | self | Set accent color |
| `position(string)` | self | Set position |
| `duration(int)` | self | Set duration (ms) |
| `seconds(int)` | self | Set duration (seconds) |
| `autoDismiss(int)` | self | Alias for seconds() |
| `persistent()` | self | Disable auto-dismiss |
| `actions(array)` | self | Set action buttons |
| `send()` | self | Send notification |
| `getId()` | string | Get unique ID |
| `jsonSerialize()` | array | Serialize to array |

#### Positions

- `top-left`
- `top-center`
- `top-right` (default)
- `bottom-left`
- `bottom-center`
- `bottom-right`

#### Action Array Structure

```php
[
    'name' => 'action-name',      // Required: identifier
    'label' => 'Button Text',     // Required: display text
    'url' => '/path',             // Optional: link URL
    'openInNewTab' => false,      // Optional: target="_blank"
    'close' => false,             // Optional: close on click
    'dispatch' => 'event-name',   // Optional: browser event
]
```

### NotificationManager Class

```php
use Accelade\Notification\NotificationManager;
```

| Method | Returns | Description |
|--------|---------|-------------|
| `make()` | Notification | Create notification |
| `title(string)` | Notification | Create with title |
| `success(string)` | Notification | Create success |
| `info(string)` | Notification | Create info |
| `warning(string)` | Notification | Create warning |
| `danger(string)` | Notification | Create danger |
| `push(Notification)` | void | Add to queue |
| `flush()` | Collection | Get and clear all |
| `toArray()` | array | Get all as array |
| `close(string $id)` | void | Remove by ID |
| `setSession(SessionStore)` | void | Set session store |
| `defaultPosition(string)` | self | Default position |
| `defaultDuration(int)` | self | Default duration |

### Event Response (Broadcasting)

```php
use Accelade\Broadcasting\EventResponse;
```

| Method | Returns | Description |
|--------|---------|-------------|
| `redirect(string $url)` | EventResponse | Create redirect action |
| `redirectToRoute(string $route, array $params)` | EventResponse | Redirect to named route |
| `refresh()` | EventResponse | Create page refresh action |
| `toast(string $message, string $type)` | EventResponse | Create toast notification |
| `success(string $message)` | EventResponse | Success toast shorthand |
| `info(string $message)` | EventResponse | Info toast shorthand |
| `warning(string $message)` | EventResponse | Warning toast shorthand |
| `danger(string $message)` | EventResponse | Danger toast shorthand |
| `->withTitle(string $title)` | self | Add title to toast |
| `->with(array $data)` | self | Add custom data |
| `->toArray()` | array | Convert for broadcasting |

#### Accelade Event Helpers

```php
use Accelade\Facades\Accelade;
```

| Method | Returns | Description |
|--------|---------|-------------|
| `redirectOnEvent(string $url)` | EventResponse | Create redirect action |
| `redirectToRouteOnEvent(string $route, array $params)` | EventResponse | Redirect to named route |
| `refreshOnEvent()` | EventResponse | Create page refresh action |
| `toastOnEvent(string $message, string $type)` | EventResponse | Create toast notification |
| `successOnEvent(string $message)` | EventResponse | Success toast shorthand |
| `infoOnEvent(string $message)` | EventResponse | Info toast shorthand |
| `warningOnEvent(string $message)` | EventResponse | Warning toast shorthand |
| `dangerOnEvent(string $message)` | EventResponse | Danger toast shorthand |

### SEO Facade

```php
use Accelade\Facades\SEO;
```

| Method | Returns | Description |
|--------|---------|-------------|
| `title(?string)` | self | Set page title |
| `getTitle()` | ?string | Get page title |
| `description(?string)` | self | Set page description |
| `getDescription()` | ?string | Get page description |
| `keywords(string\|array)` | self | Set keywords |
| `getKeywords()` | array | Get keywords |
| `canonical(?string)` | self | Set canonical URL |
| `getCanonical()` | ?string | Get canonical URL |
| `robots(?string)` | self | Set robots meta |
| `getRobots()` | ?string | Get robots meta |
| `author(?string)` | self | Set author meta |
| `getAuthor()` | ?string | Get author meta |
| `openGraphType(?string)` | self | Set OG type |
| `openGraphSiteName(?string)` | self | Set OG site name |
| `openGraphTitle(?string)` | self | Set OG title |
| `openGraphDescription(?string)` | self | Set OG description |
| `openGraphUrl(?string)` | self | Set OG URL |
| `openGraphImage(?string, ?string)` | self | Set OG image + alt |
| `openGraphLocale(?string)` | self | Set OG locale |
| `getOpenGraph()` | array | Get all OG values |
| `twitterCard(?string)` | self | Set Twitter card type |
| `twitterSite(?string)` | self | Set Twitter site |
| `twitterCreator(?string)` | self | Set Twitter creator |
| `twitterTitle(?string)` | self | Set Twitter title |
| `twitterDescription(?string)` | self | Set Twitter description |
| `twitterImage(?string, ?string)` | self | Set Twitter image + alt |
| `getTwitter()` | array | Get all Twitter values |
| `metaByName(string, string)` | self | Add meta by name |
| `metaByProperty(string, string)` | self | Add meta by property |
| `meta(array)` | self | Add custom meta |
| `getMeta()` | array | Get all custom meta |
| `reset()` | self | Reset all values |
| `buildTitle()` | ?string | Build formatted title |
| `buildDescription()` | ?string | Get effective description |
| `buildKeywords()` | array | Get effective keywords |
| `buildCanonical()` | ?string | Get effective canonical |
| `toArray()` | array | Convert to array |
| `toHtml()` | string | Generate HTML meta tags |
| `render()` | string | Alias for toHtml() |

---

## Blade Directives

### @acceladeScripts

Outputs JavaScript bundle and configuration:

```blade
@acceladeScripts
```

Output:
```html
<script>
    window.AcceladeConfig = { /* config */ };
</script>
<script src="/accelade/accelade-v2.js" defer></script>
```

### @acceladeStyles

Outputs CSS for notifications and progress bar:

```blade
@acceladeStyles
```

### @acceladeNotifications

Renders the notifications container:

```blade
@acceladeNotifications
```

### @accelade / @endaccelade

Creates a reactive component block:

```blade
@accelade(['count' => 0, 'name' => ''])
    {{-- Reactive content --}}
@endaccelade

{{-- With custom ID --}}
@accelade(['count' => 0], 'my-counter')
    {{-- Content --}}
@endaccelade
```

### SEO Directives

```blade
{{-- Set page title --}}
@seoTitle('My Page Title')

{{-- Set page description --}}
@seoDescription('Page description')

{{-- Set keywords --}}
@seoKeywords('php, laravel, accelade')

{{-- Set canonical URL --}}
@seoCanonical('https://example.com/page')

{{-- Set robots meta --}}
@seoRobots('index, follow')

{{-- Set author --}}
@seoAuthor('John Doe')

{{-- Set OpenGraph data --}}
@seoOpenGraph([
    'type' => 'article',
    'site_name' => 'My Site',
    'title' => 'OG Title',
    'description' => 'OG Description',
    'image' => 'https://example.com/og.jpg',
])

{{-- Set Twitter Card data --}}
@seoTwitter([
    'card' => 'summary_large_image',
    'site' => '@mysite',
    'creator' => '@johndoe',
])

{{-- Add custom meta by name --}}
@seoMeta('theme-color', '#6366f1')

{{-- Output all SEO meta tags (place in <head>) --}}
@seo
```

### Lazy Loading Component

```blade
{{-- Basic lazy loading with shimmer --}}
<x-accelade::lazy :shimmer="true">
    Content to load lazily
</x-accelade::lazy>

{{-- With custom shimmer options --}}
<x-accelade::lazy
    :shimmer="true"
    :shimmer-lines="5"
    shimmer-height="200px"
    :shimmer-rounded="true"
>
    Content here
</x-accelade::lazy>

{{-- Circle shimmer for avatars --}}
<x-accelade::lazy :shimmer="true" :shimmer-circle="true">
    <img src="{{ $avatar }}" alt="Avatar">
</x-accelade::lazy>

{{-- Custom placeholder --}}
<x-accelade::lazy>
    <x-slot:placeholder>
        <div class="loading">Loading...</div>
    </x-slot:placeholder>
    Content here
</x-accelade::lazy>

{{-- Load from URL --}}
<x-accelade::lazy url="/api/content" :shimmer="true" />

{{-- Conditional loading --}}
<x-accelade::lazy show="isVisible" :shimmer="true">
    Content shown when isVisible is true
</x-accelade::lazy>

{{-- With delay --}}
<x-accelade::lazy :delay="500" :shimmer="true">
    Content loads after 500ms
</x-accelade::lazy>
```

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `shimmer` | bool | `false` | Enable shimmer placeholder |
| `shimmer-lines` | int | `3` | Number of shimmer lines |
| `shimmer-height` | string | auto | Custom height |
| `shimmer-width` | string | auto | Custom width |
| `shimmer-rounded` | bool | `false` | Rounded corners |
| `shimmer-circle` | bool | `false` | Circle shape |
| `url` | string | null | URL to fetch content |
| `method` | string | `GET` | HTTP method |
| `data` | array | `[]` | POST data |
| `delay` | int | `0` | Delay in ms |
| `show` | bool/string | `true` | Condition for loading |
| `name` | string | null | Component name |

### Event Component (Laravel Echo)

```blade
{{-- Basic event listener --}}
<x-accelade::event channel="orders" listen="OrderCreated">
    <p a-if="subscribed">Listening for orders...</p>
</x-accelade::event>

{{-- Private channel --}}
<x-accelade::event
    channel="user.{{ auth()->id() }}"
    :private="true"
    listen="MessageReceived,NotificationReceived"
>
    <span a-text="events.length"></span> new events
</x-accelade::event>

{{-- Presence channel --}}
<x-accelade::event
    channel="chat.room.1"
    :presence="true"
    listen="UserJoined,UserLeft"
/>

{{-- With scroll preservation --}}
<x-accelade::event
    channel="dashboard"
    listen="DataUpdated"
    :preserve-scroll="true"
/>
```

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `channel` | string | null | Channel name to subscribe to |
| `private` | bool | `false` | Use private (authenticated) channel |
| `presence` | bool | `false` | Use presence channel |
| `listen` | string | '' | Comma-separated event names |
| `preserve-scroll` | bool | `false` | Preserve scroll on refresh |

**Exposed State:**

| State | Type | Description |
|-------|------|-------------|
| `subscribed` | boolean | Whether connected to channel |
| `events` | array | Received events with `name`, `data`, `timestamp` |

### Flash Component (Session Flash Data)

```blade
{{-- Basic flash message display --}}
<x-accelade::flash>
    <div a-if="flash.has('success')" class="alert alert-success">
        <span a-text="flash.success"></span>
    </div>

    <div a-if="flash.has('error')" class="alert alert-danger">
        <span a-text="flash.error"></span>
    </div>
</x-accelade::flash>

{{-- With custom styling --}}
<x-accelade::flash class="fixed top-4 right-4 z-50">
    <div a-show="flash.has('message')" class="p-4 bg-white shadow-lg rounded">
        <p a-text="flash.message"></p>
    </div>
</x-accelade::flash>

{{-- Disable global sharing --}}
<x-accelade::flash :shared="false">
    <p a-text="flash.info"></p>
</x-accelade::flash>
```

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `shared` | bool | `true` | Share flash data globally via `Accelade::share()` |

**Exposed State:**

| State | Type | Description |
|-------|------|-------------|
| `flash` | object | Flash data object with `.has()` method and direct property access |

**Flash Object Methods:**

| Method | Description |
|--------|-------------|
| `flash.has(key)` | Check if a flash key exists and has a truthy value |
| `flash.key` | Access flash value directly by key |
| `flash.get(key, default)` | Get value with optional default |
| `flash.all()` | Get all flash data as an object |

---

## JavaScript API

### Window.Accelade

```typescript
interface Accelade {
    init(): void;
    navigate(url: string, options?: NavigateOptions): void;
    router: Router;
    progress: Progress;
    notify: NotificationManager;
    getComponent(id: string): Component | undefined;
    extend(extensions: object): void;
}
```

### Router

```typescript
interface Router {
    currentUrl: string;
    navigate(url: string, options?: NavigateOptions): Promise<void>;
    replace(url: string): void;
    prefetch(url: string): void;
    back(): void;
    forward(): void;
}

interface NavigateOptions {
    preserveScroll?: boolean;
    preserveState?: boolean;
    replace?: boolean;
}
```

### Progress

```typescript
interface Progress {
    start(): void;
    done(force?: boolean): void;
    set(percent: number): void;
    inc(amount?: number): void;
    configure(options: ProgressOptions): void;
}

interface ProgressOptions {
    color?: string;
    height?: number;
    showBar?: boolean;
    includeSpinner?: boolean;
    spinnerPosition?: 'top-left' | 'top-right' | 'bottom-left' | 'bottom-right';
    position?: 'top' | 'bottom';
}
```

### NotificationManager (JS)

```typescript
interface NotificationManager {
    show(options: NotificationOptions): string;
    success(title: string, body?: string): string;
    info(title: string, body?: string): string;
    warning(title: string, body?: string): string;
    danger(title: string, body?: string): string;
    close(id: string): void;
    closeAll(): void;
}

interface NotificationOptions {
    id?: string;
    title: string;
    body?: string;
    status?: 'success' | 'info' | 'warning' | 'danger';
    position?: NotificationPosition;
    duration?: number;
    persistent?: boolean;
    actions?: NotificationAction[];
}
```

### Component

```typescript
interface Component {
    id: string;
    state: Record<string, any>;
    getState(): Record<string, any>;
    setState(key: string, value: any): void;
    get(key: string): any;
    set(key: string, value: any): void;
    toggle(key: string): void;
    reset(key?: string): void;
    destroy(): void;
}
```

---

## Events

### Browser Events

| Event | Detail | Description |
|-------|--------|-------------|
| `accelade:init` | `{}` | Accelade initialized |
| `accelade:navigate-start` | `{ url }` | Navigation started |
| `accelade:navigate-end` | `{ url }` | Navigation completed |
| `accelade:navigate-error` | `{ error }` | Navigation failed |
| `accelade:state-change` | `{ componentId, key, value }` | State changed |
| `accelade:notification-show` | `{ notification }` | Notification shown |
| `accelade:notification-close` | `{ id }` | Notification closed |
| `accelade:echo` | `{ name, data }` | Laravel Echo event received |

### Listening to Events

```javascript
document.addEventListener('accelade:navigate-end', (event) => {
    console.log('Navigated to:', event.detail.url);
});
```

---

## Component Directives

### Text/Content

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-text` | Vanilla | Bind text content |
| `v-text` | Vue | Bind text content |
| `state:text` | React | Bind text content |
| `bind:text` | Svelte | Bind text content |
| `ng-text` | Angular | Bind text content |

### HTML

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-html` | Vanilla | Bind HTML content |
| `v-html` | Vue | Bind HTML content |
| `state:html` | React | Bind HTML content |
| `bind:html` | Svelte | Bind HTML content |
| `ng-html` | Angular | Bind HTML content |

### Visibility

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-show` | Vanilla | Toggle display |
| `v-show` | Vue | Toggle display |
| `state:show` | React | Toggle display |
| `bind:show` | Svelte | Toggle display |
| `ng-show` | Angular | Toggle display |

### Conditional

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-if` | Vanilla | Conditional render |
| `v-if` | Vue | Conditional render |
| `state:if` | React | Conditional render |
| `bind:if` | Svelte | Conditional render |
| `ng-if` | Angular | Conditional render |

### Two-Way Binding

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-model` | Vanilla | Form input binding |
| `v-model` | Vue | Form input binding |
| `state:model` | React | Form input binding |
| `bind:value` | Svelte | Form input binding |
| `ng-model` | Angular | Form input binding |

### Events

All frameworks use `@event` syntax for event handling:

| Directive | Description | Example |
|-----------|-------------|---------|
| `@click` | Click event | `<button @click="increment()">` |
| `@submit` | Form submit | `<form @submit.prevent="save()">` |
| `@input` | Input change | `<input @input="validate()">` |
| `@change` | Value change | `<select @change="update()">` |
| `@keydown` | Key press | `<input @keydown.enter="submit()">` |

### Classes

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-class` | Vanilla | Dynamic classes |
| `v-bind:class` / `:class` | Vue | Dynamic classes |
| `state:className` | React | Dynamic classes |
| `class:name` | Svelte | Dynamic classes |
| `ng-class` | Angular | Dynamic classes |

### Server Sync

| Directive | Framework | Description |
|-----------|-----------|-------------|
| `a-sync` | All | Sync with server |

---

## Configuration Reference

```php
// config/accelade.php
return [
    // Frontend framework: vanilla, vue, react, svelte, angular
    'framework' => env('ACCELADE_FRAMEWORK', 'vanilla'),

    // Asset mode: route, published
    'asset_mode' => env('ACCELADE_ASSET_MODE', 'route'),

    // Route prefix
    'prefix' => 'accelade',

    // Middleware for routes
    'middleware' => ['web'],

    // Server state TTL (seconds)
    'state_ttl' => env('ACCELADE_STATE_TTL', 3600),

    // Sync debounce (milliseconds)
    'sync_debounce' => env('ACCELADE_SYNC_DEBOUNCE', 300),

    // Flash data sharing
    'flash' => [
        'enabled' => env('ACCELADE_FLASH_ENABLED', true),
        'keys' => null, // null = common keys, or ['message', 'success', 'error']
    ],

    // Progress bar options
    'progress' => [
        'delay' => 250,
        'color' => '#6366f1',
        'gradientColor' => '#8b5cf6',
        'gradientColor2' => '#a855f7',
        'useGradient' => true,
        'height' => 3,
        'showBar' => true,
        'includeSpinner' => true,
        'spinnerSize' => 18,
        'spinnerPosition' => 'top-right',
        'position' => 'top',
        'minimum' => 8,
        'easing' => 'ease-out',
        'speed' => 200,
        'trickleSpeed' => 200,
        'zIndex' => 99999,
    ],

    // Demo routes
    'demo' => [
        'enabled' => env('ACCELADE_DEMO_ENABLED', env('APP_ENV') !== 'production'),
        'prefix' => env('ACCELADE_DEMO_PREFIX', 'demo'),
        'middleware' => ['web'],
    ],

    // Testing
    'testing' => [
        'base_url' => env('ACCELADE_TEST_URL', env('APP_URL', 'http://localhost')),
    ],
];
```

---

## CSS Variables

```css
:root {
    /* Notification container */
    --accelade-notif-width: 24rem;
    --accelade-notif-radius: 0.75rem;
    --accelade-notif-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    --accelade-notif-bg: #fff;
    --accelade-notif-animation-duration: 0.3s;

    /* Success */
    --accelade-notif-success-icon: #10b981;
    --accelade-notif-success-bg: #ecfdf5;
    --accelade-notif-success-border: #a7f3d0;

    /* Info */
    --accelade-notif-info-icon: #3b82f6;
    --accelade-notif-info-bg: #eff6ff;
    --accelade-notif-info-border: #bfdbfe;

    /* Warning */
    --accelade-notif-warning-icon: #f59e0b;
    --accelade-notif-warning-bg: #fffbeb;
    --accelade-notif-warning-border: #fde68a;

    /* Danger */
    --accelade-notif-danger-icon: #ef4444;
    --accelade-notif-danger-bg: #fef2f2;
    --accelade-notif-danger-border: #fecaca;
}
```
