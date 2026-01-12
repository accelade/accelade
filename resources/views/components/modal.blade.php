@props([
    'name' => null,
    'maxWidth' => null,
    'position' => null,
    'slideoverPosition' => null,
    'closeExplicitly' => false,
    'closeButton' => true,
    'opened' => false,
    'slideover' => false,
    'bottomSheet' => false,
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'modal-' . uniqid());

    // Determine default max-width based on type
    $defaultMaxWidth = $slideover ? 'md' : ($bottomSheet ? '2xl' : '2xl');
    $finalMaxWidth = $maxWidth ?? $defaultMaxWidth;

    // Determine default position
    $defaultPosition = 'center';
    $finalPosition = $position ?? $defaultPosition;

    // Slideover position
    $defaultSlideoverPosition = 'right';
    $finalSlideoverPosition = $slideoverPosition ?? $defaultSlideoverPosition;
@endphp

<div
    data-accelade
    data-accelade-modal
    data-modal-id="{{ $id }}"
    @if($name) data-modal-name="{{ $name }}" @endif
    @if($slideover) data-slideover @endif
    @if($bottomSheet) data-bottom-sheet @endif
    data-max-width="{{ $finalMaxWidth }}"
    data-position="{{ $finalPosition }}"
    data-slideover-position="{{ $finalSlideoverPosition }}"
    @if($closeExplicitly) data-close-explicitly @endif
    @if(!$closeButton) data-no-close-button @endif
    @if($opened) data-opened @endif
    data-accelade-state="{{ json_encode(['isOpen' => $opened, 'modal' => ['close' => null, 'open' => null, 'setIsOpen' => null]]) }}"
    {{ $attributes->except(['id', 'name', 'maxWidth', 'position', 'slideoverPosition', 'closeExplicitly', 'closeButton', 'opened', 'slideover', 'bottomSheet'])->merge(['style' => 'display: none;']) }}
>
    {{ $slot }}
</div>
