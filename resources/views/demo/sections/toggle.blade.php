@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="toggle">
    @include('accelade::demo.partials._toggle-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
