@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="testing" :documentation="$documentation" :hasDemo="false">
    {{-- Testing page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>Learn how to test your Accelade components above.</p>
    </div>
</x-accelade::layouts.docs>
