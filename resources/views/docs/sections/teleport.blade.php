@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="teleport" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('accelade::demo.partials._teleport-component', ['prefix' => $prefix])
</x-accelade::layouts.docs>
