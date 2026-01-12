@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="modal">
    @include('accelade::demo.partials._modal-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
