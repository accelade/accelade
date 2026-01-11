@props([
    'url' => null,
    'method' => 'GET',
    'acceptHeader' => 'application/json',
    'request' => null,
    'headers' => null,
    'poll' => null,
    'manual' => false,
    'watchValue' => null,
    'watchDebounce' => 150,
])

@php
    $deferId = 'defer-' . \Illuminate\Support\Str::random(8);

    // Process request data
    $requestData = $request;
    if (is_array($requestData) || $requestData instanceof \Illuminate\Contracts\Support\Arrayable) {
        if ($requestData instanceof \Illuminate\Contracts\Support\Arrayable) {
            $requestData = $requestData->toArray();
        }
        $requestData = json_encode($requestData);
    }

    // Process headers
    $headersData = $headers;
    if (is_array($headersData) || $headersData instanceof \Illuminate\Contracts\Support\Arrayable) {
        if ($headersData instanceof \Illuminate\Contracts\Support\Arrayable) {
            $headersData = $headersData->toArray();
        }
        $headersData = json_encode($headersData);
    }

    // Initial state for the component
    $initialState = [
        'processing' => !$manual,
        'response' => null,
        'error' => null,
    ];
@endphp

<div
    data-accelade
    data-accelade-id="{{ $deferId }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    data-accelade-defer
    data-defer-url="{{ $url }}"
    data-defer-method="{{ strtoupper($method) }}"
    data-defer-accept="{{ $acceptHeader }}"
    @if($requestData) data-defer-request="{{ $requestData }}" @endif
    @if($headersData) data-defer-headers="{{ $headersData }}" @endif
    @if($poll) data-defer-poll="{{ $poll }}" @endif
    @if($manual) data-defer-manual="true" @endif
    @if($watchValue) data-defer-watch="{{ $watchValue }}" @endif
    @if($watchDebounce && $watchDebounce !== 150) data-defer-watch-debounce="{{ $watchDebounce }}" @endif
    data-accelade-cloak
    {{ $attributes }}
>
    {{ $slot }}
</div>
