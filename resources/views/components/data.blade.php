@props([
    'default' => [],
    'remember' => null,
    'localStorage' => null,
    'store' => null,
])

@php
    $dataId = 'data-' . \Illuminate\Support\Str::random(8);

    // Process default data
    $initialData = $default;

    // Convert to array if it's a string (JavaScript object notation)
    if (is_string($initialData) && !empty($initialData)) {
        // Keep as string for JS parsing
        $isJsObject = true;
    } else {
        $isJsObject = false;
        // Handle various PHP types
        if ($initialData instanceof \Illuminate\Contracts\Support\Arrayable) {
            $initialData = $initialData->toArray();
        } elseif ($initialData instanceof \Illuminate\Contracts\Support\Jsonable) {
            $initialData = json_decode($initialData->toJson(), true);
        } elseif ($initialData instanceof \JsonSerializable) {
            $initialData = $initialData->jsonSerialize();
        } elseif (is_object($initialData)) {
            $initialData = (array) $initialData;
        }

        if (!is_array($initialData)) {
            $initialData = [];
        }
    }

    // Generate the state JSON
    $stateJson = $isJsObject ? $initialData : json_encode($initialData);

    // Determine storage key for remember/localStorage
    $storageKey = $remember ?? $localStorage ?? null;

    // Reserved store names that cannot be used
    $reservedNames = ['data', 'form', 'toggle', 'state', 'store'];
    $isValidStore = $store && !in_array(strtolower($store), $reservedNames);
@endphp

<div
    data-accelade
    data-accelade-id="{{ $dataId }}"
    data-accelade-state="{{ $isJsObject ? '' : $stateJson }}"
    @if($isJsObject) data-accelade-state-js="{{ $stateJson }}" @endif
    @if($remember) data-accelade-remember="{{ $remember }}" @endif
    @if($localStorage) data-accelade-local-storage="{{ $localStorage }}" @endif
    @if($isValidStore) data-accelade-store="{{ $store }}" @endif
    data-accelade-cloak
    {{ $attributes }}
>
    {{ $slot }}
</div>
