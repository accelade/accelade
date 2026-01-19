@props([
    'text' => '',
    'position' => 'top',
    'trigger' => 'hover',
    'delay' => 0,
    'hideDelay' => 0,
    'arrow' => true,
    'animation' => 'fade',
    'interactive' => false,
    'offset' => 8,
    'maxWidth' => null,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'tooltip-' . uniqid());

    // Get animation preset if specified
    $animationConfig = null;
    if ($animation) {
        $preset = app('accelade.animation')->get($animation);
        if ($preset) {
            $animationConfig = $preset->toArray();
        }
    }

    // Build tooltip configuration
    $tooltipConfig = [
        'text' => $text,
        'position' => $position,
        'trigger' => $trigger,
        'delay' => (int) $delay,
        'hideDelay' => (int) $hideDelay,
        'arrow' => (bool) $arrow,
        'interactive' => (bool) $interactive,
        'offset' => (int) $offset,
        'maxWidth' => $maxWidth,
    ];

    // Build initial state
    $initialState = [
        'isVisible' => false,
        'tooltipText' => $text,
    ];
@endphp

<div
    data-accelade
    data-accelade-tooltip
    data-tooltip-id="{{ $id }}"
    data-tooltip-config="{{ json_encode($tooltipConfig) }}"
    @if($animationConfig) data-tooltip-animation="{{ json_encode($animationConfig) }}" @endif
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes->except(['id', 'text', 'position', 'trigger', 'delay', 'hideDelay', 'arrow', 'animation', 'interactive', 'offset', 'maxWidth'])->merge(['class' => 'relative inline-block']) }}
>{{ $slot }}</div>
