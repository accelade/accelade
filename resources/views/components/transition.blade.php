@props([
    'show' => null,
    'animation' => null,
    'enter' => null,
    'enterFrom' => null,
    'enterTo' => null,
    'leave' => null,
    'leaveFrom' => null,
    'leaveTo' => null,
])

@php
    $id = $attributes->get('id', 'transition-' . uniqid());

    // Get animation preset if specified
    $preset = null;
    if ($animation) {
        $preset = app('accelade.animation')->get($animation);
    }

    // Build transition classes - prefer explicit props over preset
    $transitionClasses = [
        'enter' => $enter ?? ($preset?->enter->transition ?? 'transition ease-out duration-200'),
        'enterFrom' => $enterFrom ?? ($preset?->enter->from ?? 'opacity-0'),
        'enterTo' => $enterTo ?? ($preset?->enter->to ?? 'opacity-100'),
        'leave' => $leave ?? ($preset?->leave->transition ?? 'transition ease-in duration-150'),
        'leaveFrom' => $leaveFrom ?? ($preset?->leave->from ?? 'opacity-100'),
        'leaveTo' => $leaveTo ?? ($preset?->leave->to ?? 'opacity-0'),
    ];

    // The show expression - can be a state property or expression
    $showExpression = $show ?? 'true';
@endphp

<div
    data-accelade-transition
    data-transition-id="{{ $id }}"
    data-transition-show="{{ $showExpression }}"
    data-transition-enter="{{ $transitionClasses['enter'] }}"
    data-transition-enter-from="{{ $transitionClasses['enterFrom'] }}"
    data-transition-enter-to="{{ $transitionClasses['enterTo'] }}"
    data-transition-leave="{{ $transitionClasses['leave'] }}"
    data-transition-leave-from="{{ $transitionClasses['leaveFrom'] }}"
    data-transition-leave-to="{{ $transitionClasses['leaveTo'] }}"
    {{ $attributes->except(['id', 'show', 'animation', 'enter', 'enterFrom', 'enterTo', 'leave', 'leaveFrom', 'leaveTo']) }}
>{{ $slot }}</div>
