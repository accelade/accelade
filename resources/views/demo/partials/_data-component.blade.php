{{-- Data Component Section - Framework Agnostic --}}
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

    $modelAttr = match($prefix) {
        'v' => 'v-model',
        'data-state' => 'data-state-model',
        's' => 's-model',
        'ng' => 'ng-model',
        default => 'a-model',
    };
@endphp

<!-- Demo: Data Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Data Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Reactive data containers with storage persistence using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::data&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Counter -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-cyan-100 text-cyan-700 rounded">Basic</span>
                Counter with Data
            </h3>
            <x-accelade::data :default="['count' => 0]">
                <div class="flex items-center gap-4">
                    <button
                        @click="$set('count', count - 1)"
                        class="w-10 h-10 flex items-center justify-center bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                    >
                        -
                    </button>
                    <span class="text-2xl font-bold text-slate-700 min-w-[3rem] text-center" {{ $textAttr }}="count">0</span>
                    <button
                        @click="$set('count', count + 1)"
                        class="w-10 h-10 flex items-center justify-center bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                    >
                        +
                    </button>
                </div>
            </x-accelade::data>
        </div>

        <!-- Session Storage (Remember) -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Session</span>
                Remember State
            </h3>
            <x-accelade::data :default="['visits' => 0]" remember="demo-visits">
                <div class="text-center">
                    <p class="text-slate-600 mb-2">Page visits this session:</p>
                    <span class="text-3xl font-bold text-amber-600" {{ $textAttr }}="visits">0</span>
                    <button
                        @click="$set('visits', visits + 1)"
                        class="block mx-auto mt-4 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors"
                    >
                        Increment Visit
                    </button>
                    <p class="text-xs text-slate-400 mt-2">Refresh the page - count persists!</p>
                </div>
            </x-accelade::data>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Local Storage -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">localStorage</span>
                Persistent Preferences
            </h3>
            <x-accelade::data :default="['theme' => 'light', 'fontSize' => 16]" local-storage="demo-preferences">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm text-slate-600 block mb-1">Theme</label>
                        <select
                            {{ $modelAttr }}="theme"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                            <option value="system">System</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600 block mb-1">Font Size: <span {{ $textAttr }}="fontSize">16</span>px</label>
                        <input
                            type="range"
                            {{ $modelAttr }}="fontSize"
                            min="12"
                            max="24"
                            class="w-full"
                        >
                    </div>
                    <p class="text-xs text-slate-400">Close browser & reopen - settings persist!</p>
                </div>
            </x-accelade::data>
        </div>

        <!-- Global Store -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Store</span>
                Global Shared State
            </h3>
            <div class="space-y-4">
                <!-- First component using the store -->
                <x-accelade::data :default="['cartCount' => 0]" store="demoCart">
                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <p class="text-sm text-purple-700">Component A - Cart: <span class="font-bold" {{ $textAttr }}="cartCount">0</span> items</p>
                        <button
                            @click="$set('cartCount', cartCount + 1)"
                            class="mt-2 px-3 py-1 text-sm bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors"
                        >
                            Add Item (A)
                        </button>
                    </div>
                </x-accelade::data>

                <!-- Second component using the same store -->
                <x-accelade::data :default="['cartCount' => 0]" store="demoCart">
                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <p class="text-sm text-purple-700">Component B - Cart: <span class="font-bold" {{ $textAttr }}="cartCount">0</span> items</p>
                        <button
                            @click="$set('cartCount', cartCount + 1)"
                            class="mt-2 px-3 py-1 text-sm bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors"
                        >
                            Add Item (B)
                        </button>
                    </div>
                </x-accelade::data>
                <p class="text-xs text-slate-400">Both components share the same state!</p>
            </div>
        </div>
    </div>

    <!-- Form with Remember -->
    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl p-6 border border-cyan-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Multi-Step Form with Session Persistence</h3>
        <x-accelade::data :default="['step' => 1, 'name' => '', 'email' => '']" remember="demo-form">
            <div {{ $showAttr }}="step === 1" class="space-y-4">
                <div>
                    <label class="text-sm text-slate-600 block mb-1">Step 1: Your Name</label>
                    <input
                        type="text"
                        {{ $modelAttr }}="name"
                        placeholder="Enter your name"
                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                </div>
                <button
                    @click="$set('step', 2)"
                    class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                >
                    Next Step
                </button>
            </div>
            <div {{ $showAttr }}="step === 2" class="space-y-4">
                <div>
                    <label class="text-sm text-slate-600 block mb-1">Step 2: Your Email</label>
                    <input
                        type="email"
                        {{ $modelAttr }}="email"
                        placeholder="Enter your email"
                        class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                    >
                </div>
                <div class="flex gap-2">
                    <button
                        @click="$set('step', 1)"
                        class="px-4 py-2 bg-slate-400 text-white rounded-lg hover:bg-slate-500 transition-colors"
                    >
                        Back
                    </button>
                    <button
                        @click="$set('step', 3)"
                        class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                    >
                        Review
                    </button>
                </div>
            </div>
            <div {{ $showAttr }}="step === 3" class="space-y-4">
                <div class="p-4 bg-white rounded-lg border border-cyan-100">
                    <h4 class="font-medium text-slate-700 mb-2">Review Your Info</h4>
                    <p class="text-slate-600">Name: <span class="font-medium" {{ $textAttr }}="name"></span></p>
                    <p class="text-slate-600">Email: <span class="font-medium" {{ $textAttr }}="email"></span></p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="$set('step', 2)"
                        class="px-4 py-2 bg-slate-400 text-white rounded-lg hover:bg-slate-500 transition-colors"
                    >
                        Back
                    </button>
                    <button
                        @click="$set('step', 1); $set('name', ''); $set('email', '')"
                        class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
                    >
                        Submit & Reset
                    </button>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-4">Navigate away and return - your progress is saved!</p>
        </x-accelade::data>
    </div>

    <x-accelade::code-block language="blade" filename="data.blade.php">
&lt;!-- Basic data container --&gt;
&lt;x-accelade::data :default="['count' => 0]"&gt;
    &lt;button @click="$set('count', count + 1)"&gt;
        Count: &lt;span {{ $textAttr }}="count"&gt;0&lt;/span&gt;
    &lt;/button&gt;
&lt;/x-accelade::data&gt;

&lt;!-- With session storage persistence --&gt;
&lt;x-accelade::data :default="['step' => 1]" remember="wizard-form"&gt;
    &lt;!-- Form content --&gt;
&lt;/x-accelade::data&gt;

&lt;!-- With localStorage persistence --&gt;
&lt;x-accelade::data :default="['theme' => 'light']" local-storage="user-prefs"&gt;
    &lt;!-- Settings content --&gt;
&lt;/x-accelade::data&gt;

&lt;!-- With global store (shared state) --&gt;
&lt;x-accelade::data :default="['count' => 0]" store="cart"&gt;
    &lt;!-- Cart content --&gt;
&lt;/x-accelade::data&gt;
    </x-accelade::code-block>
</section>
