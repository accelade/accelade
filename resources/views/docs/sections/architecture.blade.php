@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="architecture" :documentation="$documentation" :hasDemo="false">
    {{-- Architecture page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>Understand the architecture and design decisions behind Accelade above.</p>
    </div>
</x-accelade::layouts.docs>
