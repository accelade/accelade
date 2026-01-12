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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Flash Data Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Access Laravel's session flash data in your templates using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::flash&gt;</code>.
        Works seamlessly with SPA navigation.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Live Demo -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Live</span>
                Flash Messages
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                These flash messages were set on the server. They're accessible through the <code class="bg-white/50 px-1 py-0.5 rounded text-xs">flash</code> object.
            </p>

            <x-accelade::flash class="space-y-3">
                <div {{ $ifAttr }}="flash.has('message')" class="p-3 bg-white rounded-lg border border-slate-200 text-sm text-slate-700">
                    <span class="font-medium">Message:</span>
                    <span {{ $textAttr }}="flash.message"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('success')" class="p-3 bg-green-50 rounded-lg border border-green-200 text-sm text-green-700">
                    <span class="font-medium">Success:</span>
                    <span {{ $textAttr }}="flash.success"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('error')" class="p-3 bg-red-50 rounded-lg border border-red-200 text-sm text-red-700">
                    <span class="font-medium">Error:</span>
                    <span {{ $textAttr }}="flash.error"></span>
                </div>

                <div {{ $ifAttr }}="flash.has('info')" class="p-3 bg-blue-50 rounded-lg border border-blue-200 text-sm text-blue-700">
                    <span class="font-medium">Info:</span>
                    <span {{ $textAttr }}="flash.info"></span>
                </div>
            </x-accelade::flash>
        </div>

        <!-- How It Works -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">Info</span>
                How It Works
            </h3>
            <ul class="space-y-3 text-sm text-slate-600">
                <li class="flex gap-2">
                    <span class="text-amber-500">1.</span>
                    <span>Backend flashes data using <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">session()->flash()</code></span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">2.</span>
                    <span>Flash data is automatically shared via middleware</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">3.</span>
                    <span>The <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">flash</code> object provides <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">.has()</code> and direct access</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500">4.</span>
                    <span>Works with SPA navigation without full page reloads</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Flash Methods -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Flash Object Methods</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-amber-600">flash.has(key)</code>
                <p class="text-xs text-slate-500 mt-1">Check if a flash key exists and has a value</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-amber-600">flash.key</code>
                <p class="text-xs text-slate-500 mt-1">Access flash value directly by key</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-amber-600">flash.get(key, default)</code>
                <p class="text-xs text-slate-500 mt-1">Get value with optional default</p>
            </div>
        </div>
    </div>

    <!-- Configuration -->
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 rounded-xl p-6 border border-slate-200 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Configuration Options</h3>
        <p class="text-sm text-slate-600 mb-4">
            Control automatic flash data sharing in <code class="bg-white/50 px-1 py-0.5 rounded text-xs">config/accelade.php</code>:
        </p>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-slate-600">flash.enabled</code>
                <p class="text-xs text-slate-500 mt-1">Enable/disable automatic flash data sharing (default: true)</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-slate-200">
                <code class="text-sm font-mono text-slate-600">flash.keys</code>
                <p class="text-xs text-slate-500 mt-1">Array of specific keys to share, or null for all</p>
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

    <div class="mt-6">
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

    <div class="mt-6">
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
