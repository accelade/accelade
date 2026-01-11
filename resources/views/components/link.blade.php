@props([
    'href',
    'spa' => true,
    'activeClass' => 'active',
    'prefetch' => false,
    'preserveScroll' => false,
    'preserveState' => false,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $isActive = request()->is(ltrim($href, '/')) || request()->url() === url($href);
    $classes = $attributes->get('class', '');
    if ($isActive) {
        $classes .= ' ' . $activeClass;
    }
@endphp

@if($spa)
    @if($framework === 'vanilla')
        <a
            href="{{ $href }}"
            a-link
            {{ $attributes->merge(['class' => trim($classes)]) }}
            @if($prefetch) data-prefetch @endif
            @if($preserveScroll) data-preserve-scroll @endif
            @if($preserveState) data-preserve-state @endif
        >{{ $slot }}</a>
    @elseif($framework === 'vue')
        <a
            href="{{ $href }}"
            data-accelade-link
            {{ $attributes->merge(['class' => trim($classes)]) }}
            @if($prefetch) data-prefetch @endif
            @if($preserveScroll) data-preserve-scroll @endif
            @if($preserveState) data-preserve-state @endif
        >{{ $slot }}</a>
    @else
        <a
            href="{{ $href }}"
            data-spa-link
            {{ $attributes->merge(['class' => trim($classes)]) }}
            @if($prefetch) data-prefetch @endif
            @if($preserveScroll) data-preserve-scroll @endif
            @if($preserveState) data-preserve-state @endif
        >{{ $slot }}</a>
    @endif
@else
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => trim($classes)]) }}
    >{{ $slot }}</a>
@endif
