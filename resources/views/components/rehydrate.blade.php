@props([
    'on' => null,
    'poll' => null,
    'url' => null,
    'preserveScroll' => true,
])

@php
    $id = $attributes->get('id', 'rehydrate-' . uniqid());

    // Format events for data attribute
    $eventsJson = null;
    if ($on !== null) {
        if (is_array($on)) {
            $eventsJson = json_encode($on);
        } else {
            // Single event string
            $eventsJson = $on;
        }
    }
@endphp

<div
    data-accelade-rehydrate
    data-rehydrate-id="{{ $id }}"
    @if($eventsJson) data-rehydrate-on="{{ $eventsJson }}" @endif
    @if($poll) data-rehydrate-poll="{{ $poll }}" @endif
    @if($url) data-rehydrate-url="{{ $url }}" @endif
    @if($preserveScroll) data-rehydrate-preserve-scroll @endif
    {{ $attributes->except(['id', 'on', 'poll', 'url', 'preserveScroll']) }}
>{{ $slot }}</div>
