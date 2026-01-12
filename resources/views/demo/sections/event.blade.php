@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="event">
    @include('accelade::demo.partials._event-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
