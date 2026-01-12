@props([
    'channel' => null,
    'private' => false,
    'presence' => false,
    'listen' => '',
    'preserveScroll' => false,
])

@php
    $eventId = 'event-' . \Illuminate\Support\Str::random(8);

    // Initial state for the component
    $initialState = [
        'subscribed' => false,
        'events' => [],
    ];
@endphp

<div
    data-accelade
    data-accelade-id="{{ $eventId }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    data-accelade-echo
    @if($channel) data-echo-channel="{{ $channel }}" @endif
    data-echo-private="{{ $private ? 'true' : 'false' }}"
    data-echo-presence="{{ $presence ? 'true' : 'false' }}"
    @if($listen) data-echo-listen="{{ $listen }}" @endif
    data-echo-preserve-scroll="{{ $preserveScroll ? 'true' : 'false' }}"
    data-accelade-cloak
    {{ $attributes }}
>
    {{ $slot }}
</div>
