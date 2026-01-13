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
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-violet-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Event Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Listen to Laravel Echo broadcast events in real-time using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::event&gt;</code>.
        Supports automatic redirect, page refresh, and toast notifications.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Usage -->
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">Basic</span>
                Event Listener
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Subscribe to a channel and listen for events. The component exposes <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">subscribed</code> (boolean) and <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">events</code> (array) state.
            </p>

            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span style="color: var(--docs-text-muted);">Echo not connected (requires Laravel Echo setup)</span>
                </div>
            </div>
        </div>

        <!-- Private Channel -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Private</span>
                Private Channel
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use the <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">private</code> attribute for authenticated channels.
            </p>

            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <pre class="text-xs overflow-x-auto" style="color: var(--docs-text-muted);"><code>&lt;x-accelade::event
    channel="user.123"
    :private="true"
    listen="MessageReceived"
/&gt;</code></pre>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Automatic Actions</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Backend events can trigger client-side actions automatically using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">Accelade::redirectOnEvent()</code>,
                <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">Accelade::refreshOnEvent()</code>, and <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">Accelade::toastOnEvent()</code>.
            </p>

            <div class="grid md:grid-cols-3 gap-4">
                <!-- Redirect -->
                <div class="rounded-lg p-4 border border-violet-500/50" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span class="font-medium text-sm" style="color: var(--docs-text);">Redirect</span>
                    </div>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Navigate to a new page when event fires</p>
                </div>

                <!-- Refresh -->
                <div class="rounded-lg p-4 border border-violet-500/50" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium text-sm" style="color: var(--docs-text);">Refresh</span>
                    </div>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Reload the page with optional scroll preservation</p>
                </div>

                <!-- Toast -->
                <div class="rounded-lg p-4 border border-violet-500/50" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="font-medium text-sm" style="color: var(--docs-text);">Toast</span>
                    </div>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Show notification when event is received</p>
                </div>
            </div>
        </div>

        <!-- Exposed State -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Exposed Reactive State</h4>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <code class="text-sm font-mono text-violet-500">subscribed</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Boolean indicating if connected to the channel</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <code class="text-sm font-mono text-violet-500">events</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Array of received events with name, data, and timestamp</p>
                </div>
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

    <div class="mt-4">
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
