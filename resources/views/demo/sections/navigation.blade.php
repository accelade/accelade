@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="navigation">
    <!-- Demo: SPA Navigation -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">SPA Navigation</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Navigate without full page reloads using &lt;x-accelade::link&gt;.</p>

        <!-- SPA Demo (Same Framework - No Page Reload) -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">SPA Navigation (Same Framework)</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework, 'section' => 'counter']) }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition"
                >
                    Go to Counter (SPA)
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework, 'section' => 'navigation']) }}?time={{ time() }}"
                    class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition"
                >
                    Navigate with Query (SPA)
                </x-accelade::link>
            </div>
            <p class="text-xs text-slate-400 text-center mt-2">Watch the page update without a full reload!</p>
        </div>

        <!-- Preserve Scroll & State -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">Preserve Scroll & State</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework, 'section' => 'navigation']) }}?t={{ time() }}"
                    :preserveScroll="true"
                    class="px-6 py-3 bg-teal-600 text-white rounded-xl font-medium hover:bg-teal-700 transition"
                >
                    Preserve Scroll Position
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework, 'section' => 'navigation']) }}?t={{ time() }}"
                    :preserveState="true"
                    class="px-6 py-3 bg-amber-600 text-white rounded-xl font-medium hover:bg-amber-700 transition"
                >
                    Preserve Component State
                </x-accelade::link>
            </div>
        </div>

        <!-- Framework Switch -->
        <div>
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">Switch Framework (Full Reload)</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                @foreach(['vanilla', 'vue', 'react', 'svelte', 'angular'] as $fw)
                    @if($fw !== $framework)
                        <x-accelade::link
                            href="{{ route('docs.section', ['framework' => $fw, 'section' => 'navigation']) }}"
                            class="px-6 py-3 bg-slate-600 text-white rounded-xl font-medium hover:bg-slate-700 transition"
                        >
                            {{ ucfirst($fw) }} Demo
                        </x-accelade::link>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <x-accelade::code-block language="blade" filename="navigation.blade.php">
&lt;x-accelade::link href="/page" :preserveScroll="true"&gt;
    Navigate with SPA
&lt;/x-accelade::link&gt;
    </x-accelade::code-block>
</x-accelade::layouts.demo-sidebar>
