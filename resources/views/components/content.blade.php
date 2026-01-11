@props([
    'html' => '',
    'as' => 'div',
])

@php
    $tag = $as;

    // Ensure the tag is a valid HTML element
    $validTags = ['div', 'span', 'article', 'section', 'aside', 'main', 'header', 'footer', 'nav', 'p', 'blockquote', 'pre', 'code', 'figure', 'figcaption', 'details', 'summary'];
    if (!in_array($tag, $validTags)) {
        $tag = 'div';
    }
@endphp

<{{ $tag }} {{ $attributes }}>
    {!! $html !!}
</{{ $tag }}>
