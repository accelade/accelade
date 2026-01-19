@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="draggable" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('accelade::demo.partials._draggable-component', ['prefix' => $prefix])
</x-accelade::layouts.docs>
