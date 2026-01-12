@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="shared-data">
    @include('accelade::demo.partials._shared-data', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
