# Lazy Loading

Accelade provides a powerful lazy loading component for deferring content rendering. Show beautiful shimmer placeholders while content loads, reducing initial page load time.

## Quick Start

```blade
{{-- Basic lazy loading with shimmer --}}
<x-accelade::lazy :shimmer="true">
    @foreach($items as $item)
        <div class="item">{{ $item->name }}</div>
    @endforeach
</x-accelade::lazy>
```

The content will display a shimmer animation while loading, then smoothly fade in.

## Shimmer Placeholder

Enable shimmer with a single attribute:

```blade
{{-- Simple shimmer with 3 lines (default) --}}
<x-accelade::lazy :shimmer="true">
    Content here
</x-accelade::lazy>

{{-- Custom number of lines --}}
<x-accelade::lazy :shimmer="true" :shimmer-lines="5">
    Content here
</x-accelade::lazy>

{{-- Custom height --}}
<x-accelade::lazy :shimmer="true" shimmer-height="200px">
    Content here
</x-accelade::lazy>

{{-- Custom width --}}
<x-accelade::lazy :shimmer="true" shimmer-width="50%">
    Content here
</x-accelade::lazy>

{{-- Rounded shimmer --}}
<x-accelade::lazy :shimmer="true" :shimmer-rounded="true">
    Content here
</x-accelade::lazy>

{{-- Circle shimmer (for avatars) --}}
<x-accelade::lazy :shimmer="true" :shimmer-circle="true">
    <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
</x-accelade::lazy>
```

## Custom Placeholder

Use the `placeholder` slot for custom loading content:

```blade
<x-accelade::lazy>
    <x-slot:placeholder>
        <div class="flex items-center gap-4">
            <div class="animate-pulse bg-gray-200 rounded-full w-12 h-12"></div>
            <div class="space-y-2">
                <div class="animate-pulse bg-gray-200 h-4 w-32 rounded"></div>
                <div class="animate-pulse bg-gray-200 h-3 w-24 rounded"></div>
            </div>
        </div>
    </x-slot:placeholder>

    <div class="flex items-center gap-4">
        <img src="{{ $user->avatar }}" class="w-12 h-12 rounded-full">
        <div>
            <h3>{{ $user->name }}</h3>
            <p>{{ $user->email }}</p>
        </div>
    </div>
</x-accelade::lazy>
```

## Loading from URL

Load content from a separate endpoint:

```blade
<x-accelade::lazy url="/api/notifications" :shimmer="true">
</x-accelade::lazy>
```

With POST method and data:

```blade
<x-accelade::lazy
    url="/api/search"
    method="POST"
    :data="['query' => $searchQuery]"
    :shimmer="true"
>
</x-accelade::lazy>
```

## Delayed Loading

Add a delay before content loads:

```blade
{{-- Wait 500ms before loading --}}
<x-accelade::lazy :delay="500" :shimmer="true">
    Content loads after delay
</x-accelade::lazy>
```

## Conditional Loading

Show content based on a condition:

```blade
@accelade(['showNotifications' => false])
    <button @click="$toggle('showNotifications')">
        Toggle Notifications
    </button>

    <x-accelade::lazy show="showNotifications" :shimmer="true">
        @foreach($notifications as $notification)
            <div>{{ $notification->message }}</div>
        @endforeach
    </x-accelade::lazy>
@endaccelade
```

The content loads fresh from the server each time the condition becomes true.

## JavaScript API

Control lazy loading programmatically:

```javascript
// Get a lazy instance by ID
const lazy = Accelade.lazy.get('lazy-abc123');

// Manually load content
Accelade.lazy.load('lazy-abc123');

// Reload content
Accelade.lazy.reload('lazy-abc123');

// Hide content (show placeholder)
Accelade.lazy.hide('lazy-abc123');

// Listen for events
Accelade.lazy.on('loaded', (instance) => {
    console.log('Content loaded:', instance.id);
});

// Configure global options
Accelade.lazy.configure({
    delay: 100, // Default delay for all lazy components
});
```

## Events

| Event | Description |
|-------|-------------|
| `load` | Content is about to load |
| `loaded` | Content has finished loading |
| `error` | Loading failed |

```javascript
Accelade.lazy.on('error', (instance, error) => {
    console.error('Failed to load:', instance.id, error);
});
```

## Component Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `shimmer` | bool | `false` | Enable shimmer placeholder |
| `shimmer-lines` | int | `3` | Number of shimmer lines |
| `shimmer-height` | string | auto | Custom height (e.g., "200px") |
| `shimmer-width` | string | auto | Custom width (e.g., "50%") |
| `shimmer-rounded` | bool | `false` | Rounded corners |
| `shimmer-circle` | bool | `false` | Circle shape (for avatars) |
| `url` | string | null | URL to fetch content from |
| `method` | string | `GET` | HTTP method for URL requests |
| `data` | array | `[]` | Data to send with POST requests |
| `delay` | int | `0` | Delay in ms before loading |
| `show` | bool/string | `true` | Condition for loading |
| `name` | string | null | Optional name for the component |

## Shimmer CSS Classes

Use these classes for custom shimmer effects:

```html
<!-- Container -->
<div class="accelade-shimmer-container">
    <div class="accelade-shimmer-line"></div>
    <div class="accelade-shimmer-line"></div>
    <div class="accelade-shimmer-line accelade-shimmer-line-short"></div>
</div>

<!-- Modifiers -->
<div class="accelade-shimmer-container accelade-shimmer-rounded">...</div>
<div class="accelade-shimmer-container accelade-shimmer-circle">...</div>
<div class="accelade-shimmer-container accelade-shimmer-dark">...</div>

<!-- Presets -->
<div class="accelade-shimmer-card">...</div>
<div class="accelade-shimmer-image">...</div>
<div class="accelade-shimmer-avatar">...</div>

<!-- Inline -->
<span class="accelade-shimmer-inline" style="width: 100px;"></span>
```

## Examples

### Card Skeleton

```blade
<x-accelade::lazy :shimmer="true" shimmer-height="200px">
    <x-slot:placeholder>
        <div class="accelade-shimmer-card">
            <div class="accelade-shimmer-line" style="height: 1.5rem; width: 60%;"></div>
            <div class="accelade-shimmer-line"></div>
            <div class="accelade-shimmer-line"></div>
            <div class="accelade-shimmer-line accelade-shimmer-line-short"></div>
        </div>
    </x-slot:placeholder>

    <div class="card">
        <h2>{{ $post->title }}</h2>
        <p>{{ $post->excerpt }}</p>
    </div>
</x-accelade::lazy>
```

### User List

```blade
<x-accelade::lazy :shimmer="true" :shimmer-lines="6">
    @foreach($users as $user)
        <div class="flex items-center gap-3 py-2">
            <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full">
            <span>{{ $user->name }}</span>
        </div>
    @endforeach
</x-accelade::lazy>
```

### Image Gallery

```blade
<div class="grid grid-cols-3 gap-4">
    @foreach($images as $image)
        <x-accelade::lazy :shimmer="true" :shimmer-rounded="true" shimmer-height="150px">
            <img src="{{ $image->url }}" alt="{{ $image->alt }}" class="rounded-lg">
        </x-accelade::lazy>
    @endforeach
</div>
```

### Comment Section (Load on Demand)

```blade
@accelade(['showComments' => false])
    <button @click="$toggle('showComments')" class="btn">
        <span a-show="!showComments">Show Comments</span>
        <span a-show="showComments">Hide Comments</span>
    </button>

    <x-accelade::lazy show="showComments" url="/posts/{{ $post->id }}/comments" :shimmer="true">
    </x-accelade::lazy>
@endaccelade
```

## Best Practices

1. **Use Shimmer for Better UX** - Shimmer provides visual feedback that content is loading
2. **Keep Placeholders Similar** - Match placeholder dimensions to actual content
3. **Lazy Load Heavy Content** - Use for images, lists, and data-heavy sections
4. **Conditional Loading** - Load content only when needed (tabs, accordions)
5. **Add Delays Sparingly** - Only use delays when necessary for visual effect

## Next Steps

- [Components](components.md) - Building reactive components
- [SPA Navigation](spa-navigation.md) - Client-side routing
- [API Reference](api-reference.md) - Complete API documentation
