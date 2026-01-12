@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="data">
    @include('accelade::demo.partials._data-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
