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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-violet-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Event Bus</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Decoupled component communication through a global event bus.
        Components can emit and listen to events without direct references.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- API Overview -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-violet-100 text-violet-700 rounded">API</span>
                Global Methods
            </h3>
            <div class="space-y-2 text-sm font-mono text-slate-600">
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>Accelade.emit('event', data)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>Accelade.on('event', callback)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>Accelade.once('event', callback)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>Accelade.off('event', callback)</code>
                </div>
            </div>
        </div>

        <!-- Component Context -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Context</span>
                In Components
            </h3>
            <div class="space-y-2 text-sm font-mono text-slate-600">
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>$emit('event', data)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>$on('event', callback)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>$once('event', callback)</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <code>$off('event', callback)</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Example: Sender and Receiver -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-6 border border-violet-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Live Example: Component Communication</h3>
        <p class="text-sm text-slate-600 mb-4">
            Two independent components communicating through the event bus.
            The sender emits events, and the receiver listens for them.
        </p>

        <div class="grid md:grid-cols-2 gap-4">
            <!-- Sender Component -->
            <div class="bg-white rounded-lg p-4 border border-violet-200">
                <h4 class="text-sm font-medium text-violet-700 mb-3">Sender Component</h4>
                <x-accelade::data :default="['message' => 'Hello from sender!']">
                    <div class="space-y-3">
                        <input
                            type="text"
                            {{ $prefix }}-model="message"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"
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
            <div class="bg-white rounded-lg p-4 border border-purple-200">
                <h4 class="text-sm font-medium text-purple-700 mb-3">Receiver Component</h4>
                <x-accelade::data :default="['lastMessage' => 'No messages yet', 'lastTime' => '', 'counter' => 0]">
                    <div class="space-y-3">
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <div class="text-xs text-purple-500 mb-1">Last Message:</div>
                            <div class="text-sm text-purple-900" {{ $textAttr }}="lastMessage">No messages yet</div>
                            <div {{ $showAttr }}="lastTime" class="text-xs text-purple-400 mt-1">
                                at <span {{ $textAttr }}="lastTime"></span>
                            </div>
                        </div>
                        <div class="bg-violet-50 p-3 rounded-lg flex items-center justify-between">
                            <span class="text-sm text-violet-700">Counter:</span>
                            <span class="text-2xl font-bold text-violet-900" {{ $textAttr }}="counter">0</span>
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
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">One-time Events</h3>
        <p class="text-sm text-slate-600 mb-4">
            Use <code class="bg-slate-200 px-1 py-0.5 rounded text-xs">$once()</code> to listen for an event only once.
            After the first trigger, the listener is automatically removed.
        </p>

        <x-accelade::data :default="['triggered' => false, 'count' => 0]">
            <div class="flex items-center gap-4">
                <button
                    @click="$emit('one-time-event')"
                    class="px-4 py-2 text-sm bg-slate-500 text-white rounded-lg hover:bg-slate-600 transition-colors"
                >
                    Emit One-Time Event
                </button>
                <div class="text-sm">
                    <span class="text-slate-500">Status:</span>
                    <span {{ $showAttr }}="!triggered" class="text-amber-600">Waiting for first event...</span>
                    <span {{ $showAttr }}="triggered" class="text-green-600">
                        Triggered! (only fires once, click again to verify)
                    </span>
                </div>
                <div class="text-sm text-slate-500">
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
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">JavaScript API</h3>
        <p class="text-sm text-slate-600 mb-4">
            Access the event bus from anywhere in your JavaScript code.
        </p>

        <div class="flex gap-4">
            <button
                onclick="window.Accelade.emit('js-event', { source: 'button', timestamp: Date.now() })"
                class="px-4 py-2 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
                Emit from JS
            </button>
            <div id="js-event-receiver" class="text-sm text-slate-600 flex items-center">
                Click the button to emit an event from vanilla JS
            </div>
        </div>

        <script>
            // Listen from vanilla JS - wait for Accelade to be ready
            document.addEventListener('DOMContentLoaded', () => {
                window.Accelade?.on('js-event', (data) => {
                    const el = document.getElementById('js-event-receiver');
                    if (el) {
                        el.innerHTML = `<span class="text-green-600">Received from ${data.source}!</span>`;
                        setTimeout(() => {
                            el.innerHTML = 'Click the button to emit an event from vanilla JS';
                        }, 2000);
                    }
                });
            });
        </script>
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

    <div class="mt-6 p-4 bg-violet-50 rounded-lg border border-violet-200">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-violet-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-medium text-violet-800 mb-1">Best Practices</h4>
                <ul class="text-sm text-violet-700 space-y-1">
                    <li>Use descriptive event names like <code class="bg-violet-100 px-1 py-0.5 rounded text-xs">user-selected</code> or <code class="bg-violet-100 px-1 py-0.5 rounded text-xs">cart-updated</code></li>
                    <li>Always unsubscribe when a component is destroyed to prevent memory leaks</li>
                    <li>Use <code class="bg-violet-100 px-1 py-0.5 rounded text-xs">$once()</code> for initialization events that should only trigger once</li>
                    <li>Keep event payloads simple and serializable</li>
                </ul>
            </div>
        </div>
    </div>
</section>
