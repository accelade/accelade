@props([
    'data' => false,
    'animation' => null,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'toggle-' . uniqid());

    // Get animation preset if specified
    $animationConfig = null;
    if ($animation) {
        $preset = app('accelade.animation')->get($animation);
        if ($preset) {
            $animationConfig = $preset->toArray();
        }
    }

    // Parse data attribute to determine keys and default value
    $keys = [];
    $defaultValue = false;

    if (is_bool($data)) {
        // Single toggle with boolean default: :data="true" or :data="false"
        $keys = ['toggled'];
        $defaultValue = $data;
    } elseif (is_string($data)) {
        if ($data === 'true') {
            // String "true" - single toggle default true
            $keys = ['toggled'];
            $defaultValue = true;
        } elseif ($data === 'false' || $data === '') {
            // String "false" or empty - single toggle default false
            $keys = ['toggled'];
            $defaultValue = false;
        } else {
            // Comma-separated keys: data="isCompany, hasVatNumber"
            $keys = array_map('trim', explode(',', $data));
            $keys = array_filter($keys, fn($k) => !empty($k));
            $defaultValue = false;
        }
    } elseif (is_array($data)) {
        // Array of keys with values: :data="['isCompany' => true, 'hasVat' => false]"
        $keys = array_keys($data);
        // Use the array as initial state
    }

    // Build initial state
    $initialState = [];
    if (is_array($data) && !empty($data)) {
        // Check if it's an associative array with values (not just keys)
        $firstKey = array_key_first($data);
        if (is_string($firstKey)) {
            // Associative array with values: ['isCompany' => true, 'hasVat' => false]
            foreach ($data as $key => $value) {
                $initialState[$key] = (bool) $value;
            }
        } else {
            // Numeric keys - treat as key names with default value
            foreach ($data as $key) {
                $initialState[$key] = $defaultValue;
            }
        }
    } else {
        // Keys with default value
        foreach ($keys as $key) {
            $initialState[$key] = $defaultValue;
        }
    }

    // For toggle data attribute (passed to JS)
    $toggleData = is_bool($data) ? ($data ? 'true' : 'false') : (is_array($data) ? implode(', ', $keys) : $data);
@endphp

<div
    data-accelade
    data-accelade-toggle
    data-toggle-id="{{ $id }}"
    @if($toggleData) data-toggle-data="{{ $toggleData }}" @endif
    data-toggle-default="{{ $defaultValue ? 'true' : 'false' }}"
    @if($animationConfig) data-toggle-animation="{{ json_encode($animationConfig) }}" @endif
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes->except(['id', 'data', 'animation']) }}
>{{ $slot }}</div>
