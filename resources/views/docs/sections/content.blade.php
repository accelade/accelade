@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="content" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('accelade::demo.partials._content')
</x-accelade::layouts.docs>
