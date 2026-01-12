@props([
    'href',
    'method' => 'GET',
    'data' => null,
    'headers' => null,
    'spa' => true,
    'away' => false,
    'activeClass' => 'active',
    'prefetch' => false,
    'preserveScroll' => false,
    'preserveState' => false,
    'replace' => false,
    'confirm' => null,
    'confirmText' => null,
    'confirmTitle' => null,
    'confirmButton' => null,
    'cancelButton' => null,
    'confirmDanger' => false,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $isActive = request()->is(ltrim($href, '/')) || request()->url() === url($href);
    $classes = $attributes->get('class', '');
    if ($isActive) {
        $classes .= ' ' . $activeClass;
    }

    // Determine if confirmation is needed
    $hasConfirm = $confirm !== null || $confirmText !== null;

    // Prepare data and headers as JSON if provided
    $dataJson = $data ? json_encode($data) : null;
    $headersJson = $headers ? json_encode($headers) : null;

    // Determine confirmation text
    $finalConfirmText = $confirmText ?? ($confirm === true ? 'Are you sure you want to continue?' : $confirm);
@endphp

@if($spa && !$away)
    <a
        href="{{ $href }}"
        @if($framework === 'vanilla') a-link @elseif($framework === 'vue') data-accelade-link @else data-spa-link @endif
        {{ $attributes->merge(['class' => trim($classes)]) }}
        @if($method !== 'GET') data-method="{{ strtoupper($method) }}" @endif
        @if($dataJson) data-data="{{ $dataJson }}" @endif
        @if($headersJson) data-headers="{{ $headersJson }}" @endif
        @if($prefetch) data-prefetch @endif
        @if($preserveScroll) data-preserve-scroll @endif
        @if($preserveState) data-preserve-state @endif
        @if($replace) data-replace @endif
        @if($hasConfirm) data-confirm="{{ $finalConfirmText }}" @endif
        @if($confirmTitle) data-confirm-title="{{ $confirmTitle }}" @endif
        @if($confirmButton) data-confirm-button="{{ $confirmButton }}" @endif
        @if($cancelButton) data-cancel-button="{{ $cancelButton }}" @endif
        @if($confirmDanger) data-confirm-danger @endif
    >{{ $slot }}</a>
@elseif($away)
    <a
        href="{{ $href }}"
        @if($framework === 'vanilla') a-link @elseif($framework === 'vue') data-accelade-link @else data-spa-link @endif
        {{ $attributes->merge(['class' => trim($classes)]) }}
        data-away
        @if($hasConfirm) data-confirm="{{ $finalConfirmText }}" @endif
        @if($confirmTitle) data-confirm-title="{{ $confirmTitle }}" @endif
        @if($confirmButton) data-confirm-button="{{ $confirmButton }}" @endif
        @if($cancelButton) data-cancel-button="{{ $cancelButton }}" @endif
        @if($confirmDanger) data-confirm-danger @endif
    >{{ $slot }}</a>
@else
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => trim($classes)]) }}
    >{{ $slot }}</a>
@endif
