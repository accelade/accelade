@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="transition">
    @include('accelade::demo.partials._transition-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
