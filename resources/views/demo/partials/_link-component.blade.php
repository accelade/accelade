{{-- Link Component Section - Enhanced Navigation --}}
@props(['prefix' => 'a'])

<!-- Demo: Link Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Link Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Enhanced link component with HTTP methods, confirmation dialogs, and navigation options using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::link&gt;</code>.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Navigation -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Basic</span>
                SPA Navigation
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Click links to navigate without full page reload. The link component handles SPA navigation automatically.
            </p>

            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Go to Counter
                </x-accelade::link>

                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'notifications']) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >
                    Go to Notifications
                </x-accelade::link>
            </div>
        </div>

        <!-- Confirmation Dialog -->
        <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">Confirm</span>
                Confirmation Dialog
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Show a confirmation dialog before navigation. Great for destructive actions.
            </p>

            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    :confirm="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition"
                >
                    Simple Confirm
                </x-accelade::link>

                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    confirm-text="Are you sure you want to proceed? This action cannot be undone."
                    confirm-title="Confirm Navigation"
                    confirm-button="Yes, proceed"
                    cancel-button="No, stay here"
                    :confirm-danger="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                >
                    Danger Confirm
                </x-accelade::link>
            </div>
        </div>

        <!-- HTTP Methods -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Methods</span>
                HTTP Methods
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Support for POST, PUT, PATCH, DELETE methods - like form submissions but as links.
            </p>

            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <pre class="text-xs overflow-x-auto" style="color: var(--docs-text-muted);"><code>&lt;x-accelade::link
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
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 border border-[var(--docs-border)] rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">External</span>
                External Links (Away)
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use the <code class="px-1 py-0.5 rounded text-xs border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">away</code> attribute for external links with optional confirmation.
            </p>

            <div class="flex flex-wrap gap-2">
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
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition border border-[var(--docs-border)]"
                    style="background: var(--docs-bg-alt); color: var(--docs-text);"
                >
                    GitHub (with confirm)
                </x-accelade::link>
            </div>
        </div>

        <!-- Navigation Options -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Navigation Options</h4>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-emerald-500">preserve-scroll</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Keep scroll position after navigation</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-emerald-500">preserve-state</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Preserve component state across navigation</p>
                </div>
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <code class="text-sm font-mono text-emerald-500">prefetch</code>
                    <p class="text-xs mt-1" style="color: var(--docs-text-muted);">Prefetch page on hover for faster navigation</p>
                </div>
            </div>
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
                        <td class="py-2 px-3"><code class="text-blue-500">href</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">-</td>
                        <td class="py-2 px-3">Target URL (required)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">method</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">GET</td>
                        <td class="py-2 px-3">HTTP method (GET, POST, PUT, PATCH, DELETE)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">data</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Request payload data</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">headers</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Custom HTTP headers</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">away</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Treat as external link</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">confirm</code></td>
                        <td class="py-2 px-3">bool/string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Show confirmation dialog</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">confirm-text</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Custom confirmation message</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">confirm-title</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Confirmation dialog title</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">confirm-button</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Confirm</td>
                        <td class="py-2 px-3">Confirm button label</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">cancel-button</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Cancel</td>
                        <td class="py-2 px-3">Cancel button label</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">confirm-danger</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Red confirm button style</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">preserve-scroll</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Keep scroll position</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">preserve-state</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Keep component state</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-blue-500">prefetch</code></td>
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
