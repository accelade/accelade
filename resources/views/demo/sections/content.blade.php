@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="content">
    @include('accelade::demo.partials._content')
</x-accelade::layouts.demo-sidebar>
