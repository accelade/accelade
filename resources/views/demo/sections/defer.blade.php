@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="defer">
    @include('accelade::demo.partials._defer-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
