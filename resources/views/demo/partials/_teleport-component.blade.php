{{-- Teleport Component Section - Relocate Content to Different DOM Locations --}}
@props(['prefix' => 'a'])

@php
    $textAttr = match($prefix) {
        'v' => 'v-text',
        'data-state' => 'data-state-text',
        's' => 's-text',
        'ng' => 'ng-text',
        default => 'a-text',
    };

    $modelAttr = match($prefix) {
        'v' => 'v-model',
        'data-state' => 'data-state-model',
        's' => 's-model',
        'ng' => 'ng-model',
        default => 'a-model',
    };

    $showAttr = match($prefix) {
        'v' => 'v-show',
        'data-state' => 'data-state-show',
        's' => 's-show',
        'ng' => 'ng-show',
        default => 'a-show',
    };
@endphp

<!-- Demo: Teleport Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Teleport Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Relocate template sections to different DOM nodes with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::teleport&gt;</code>.
        Content is "teleported" while preserving reactivity.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Teleport -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Basic</span>
                Teleport to Footer
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Content from here appears in the target section below.
            </p>

            <x-accelade::data :default="['searchQuery' => '']">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
                    <label class="block text-sm font-medium mb-2" style="color: var(--docs-text);">Search Input</label>
                    <input
                        type="text"
                        {{ $modelAttr }}="searchQuery"
                        placeholder="Type something..."
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-cyan-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
                    >
                </div>

                <!-- This content teleports to #search-preview -->
                <x-accelade::teleport to="#search-preview">
                    <div class="p-3 bg-cyan-500 text-white rounded-lg">
                        <p class="text-sm">
                            <strong>Search Preview:</strong>
                            <span {{ $textAttr }}="searchQuery || 'Nothing yet...'">Nothing yet...</span>
                        </p>
                    </div>
                </x-accelade::teleport>

                <!-- Target for teleported content -->
                <div class="mt-4 p-3 rounded-lg border-2 border-dashed border-cyan-500/50" style="background: var(--docs-bg);">
                    <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Teleport Target (#search-preview):</p>
                    <div id="search-preview"></div>
                </div>
            </x-accelade::data>
        </div>

        <!-- Reactive Data Teleport -->
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">Reactive</span>
                Maintains Reactivity
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Teleported content stays reactive with parent component data.
            </p>

            <x-accelade::data :default="['count' => 0, 'message' => 'Hello']">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4 space-y-3" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-3">
                        <button
                            @click="$set('count', count - 1)"
                            class="px-3 py-1 bg-violet-500/20 text-violet-500 rounded hover:bg-violet-500/30"
                        >-</button>
                        <span class="font-mono text-lg" style="color: var(--docs-text);" {{ $textAttr }}="count">0</span>
                        <button
                            @click="$set('count', count + 1)"
                            class="px-3 py-1 bg-violet-500/20 text-violet-500 rounded hover:bg-violet-500/30"
                        >+</button>
                    </div>
                    <input
                        type="text"
                        {{ $modelAttr }}="message"
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-violet-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
                    >
                </div>

                <!-- Teleported content with reactive bindings -->
                <x-accelade::teleport to="#reactive-target">
                    <div class="p-3 bg-violet-500 text-white rounded-lg space-y-1">
                        <p class="text-sm">
                            <strong>Count:</strong> <span {{ $textAttr }}="count">0</span>
                        </p>
                        <p class="text-sm">
                            <strong>Message:</strong> <span {{ $textAttr }}="message">Hello</span>
                        </p>
                        <p class="text-sm">
                            <strong>Computed:</strong> <span {{ $textAttr }}="message + ' (x' + count + ')'">Hello (x0)</span>
                        </p>
                    </div>
                </x-accelade::teleport>

                <!-- Target -->
                <div class="mt-4 p-3 rounded-lg border-2 border-dashed border-violet-500/50" style="background: var(--docs-bg);">
                    <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Teleport Target (#reactive-target):</p>
                    <div id="reactive-target"></div>
                </div>
            </x-accelade::data>
        </div>

        <!-- Multiple Teleports -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Multiple</span>
                Multiple Teleports
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Multiple components can teleport to the same target.
            </p>

            <x-accelade::data :default="['name' => 'Alice']">
                <input
                    type="text"
                    {{ $modelAttr }}="name"
                    placeholder="Enter name"
                    class="w-full px-3 py-2 rounded-lg mb-3 border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >

                <x-accelade::teleport to="#multi-target" id="teleport-1">
                    <div class="p-2 bg-amber-500 text-white rounded text-sm mb-1">
                        Teleport 1: <span {{ $textAttr }}="name">Alice</span>
                    </div>
                </x-accelade::teleport>
            </x-accelade::data>

            <x-accelade::data :default="['color' => 'blue']">
                <input
                    type="text"
                    {{ $modelAttr }}="color"
                    placeholder="Enter color"
                    class="w-full px-3 py-2 rounded-lg mb-3 border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >

                <x-accelade::teleport to="#multi-target" id="teleport-2">
                    <div class="p-2 bg-yellow-500 text-white rounded text-sm">
                        Teleport 2: <span {{ $textAttr }}="color">blue</span>
                    </div>
                </x-accelade::teleport>
            </x-accelade::data>

            <!-- Target for multiple teleports -->
            <div class="mt-4 p-3 rounded-lg border-2 border-dashed border-amber-500/50" style="background: var(--docs-bg);">
                <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Shared Target (#multi-target):</p>
                <div id="multi-target" class="space-y-1"></div>
            </div>
        </div>

        <!-- Disabled Teleport -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 border border-[var(--docs-border)] rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">Disabled</span>
                Conditional Teleport
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Disabled teleport keeps content in its original location.
            </p>

            <div class="space-y-4">
                <!-- Disabled teleport -->
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <p class="text-sm mb-2" style="color: var(--docs-text-muted);">This teleport is disabled:</p>
                    <x-accelade::teleport to="#disabled-target" :disabled="true">
                        <div class="p-2 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg); color: var(--docs-text);">
                            Content stays here (disabled)
                        </div>
                    </x-accelade::teleport>
                </div>

                <!-- Target (will be empty) -->
                <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <p class="text-xs mb-2" style="color: var(--docs-text-muted);">Target (#disabled-target) - Empty:</p>
                    <div id="disabled-target" class="min-h-[20px]"></div>
                </div>
            </div>
        </div>

        <!-- Global Targets Demo -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Global</span>
                Teleport to Global Targets
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Teleport content to fixed positions like header, footer, or modal containers anywhere in the page.
            </p>

            <x-accelade::data :default="['notification' => 'Important announcement!']">
                <div class="p-4 rounded-lg border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
                    <label class="block text-sm font-medium mb-2" style="color: var(--docs-text);">Notification Message</label>
                    <input
                        type="text"
                        {{ $modelAttr }}="notification"
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-emerald-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
                    >
                </div>

                <!-- Teleport to global notification area -->
                <x-accelade::teleport to="#global-notification">
                    <div class="p-3 bg-emerald-500 text-white rounded-lg shadow-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span {{ $textAttr }}="notification">Important announcement!</span>
                        </div>
                    </div>
                </x-accelade::teleport>
            </x-accelade::data>
        </div>
    </div>

    <!-- Global Notification Target (simulating a fixed position) -->
    <div class="bg-slate-800 rounded-xl p-4 mb-4">
        <p class="text-xs text-slate-400 mb-2">Global Notification Area (#global-notification):</p>
        <div id="global-notification"></div>
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
                        <td class="py-2 px-3"><code class="text-cyan-500">to</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">CSS selector for target element (required)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-cyan-500">disabled</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Keep content in original location</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="teleport-examples.blade.php">
{{-- Basic teleport to target element --}}
&lt;x-accelade::teleport to="#footer"&gt;
    &lt;p&gt;This content appears in the footer&lt;/p&gt;
&lt;/x-accelade::teleport&gt;

&lt;div id="footer"&gt;&lt;/div&gt;

{{-- Teleport with reactive data --}}
&lt;div data-accelade data-accelade-state='{"search": ""}'&gt;
    &lt;input a-model="search" placeholder="Search..."&gt;

    &lt;x-accelade::teleport to="#search-results"&gt;
        &lt;p&gt;Searching for: &lt;span a-text="search"&gt;&lt;/span&gt;&lt;/p&gt;
    &lt;/x-accelade::teleport&gt;
&lt;/div&gt;

{{-- Disabled teleport (content stays in place) --}}
&lt;x-accelade::teleport to="#target" :disabled="true"&gt;
    &lt;p&gt;This stays here&lt;/p&gt;
&lt;/x-accelade::teleport&gt;

{{-- JavaScript API --}}
&lt;script&gt;
// Get teleport instance
const instance = Accelade.teleport.get('my-teleport');

// Update target dynamically
Accelade.teleport.updateTarget('my-teleport', '#new-target');

// Return content to original position
Accelade.teleport.return('my-teleport');

// Re-teleport
Accelade.teleport.teleport('my-teleport');
&lt;/script&gt;
    </x-accelade::code-block>
</section>
