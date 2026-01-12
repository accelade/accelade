@props([
    'bag' => 'default',
])

@php
    $errorsId = 'errors-' . \Illuminate\Support\Str::random(8);

    // Get the error bag
    $errorBag = $errors->getBag($bag);

    // Convert errors to array format
    $errorsArray = [];
    foreach ($errorBag->getMessages() as $key => $messages) {
        $errorsArray[$key] = $messages;
    }

    // Initial state with errors data
    $initialState = [
        'errors' => $errorsArray,
    ];
@endphp

<div
    data-accelade
    data-accelade-id="{{ $errorsId }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    data-accelade-errors
    data-accelade-cloak
    {{ $attributes }}
>
    <accelade:script>
        // Create errors helper object
        const errorsData = state.errors || {};

        return {
            errors: {
                // Check if a field has errors
                has(key) {
                    return errorsData.hasOwnProperty(key) && errorsData[key].length > 0;
                },

                // Get first error for a field
                first(key) {
                    if (errorsData.hasOwnProperty(key) && errorsData[key].length > 0) {
                        return errorsData[key][0];
                    }
                    return null;
                },

                // Get all errors for a field
                get(key) {
                    return errorsData[key] || [];
                },

                // Get all errors as object
                get all() {
                    return errorsData;
                },

                // Check if there are any errors
                get any() {
                    return Object.keys(errorsData).length > 0;
                },

                // Get count of all errors
                get count() {
                    return Object.values(errorsData).reduce((sum, arr) => sum + arr.length, 0);
                },

                // Get all keys that have errors
                get keys() {
                    return Object.keys(errorsData);
                }
            }
        };
    </accelade:script>
    {{ $slot }}
</div>
