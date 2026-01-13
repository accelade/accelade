@php
    $bridgeConfig = $getJsonConfig();

    // Build initial state from props for Accelade component
    $initialState = [
        'props' => json_decode($bridgeConfig, true)['props'] ?? [],
    ];

    // Render the inner component's view if no slot content provided
    $hasSlot = isset($slot) && !$slot->isEmpty();
    $innerContent = null;

    if (!$hasSlot && method_exists($component, 'render')) {
        $innerView = $component->render();
        if ($innerView instanceof \Illuminate\Contracts\View\View) {
            // Pass component's public properties to the view
            $innerContent = $innerView->with($component->data())->render();
        }
    }
@endphp

<div
    data-accelade
    data-accelade-bridge="{{ $bridgeConfig }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes }}
>
    @if($hasSlot)
        {{ $slot }}
    @elseif($innerContent)
        {!! $innerContent !!}
    @endif
</div>
