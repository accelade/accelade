@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="lazy">
    @include('accelade::demo.partials._lazy-loading', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
