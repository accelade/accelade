@props([
    'name' => null,
    'size' => 'base',
    'fallback' => null,
    'showFallback' => true,
])

@php
    use BladeUI\Icons\Factory as IconFactory;

    // Predefined size classes
    $sizes = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'base' => 'w-6 h-6',
        'lg' => 'w-7 h-7',
        'xl' => 'w-8 h-8',
        '2xl' => 'w-10 h-10',
        '3xl' => 'w-12 h-12',
        '4xl' => 'w-16 h-16',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['base'];

    // Default fallback SVG (question mark circle)
    $fallbackSvg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="' . e($sizeClass) . '"><circle cx="12" cy="12" r="10" stroke-opacity="0.3"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/></svg>';

    $svg = null;

    if ($name && app()->bound(IconFactory::class)) {
        $factory = app(IconFactory::class);
        $iconClasses = $sizeClass . ' ' . $attributes->get('class', '');

        // Try to render the main icon
        try {
            $svg = $factory->svg($name, trim($iconClasses))->toHtml();
        } catch (\Exception $e) {
            // Try fallback icon if specified
            if ($fallback) {
                try {
                    $svg = $factory->svg($fallback, trim($iconClasses))->toHtml();
                } catch (\Exception $e2) {
                    // Use default fallback
                }
            }
        }
    }

    // If no SVG was rendered and showFallback is true, use the fallback SVG
    if (!$svg && $showFallback) {
        $svg = str_replace('class="' . e($sizeClass) . '"', 'class="' . e($sizeClass . ' ' . $attributes->get('class', '')) . '"', $fallbackSvg);
    }
@endphp

@if($svg)
    {!! $svg !!}
@endif
