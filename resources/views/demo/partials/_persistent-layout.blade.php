{{-- Persistent Layout Section --}}
@props(['prefix' => 'a'])

@php
    $textAttr = match($prefix) {
        'v' => 'v-text',
        'data-state' => 'data-state-text',
        's' => 's-text',
        'ng' => 'ng-text',
        default => 'a-text',
    };

    $showAttr = match($prefix) {
        'v' => 'v-show',
        'data-state' => 'data-state-show',
        's' => 's-show',
        'ng' => 'ng-show',
        default => 'a-show',
    };
@endphp

<!-- Demo: Persistent Layout -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Persistent Layout</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Keep elements like media players active during SPA navigation using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::persistent&gt;</code>.
    </p>

    <div class="space-y-4 mb-4">
        <!-- How It Works -->
        <div class="rounded-xl p-4 border border-rose-500/30" style="background: rgba(244, 63, 94, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-rose-500/20 text-rose-500 rounded">Concept</span>
                How It Works
            </h4>
            <div class="space-y-3 text-sm" style="color: var(--docs-text-muted);">
                <p>When navigating via SPA links, persistent elements are:</p>
                <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li>Saved before the page updates</li>
                    <li>Restored to matching placeholders in new content</li>
                    <li>Media playback state is preserved</li>
                </ol>
            </div>
        </div>

        <!-- Use Cases -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Use Cases</span>
                When to Use
            </h4>
            <ul class="space-y-2 text-sm" style="color: var(--docs-text-muted);">
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Video/audio players that continue during navigation</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Chat widgets that maintain connection</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    <span>Music players with continuous playback</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span>Notification panels</span>
                </li>
            </ul>
        </div>

        <!-- Live Example -->
        <div class="rounded-xl p-4 border border-pink-500/30" style="background: rgba(236, 72, 153, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Live Example</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                The persistent element below will be preserved when you navigate to other demo pages using the sidebar links.
                Notice how its state (like playback position if it were a video) would be maintained.
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Regular element -->
                <div class="rounded-lg p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <h4 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Regular Element</h4>
                    <p class="text-xs mb-3" style="color: var(--docs-text-muted);">Re-renders on navigation</p>
                    <x-accelade::data :default="['count' => 0]">
                        <div class="flex items-center gap-3">
                            <button
                                @click="$set('count', count + 1)"
                                class="px-3 py-1 text-sm rounded transition-colors border border-[var(--docs-border)]"
                                style="background: var(--docs-bg-alt); color: var(--docs-text);"
                            >
                                Click: <span {{ $textAttr }}="count">0</span>
                            </button>
                            <span class="text-xs" style="color: var(--docs-text-muted);">Resets on navigation</span>
                        </div>
                    </x-accelade::data>
                </div>

                <!-- Persistent element -->
                <div class="rounded-lg p-4 border border-rose-500/50" style="background: var(--docs-bg);">
                    <h4 class="text-sm font-medium text-rose-500 mb-2">Persistent Element</h4>
                    <p class="text-xs text-rose-400 mb-3">Preserved across navigation</p>
                    <x-accelade::persistent id="demo-counter">
                        <x-accelade::data :default="['count' => 0]">
                            <div class="flex items-center gap-3">
                                <button
                                    @click="$set('count', count + 1)"
                                    class="px-3 py-1 text-sm bg-rose-500 text-white rounded hover:bg-rose-600 transition-colors"
                                >
                                    Click: <span {{ $textAttr }}="count">0</span>
                                </button>
                                <span class="text-xs text-rose-400">Survives navigation!</span>
                            </div>
                        </x-accelade::data>
                    </x-accelade::persistent>
                </div>
            </div>

            <p class="text-xs mt-4" style="color: var(--docs-text-muted);">
                <strong style="color: var(--docs-text);">Try it:</strong> Increment both counters, then navigate to another section using the sidebar and come back.
                The persistent counter keeps its value!
            </p>
        </div>

        <!-- Creating Custom Persistent Layouts -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Creating Custom Persistent Layouts</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                For complex use cases, create a custom layout component by extending <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">PersistentComponent</code>:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="rounded-lg p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <h4 class="text-sm font-medium mb-2" style="color: var(--docs-text-muted);">1. Create PHP Component</h4>
                    <pre class="text-xs overflow-x-auto" style="color: var(--docs-text-muted);"><code>php artisan make:component VideoLayout

// app/View/Components/VideoLayout.php
use Accelade\Components\PersistentComponent;

class VideoLayout extends PersistentComponent
{
    public function render()
    {
        return view('components.video-layout');
    }
}</code></pre>
                </div>

                <div class="rounded-lg p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <h4 class="text-sm font-medium mb-2" style="color: var(--docs-text-muted);">2. Create Blade View</h4>
                    <pre class="text-xs overflow-x-auto" style="color: var(--docs-text-muted);"><code>&lt;!-- video-layout.blade.php --&gt;
&lt;div&gt;
    &lt;main data-accelade-page&gt;
        @{{ $slot }}
    &lt;/main&gt;

    &lt;x-accelade::persistent id="video-player"&gt;
        &lt;div class="fixed bottom-0 ..."&gt;
            &lt;video src="..."&gt;&lt;/video&gt;
        &lt;/div&gt;
    &lt;/x-accelade::persistent&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="persistent-examples.blade.php">
&lt;!-- Simple persistent wrapper --&gt;
&lt;x-accelade::persistent id="music-player"&gt;
    &lt;audio src="/music.mp3" controls&gt;&lt;/audio&gt;
&lt;/x-accelade::persistent&gt;

&lt;!-- Persistent chat widget --&gt;
&lt;x-accelade::persistent id="chat-widget"&gt;
    &lt;div class="fixed bottom-4 right-4"&gt;
        &lt;!-- Chat component content --&gt;
    &lt;/div&gt;
&lt;/x-accelade::persistent&gt;

&lt;!-- Multiple persistent elements --&gt;
&lt;x-accelade::persistent id="notifications"&gt;
    &lt;!-- Notification panel --&gt;
&lt;/x-accelade::persistent&gt;

&lt;x-accelade::persistent id="mini-player"&gt;
    &lt;!-- Video mini-player --&gt;
&lt;/x-accelade::persistent&gt;
    </x-accelade::code-block>

    <div class="mt-4 p-4 bg-amber-500/10 rounded-lg border border-amber-500/30">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h4 class="font-medium text-amber-500 mb-1">Important</h4>
                <p class="text-sm text-amber-400">
                    Persistent elements require the same <code class="bg-amber-500/20 px-1 py-0.5 rounded text-xs">id</code> attribute
                    on both the source and destination pages. Navigation must use SPA links
                    (<code class="bg-amber-500/20 px-1 py-0.5 rounded text-xs">&lt;x-accelade::link&gt;</code>) for persistence to work.
                </p>
            </div>
        </div>
    </div>
</section>
