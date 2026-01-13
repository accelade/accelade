@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="installation" :documentation="$documentation" :hasDemo="false">
    {{-- Installation page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>Follow the installation steps above to get started with Accelade.</p>
    </div>
</x-accelade::layouts.docs>
