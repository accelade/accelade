{{-- Event Component Section - Laravel Echo Integration --}}
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

    $ifAttr = match($prefix) {
        'v' => 'v-if',
        'data-state' => 'data-state-if',
        's' => 's-if',
        'ng' => 'ng-if',
        default => 'a-if',
    };
@endphp

<!-- Demo: Event Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-violet-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Event Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Listen to Laravel Echo broadcast events in real-time using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::event&gt;</code>.
        Supports automatic redirect, page refresh, and toast notifications.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Usage -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-violet-100 text-violet-700 rounded">Basic</span>
                Event Listener
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Subscribe to a channel and listen for events. The component exposes <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">subscribed</code> (boolean) and <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">events</code> (array) state.
            </p>

            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="text-slate-600">Echo not connected (requires Laravel Echo setup)</span>
                </div>
            </div>
        </div>

        <!-- Private Channel -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Private</span>
                Private Channel
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Use the <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">private</code> attribute for authenticated channels.
            </p>

            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <pre class="text-xs text-slate-600 overflow-x-auto"><code>&lt;x-accelade::event
    channel="user.123"
    :private="true"
    listen="MessageReceived"
/&gt;</code></pre>
            </div>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-6 border border-violet-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Automatic Actions</h3>
        <p class="text-sm text-slate-600 mb-4">
            Backend events can trigger client-side actions automatically using <code class="bg-white/50 px-1 py-0.5 rounded text-xs">Accelade::redirectOnEvent()</code>,
            <code class="bg-white/50 px-1 py-0.5 rounded text-xs">Accelade::refreshOnEvent()</code>, and <code class="bg-white/50 px-1 py-0.5 rounded text-xs">Accelade::toastOnEvent()</code>.
        </p>

        <div class="grid md:grid-cols-3 gap-4">
            <!-- Redirect -->
            <div class="bg-white rounded-lg p-4 border border-violet-200">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="font-medium text-sm text-slate-700">Redirect</span>
                </div>
                <p class="text-xs text-slate-500">Navigate to a new page when event fires</p>
            </div>

            <!-- Refresh -->
            <div class="bg-white rounded-lg p-4 border border-violet-200">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="font-medium text-sm text-slate-700">Refresh</span>
                </div>
                <p class="text-xs text-slate-500">Reload the page with optional scroll preservation</p>
            </div>

            <!-- Toast -->
            <div class="bg-white rounded-lg p-4 border border-violet-200">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="font-medium text-sm text-slate-700">Toast</span>
                </div>
                <p class="text-xs text-slate-500">Show notification when event is received</p>
            </div>
        </div>
    </div>

    <!-- Exposed State -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Exposed Reactive State</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-violet-600">subscribed</code>
                <p class="text-xs text-slate-500 mt-1">Boolean indicating if connected to the channel</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-violet-600">events</code>
                <p class="text-xs text-slate-500 mt-1">Array of received events with name, data, and timestamp</p>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="event-examples.blade.php">
&lt;!-- Basic event listener --&gt;
&lt;x-accelade::event channel="orders" listen="OrderCreated,OrderUpdated"&gt;
    &lt;p {{ $ifAttr }}="subscribed"&gt;Listening for order events...&lt;/p&gt;
    &lt;p {{ $ifAttr }}="!subscribed"&gt;Not connected to channel.&lt;/p&gt;
&lt;/x-accelade::event&gt;

&lt;!-- Private channel with user ID --&gt;
&lt;x-accelade::event
    channel="user.@{{ auth()-&gt;id() }}"
    :private="true"
    listen="NotificationReceived"
&gt;
    &lt;div {{ $showAttr }}="events.length &gt; 0"&gt;
        You have &lt;span {{ $textAttr }}="events.length"&gt;&lt;/span&gt; new notifications
    &lt;/div&gt;
&lt;/x-accelade::event&gt;

&lt;!-- Refresh page on event (preserve scroll) --&gt;
&lt;x-accelade::event
    channel="dashboard"
    listen="DataUpdated"
    :preserve-scroll="true"
/&gt;
    </x-accelade::code-block>

    <div class="mt-6">
        <x-accelade::code-block language="php" filename="OrderCreated.php">
&lt;?php
// Broadcasting event with Accelade action
use Accelade\Accelade;

class OrderCreated implements ShouldBroadcast
{
    public function __construct(public Order $order) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders.'.$this->order->user_id);
    }

    public function broadcastWith(): array
    {
        // Redirect client to order page
        return Accelade::redirectOnEvent(
            route('orders.show', $this->order)
        )->with(['order_id' => $this->order->id])->toArray();

        // Or show a toast notification
        return Accelade::toastOnEvent(
            'New order #'.$this->order->id.' created!',
            'success'
        )->toArray();

        // Or refresh the page
        return Accelade::refreshOnEvent()->toArray();
    }
}
        </x-accelade::code-block>
    </div>
</section>
