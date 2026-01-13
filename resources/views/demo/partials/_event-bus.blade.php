{{-- Event Bus Section --}}
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

<!-- Demo: Event Bus -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-violet-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Event Bus</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Decoupled component communication through a global event bus.
        Components can emit and listen to events without direct references.
    </p>

    <div class="space-y-4 mb-4">
        <!-- API Overview -->
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">API</span>
                Global Methods
            </h4>
            <div class="space-y-2 text-sm font-mono" style="color: var(--docs-text-muted);">
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>Accelade.emit('event', data)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>Accelade.on('event', callback)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>Accelade.once('event', callback)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>Accelade.off('event', callback)</code>
                </div>
            </div>
        </div>

        <!-- Component Context -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Context</span>
                In Components
            </h4>
            <div class="space-y-2 text-sm font-mono" style="color: var(--docs-text-muted);">
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>$emit('event', data)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>$on('event', callback)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>$once('event', callback)</code>
                </div>
                <div class="p-2 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code>$off('event', callback)</code>
                </div>
            </div>
        </div>

        <!-- Live Example: Sender and Receiver -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Live Example: Component Communication</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Two independent components communicating through the event bus.
                The sender emits events, and the receiver listens for them.
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Sender Component -->
                <div class="rounded-lg p-4 border border-violet-500/50" style="background: var(--docs-bg);">
                    <h4 class="text-sm font-medium text-violet-500 mb-3">Sender Component</h4>
                    <x-accelade::data :default="['message' => 'Hello from sender!']">
                        <div class="space-y-3">
                            <input
                                type="text"
                                {{ $prefix }}-model="message"
                                class="w-full px-3 py-2 rounded-lg text-sm border border-[var(--docs-border)]"
                                style="background: var(--docs-bg-alt); color: var(--docs-text);"
                                placeholder="Enter message..."
                            >
                            <div class="flex gap-2">
                                <button
                                    @click="$emit('message-sent', { text: message, time: new Date().toLocaleTimeString() })"
                                    class="px-4 py-2 text-sm bg-violet-500 text-white rounded-lg hover:bg-violet-600 transition-colors"
                                >
                                    Send Message
                                </button>
                                <button
                                    @click="$emit('counter-increment', { amount: 1 })"
                                    class="px-4 py-2 text-sm bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors"
                                >
                                    +1 Counter
                                </button>
                            </div>
                        </div>
                    </x-accelade::data>
                </div>

                <!-- Receiver Component -->
                <div class="rounded-lg p-4 border border-purple-500/50" style="background: var(--docs-bg);">
                    <h4 class="text-sm font-medium text-purple-500 mb-3">Receiver Component</h4>
                    <x-accelade::data :default="['lastMessage' => 'No messages yet', 'lastTime' => '', 'counter' => 0]">
                        <div class="space-y-3">
                            <div class="bg-purple-500/10 p-3 rounded-lg">
                                <div class="text-xs text-purple-500 mb-1">Last Message:</div>
                                <div class="text-sm" style="color: var(--docs-text);" {{ $textAttr }}="lastMessage">No messages yet</div>
                                <div {{ $showAttr }}="lastTime" class="text-xs text-purple-400 mt-1">
                                    at <span {{ $textAttr }}="lastTime"></span>
                                </div>
                            </div>
                            <div class="bg-violet-500/10 p-3 rounded-lg flex items-center justify-between">
                                <span class="text-sm text-violet-500">Counter:</span>
                                <span class="text-2xl font-bold text-violet-500" {{ $textAttr }}="counter">0</span>
                            </div>
                        </div>
                        <accelade:script>
                            // Listen for message events
                            $on('message-sent', (data) => {
                                $set('lastMessage', data.text);
                                $set('lastTime', data.time);
                            });

                            // Listen for counter events
                            $on('counter-increment', (data) => {
                                $set('counter', $get('counter') + data.amount);
                            });
                        </accelade:script>
                    </x-accelade::data>
                </div>
            </div>
        </div>

        <!-- One-time Events Example -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">One-time Events</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">$once()</code> to listen for an event only once.
                After the first trigger, the listener is automatically removed.
            </p>

            <x-accelade::data :default="['triggered' => false, 'count' => 0]">
                <div class="flex flex-wrap items-center gap-4">
                    <button
                        @click="$emit('one-time-event')"
                        class="px-4 py-2 text-sm rounded-lg transition-colors border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
                    >
                        Emit One-Time Event
                    </button>
                    <div class="text-sm">
                        <span style="color: var(--docs-text-muted);">Status:</span>
                        <span {{ $showAttr }}="!triggered" class="text-amber-500">Waiting for first event...</span>
                        <span {{ $showAttr }}="triggered" class="text-green-500">
                            Triggered! (only fires once, click again to verify)
                        </span>
                    </div>
                    <div class="text-sm" style="color: var(--docs-text-muted);">
                        Trigger count: <span {{ $textAttr }}="count">0</span>
                    </div>
                </div>
                <accelade:script>
                    // This will only fire once
                    $once('one-time-event', () => {
                        $set('triggered', true);
                        $set('count', $get('count') + 1);
                    });
                </accelade:script>
            </x-accelade::data>
        </div>

        <!-- JavaScript API Example -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">JS</span>
                JavaScript API
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Access the event bus from anywhere in your JavaScript code.
            </p>

            <div class="flex gap-4">
                <button
                    onclick="window.Accelade.emit('js-event', { source: 'button', timestamp: Date.now() })"
                    class="px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                >
                    Emit from JS
                </button>
                <div id="js-event-receiver" class="text-sm flex items-center" style="color: var(--docs-text-muted);">
                    Click the button to emit an event from vanilla JS
                </div>
            </div>

            <script>
                // Listen from vanilla JS - wait for Accelade to be ready
                document.addEventListener('DOMContentLoaded', () => {
                    window.Accelade?.on('js-event', (data) => {
                        const el = document.getElementById('js-event-receiver');
                        if (el) {
                            el.innerHTML = `<span class="text-green-500">Received from ${data.source}!</span>`;
                            setTimeout(() => {
                                el.innerHTML = 'Click the button to emit an event from vanilla JS';
                            }, 2000);
                        }
                    });
                });
            </script>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="event-bus-examples.blade.php">
&lt;!-- Emitting events --&gt;
&lt;button @click="$emit('user-selected', { id: 123, name: 'John' })"&gt;
    Select User
&lt;/button&gt;

&lt;!-- Listening for events in a component --&gt;
&lt;x-accelade::data :default="['selectedUser' =&gt; null]"&gt;
    &lt;div a-show="selectedUser"&gt;
        Selected: &lt;span a-text="selectedUser?.name"&gt;&lt;/span&gt;
    &lt;/div&gt;
    &lt;accelade:script&gt;
        $on('user-selected', (user) =&gt; {
            $set('selectedUser', user);
        });
    &lt;/accelade:script&gt;
&lt;/x-accelade::data&gt;

&lt;!-- Using from JavaScript --&gt;
&lt;script&gt;
    // Emit an event
    window.Accelade.emit('custom-event', { foo: 'bar' });

    // Listen for events
    const unsubscribe = window.Accelade.on('custom-event', (data) =&gt; {
        console.log('Received:', data);
    });

    // Stop listening
    unsubscribe();

    // Or use off() directly
    window.Accelade.off('custom-event', callback);

    // Listen once
    window.Accelade.once('init-complete', () =&gt; {
        console.log('Initialization complete!');
    });
&lt;/script&gt;
    </x-accelade::code-block>

    <div class="mt-4 p-4 bg-violet-500/10 rounded-lg border border-violet-500/30">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-violet-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-medium text-violet-500 mb-1">Best Practices</h4>
                <ul class="text-sm text-violet-400 space-y-1">
                    <li>Use descriptive event names like <code class="bg-violet-500/20 px-1 py-0.5 rounded text-xs">user-selected</code> or <code class="bg-violet-500/20 px-1 py-0.5 rounded text-xs">cart-updated</code></li>
                    <li>Always unsubscribe when a component is destroyed to prevent memory leaks</li>
                    <li>Use <code class="bg-violet-500/20 px-1 py-0.5 rounded text-xs">$once()</code> for initialization events that should only trigger once</li>
                    <li>Keep event payloads simple and serializable</li>
                </ul>
            </div>
        </div>
    </div>
</section>
