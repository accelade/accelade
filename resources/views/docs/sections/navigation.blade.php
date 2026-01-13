@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="navigation" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: SPA Navigation -->
    <section class="bg-[var(--docs-bg-secondary)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
            <h3 class="text-lg font-semibold">SPA Navigation</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Navigate without full page reloads using &lt;x-accelade::link&gt;.</p>

        <!-- SPA Demo -->
        <div class="mb-4">
            <h4 class="text-sm font-medium text-center mb-3">SPA Navigation (Same Framework)</h4>
            <div class="flex gap-3 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('docs.section', ['section' => 'counter', 'framework' => $framework]) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition"
                >
                    Go to Counter (SPA)
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('docs.section', ['section' => 'navigation', 'framework' => $framework]) }}?time={{ time() }}"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition"
                >
                    Navigate with Query (SPA)
                </x-accelade::link>
            </div>
            <p class="text-xs text-[var(--docs-text-muted)] text-center mt-2">Watch the page update without a full reload!</p>
        </div>

        <!-- Preserve Scroll & State -->
        <div>
            <h4 class="text-sm font-medium text-center mb-3">Preserve Scroll & State</h4>
            <div class="flex gap-3 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('docs.section', ['section' => 'navigation', 'framework' => $framework]) }}?t={{ time() }}"
                    :preserveScroll="true"
                    class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition"
                >
                    Preserve Scroll Position
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('docs.section', ['section' => 'navigation', 'framework' => $framework]) }}?t={{ time() }}"
                    :preserveState="true"
                    class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition"
                >
                    Preserve Component State
                </x-accelade::link>
            </div>
        </div>
    </section>

    <x-accelade::code-block language="blade" filename="navigation.blade.php">
&lt;x-accelade::link href="/page" :preserveScroll="true"&gt;
    Navigate with SPA
&lt;/x-accelade::link&gt;
    </x-accelade::code-block>
</x-accelade::layouts.docs>
