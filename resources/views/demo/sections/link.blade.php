@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="link">
    @include('accelade::demo.partials._link-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
