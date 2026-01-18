@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false, 'section' => '', 'docSection' => null])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" :section="$section" :documentation="$documentation" :hasDemo="false">
    {{-- Markdown-only section - no interactive demo --}}
</x-accelade::layouts.docs>
