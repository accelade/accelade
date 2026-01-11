@props([
    'url' => null,
    'show' => true,
    'shimmer' => false,
    'shimmerLines' => 3,
    'shimmerHeight' => null,
    'shimmerWidth' => null,
    'shimmerRounded' => false,
    'shimmerCircle' => false,
    'delay' => 0,
    'name' => null,
    'method' => 'GET',
    'data' => [],
])

@php
    $lazyId = 'lazy-' . \Illuminate\Support\Str::random(8);
    $showValue = is_bool($show) ? ($show ? 'true' : 'false') : $show;
    $isConditional = !is_bool($show);
    $hasUrl = !empty($url);

    // Shimmer styles
    $shimmerStyles = collect([
        $shimmerHeight ? "height: {$shimmerHeight}" : null,
        $shimmerWidth ? "width: {$shimmerWidth}" : null,
    ])->filter()->implode('; ');

    // Shimmer classes
    $shimmerClasses = collect([
        'accelade-shimmer-container',
        $shimmerRounded ? 'accelade-shimmer-rounded' : null,
        $shimmerCircle ? 'accelade-shimmer-circle' : null,
    ])->filter()->implode(' ');
@endphp

<div
    data-accelade-lazy
    data-lazy-id="{{ $lazyId }}"
    @if($hasUrl) data-lazy-url="{{ $url }}" @endif
    data-lazy-show="{{ $showValue }}"
    data-lazy-conditional="{{ $isConditional ? 'true' : 'false' }}"
    data-lazy-delay="{{ $delay }}"
    data-lazy-method="{{ strtoupper($method) }}"
    @if(!empty($data)) data-lazy-data="{{ json_encode($data) }}" @endif
    @if($name) data-lazy-name="{{ $name }}" @endif
    @if($hasUrl) data-lazy-mode="url" @else data-lazy-mode="inline" @endif
    {{ $attributes->class(['accelade-lazy-wrapper']) }}
>
    {{-- Placeholder content --}}
    <div class="accelade-lazy-placeholder" data-lazy-placeholder>
        @if(isset($placeholder))
            {{ $placeholder }}
        @elseif($shimmer)
            <div class="{{ $shimmerClasses }}" @if($shimmerStyles) style="{{ $shimmerStyles }}" @endif>
                @if($shimmerCircle)
                    <div class="accelade-shimmer-circle-inner"></div>
                @else
                    @for($i = 0; $i < $shimmerLines; $i++)
                        <div class="accelade-shimmer-line @if($i === $shimmerLines - 1 && $shimmerLines > 1) accelade-shimmer-line-short @endif"></div>
                    @endfor
                @endif
            </div>
        @else
            <div class="accelade-lazy-loading">
                <div class="accelade-lazy-spinner"></div>
            </div>
        @endif
    </div>

    {{-- Content container --}}
    <div class="accelade-lazy-content" data-lazy-content style="display: none;">
        @unless($hasUrl)
            {{-- Inline mode: content is already here but hidden --}}
            {{ $slot }}
        @endunless
    </div>
</div>
