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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Teleport Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Relocate template sections to different DOM nodes with <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::teleport&gt;</code>.
        Content is "teleported" while preserving reactivity.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Teleport -->
        <div class="bg-gradient-to-r from-cyan-50 to-sky-50 rounded-xl p-6 border border-cyan-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-cyan-100 text-cyan-700 rounded">Basic</span>
                Teleport to Footer
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Content from here appears in the footer section below.
            </p>

            <div data-accelade data-accelade-state='{"searchQuery": ""}'>
                <div class="p-4 bg-white rounded-lg border border-cyan-100 mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Search Input</label>
                    <input
                        type="text"
                        {{ $modelAttr }}="searchQuery"
                        placeholder="Type something..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500"
                    >
                </div>

                <!-- This content teleports to #search-preview -->
                <x-accelade::teleport to="#search-preview">
                    <div class="p-3 bg-cyan-50 rounded-lg border border-cyan-200">
                        <p class="text-sm text-cyan-700">
                            <strong>Search Preview:</strong>
                            <span {{ $textAttr }}="searchQuery || 'Nothing yet...'"></span>
                        </p>
                    </div>
                </x-accelade::teleport>
            </div>

            <!-- Target for teleported content -->
            <div class="mt-4 p-3 bg-slate-100 rounded-lg">
                <p class="text-xs text-slate-500 mb-2">Teleport Target (#search-preview):</p>
                <div id="search-preview"></div>
            </div>
        </div>

        <!-- Reactive Data Teleport -->
        <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-6 border border-violet-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-violet-100 text-violet-700 rounded">Reactive</span>
                Maintains Reactivity
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Teleported content stays reactive with parent component data.
            </p>

            <div data-accelade data-accelade-state='{"count": 0, "message": "Hello"}'>
                <div class="p-4 bg-white rounded-lg border border-violet-100 mb-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <button
                            @click="$set('count', count - 1)"
                            class="px-3 py-1 bg-violet-100 text-violet-700 rounded hover:bg-violet-200"
                        >-</button>
                        <span class="font-mono text-lg" {{ $textAttr }}="count">0</span>
                        <button
                            @click="$set('count', count + 1)"
                            class="px-3 py-1 bg-violet-100 text-violet-700 rounded hover:bg-violet-200"
                        >+</button>
                    </div>
                    <input
                        type="text"
                        {{ $modelAttr }}="message"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-violet-500"
                    >
                </div>

                <!-- Teleported content with reactive bindings -->
                <x-accelade::teleport to="#reactive-target">
                    <div class="p-3 bg-violet-50 rounded-lg border border-violet-200 space-y-2">
                        <p class="text-sm text-violet-700">
                            <strong>Count:</strong> <span {{ $textAttr }}="count">0</span>
                        </p>
                        <p class="text-sm text-violet-700">
                            <strong>Message:</strong> <span {{ $textAttr }}="message">Hello</span>
                        </p>
                        <p class="text-sm text-violet-700">
                            <strong>Computed:</strong> <span {{ $textAttr }}="message + ' (x' + count + ')'"></span>
                        </p>
                    </div>
                </x-accelade::teleport>
            </div>

            <!-- Target -->
            <div class="mt-4 p-3 bg-slate-100 rounded-lg">
                <p class="text-xs text-slate-500 mb-2">Teleport Target (#reactive-target):</p>
                <div id="reactive-target"></div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Multiple Teleports -->
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Multiple</span>
                Multiple Teleports
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Multiple components can teleport to the same target.
            </p>

            <div data-accelade data-accelade-state='{"name": "Alice"}'>
                <input
                    type="text"
                    {{ $modelAttr }}="name"
                    placeholder="Enter name"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg mb-3"
                >

                <x-accelade::teleport to="#multi-target" id="teleport-1">
                    <div class="p-2 bg-amber-100 rounded text-amber-800 text-sm mb-1">
                        Teleport 1: <span {{ $textAttr }}="name">Alice</span>
                    </div>
                </x-accelade::teleport>
            </div>

            <div data-accelade data-accelade-state='{"color": "blue"}'>
                <input
                    type="text"
                    {{ $modelAttr }}="color"
                    placeholder="Enter color"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg mb-3"
                >

                <x-accelade::teleport to="#multi-target" id="teleport-2">
                    <div class="p-2 bg-yellow-100 rounded text-yellow-800 text-sm">
                        Teleport 2: <span {{ $textAttr }}="color">blue</span>
                    </div>
                </x-accelade::teleport>
            </div>

            <!-- Target for multiple teleports -->
            <div class="mt-4 p-3 bg-slate-100 rounded-lg">
                <p class="text-xs text-slate-500 mb-2">Shared Target (#multi-target):</p>
                <div id="multi-target" class="space-y-1"></div>
            </div>
        </div>

        <!-- Disabled Teleport -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">Disabled</span>
                Conditional Teleport
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Disabled teleport keeps content in its original location.
            </p>

            <div class="space-y-4">
                <!-- Disabled teleport -->
                <div class="p-4 bg-white rounded-lg border border-slate-200">
                    <p class="text-sm text-slate-600 mb-2">This teleport is disabled:</p>
                    <x-accelade::teleport to="#disabled-target" :disabled="true">
                        <div class="p-2 bg-slate-100 rounded text-slate-700 text-sm">
                            Content stays here (disabled)
                        </div>
                    </x-accelade::teleport>
                </div>

                <!-- Target (will be empty) -->
                <div class="p-3 bg-slate-100 rounded-lg">
                    <p class="text-xs text-slate-500 mb-2">Target (#disabled-target) - Empty:</p>
                    <div id="disabled-target" class="min-h-[20px]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Targets Demo -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Global</span>
            Teleport to Global Targets
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Teleport content to fixed positions like header, footer, or modal containers anywhere in the page.
        </p>

        <div data-accelade data-accelade-state='{"notification": "Important announcement!"}'>
            <div class="p-4 bg-white rounded-lg border border-emerald-100 mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Notification Message</label>
                <input
                    type="text"
                    {{ $modelAttr }}="notification"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500"
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
        </div>
    </div>

    <!-- Global Notification Target (simulating a fixed position) -->
    <div class="bg-slate-800 rounded-xl p-4 mb-6">
        <p class="text-xs text-slate-400 mb-2">Global Notification Area (#global-notification):</p>
        <div id="global-notification"></div>
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
                        <td class="py-2 px-3"><code class="text-cyan-600">to</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">CSS selector for target element (required)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-cyan-600">disabled</code></td>
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
