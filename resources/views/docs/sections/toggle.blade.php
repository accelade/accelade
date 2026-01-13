@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="toggle" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('accelade::demo.partials._toggle-component', ['prefix' => $prefix])
</x-accelade::layouts.docs>
