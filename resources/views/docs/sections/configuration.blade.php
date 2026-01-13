@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="configuration" :documentation="$documentation" :hasDemo="false">
    {{-- Configuration page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>See the configuration options above to customize Accelade for your project.</p>
    </div>
</x-accelade::layouts.docs>
