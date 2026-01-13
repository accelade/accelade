{{-- Toggle Component Section - Simplified Boolean State Management --}}
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

    $ifAttr = match($prefix) {
        'v' => 'v-if',
        'data-state' => 'data-state-if',
        's' => 's-if',
        'ng' => 'ng-if',
        default => 'a-if',
    };
@endphp

<!-- Demo: Toggle Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Toggle Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Streamlined boolean state management with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::toggle&gt;</code>.
        Perfect for show/hide toggles, accordions, and boolean flags.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Toggle -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Basic</span>
                Simple Toggle
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Single boolean toggle with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">toggle()</code> and <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">setToggle()</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors"
                    >
                        Show Content
                    </button>

                    <div {{ $showAttr }}="toggled" class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="mb-3" style="color: var(--docs-text);">This content is now visible!</p>
                        <button
                            @click.prevent="setToggle(false)"
                            class="px-3 py-1 rounded hover:opacity-80 transition-colors border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                        >
                            Hide Content
                        </button>
                    </div>

                    <p class="text-sm" style="color: var(--docs-text-muted);">
                        State: <span class="font-mono" {{ $textAttr }}="toggled ? 'true' : 'false'">false</span>
                    </p>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Default True -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Default</span>
                Initially Visible
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Start with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">toggled</code> set to true using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">:data="true"</code>.
            </p>

            <x-accelade::toggle :data="true">
                <div class="space-y-4">
                    <div {{ $showAttr }}="toggled" class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p style="color: var(--docs-text);">This content is visible by default!</p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            @click.prevent="setToggle(true)"
                            class="px-3 py-1.5 bg-emerald-500 text-white rounded hover:bg-emerald-600 transition-colors"
                        >
                            Show
                        </button>
                        <button
                            @click.prevent="setToggle(false)"
                            class="px-3 py-1.5 rounded hover:opacity-80 transition-colors border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                        >
                            Hide
                        </button>
                        <button
                            @click.prevent="toggle()"
                            class="px-3 py-1.5 bg-emerald-500/20 text-emerald-500 rounded hover:bg-emerald-500/30 transition-colors"
                        >
                            Toggle
                        </button>
                    </div>

                    <p class="text-sm" style="color: var(--docs-text-muted);">
                        State: <span class="font-mono" {{ $textAttr }}="toggled ? 'true' : 'false'">true</span>
                    </p>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Multiple Toggles -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Multiple</span>
                Named Toggles
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Manage multiple booleans with comma-separated keys: <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">data="isCompany, hasVat"</code>.
            </p>

            <x-accelade::toggle data="isCompany, hasVatNumber, wantsNewsletter">
                <div class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        <button
                            @click.prevent="toggle('isCompany')"
                            class="px-3 py-1.5 bg-amber-500 text-white rounded hover:bg-amber-600 transition-colors"
                        >
                            Toggle Company
                        </button>
                        <button
                            @click.prevent="toggle('hasVatNumber')"
                            class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition-colors"
                        >
                            Toggle VAT
                        </button>
                        <button
                            @click.prevent="toggle('wantsNewsletter')"
                            class="px-3 py-1.5 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition-colors"
                        >
                            Toggle Newsletter
                        </button>
                    </div>

                    <div class="p-4 rounded-lg border border-[var(--docs-border)] space-y-2" style="background: var(--docs-bg);">
                        <div {{ $showAttr }}="isCompany" class="p-2 bg-amber-500/20 rounded text-amber-500">
                            Company mode enabled - show company fields
                        </div>
                        <div {{ $showAttr }}="hasVatNumber" class="p-2 bg-orange-500/20 rounded text-orange-500">
                            VAT number required - show VAT input
                        </div>
                        <div {{ $showAttr }}="wantsNewsletter" class="p-2 bg-yellow-500/20 rounded text-yellow-500">
                            Newsletter subscription active
                        </div>
                    </div>

                    <div class="text-sm space-y-1" style="color: var(--docs-text-muted);">
                        <p>isCompany: <span class="font-mono" {{ $textAttr }}="isCompany ? 'true' : 'false'">false</span></p>
                        <p>hasVatNumber: <span class="font-mono" {{ $textAttr }}="hasVatNumber ? 'true' : 'false'">false</span></p>
                        <p>wantsNewsletter: <span class="font-mono" {{ $textAttr }}="wantsNewsletter ? 'true' : 'false'">false</span></p>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Form Example -->
        <div class="rounded-xl p-4 border border-sky-500/30" style="background: rgba(14, 165, 233, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-sky-500/20 text-sky-500 rounded">Form</span>
                Registration Form
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Conditional form fields based on toggle state.
            </p>

            <x-accelade::toggle data="isCompany, showAddress">
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Name</label>
                        <input
                            type="text"
                            placeholder="Your name"
                            class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-sky-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg); color: var(--docs-text);"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click.prevent="toggle('isCompany')"
                            class="px-3 py-1.5 rounded transition-colors"
                            :class="isCompany ? 'bg-sky-500 text-white' : 'border border-[var(--docs-border)]'"
                            :style="!isCompany ? 'background: var(--docs-bg-alt); color: var(--docs-text);' : ''"
                        >
                            I'm a company
                        </button>
                    </div>

                    <div {{ $showAttr }}="isCompany" class="space-y-3 p-3 bg-sky-500/10 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Company Name</label>
                            <input
                                type="text"
                                placeholder="Company name"
                                class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-sky-500 border border-[var(--docs-border)]"
                                style="background: var(--docs-bg); color: var(--docs-text);"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">VAT Number</label>
                            <input
                                type="text"
                                placeholder="VAT number"
                                class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-sky-500 border border-[var(--docs-border)]"
                                style="background: var(--docs-bg); color: var(--docs-text);"
                            >
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click.prevent="toggle('showAddress')"
                            class="text-sm text-sky-500 hover:text-sky-400"
                        >
                            <span {{ $showAttr }}="!showAddress">+ Add shipping address</span>
                            <span {{ $showAttr }}="showAddress">- Hide shipping address</span>
                        </button>
                    </div>

                    <div {{ $showAttr }}="showAddress" class="p-3 bg-sky-500/10 rounded-lg">
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Address</label>
                        <textarea
                            placeholder="Shipping address"
                            rows="2"
                            class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-sky-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg); color: var(--docs-text);"
                        ></textarea>
                    </div>
                </form>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Accordion Example -->
    <div class="rounded-xl p-4 border border-purple-500/30 mb-4" style="background: rgba(168, 85, 247, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Accordion</span>
            FAQ Accordion
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
            Build accordions by combining multiple toggle components.
        </p>

        <div class="space-y-2">
            <x-accelade::toggle>
                <div class="border border-purple-500/30 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-purple-500/10 transition-colors"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span class="font-medium">What is Accelade?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-500/10 border-t border-purple-500/30">
                        <p style="color: var(--docs-text-muted);">Accelade is a Laravel package that adds reactivity to your Blade templates without requiring a full JavaScript framework.</p>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle>
                <div class="border border-purple-500/30 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-purple-500/10 transition-colors"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span class="font-medium">How does the Toggle component work?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-500/10 border-t border-purple-500/30">
                        <p style="color: var(--docs-text-muted);">The Toggle component provides a simplified interface for managing boolean values. It exposes <code class="px-1 rounded" style="background: var(--docs-bg);">toggle()</code>, <code class="px-1 rounded" style="background: var(--docs-bg);">setToggle()</code>, and <code class="px-1 rounded" style="background: var(--docs-bg);">toggled</code> properties.</p>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle>
                <div class="border border-purple-500/30 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-purple-500/10 transition-colors"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span class="font-medium">Can I use multiple toggles?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-500/10 border-t border-purple-500/30">
                        <p style="color: var(--docs-text-muted);">Yes! Pass comma-separated keys to manage multiple boolean values: <code class="px-1 rounded" style="background: var(--docs-bg);">data="key1, key2, key3"</code>. Then use <code class="px-1 rounded" style="background: var(--docs-bg);">toggle('key1')</code> to toggle specific values.</p>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Animation Presets -->
    <div class="rounded-xl p-4 border border-green-500/30 mb-4" style="background: rgba(34, 197, 94, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-green-500 text-white rounded">Animation</span>
            Toggle with Animation
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
            Add smooth animations with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="preset"</code>.
            All <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">{{ $showAttr }}</code> elements automatically animate.
        </p>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <x-accelade::toggle animation="fade">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600">fade</button>
                    <div {{ $showAttr }}="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg); color: var(--docs-text);">Fades</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="scale">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600">scale</button>
                    <div {{ $showAttr }}="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg); color: var(--docs-text);">Scales</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-up">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-teal-500 text-white text-sm rounded hover:bg-teal-600">slide-up</button>
                    <div class="overflow-hidden">
                        <div {{ $showAttr }}="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg); color: var(--docs-text);">Slides</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-down">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-cyan-500 text-white text-sm rounded hover:bg-cyan-600">slide-down</button>
                    <div class="overflow-hidden">
                        <div {{ $showAttr }}="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg); color: var(--docs-text);">Slides</div>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <p class="text-xs" style="color: var(--docs-text-muted);">Available: <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">default</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">fade</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">scale</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">collapse</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">slide-up</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">slide-down</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">slide-left</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">slide-right</code></p>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props & Methods</h4>
        <div class="overflow-x-auto mb-4">
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
                        <td class="py-2 px-3"><code class="text-indigo-500">data</code></td>
                        <td class="py-2 px-3">bool|string</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Default value (bool) or comma-separated keys (string)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-500">animation</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Animation preset for show/hide (fade, scale, slide-*)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">Exposed Properties</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Property/Method</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">toggled</code></td>
                        <td class="py-2 px-3">Boolean state (single mode)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">toggle(key?)</code></td>
                        <td class="py-2 px-3">Toggle the value (key required for multi-mode)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-500">setToggle(value)</code></td>
                        <td class="py-2 px-3">Set specific value (single mode: bool, multi mode: key + bool)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="toggle-examples.blade.php">
{{-- Basic toggle --}}
&lt;x-accelade::toggle&gt;
    &lt;button @click.prevent="toggle()"&gt;Show/Hide&lt;/button&gt;
    &lt;div a-show="toggled"&gt;Toggled content&lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- With animation (recommended!) --}}
&lt;x-accelade::toggle animation="fade"&gt;
    &lt;button @click.prevent="toggle()"&gt;Toggle&lt;/button&gt;
    &lt;div a-show="toggled"&gt;Animated content!&lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- Collapse animation for accordions (no overlap) --}}
&lt;x-accelade::toggle animation="collapse"&gt;
    &lt;button @click.prevent="toggle()"&gt;Accordion Header&lt;/button&gt;
    &lt;div a-show="toggled"&gt;Accordion content...&lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- Multiple toggles --}}
&lt;x-accelade::toggle data="isCompany, hasVat" animation="scale"&gt;
    &lt;button @click.prevent="toggle('isCompany')"&gt;Company&lt;/button&gt;
    &lt;div a-show="isCompany"&gt;Company fields...&lt;/div&gt;
&lt;/x-accelade::toggle&gt;
    </x-accelade::code-block>
</section>
