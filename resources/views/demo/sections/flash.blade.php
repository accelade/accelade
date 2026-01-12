@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);

    // Set demo flash messages for testing
    session()->flash('message', 'This is a demo flash message!');
    session()->flash('success', 'Operation completed successfully!');
    session()->flash('error', 'Oops! Something went wrong.');
    session()->flash('info', 'Here is some useful information.');
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="flash">
    @include('accelade::demo.partials._flash-component', ['prefix' => $prefix])
</x-accelade::layouts.demo-sidebar>
