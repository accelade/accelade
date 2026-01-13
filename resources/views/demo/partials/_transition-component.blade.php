{{-- Transition Component Section - CSS Class-Based Animations --}}
@props(['prefix' => 'a'])

<!-- Demo: Transition Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-violet-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Transition & Animation</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        CSS class-based enter/leave animations. Use animation presets directly on toggle components, or use the standalone <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::transition&gt;</code> component.
    </p>

    <!-- Simplest Usage - Toggle with Animation -->
    <div class="rounded-xl p-4 border-2 border-green-500/50 mb-4" style="background: rgba(34, 197, 94, 0.1);">
        <h4 class="font-semibold mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-green-500 text-white rounded">Recommended</span>
            Simplest Usage: Toggle + Animation
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
            Just add <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="preset"</code> to the toggle component.
            All <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">{{ $prefix }}-show</code> elements inside will automatically animate.
        </p>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <!-- Fade -->
            <x-accelade::toggle animation="fade">
                <div class="space-y-3">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                    >
                        Fade
                    </button>
                    <div {{ $prefix }}-show="toggled" class="p-3 rounded-lg border border-[var(--docs-border)] text-center" style="background: var(--docs-bg);">
                        <span style="color: var(--docs-text);">Fades in/out</span>
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
                    <div {{ $prefix }}-show="toggled" class="p-3 rounded-lg border border-[var(--docs-border)] text-center" style="background: var(--docs-bg);">
                        <span style="color: var(--docs-text);">Scales in/out</span>
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
                        <div {{ $prefix }}-show="toggled" class="p-3 rounded-lg border border-[var(--docs-border)] text-center" style="background: var(--docs-bg);">
                            <span style="color: var(--docs-text);">Slides up</span>
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
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">All Animation Presets</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-accelade::toggle animation="default">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700">default</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Default</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="fade">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-violet-500 text-white text-sm rounded hover:bg-violet-600">fade</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Fade</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="scale">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-pink-500 text-white text-sm rounded hover:bg-pink-600">scale</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Scale</div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-left">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">slide-left</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Left</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-right">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-cyan-500 text-white text-sm rounded hover:bg-cyan-600">slide-right</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Right</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-up">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600">slide-up</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Up</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="slide-down">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-amber-500 text-white text-sm rounded hover:bg-amber-600">slide-down</button>
                    <div class="overflow-hidden">
                        <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Down</div>
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="opacity">
                <div class="space-y-2">
                    <button @click.prevent="toggle()" class="w-full px-3 py-2 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">opacity</button>
                    <div {{ $prefix }}-show="toggled" class="p-2 rounded border border-[var(--docs-border)] text-center text-sm" style="background: var(--docs-bg-alt); color: var(--docs-text);">Opacity</div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Accordion Example with Animation -->
    <div class="rounded-xl p-4 border border-indigo-500/30 mb-4" style="background: rgba(99, 102, 241, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Example</span>
            Accordion with Animation
        </h4>

        <div class="space-y-2">
            <x-accelade::toggle animation="collapse">
                <div class="rounded-lg border border-indigo-500/30 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium hover:bg-indigo-500/10 flex justify-between items-center"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span>What is Accelade?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-500/30 bg-indigo-500/10" style="color: var(--docs-text-muted);">
                        Accelade is a reactive UI library for Laravel Blade that brings modern reactivity without a JavaScript framework.
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="collapse">
                <div class="rounded-lg border border-indigo-500/30 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium hover:bg-indigo-500/10 flex justify-between items-center"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span>How do animations work?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-500/30 bg-indigo-500/10" style="color: var(--docs-text-muted);">
                        Simply add the <code class="px-1 rounded" style="background: var(--docs-bg);">animation</code> prop to your toggle component. CSS classes are applied during enter/leave transitions.
                    </div>
                </div>
            </x-accelade::toggle>

            <x-accelade::toggle animation="collapse">
                <div class="rounded-lg border border-indigo-500/30 overflow-hidden">
                    <button
                        @click.prevent="toggle()"
                        class="w-full px-4 py-3 text-left font-medium hover:bg-indigo-500/10 flex justify-between items-center"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                        <span>Can I create custom animations?</span>
                        <svg class="w-5 h-5 transition-transform" {{ $prefix }}-class="{'rotate-180': toggled}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div {{ $prefix }}-show="toggled" class="px-4 py-3 border-t border-indigo-500/30 bg-indigo-500/10" style="color: var(--docs-text-muted);">
                        Yes! Use the <code class="px-1 rounded" style="background: var(--docs-bg);">Animation</code> facade to register custom presets with your own Tailwind classes.
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <hr class="my-6 border-[var(--docs-border)]">

    <h4 class="text-lg font-semibold mb-3" style="color: var(--docs-text);">Standalone Transition Component</h4>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        For more control, use the <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::transition&gt;</code> component directly.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Fade Animation -->
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">Preset</span>
                Fade Animation
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Simple fade in/out using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="fade"</code>.
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
                        <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                            <p style="color: var(--docs-text);">This content fades in and out smoothly!</p>
                        </div>
                    </x-accelade::transition>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Scale Animation -->
        <div class="rounded-xl p-4 border border-pink-500/30" style="background: rgba(236, 72, 153, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-pink-500/20 text-pink-500 rounded">Preset</span>
                Scale Animation
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Scale from center using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="scale"</code>.
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
                        <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                            <p style="color: var(--docs-text);">This content scales in from the center!</p>
                        </div>
                    </x-accelade::transition>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Left -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Preset</span>
                Slide Left
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Slide from left using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="slide-left"</code>.
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
                            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                                <p style="color: var(--docs-text);">Slides in from the left!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Right -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Preset</span>
                Slide Right
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Slide from right using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="slide-right"</code>.
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
                            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                                <p style="color: var(--docs-text);">Slides in from the right!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Up -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Preset</span>
                Slide Up
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Slide from bottom using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="slide-up"</code>.
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
                            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                                <p style="color: var(--docs-text);">Slides up from the bottom!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>

        <!-- Slide Down -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Preset</span>
                Slide Down
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Slide from top using <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">animation="slide-down"</code>.
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
                            <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                                <p style="color: var(--docs-text);">Slides down from the top!</p>
                            </div>
                        </x-accelade::transition>
                    </div>
                </div>
            </x-accelade::toggle>
        </div>
    </div>

    <!-- Custom Classes -->
    <div class="rounded-xl p-4 border border-orange-500/30 mb-4" style="background: rgba(249, 115, 22, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-orange-500/20 text-orange-500 rounded">Custom</span>
            Custom Tailwind Classes
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
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
                    <div class="p-4 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p style="color: var(--docs-text);">Custom animation with rotation and translate!</p>
                    </div>
                </x-accelade::transition>
            </div>
        </x-accelade::toggle>
    </div>

    <!-- Dropdown Example -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 border border-[var(--docs-border)] rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">Example</span>
            Dropdown Menu
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
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
                    <div class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg border border-[var(--docs-border)] py-1 z-10" style="background: var(--docs-bg);">
                        <a href="#" class="block px-4 py-2 hover:bg-[var(--docs-bg-alt)]" style="color: var(--docs-text);">Profile</a>
                        <a href="#" class="block px-4 py-2 hover:bg-[var(--docs-bg-alt)]" style="color: var(--docs-text);">Settings</a>
                        <a href="#" class="block px-4 py-2 hover:bg-[var(--docs-bg-alt)]" style="color: var(--docs-text);">Notifications</a>
                        <hr class="my-1 border-[var(--docs-border)]">
                        <a href="#" class="block px-4 py-2 text-red-500 hover:bg-red-500/10">Sign Out</a>
                    </div>
                </x-accelade::transition>
            </div>
        </x-accelade::toggle>
    </div>

    <!-- Modal/Dialog Example -->
    <div class="rounded-xl p-4 border border-indigo-500/30 mb-4" style="background: rgba(99, 102, 241, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Example</span>
            Notification Toast
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
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
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Built-in Animation Presets</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Preset</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">default</code></td>
                        <td class="py-2 px-3">Subtle fade with scale (95% to 100%)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">fade</code> / <code class="text-violet-500">opacity</code></td>
                        <td class="py-2 px-3">Simple opacity fade</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">scale</code></td>
                        <td class="py-2 px-3">Scale from center (0% to 100%)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">slide-left</code></td>
                        <td class="py-2 px-3">Slide in from left</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">slide-right</code></td>
                        <td class="py-2 px-3">Slide in from right</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">slide-up</code></td>
                        <td class="py-2 px-3">Slide up from bottom</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">slide-down</code></td>
                        <td class="py-2 px-3">Slide down from top</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-violet-500">collapse</code></td>
                        <td class="py-2 px-3">Fade only (ideal for accordions)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Props Reference -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">show</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">State expression to watch (e.g., "toggled")</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">animation</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Preset name (default, fade, scale, slide-*)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">enter</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes during enter transition</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">enter-from</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at start of enter</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">enter-to</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at end of enter</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">leave</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes during leave transition</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-violet-500">leave-from</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">Classes at start of leave</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-violet-500">leave-to</code></td>
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
