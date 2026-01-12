{{-- Transition Component Section - CSS Class-Based Animations --}}
@props(['prefix' => 'a'])

<!-- Demo: Transition Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-violet-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Transition & Animation</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        CSS class-based enter/leave animations. Use animation presets directly on toggle components, or use the standalone <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::transition&gt;</code> component.
    </p>

    <!-- Simplest Usage - Toggle with Animation -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200 mb-6">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-green-500 text-white rounded">Recommended</span>
            Simplest Usage: Toggle + Animation
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Just add <code class="bg-slate-100 px-1 rounded">animation="preset"</code> to the toggle component.
            All <code class="bg-slate-100 px-1 rounded">{{ $prefix }}-show</code> elements inside will automatically animate.
        </p>

        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <!-- Fade -->
            <x-accelade::toggle animation="fade">
                <div class="space-y-3">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                    >
                        Fade
                    </button>
                    <div {{ $prefix }}-show="toggled" class="p-3 bg-white rounded-lg border border-green-200 text-center">
                        <span class="text-slate-700">Fades in/out</span>
                    </div>
                </div>
            </x-accelade::toggle>

            <!-- Scale -->
            <x-accelade::toggle animation="scale">
                <div class="space-y-3">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
                    >
                        Scale
                    </button>
                    <div {{ $prefix }}-show="toggled" class="p-3 bg-white rounded-lg border border-emerald-200 text-center">
                        <span class="text-slate-700">Scales in/out</span>
                    </div>
                </div>
            </x-accelade::toggle>

            <!-- Slide Up -->
            <x-accelade::toggle animation="slide-up">
                <div class="space-y-3">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition-colors"
                    >
                        Slide Up
                    </button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-3 bg-white rounded-lg border border-teal-200 text-center">
                            <span class="text-slate-700">Slides up</span>
                        </div>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <x-accelade::code-block language="blade" filename="simple-toggle-animation.blade.php">
{{-- Just add animation prop to toggle! --}}
&lt;x-accelade::toggle animation="fade"&gt;
    &lt;button @click.prevent="toggle()"&gt;Toggle&lt;/button&gt;
    &lt;div {{ $prefix }}-show="toggled"&gt;Animated content!&lt;/div&gt;
&lt;/x-accelade::toggle&gt;

{{-- Available presets: fade, scale, slide-up, slide-down, slide-left, slide-right --}}
        </x-accelade::code-block>
    </div>

    <!-- All Presets Demo -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">All Animation Presets</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-accelade::toggle animation="default">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700">default</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Default</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="fade">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-violet-500 text-white text-sm rounded hover:bg-violet-600">fade</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Fade</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="scale">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-pink-500 text-white text-sm rounded hover:bg-pink-600">scale</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Scale</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-left">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">slide-left</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Left</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-right">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-cyan-500 text-white text-sm rounded hover:bg-cyan-600">slide-right</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Right</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-up">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600">slide-up</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Up</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-down">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-amber-500 text-white text-sm rounded hover:bg-amber-600">slide-down</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Down</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="opacity">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">opacity</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 bg-white rounded border text-center text-sm">Opacity</div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Accordion Example with Animation -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-6 border border-indigo-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">Example</span>
            Accordion with Animation
        </h3>

        <div class="space-y-2">
            <x-accelade::toggle animation="collapse">
                <div class="bg-white rounded-lg border border-indigo-200 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium text-slate-700 hover:bg-indigo-50 flex justify-between items-center"
                    >
                        <span>What is Accelade?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-100 text-slate-600">
                        Accelade is a reactive UI library for Laravel Blade that brings modern reactivity without a JavaScript framework.
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="collapse">
                <div class="bg-white rounded-lg border border-indigo-200 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium text-slate-700 hover:bg-indigo-50 flex justify-between items-center"
                    >
                        <span>How do animations work?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-100 text-slate-600">
                        Simply add the <code class="bg-slate-100 px-1 rounded">animation</code> prop to your toggle component. CSS classes are applied during enter/leave transitions.
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="collapse">
                <div class="bg-white rounded-lg border border-indigo-200 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium text-slate-700 hover:bg-indigo-50 flex justify-between items-center"
                    >
                        <span>Can I create custom animations?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-100 text-slate-600">
                        Yes! Use the <code class="bg-slate-100 px-1 rounded">Animation</code> facade to register custom presets with your own Tailwind classes.
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <hr class="my-8 border-slate-200">

    <h3 class="text-xl font-semibold text-slate-700 mb-4">Standalone Transition Component</h3>
    <p class="text-slate-500 mb-6">
        For more control, use the <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::transition&gt;</code> component directly.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Fade Animation -->
        <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-6 border border-violet-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-violet-100 text-violet-700 rounded">Preset</span>
                Fade Animation
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Simple fade in/out using <code class="bg-slate-100 px-1 rounded">animation="fade"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-violet-500 text-white rounded-lg hover:bg-violet-600 transition-colors"
                    >
                        Toggle Fade
                    </button>

                    <x-accelade::transition show="toggled" animation="fade">
                        <div class="p-4 bg-white rounded-lg border border-violet-200">
                            <p class="text-slate-700">This content fades in and out smoothly!</p>
                        </div>
                    </x-accelade::transition>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Scale Animation -->
        <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl p-6 border border-pink-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-pink-100 text-pink-700 rounded">Preset</span>
                Scale Animation
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Scale from center using <code class="bg-slate-100 px-1 rounded">animation="scale"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors"
                    >
                        Toggle Scale
                    </button>

                    <x-accelade::transition show="toggled" animation="scale">
                        <div class="p-4 bg-white rounded-lg border border-pink-200">
                            <p class="text-slate-700">This content scales in from the center!</p>
                        </div>
                    </x-accelade::transition>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Slide Left -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">Preset</span>
                Slide Left
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Slide from left using <code class="bg-slate-100 px-1 rounded">animation="slide-left"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    >
                        Toggle Slide Left
                    </button>

                    <div class="overflow-hidden">
                        <x-accelade::transition show="toggled" animation="slide-left">
                            <div class="p-4 bg-white rounded-lg border border-blue-200">
                                <p class="text-slate-700">Slides in from the left!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Right -->
        <div class="bg-gradient-to-r from-cyan-50 to-teal-50 rounded-xl p-6 border border-cyan-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-cyan-100 text-cyan-700 rounded">Preset</span>
                Slide Right
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Slide from right using <code class="bg-slate-100 px-1 rounded">animation="slide-right"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                    >
                        Toggle Slide Right
                    </button>

                    <div class="overflow-hidden">
                        <x-accelade::transition show="toggled" animation="slide-right">
                            <div class="p-4 bg-white rounded-lg border border-cyan-200">
                                <p class="text-slate-700">Slides in from the right!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Slide Up -->
        <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl p-6 border border-emerald-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Preset</span>
                Slide Up
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Slide from bottom using <code class="bg-slate-100 px-1 rounded">animation="slide-up"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors"
                    >
                        Toggle Slide Up
                    </button>

                    <div class="overflow-hidden">
                        <x-accelade::transition show="toggled" animation="slide-up">
                            <div class="p-4 bg-white rounded-lg border border-emerald-200">
                                <p class="text-slate-700">Slides up from the bottom!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Down -->
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Preset</span>
                Slide Down
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Slide from top using <code class="bg-slate-100 px-1 rounded">animation="slide-down"</code>.
            </p>

            <x-accelade::toggle>
                <div class="space-y-4">
                    <button
                        @click.prevent="toggle()"
                        class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors"
                    >
                        Toggle Slide Down
                    </button>

                    <div class="overflow-hidden">
                        <x-accelade::transition show="toggled" animation="slide-down">
                            <div class="p-4 bg-white rounded-lg border border-amber-200">
                                <p class="text-slate-700">Slides down from the top!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Custom Classes -->
    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6 border border-orange-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded">Custom</span>
            Custom Tailwind Classes
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Define your own transition using individual class props.
        </p>

        <x-accelade::toggle>
            <div class="space-y-4">
                <button
                    @click.prevent="toggle()"
                    class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors"
                >
                    Toggle Custom Animation
                </button>

                <x-accelade::transition
                    show="toggled"
                    enter="transition-all duration-500 ease-out"
                    enter-from="opacity-0 translate-y-8 rotate-6"
                    enter-to="opacity-100 translate-y-0 rotate-0"
                    leave="transition-all duration-300 ease-in"
                    leave-from="opacity-100 translate-y-0 rotate-0"
                    leave-to="opacity-0 translate-y-8 -rotate-6"
                >
                    <div class="p-4 bg-white rounded-lg border border-orange-200">
                        <p class="text-slate-700">Custom animation with rotation and translate!</p>
                    </div>
                </x-accelade::transition>
            </div>
        </x-accelade::toggle>
    </div>

    <!-- Dropdown Example -->
    <div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-6 border border-slate-200 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">Example</span>
            Dropdown Menu
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Real-world dropdown example using transition with default animation.
        </p>

        <x-accelade::toggle>
            <div class="relative inline-block">
                <button
                    @click.prevent="toggle()"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-2"
                >
                    <span>Menu</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <x-accelade::transition show="toggled" animation="default">
                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-10">
                        <a href="#" class="block px-4 py-2 text-slate-700 hover:bg-slate-100">Profile</a>
                        <a href="#" class="block px-4 py-2 text-slate-700 hover:bg-slate-100">Settings</a>
                        <a href="#" class="block px-4 py-2 text-slate-700 hover:bg-slate-100">Notifications</a>
                        <hr class="my-1 border-slate-200">
                        <a href="#" class="block px-4 py-2 text-red-600 hover:bg-red-50">Sign Out</a>
                    </div>
                </x-accelade::transition>
            </div>
        </x-accelade::toggle>
    </div>

    <!-- Modal/Dialog Example -->
    <div class="bg-gradient-to-r from-indigo-50 to-violet-50 rounded-xl p-6 border border-indigo-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
            <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">Example</span>
            Notification Toast
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            Toast notification with slide-up animation.
        </p>

        <x-accelade::toggle>
            <div class="space-y-4">
                <button
                    @click.prevent="setToggle(true)"
                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors"
                >
                    Show Toast
                </button>

                <x-accelade::transition show="toggled" animation="slide-up">
                    <div class="p-4 bg-green-500 text-white rounded-lg shadow-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Successfully saved!</span>
                        </div>
                        <button @click.prevent="setToggle(false)" class="text-green-200 hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-accelade::transition>
            </div>
        </x-accelade::toggle>
    </div>

    <!-- All Presets Reference -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Built-in Animation Presets</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Preset</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">default</code></td>
                        <td class="py-2 px-3">Subtle fade with scale (95% to 100%)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">fade</code> / <code class="text-violet-600">opacity</code></td>
                        <td class="py-2 px-3">Simple opacity fade</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">scale</code></td>
                        <td class="py-2 px-3">Scale from center (0% to 100%)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">slide-left</code></td>
                        <td class="py-2 px-3">Slide in from left</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">slide-right</code></td>
                        <td class="py-2 px-3">Slide in from right</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">slide-up</code></td>
                        <td class="py-2 px-3">Slide up from bottom</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">slide-down</code></td>
                        <td class="py-2 px-3">Slide down from top</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-violet-600">collapse</code></td>
                        <td class="py-2 px-3">Fade only (ideal for accordions)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Props Reference -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Component Props</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Prop</th>
                        <th class="text-left py-2 px-3 text-slate-600">Type</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">show</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">State expression to watch (e.g., "toggled")</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">animation</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Preset name (default, fade, scale, slide-*)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">enter</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes during enter transition</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">enter-from</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at start of enter</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">enter-to</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at end of enter</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">leave</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes during leave transition</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-violet-600">leave-from</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at start of leave</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-violet-600">leave-to</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at end of leave</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="transition-examples.blade.php">
{{-- Basic with preset --}}
&lt;x-accelade::toggle&gt;
    &lt;button @click.prevent="toggle()"&gt;Toggle&lt;/button&gt;
    &lt;x-accelade::transition show="toggled" animation="fade"&gt;
        &lt;div&gt;Fades in and out!&lt;/div&gt;
    &lt;/x-accelade::transition&gt;
&lt;/x-accelade::toggle&gt;

{{-- Custom classes --}}
&lt;x-accelade::transition
    show="toggled"
    enter="transition-opacity duration-300"
    enter-from="opacity-0"
    enter-to="opacity-100"
    leave="transition-opacity duration-200"
    leave-from="opacity-100"
    leave-to="opacity-0"
&gt;
    &lt;div&gt;Custom transition!&lt;/div&gt;
&lt;/x-accelade::transition&gt;

{{-- Register custom preset in AppServiceProvider --}}
Animation::new(
    name: 'bounce',
    enter: 'transition ease-bounce duration-300',
    enterFrom: 'opacity-0 scale-50',
    enterTo: 'opacity-100 scale-100',
    leave: 'transition ease-in duration-200',
    leaveFrom: 'opacity-100 scale-100',
    leaveTo: 'opacity-0 scale-50',
);
    </x-accelade::code-block>
</section>
