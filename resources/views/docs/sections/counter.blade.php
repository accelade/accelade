@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="counter" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: Counter (Client-side) -->
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Counter Component</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">A simple reactive counter. No build tools required!</p>

        <div class="flex justify-center mb-4">
            <x-accelade::counter :initial-count="0" :framework="$framework" />
        </div>

        <x-accelade::code-block language="blade" filename="counter.blade.php">
&lt;x-accelade::counter :initial-count="0" /&gt;
        </x-accelade::code-block>
    </section>

    <!-- Demo: Counter with Server Sync -->
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full animate-pulse"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Counter with Server Sync</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Counter that syncs state with the server (persisted across requests).</p>

        <div class="flex justify-center mb-4">
            <x-accelade::counter :initial-count="10" sync="count" :framework="$framework" />
        </div>

        <x-accelade::code-block language="blade" filename="counter-sync.blade.php">
&lt;x-accelade::counter :initial-count="10" sync="count" /&gt;
        </x-accelade::code-block>
    </section>
</x-accelade::layouts.docs>
