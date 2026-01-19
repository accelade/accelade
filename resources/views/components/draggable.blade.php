@props([
    'group' => null,
    'handle' => null,
    'animation' => 150,
    'ghostClass' => 'opacity-50',
    'dragClass' => 'shadow-lg',
    'disabled' => false,
    'sortable' => true,
    'dropzone' => false,
    'accepts' => null,
    'axis' => null,
    'tree' => false,
    'nestedContainer' => '[data-draggable-children]',
    'maxDepth' => 0,
    'indentSize' => 24,
    'dropIndicatorClass' => 'bg-blue-500',
    'nestIndicatorClass' => 'ring-2 ring-blue-500 ring-opacity-50',
    'nestThreshold' => 30,
    'springAnimation' => true,
    'springStiffness' => 300,
    'springDamping' => 25,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'draggable-' . uniqid());

    // Build draggable configuration
    $draggableConfig = [
        'group' => $group,
        'handle' => $handle,
        'animation' => (int) $animation,
        'ghostClass' => $ghostClass,
        'dragClass' => $dragClass,
        'disabled' => (bool) $disabled,
        'sortable' => (bool) $sortable,
        'dropzone' => (bool) $dropzone,
        'accepts' => $accepts,
        'axis' => $axis,
        'tree' => (bool) $tree,
        'nestedContainer' => $nestedContainer,
        'maxDepth' => (int) $maxDepth,
        'indentSize' => (int) $indentSize,
        'dropIndicatorClass' => $dropIndicatorClass,
        'nestIndicatorClass' => $nestIndicatorClass,
        'nestThreshold' => (int) $nestThreshold,
        'springAnimation' => (bool) $springAnimation,
        'springStiffness' => (int) $springStiffness,
        'springDamping' => (int) $springDamping,
    ];

    // Build initial state
    $initialState = [
        'isDragging' => false,
        'isDragOver' => false,
        'draggedItem' => null,
        'draggedIndex' => null,
        'items' => [],
    ];
@endphp

<div
    data-accelade
    data-accelade-draggable
    data-draggable-id="{{ $id }}"
    data-draggable-config="{{ json_encode($draggableConfig) }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes->except(['id', 'group', 'handle', 'animation', 'ghostClass', 'dragClass', 'disabled', 'sortable', 'dropzone', 'accepts', 'axis', 'tree', 'nestedContainer', 'maxDepth', 'indentSize', 'dropIndicatorClass', 'nestIndicatorClass', 'nestThreshold', 'springAnimation', 'springStiffness', 'springDamping']) }}
>{{ $slot }}</div>
