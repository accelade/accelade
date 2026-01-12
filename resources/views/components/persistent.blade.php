@props([
    'id' => null,
])

@php
    $persistentId = $id ?? 'persistent-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    data-accelade-persistent="{{ $persistentId }}"
    {{ $attributes }}
>
    {{ $slot }}
</div>
