@props([
    'to' => null,
    'disabled' => false,
])

@php
    $id = $attributes->get('id', 'teleport-' . uniqid());
@endphp

<div
    data-accelade-teleport
    data-teleport-id="{{ $id }}"
    @if($to)
        data-teleport-to="{{ $to }}"
    @endif
    @if($disabled)
        data-teleport-disabled="true"
    @endif
    {{ $attributes->except(['id', 'to', 'disabled']) }}
>{{ $slot }}</div>
