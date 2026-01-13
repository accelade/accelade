{{-- Rehydrate Component Section - Selective Content Reloading --}}
@props(['prefix' => 'a'])

@php
    // Determine framework-specific attributes
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

<!-- Demo: Rehydrate Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-teal-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Rehydrate Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Selective section reloading with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::rehydrate&gt;</code>.
        Trigger updates via events or automatic polling without full page refresh.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Event-Triggered Rehydrate -->
        <div class="rounded-xl p-4 border border-teal-500/30" style="background: rgba(20, 184, 166, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-teal-500/20 text-teal-500 rounded">Event</span>
                Event-Triggered Reload
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Click the button to emit an event that triggers the content to reload from the server.
            </p>

            <x-accelade::rehydrate on="counter-updated" id="event-demo">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
                    <p style="color: var(--docs-text);">
                        Current server time: <strong>{{ now()->format('H:i:s') }}</strong>
                    </p>
                    <p class="text-sm mt-1" style="color: var(--docs-text-muted);">
                        Random number: <strong>{{ rand(1000, 9999) }}</strong>
                    </p>
                </div>
            </x-accelade::rehydrate>

            <button
                onclick="window.Accelade.emit('counter-updated')"
                class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors text-sm"
            >
                Emit Event & Reload
            </button>
        </div>

        <!-- Polling Rehydrate -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Polling</span>
                Auto-Refresh (5 seconds)
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                This section automatically refreshes every 5 seconds.
            </p>

            <x-accelade::rehydrate :poll="5000" id="poll-demo">
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <p style="color: var(--docs-text);">
                        Last updated: <strong>{{ now()->format('H:i:s') }}</strong>
                    </p>
                    <p class="text-sm mt-1" style="color: var(--docs-text-muted);">
                        Random color:
                        @php $color = ['red', 'blue', 'green', 'purple', 'orange'][rand(0, 4)]; @endphp
                        <span class="inline-block px-2 py-0.5 rounded text-white text-xs bg-{{ $color }}-500">
                            {{ $color }}
                        </span>
                    </p>
                </div>
            </x-accelade::rehydrate>
        </div>

        <!-- Multiple Events -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Multiple</span>
                Multiple Event Listeners
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Listen to multiple events with a single component.
            </p>

            <x-accelade::rehydrate :on="['item-added', 'item-removed']" id="multi-event-demo">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
                    <p class="font-medium" style="color: var(--docs-text);">Inventory Status</p>
                    <p class="text-sm mt-1" style="color: var(--docs-text-muted);">
                        Items: <strong>{{ rand(1, 100) }}</strong> |
                        Updated: <strong>{{ now()->format('H:i:s') }}</strong>
                    </p>
                </div>
            </x-accelade::rehydrate>

            <div class="flex gap-2">
                <button
                    onclick="window.Accelade.emit('item-added')"
                    class="px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm"
                >
                    Add Item
                </button>
                <button
                    onclick="window.Accelade.emit('item-removed')"
                    class="px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm"
                >
                    Remove Item
                </button>
            </div>
        </div>

        <!-- Manual Reload -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 border border-[var(--docs-border)] rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">API</span>
                JavaScript API
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Trigger rehydration programmatically using the JavaScript API.
            </p>

            <x-accelade::rehydrate id="api-demo">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg-alt);">
                    <p style="color: var(--docs-text);">
                        Server timestamp: <strong>{{ now()->timestamp }}</strong>
                    </p>
                    <p class="text-sm mt-1" style="color: var(--docs-text-muted);">
                        Human: {{ now()->diffForHumans() }}
                    </p>
                </div>
            </x-accelade::rehydrate>

            <button
                onclick="window.Accelade.rehydrate.reload('api-demo')"
                class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors text-sm"
            >
                Reload via API
            </button>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-teal-500">on</code></td>
                        <td class="py-2 px-3">string|array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Event name(s) that trigger rehydration</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-teal-500">poll</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Polling interval in milliseconds</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-teal-500">url</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">current page</td>
                        <td class="py-2 px-3">URL to fetch content from</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-teal-500">preserve-scroll</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">true</td>
                        <td class="py-2 px-3">Preserve scroll position after reload</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="rehydrate-examples.blade.php">
{{-- Event-triggered rehydrate --}}
&lt;x-accelade::rehydrate on="item-created"&gt;
    @{{ foreach($items as $item) }}
        &lt;li&gt;@{{ $item->name }}&lt;/li&gt;
    @{{ endforeach }}
&lt;/x-accelade::rehydrate&gt;

{{-- Emit event from form success --}}
&lt;form @success="Accelade.emit('item-created')"&gt;...&lt;/form&gt;

{{-- Multiple events --}}
&lt;x-accelade::rehydrate :on="['created', 'updated', 'deleted']"&gt;
    ...
&lt;/x-accelade::rehydrate&gt;

{{-- Auto-polling every 5 seconds --}}
&lt;x-accelade::rehydrate :poll="5000"&gt;
    Score: @{{ $score }}
&lt;/x-accelade::rehydrate&gt;

{{-- JavaScript API --}}
&lt;script&gt;
// Emit event
Accelade.emit('data-updated');

// Reload specific component
Accelade.rehydrate.reload('component-id');

// Reload all rehydrate components
Accelade.rehydrate.reloadAll();
&lt;/script&gt;
    </x-accelade::code-block>
</section>
