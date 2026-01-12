@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    // Set framework before any components render
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="counter">
    <!-- Demo: Counter (Client-side) -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Counter Component</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">A simple reactive counter. No build tools required!</p>

        <div class="flex justify-center mb-6">
            <x-accelade::counter :initial-count="0" :framework="$framework" />
        </div>

        <x-accelade::code-block language="blade" filename="counter.blade.php">
&lt;x-accelade::counter :initial-count="0" /&gt;
        </x-accelade::code-block>
    </section>

    <!-- Demo: Counter with Server Sync -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-indigo-500 rounded-full animate-pulse"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Counter with Server Sync</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Counter that syncs state with the server (persisted across requests).</p>

        <div class="flex justify-center mb-6">
            <x-accelade::counter :initial-count="10" sync="count" :framework="$framework" />
        </div>

        <x-accelade::code-block language="blade" filename="counter-sync.blade.php">
&lt;x-accelade::counter :initial-count="10" sync="count" /&gt;
        </x-accelade::code-block>
    </section>
</x-accelade::layouts.demo-sidebar>
