@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="bridge">
    @include('accelade::demo.partials._bridge', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
