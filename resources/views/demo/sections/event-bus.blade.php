@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="event-bus">
    @include('accelade::demo.partials._event-bus', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
