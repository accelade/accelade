{{-- Flash Component Section - Laravel Flash Data --}}
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

<!-- Demo: Flash Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Flash Data Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Access Laravel's session flash data in your templates using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::flash&gt;</code>.
        Works seamlessly with SPA navigation.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Live Demo -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Live</span>
                Flash Messages
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                These flash messages were set on the server. They're accessible through the <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">flash</code> object.
            </p>

            <x-accelade::flash class="space-y-3">
                <div {{ $ifAttr }}="flash.has('message')" class="p-3 rounded-lg border border-[var(--docs-border)] text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                    <span class="font-medium">Message:</span>
                    <span {{ $textAttr }}="flash.message"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('success')" class="p-3 bg-green-500/10 rounded-lg border border-green-500/30 text-sm text-green-500">
                    <span class="font-medium">Success:</span>
                    <span {{ $textAttr }}="flash.success"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('error')" class="p-3 bg-red-500/10 rounded-lg border border-red-500/30 text-sm text-red-500">
                    <span class="font-medium">Error:</span>
                    <span {{ $textAttr }}="flash.error"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('info')" class="p-3 bg-blue-500/10 rounded-lg border border-blue-500/30 text-sm text-blue-500">
                    <span class="font-medium">Info:</span>
                    <span {{ $textAttr }}="flash.info"></span>
                </div>
            </x-accelade::flash>
        </div>

        <!-- How It Works -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 border border-[var(--docs-border)] rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">Info</span>
                How It Works
            </h4>
            <ul class="space-y-3 text-sm" style="color: var(--docs-text-muted);">
                <li class="flex gap-2">
                    <span class="text-amber-500">1.</span>
                    <span>Backend flashes data using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">session()->flash()</code></span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">2.</span>
                    <span>Flash data is automatically shared via middleware</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">3.</span>
                    <span>The <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">flash</code> object provides <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">.has()</code> and direct access</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">4.</span>
                    <span>Works with SPA navigation without full page reloads</span>
                </li>
            </ul>
        </div>

        <!-- Flash Methods -->
        <div class="rounded-xl p-4 border border-orange-500/30" style="background: rgba(249, 115, 22, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Flash Object Methods</h4>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-amber-500">flash.has(key)</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Check if a flash key exists and has a value</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-amber-500">flash.key</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Access flash value directly by key</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-amber-500">flash.get(key, default)</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Get value with optional default</p>
                </div>
            </div>
        </div>

        <!-- Configuration -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Configuration Options</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Control automatic flash data sharing in <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">config/accelade.php</code>:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <code class="text-sm font-mono" style="color: var(--docs-text-muted);">flash.enabled</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Enable/disable automatic flash data sharing (default: true)</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <code class="text-sm font-mono" style="color: var(--docs-text-muted);">flash.keys</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Array of specific keys to share, or null for all</p>
                </div>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="flash-examples.blade.php">
&lt;!-- Basic flash message display --&gt;
&lt;x-accelade::flash&gt;
    &lt;div {{ $ifAttr }}="flash.has('success')" class="alert alert-success"&gt;
        &lt;span {{ $textAttr }}="flash.success"&gt;&lt;/span&gt;
    &lt;/div&gt;

    &lt;div {{ $ifAttr }}="flash.has('error')" class="alert alert-danger"&gt;
        &lt;span {{ $textAttr }}="flash.error"&gt;&lt;/span&gt;
    &lt;/div&gt;
&lt;/x-accelade::flash&gt;

&lt;!-- Notification-style flash messages --&gt;
&lt;x-accelade::flash class="fixed top-4 right-4 space-y-2"&gt;
    &lt;div {{ $showAttr }}="flash.has('message')"
         class="p-4 bg-white shadow-lg rounded-lg"&gt;
        &lt;p {{ $textAttr }}="flash.message"&gt;&lt;/p&gt;
    &lt;/div&gt;
&lt;/x-accelade::flash&gt;
    </x-accelade::code-block>

    <div class="mt-4">
        <x-accelade::code-block language="php" filename="Controller.php">
&lt;?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Create the order...
        $order = Order::create($request->validated());

        // Flash success message
        session()->flash('success', 'Order created successfully!');

        return redirect()->route('orders.index');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        // Flash with different types
        session()->flash('message', 'Order has been deleted.');
        session()->flash('info', 'You can restore it within 30 days.');

        return redirect()->route('orders.index');
    }
}
        </x-accelade::code-block>
    </div>

    <div class="mt-4">
        <x-accelade::code-block language="php" filename="config/accelade.php">
// Flash data configuration
'flash' => [
    // Enable automatic flash data sharing
    'enabled' => env('ACCELADE_FLASH_ENABLED', true),

    // Specific keys to share (null = all keys)
    // 'keys' => ['message', 'success', 'error', 'warning', 'info'],
    'keys' => null,
],
        </x-accelade::code-block>
    </div>
</section>
