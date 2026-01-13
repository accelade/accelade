@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="frameworks" :documentation="$documentation" :hasDemo="false">
    {{-- Frameworks page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>Learn about using Accelade with different frontend frameworks above.</p>
    </div>
</x-accelade::layouts.docs>
