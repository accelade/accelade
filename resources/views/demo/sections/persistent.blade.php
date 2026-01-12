@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="persistent">
    @include('accelade::demo.partials._persistent-layout', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
