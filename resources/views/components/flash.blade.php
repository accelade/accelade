@props([
    'shared' => true,
])

@php
    $flashId = 'flash-' . \Illuminate\Support\Str::random(8);

    // Get flash data from session
    $flashData = [];

    // Collect all flash data from session
    if (session()->has('_flash.old')) {
        foreach (session()->get('_flash.old', []) as $key) {
            if (session()->has($key)) {
                $flashData[$key] = session()->get($key);
            }
        }
    }

    // Also check for explicit flash keys that are commonly used
    $commonFlashKeys = ['message', 'success', 'error', 'warning', 'info', 'status', 'notification'];
    foreach ($commonFlashKeys as $key) {
        if (session()->has($key) && !isset($flashData[$key])) {
            $flashData[$key] = session()->get($key);
        }
    }

    // If shared mode is enabled and Accelade::share is available, share flash data
    if ($shared && class_exists(\Accelade\Facades\Accelade::class)) {
        \Accelade\Facades\Accelade::share('flash', $flashData);
    }

    // Initial state for the component
    $initialState = [
        'flash' => $flashData,
    ];
@endphp

<div
    data-accelade
    data-accelade-id="{{ $flashId }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    data-accelade-flash
    data-flash-data="{{ json_encode($flashData) }}"
    @if(!$shared) data-flash-no-share @endif
    data-accelade-cloak
    {{ $attributes }}
>
    {{ $slot }}
</div>
