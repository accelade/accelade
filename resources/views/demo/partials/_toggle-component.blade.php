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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Toggle Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Streamlined boolean state management with <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::toggle&gt;</code>.
        Perfect for show/hide toggles, accordions, and boolean flags.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Toggle -->
        <div class="bg-gradient-to-r from-indigo-50 to-violet-50 rounded-xl p-6 border border-indigo-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">Basic</span>
                Simple Toggle
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Single boolean toggle with <code class="bg-slate-100 px-1 rounded">toggle()</code> and <code class="bg-slate-100 px-1 rounded">setToggle()</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors"
                    >
                        Show Content
                    </button>

                    <div {{ $showAttr }}="toggled" class="p-4 bg-white rounded-lg border border-indigo-200">
                        <p class="text-slate-700 mb-3">This content is now visible!</p>
                        <button
                            @click.prevent="setToggle(false)"
                            class="px-3 py-1 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors"
                        >
                            Hide Content
                        </button>
                    </div>

                    <p class="text-sm text-slate-500">
                        State: <span class="font-mono" {{ $textAttr }}="toggled ? 'true' : 'false'">false</span>
                    </p>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Default True -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Default</span>
                Initially Visible
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Start with <code class="bg-slate-100 px-1 rounded">toggled</code> set to true using <code class="bg-slate-100 px-1 rounded">:data="true"</code>.
            </p>

            <x-accelade::toggle :data="true">
                <div class="space-y-4">
                    <div {{ $showAttr }}="toggled" class="p-4 bg-white rounded-lg border border-emerald-200">
                        <p class="text-slate-700">This content is visible by default!</p>
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
                            class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition-colors"
                        >
                            Hide
                        </button>
                        <button
                            @click.prevent="toggle()"
                            class="px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 transition-colors"
                        >
                            Toggle
                        </button>
                    </div>

                    <p class="text-sm text-slate-500">
                        State: <span class="font-mono" {{ $textAttr }}="toggled ? 'true' : 'false'">true</span>
                    </p>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Multiple Toggles -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-6 border border-amber-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Multiple</span>
                Named Toggles
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Manage multiple booleans with comma-separated keys: <code class="bg-slate-100 px-1 rounded">data="isCompany, hasVat"</code>.
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

                    <div class="p-4 bg-white rounded-lg border border-amber-200 space-y-2">
                        <div {{ $showAttr }}="isCompany" class="p-2 bg-amber-50 rounded text-amber-700">
                            Company mode enabled - show company fields
                        </div>
                        <div {{ $showAttr }}="hasVatNumber" class="p-2 bg-orange-50 rounded text-orange-700">
                            VAT number required - show VAT input
                        </div>
                        <div {{ $showAttr }}="wantsNewsletter" class="p-2 bg-yellow-50 rounded text-yellow-700">
                            Newsletter subscription active
                        </div>
                    </div>

                    <div class="text-sm text-slate-500 space-y-1">
                        <p>isCompany: <span class="font-mono" {{ $textAttr }}="isCompany ? 'true' : 'false'">false</span></p>
                        <p>hasVatNumber: <span class="font-mono" {{ $textAttr }}="hasVatNumber ? 'true' : 'false'">false</span></p>
                        <p>wantsNewsletter: <span class="font-mono" {{ $textAttr }}="wantsNewsletter ? 'true' : 'false'">false</span></p>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Form Example -->
        <div class="bg-gradient-to-r from-sky-50 to-cyan-50 rounded-xl p-6 border border-sky-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-sky-100 text-sky-700 rounded">Form</span>
                Registration Form
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Conditional form fields based on toggle state.
            </p>

            <x-accelade::toggle data="isCompany, showAddress">
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                        <input
                            type="text"
                            placeholder="Your name"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-sky-500"
                        >
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click.prevent="toggle('isCompany')"
                            class="px-3 py-1.5 rounded transition-colors"
                            :class="isCompany ? 'bg-sky-500 text-white' : 'bg-slate-200 text-slate-700'"
                        >
                            I'm a company
                        </button>
                    </div>

                    <div {{ $showAttr }}="isCompany" class="space-y-3 p-3 bg-sky-50 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                            <input
                                type="text"
                                placeholder="Company name"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-sky-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">VAT Number</label>
                            <input
                                type="text"
                                placeholder="VAT number"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-sky-500"
                            >
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click.prevent="toggle('showAddress')"
                            class="text-sm text-sky-600 hover:text-sky-700"
                        >
                            <span {{ $showAttr }}="!showAddress">+ Add shipping address</span>
                            <span {{ $showAttr }}="showAddress">- Hide shipping address</span>
                        </button>
                    </div>

                    <div {{ $showAttr }}="showAddress" class="p-3 bg-sky-50 rounded-lg">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                        <textarea
                            placeholder="Shipping address"
                            rows="2"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-sky-500"
                        ></textarea>
                    </div>
                </form>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Accordion Example -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Accordion</span>
            FAQ Accordion
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Build accordions by combining multiple toggle components.
        </p>

        <div class="space-y-2">
            <x-accelade::toggle>
                <div class="border border-purple-200 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 bg-white text-left flex items-center justify-between hover:bg-purple-50 transition-colors"
                    >
                        <span class="font-medium text-slate-700">What is Accelade?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-50 border-t border-purple-200">
                        <p class="text-slate-600">Accelade is a Laravel package that adds reactivity to your Blade templates without requiring a full JavaScript framework.</p>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle>
                <div class="border border-purple-200 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 bg-white text-left flex items-center justify-between hover:bg-purple-50 transition-colors"
                    >
                        <span class="font-medium text-slate-700">How does the Toggle component work?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-50 border-t border-purple-200">
                        <p class="text-slate-600">The Toggle component provides a simplified interface for managing boolean values. It exposes <code>toggle()</code>, <code>setToggle()</code>, and <code>toggled</code> properties.</p>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle>
                <div class="border border-purple-200 rounded-lg overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 bg-white text-left flex items-center justify-between hover:bg-purple-50 transition-colors"
                    >
                        <span class="font-medium text-slate-700">Can I use multiple toggles?</span>
                        <span class="text-purple-500" {{ $textAttr }}="toggled ? '−' : '+'">+</span>
                    </button>
                    <div {{ $showAttr }}="toggled" class="px-4 py-3 bg-purple-50 border-t border-purple-200">
                        <p class="text-slate-600">Yes! Pass comma-separated keys to manage multiple boolean values: <code>data="key1, key2, key3"</code>. Then use <code>toggle('key1')</code> to toggle specific values.</p>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- All Props -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Component Props & Methods</h3>
        <div class="overflow-x-auto mb-4">
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
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-600">data</code></td>
                        <td class="py-2 px-3">bool|string</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Default value (bool) or comma-separated keys (string)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h4 class="font-medium text-slate-700 mb-2">Exposed Properties</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Property/Method</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">toggled</code></td>
                        <td class="py-2 px-3">Boolean state (single mode)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">toggle(key?)</code></td>
                        <td class="py-2 px-3">Toggle the value (key required for multi-mode)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-600">setToggle(value)</code></td>
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
    &lt;div a-show="toggled"&gt;
        &lt;p&gt;Toggled content&lt;/p&gt;
        &lt;button @click.prevent="setToggle(false)"&gt;Hide&lt;/button&gt;
    &lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- Default true --}}
&lt;x-accelade::toggle :data="true"&gt;
    &lt;div a-show="toggled"&gt;Visible by default&lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- Multiple toggles --}}
&lt;x-accelade::toggle data="isCompany, hasVatNumber"&gt;
    &lt;button @click.prevent="toggle('isCompany')"&gt;Company&lt;/button&gt;
    &lt;div a-show="isCompany"&gt;Company fields...&lt;/div&gt;

    &lt;button @click.prevent="setToggle('hasVatNumber', true)"&gt;Enable VAT&lt;/button&gt;
    &lt;div a-show="hasVatNumber"&gt;VAT fields...&lt;/div&gt;
&lt;/x-accelade::toggle&gt;
    </x-accelade::code-block>
</section>
