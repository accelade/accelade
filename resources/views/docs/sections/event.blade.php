@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="event" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('accelade::demo.partials._event-component', ['prefix' => $prefix])
</x-accelade::layouts.docs>
