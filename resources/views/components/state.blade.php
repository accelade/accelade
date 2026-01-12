@props([
    'errors' => null,
    'flash' => null,
    'shared' => null,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'state-' . uniqid());

    // Get validation errors from the error bag
    $validationErrors = [];
    if ($errors !== null) {
        // If errors passed explicitly
        $validationErrors = is_array($errors) ? $errors : [];
    } elseif (isset($__errorBag) && $__errorBag instanceof \Illuminate\Support\ViewErrorBag) {
        // Get errors from the default error bag
        $bag = $__errorBag->getBag('default');
        $validationErrors = $bag->toArray();
    } elseif (session()->has('errors')) {
        // Fall back to session errors
        $sessionErrors = session('errors');
        if ($sessionErrors instanceof \Illuminate\Support\ViewErrorBag) {
            $validationErrors = $sessionErrors->getBag('default')->toArray();
        }
    }

    // Get flash data
    $flashData = [];
    if ($flash !== null) {
        $flashData = is_array($flash) ? $flash : [];
    } else {
        // Get all flash data from session
        $flashData = session()->all();
        // Filter out internal Laravel keys
        $internalKeys = ['_token', '_previous', '_flash', 'errors'];
        $flashData = array_diff_key($flashData, array_flip($internalKeys));
    }

    // Get shared data
    $sharedData = [];
    if ($shared !== null) {
        $sharedData = is_array($shared) ? $shared : [];
    } else {
        // Get from Accelade shared data
        $sharedData = app('accelade')->allShared();
    }

    // Build state object for initial render
    $stateErrors = [];
    foreach ($validationErrors as $key => $messages) {
        $stateErrors[$key] = is_array($messages) ? ($messages[0] ?? '') : $messages;
    }

    $initialState = [
        'state' => [
            'errors' => $stateErrors,
            'rawErrors' => $validationErrors,
            'hasErrors' => !empty($validationErrors),
            'flash' => $flashData,
            'shared' => $sharedData,
        ],
    ];
@endphp

<div
    data-accelade
    data-accelade-state-component
    data-state-id="{{ $id }}"
    data-state-errors="{{ json_encode($validationErrors) }}"
    data-state-flash="{{ json_encode($flashData) }}"
    data-state-shared="{{ json_encode($sharedData) }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes->except(['id', 'errors', 'flash', 'shared']) }}
>{{ $slot }}</div>
