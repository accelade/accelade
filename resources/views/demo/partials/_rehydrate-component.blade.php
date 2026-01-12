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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-teal-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Rehydrate Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Selective section reloading with <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::rehydrate&gt;</code>.
        Trigger updates via events or automatic polling without full page refresh.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Event-Triggered Rehydrate -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-teal-100 text-teal-700 rounded">Event</span>
                Event-Triggered Reload
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Click the button to emit an event that triggers the content to reload from the server.
            </p>

            <x-accelade::rehydrate on="counter-updated" id="event-demo">
                <div class="p-4 bg-white rounded-lg border border-teal-100 mb-4">
                    <p class="text-slate-700">
                        Current server time: <strong>{{ now()->format('H:i:s') }}</strong>
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
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
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Polling</span>
                Auto-Refresh (5 seconds)
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                This section automatically refreshes every 5 seconds.
            </p>

            <x-accelade::rehydrate :poll="5000" id="poll-demo">
                <div class="p-4 bg-white rounded-lg border border-purple-100">
                    <p class="text-slate-700">
                        Last updated: <strong>{{ now()->format('H:i:s') }}</strong>
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Random color:
                        @php $color = ['red', 'blue', 'green', 'purple', 'orange'][rand(0, 4)]; @endphp
                        <span class="inline-block px-2 py-0.5 rounded text-white text-xs bg-{{ $color }}-500">
                            {{ $color }}
                        </span>
                    </p>
                </div>
            </x-accelade::rehydrate>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Multiple Events -->
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Multiple</span>
                Multiple Event Listeners
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Listen to multiple events with a single component.
            </p>

            <x-accelade::rehydrate :on="['item-added', 'item-removed']" id="multi-event-demo">
                <div class="p-4 bg-white rounded-lg border border-amber-100 mb-4">
                    <p class="text-slate-700 font-medium">Inventory Status</p>
                    <p class="text-sm text-slate-500 mt-1">
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
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">API</span>
                JavaScript API
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Trigger rehydration programmatically using the JavaScript API.
            </p>

            <x-accelade::rehydrate id="api-demo">
                <div class="p-4 bg-white rounded-lg border border-slate-200 mb-4">
                    <p class="text-slate-700">
                        Server timestamp: <strong>{{ now()->timestamp }}</strong>
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
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
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Component Props</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Prop</th>
                        <th class="text-left py-2 px-3 text-slate-600">Type</th>
                        <th class="text-left py-2 px-3 text-slate-600">Default</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-teal-600">on</code></td>
                        <td class="py-2 px-3">string|array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Event name(s) that trigger rehydration</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-teal-600">poll</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Polling interval in milliseconds</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-teal-600">url</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">current page</td>
                        <td class="py-2 px-3">URL to fetch content from</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-teal-600">preserve-scroll</code></td>
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
