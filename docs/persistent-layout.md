# Persistent Layout

Accelade's Persistent Layout feature allows certain elements to remain active and unaffected during SPA navigation. This is ideal for media players, chat widgets, and other components that should continue functioning as users navigate your application.

## Quick Start

Wrap any element you want to persist across navigation:

```blade
<x-accelade::persistent id="music-player">
    <audio src="/music.mp3" controls></audio>
</x-accelade::persistent>
```

That's it! The audio player will continue playing even when users navigate to other pages.

## How It Works

1. When SPA navigation begins, Accelade saves all elements with `data-accelade-persistent`
2. After the new page content loads, matching persistent elements are restored
3. The original DOM elements are moved (not cloned), preserving their state
4. Media playback, form inputs, and component state remain intact

## The Persistent Component

### Basic Usage

```blade
<x-accelade::persistent id="my-widget">
    <div class="fixed bottom-4 right-4">
        <!-- This content persists across navigation -->
    </div>
</x-accelade::persistent>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | auto-generated | Unique identifier to match elements across pages |

### Important Notes

- The `id` must be identical on both the source and destination pages
- Navigation must use SPA links (`<x-accelade::link>`) for persistence to work
- Traditional page loads will reset persistent elements

## Custom Persistent Layouts

For complex layouts where you want specific regions to persist, create a custom component extending `PersistentComponent`.

### Step 1: Create the Component

```bash
php artisan make:component VideoLayout
```

### Step 2: Extend PersistentComponent

```php
<?php

namespace App\View\Components;

use Accelade\Components\PersistentComponent;

class VideoLayout extends PersistentComponent
{
    public function render()
    {
        return view('components.video-layout');
    }
}
```

### Step 3: Create the View

```blade
{{-- resources/views/components/video-layout.blade.php --}}
<div class="min-h-screen">
    {{-- Main content area (changes with navigation) --}}
    <main data-accelade-page class="pb-24">
        {{ $slot }}
    </main>

    {{-- Persistent video player --}}
    <x-accelade::persistent id="video-player">
        <div class="fixed bottom-0 left-0 right-0 h-24 bg-black">
            <video id="main-video" controls class="h-full mx-auto">
                <source src="/video.mp4" type="video/mp4">
            </video>
        </div>
    </x-accelade::persistent>
</div>
```

### Step 4: Use the Layout

```blade
<x-video-layout>
    <h1>Page Title</h1>
    <p>Your page content here...</p>

    <x-accelade::link href="/another-page">
        Go to another page (video keeps playing!)
    </x-accelade::link>
</x-video-layout>
```

## Use Cases

### Music Player

```blade
<x-accelade::persistent id="audio-player">
    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4">
        <audio id="player" src="/current-track.mp3" controls class="w-full"></audio>
        <div class="flex justify-between mt-2">
            <span id="track-title">Now Playing: Track Name</span>
            <button onclick="togglePlaylist()">Playlist</button>
        </div>
    </div>
</x-accelade::persistent>
```

### Chat Widget

```blade
<x-accelade::persistent id="chat-widget">
    <div class="fixed bottom-4 right-4 w-80">
        <div class="bg-white rounded-lg shadow-xl">
            <div class="p-4 bg-blue-600 text-white rounded-t-lg">
                Live Chat
            </div>
            <div class="h-64 overflow-y-auto p-4">
                <!-- Chat messages preserved across navigation -->
            </div>
            <div class="p-4 border-t">
                <input type="text" placeholder="Type a message..." class="w-full">
            </div>
        </div>
    </div>
</x-accelade::persistent>
```

### Video Mini-Player

```blade
<x-accelade::persistent id="mini-player">
    <div class="fixed bottom-4 right-4 w-64 shadow-xl rounded-lg overflow-hidden">
        <video id="mini-video" autoplay muted class="w-full">
            <source src="/video.mp4" type="video/mp4">
        </video>
        <div class="absolute top-2 right-2">
            <button onclick="closeMiniPlayer()" class="text-white">×</button>
        </div>
    </div>
</x-accelade::persistent>
```

### Notification Panel

```blade
<x-accelade::persistent id="notifications">
    <div class="fixed top-4 right-4 w-80">
        @foreach($notifications as $notification)
            <div class="bg-white p-4 rounded shadow mb-2">
                {{ $notification->message }}
            </div>
        @endforeach
    </div>
</x-accelade::persistent>
```

## Multiple Persistent Elements

You can have multiple persistent elements on a page:

```blade
<x-accelade::persistent id="music-player">
    <!-- Music player -->
</x-accelade::persistent>

<x-accelade::persistent id="chat-widget">
    <!-- Chat widget -->
</x-accelade::persistent>

<x-accelade::persistent id="notifications">
    <!-- Notification panel -->
</x-accelade::persistent>
```

Each element is saved and restored independently based on its `id`.

## JavaScript API

You can check if the current page has persistent elements:

```javascript
const router = window.Accelade.router.instance();

// Check if page has persistent elements
if (router.hasPersistentElements()) {
    console.log('This page has persistent elements');
}
```

## Best Practices

### 1. Use Unique IDs

Always use unique, descriptive IDs for persistent elements:

```blade
{{-- Good --}}
<x-accelade::persistent id="main-video-player">
<x-accelade::persistent id="support-chat-widget">

{{-- Avoid --}}
<x-accelade::persistent id="widget1">
<x-accelade::persistent id="player">
```

### 2. Keep Persistent Elements Simple

Persistent elements work best when they're self-contained:

```blade
{{-- Good: Self-contained component --}}
<x-accelade::persistent id="audio-player">
    <livewire:audio-player />
</x-accelade::persistent>

{{-- Avoid: Complex nested structures --}}
<x-accelade::persistent id="complex">
    <div>
        <!-- Many nested components that reference external state -->
    </div>
</x-accelade::persistent>
```

### 3. Use Fixed Positioning

Persistent elements typically use fixed positioning:

```blade
<x-accelade::persistent id="player" class="fixed bottom-0 left-0 right-0">
    <!-- Content -->
</x-accelade::persistent>
```

### 4. Handle State Carefully

If your persistent element has JavaScript state, ensure it's properly managed:

```blade
<x-accelade::persistent id="stateful-widget">
    <div x-data="{ isOpen: false }">
        <!-- Alpine.js state will be preserved -->
    </div>
</x-accelade::persistent>
```

## Limitations

1. **SPA Navigation Required**: Persistent elements only work with SPA navigation. Traditional page loads will reset them.

2. **Same ID Required**: The persistent element must have the same `id` on both the source and destination pages.

3. **No Server-Side State**: Persistent elements maintain client-side DOM state only. Server-rendered content will be from the new page.

## Comparison with Keep-Alive

| Feature | Persistent Layout | Keep-Alive Cache |
|---------|-------------------|------------------|
| Preserves | Specific elements | Entire page state |
| Navigation | Forward and back | Back/forward only |
| Use case | Media players, widgets | Form state, scroll position |
| Memory | Low (specific elements) | Higher (full pages) |

Use **Persistent Layout** for elements that should remain active during forward navigation (like media players). Use **Keep-Alive Cache** for preserving page state when users navigate back.

## Next Steps

- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Components](components.md) - Reactive components
- [Configuration](configuration.md) - All config options
