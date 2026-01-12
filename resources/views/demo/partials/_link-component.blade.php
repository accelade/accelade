{{-- Link Component Section - Enhanced Navigation --}}
@props(['prefix' => 'a'])

<!-- Demo: Link Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Link Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Enhanced link component with HTTP methods, confirmation dialogs, and navigation options using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::link&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Navigation -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">Basic</span>
                SPA Navigation
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Click links to navigate without full page reload. The link component handles SPA navigation automatically.
            </p>

            <div class="space-y-2">
                <x-accelade::link
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Go to Counter
                </x-accelade::link>

                <x-accelade::link
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'notifications']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition ml-2"
                >
                    Go to Notifications
                </x-accelade::link>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-6 border border-red-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Confirm</span>
                Confirmation Dialog
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Show a confirmation dialog before navigation. Great for destructive actions.
            </p>

            <div class="space-y-2">
                <x-accelade::link
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    :confirm="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition"
                >
                    Simple Confirm
                </x-accelade::link>

                <x-accelade::link
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    confirm-text="Are you sure you want to proceed? This action cannot be undone."
                    confirm-title="Confirm Navigation"
                    confirm-button="Yes, proceed"
                    cancel-button="No, stay here"
                    :confirm-danger="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition ml-2"
                >
                    Danger Confirm
                </x-accelade::link>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- HTTP Methods -->
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Methods</span>
                HTTP Methods
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Support for POST, PUT, PATCH, DELETE methods - like form submissions but as links.
            </p>

            <div class="p-4 bg-white rounded-lg border border-purple-200">
                <pre class="text-xs text-slate-600 overflow-x-auto"><code>&lt;x-accelade::link
    href="/api/resource"
    method="POST"
    :data="['name' => 'New Item']"
&gt;
    Create Resource
&lt;/x-accelade::link&gt;

&lt;x-accelade::link
    href="/api/resource/1"
    method="DELETE"
    :confirm-danger="true"
    confirm-text="Delete this item?"
&gt;
    Delete
&lt;/x-accelade::link&gt;</code></pre>
            </div>
        </div>

        <!-- External Links -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">External</span>
                External Links (Away)
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Use the <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">away</code> attribute for external links with optional confirmation.
            </p>

            <div class="space-y-2">
                <x-accelade::link
                    href="https://laravel.com"
                    :away="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Visit Laravel
                </x-accelade::link>

                <x-accelade::link
                    href="https://github.com"
                    :away="true"
                    confirm-text="You are leaving this site. Continue?"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition ml-2"
                >
                    GitHub (with confirm)
                </x-accelade::link>
            </div>
        </div>
    </div>

    <!-- Navigation Options -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Navigation Options</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-lg border border-green-200">
                <code class="text-sm font-mono text-green-600">preserve-scroll</code>
                <p class="text-xs text-slate-500 mt-1">Keep scroll position after navigation</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-green-200">
                <code class="text-sm font-mono text-green-600">preserve-state</code>
                <p class="text-xs text-slate-500 mt-1">Preserve component state across navigation</p>
            </div>
            <div class="p-4 bg-white rounded-lg border border-green-200">
                <code class="text-sm font-mono text-green-600">prefetch</code>
                <p class="text-xs text-slate-500 mt-1">Prefetch page on hover for faster navigation</p>
            </div>
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
                        <td class="py-2 px-3"><code class="text-blue-600">href</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">-</td>
                        <td class="py-2 px-3">Target URL (required)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">method</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">GET</td>
                        <td class="py-2 px-3">HTTP method (GET, POST, PUT, PATCH, DELETE)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">data</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Request payload data</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">headers</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Custom HTTP headers</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">away</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Treat as external link</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">confirm</code></td>
                        <td class="py-2 px-3">bool/string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Show confirmation dialog</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">confirm-text</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Custom confirmation message</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">confirm-title</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Confirmation dialog title</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">confirm-button</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Confirm</td>
                        <td class="py-2 px-3">Confirm button label</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">cancel-button</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Cancel</td>
                        <td class="py-2 px-3">Cancel button label</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">confirm-danger</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Red confirm button style</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">preserve-scroll</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Keep scroll position</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-blue-600">preserve-state</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Keep component state</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-blue-600">prefetch</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Prefetch on hover</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="link-examples.blade.php">
{{-- Basic SPA navigation --}}
&lt;x-accelade::link href="/dashboard"&gt;Dashboard&lt;/x-accelade::link&gt;

{{-- With confirmation --}}
&lt;x-accelade::link
    href="/account/delete"
    :confirm="true"
    confirm-text="Delete your account? This cannot be undone."
    :confirm-danger="true"
&gt;
    Delete Account
&lt;/x-accelade::link&gt;

{{-- POST request (form-like) --}}
&lt;x-accelade::link
    href="/api/items"
    method="POST"
    :data="['name' => 'New Item', 'status' => 'active']"
&gt;
    Create Item
&lt;/x-accelade::link&gt;

{{-- DELETE with confirmation --}}
&lt;x-accelade::link
    href="/api/items/123"
    method="DELETE"
    confirm-text="Delete this item?"
    confirm-button="Delete"
    :confirm-danger="true"
&gt;
    Delete Item
&lt;/x-accelade::link&gt;

{{-- External link --}}
&lt;x-accelade::link
    href="https://example.com"
    :away="true"
    confirm-text="You are leaving this site."
&gt;
    Visit External Site
&lt;/x-accelade::link&gt;

{{-- Preserve scroll position --}}
&lt;x-accelade::link
    href="/page"
    :preserve-scroll="true"
&gt;
    Update (keep scroll)
&lt;/x-accelade::link&gt;
    </x-accelade::code-block>
</section>
