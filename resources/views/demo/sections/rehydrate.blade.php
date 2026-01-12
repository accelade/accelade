@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="rehydrate">
    @include('accelade::demo.partials._rehydrate-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
