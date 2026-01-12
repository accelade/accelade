@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="teleport">
    @include('accelade::demo.partials._teleport-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
