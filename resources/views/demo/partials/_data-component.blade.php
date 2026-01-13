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
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Data Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Reactive data containers with storage persistence using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::data&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Basic Counter -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Basic</span>
                Counter with Data
            </h4>
            <x-accelade::data :default="['count' => 0]">
                <div class="flex items-center gap-4">
                    <button
                        @click="$set('count', count - 1)"
                        class="w-10 h-10 flex items-center justify-center bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                    >
                        -
                    </button>
                    <span class="text-2xl font-bold min-w-[3rem] text-center" style="color: var(--docs-text);" {{ $textAttr }}="count">0</span>
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
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Session</span>
                Remember State
            </h4>
            <x-accelade::data :default="['visits' => 0]" remember="demo-visits">
                <div class="text-center">
                    <p class="mb-2" style="color: var(--docs-text-muted);">Page visits this session:</p>
                    <span class="text-3xl font-bold text-amber-500" {{ $textAttr }}="visits">0</span>
                    <button
                        @click="$set('visits', visits + 1)"
                        class="block mx-auto mt-4 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors"
                    >
                        Increment Visit
                    </button>
                    <p class="text-xs mt-2" style="color: var(--docs-text-muted);">Refresh the page - count persists!</p>
                </div>
            </x-accelade::data>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Local Storage -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">localStorage</span>
                Persistent Preferences
            </h4>
            <x-accelade::data :default="['theme' => 'light', 'fontSize' => 16]" local-storage="demo-preferences">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm block mb-1" style="color: var(--docs-text-muted);">Theme</label>
                        <select
                            {{ $modelAttr }}="theme"
                            class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                        >
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                            <option value="system">System</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm block mb-1" style="color: var(--docs-text-muted);">Font Size: <span {{ $textAttr }}="fontSize">16</span>px</label>
                        <input
                            type="range"
                            {{ $modelAttr }}="fontSize"
                            min="12"
                            max="24"
                            class="w-full"
                        >
                    </div>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Close browser & reopen - settings persist!</p>
                </div>
            </x-accelade::data>
        </div>

        <!-- Global Store -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Store</span>
                Global Shared State
            </h4>
            <div class="space-y-3">
                <!-- First component using the store -->
                <x-accelade::data :default="['cartCount' => 0]" store="demoCart">
                    <div class="p-3 rounded-lg border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
                        <p class="text-sm text-purple-500">Component A - Cart: <span class="font-bold" {{ $textAttr }}="cartCount">0</span> items</p>
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
                    <div class="p-3 rounded-lg border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
                        <p class="text-sm text-purple-500">Component B - Cart: <span class="font-bold" {{ $textAttr }}="cartCount">0</span> items</p>
                        <button
                            @click="$set('cartCount', cartCount + 1)"
                            class="mt-2 px-3 py-1 text-sm bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors"
                        >
                            Add Item (B)
                        </button>
                    </div>
                </x-accelade::data>
                <p class="text-xs" style="color: var(--docs-text-muted);">Both components share the same state!</p>
            </div>
        </div>
    </div>

    <!-- Form with Remember -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-3" style="color: var(--docs-text);">Multi-Step Form with Session Persistence</h4>
        <x-accelade::data :default="['step' => 1, 'name' => '', 'email' => '']" remember="demo-form">
            <div {{ $showAttr }}="step === 1" class="space-y-4">
                <div>
                    <label class="text-sm block mb-1" style="color: var(--docs-text-muted);">Step 1: Your Name</label>
                    <input
                        type="text"
                        {{ $modelAttr }}="name"
                        placeholder="Enter your name"
                        class="w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
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
                    <label class="text-sm block mb-1" style="color: var(--docs-text-muted);">Step 2: Your Email</label>
                    <input
                        type="email"
                        {{ $modelAttr }}="email"
                        placeholder="Enter your email"
                        class="w-full px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg-alt); color: var(--docs-text);"
                    >
                </div>
                <div class="flex gap-2">
                    <button
                        @click="$set('step', 1)"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
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
                <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
                    <h5 class="font-medium mb-2" style="color: var(--docs-text);">Review Your Info</h5>
                    <p style="color: var(--docs-text-muted);">Name: <span class="font-medium" style="color: var(--docs-text);" {{ $textAttr }}="name"></span></p>
                    <p style="color: var(--docs-text-muted);">Email: <span class="font-medium" style="color: var(--docs-text);" {{ $textAttr }}="email"></span></p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="$set('step', 2)"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
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
            <p class="text-xs mt-4" style="color: var(--docs-text-muted);">Navigate away and return - your progress is saved!</p>
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
