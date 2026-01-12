@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="errors">
    @include('accelade::demo.partials._errors-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
