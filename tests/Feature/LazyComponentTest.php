<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

test('lazy component renders wrapper with data attributes', function () {
    $html = Blade::render('<x-accelade::lazy>Content</x-accelade::lazy>');

    expect($html)->toContain('data-accelade-lazy');
    expect($html)->toContain('data-lazy-id="lazy-');
    expect($html)->toContain('data-lazy-show="true"');
    expect($html)->toContain('data-lazy-mode="inline"');
});

test('lazy component renders placeholder by default', function () {
    $html = Blade::render('<x-accelade::lazy>Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-placeholder');
    expect($html)->toContain('accelade-lazy-spinner');
});

test('lazy component renders shimmer when enabled', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true">Content</x-accelade::lazy>');

    expect($html)->toContain('accelade-shimmer-container');
    expect($html)->toContain('accelade-shimmer-line');
});

test('lazy component renders custom shimmer lines', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" :shimmer-lines="5">Content</x-accelade::lazy>');

    // Count shimmer line divs (class contains "accelade-shimmer-line" but short line also matches)
    // We count the opening divs with accelade-shimmer-line class
    preg_match_all('/<div class="accelade-shimmer-line/', $html, $matches);
    expect(count($matches[0]))->toBe(5);
});

test('lazy component renders shimmer with custom height', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" shimmer-height="200px">Content</x-accelade::lazy>');

    expect($html)->toContain('height: 200px');
});

test('lazy component renders shimmer with custom width', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" shimmer-width="50%">Content</x-accelade::lazy>');

    expect($html)->toContain('width: 50%');
});

test('lazy component renders rounded shimmer', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" :shimmer-rounded="true">Content</x-accelade::lazy>');

    expect($html)->toContain('accelade-shimmer-rounded');
});

test('lazy component renders circle shimmer', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" :shimmer-circle="true">Content</x-accelade::lazy>');

    expect($html)->toContain('accelade-shimmer-circle');
    expect($html)->toContain('accelade-shimmer-circle-inner');
});

test('lazy component renders custom placeholder', function () {
    $html = Blade::render('
        <x-accelade::lazy>
            <x-slot:placeholder>
                <div class="custom-loading">Loading...</div>
            </x-slot:placeholder>
            Content
        </x-accelade::lazy>
    ');

    expect($html)->toContain('custom-loading');
    expect($html)->toContain('Loading...');
});

test('lazy component includes content in inline mode', function () {
    $html = Blade::render('<x-accelade::lazy>Hello World</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-content');
    expect($html)->toContain('Hello World');
});

test('lazy component renders with url mode', function () {
    $html = Blade::render('<x-accelade::lazy url="/api/content">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-url="/api/content"');
    expect($html)->toContain('data-lazy-mode="url"');
});

test('lazy component does not include slot content in url mode', function () {
    $html = Blade::render('<x-accelade::lazy url="/api/content">Secret Content</x-accelade::lazy>');

    expect($html)->not->toContain('Secret Content');
});

test('lazy component renders with delay', function () {
    $html = Blade::render('<x-accelade::lazy :delay="500">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-delay="500"');
});

test('lazy component renders with conditional show as string', function () {
    $html = Blade::render('<x-accelade::lazy show="isVisible">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-show="isVisible"');
    expect($html)->toContain('data-lazy-conditional="true"');
});

test('lazy component renders with name attribute', function () {
    $html = Blade::render('<x-accelade::lazy name="my-section">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-name="my-section"');
});

test('lazy component renders with POST method', function () {
    $html = Blade::render('<x-accelade::lazy url="/api/content" method="POST">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-method="POST"');
});

test('lazy component renders with data attribute', function () {
    $html = Blade::render('<x-accelade::lazy url="/api/content" :data="[\'key\' => \'value\']">Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-data=');
    expect($html)->toContain('key');
    expect($html)->toContain('value');
});

test('lazy component renders hidden content container', function () {
    $html = Blade::render('<x-accelade::lazy>Content</x-accelade::lazy>');

    expect($html)->toContain('data-lazy-content');
    expect($html)->toContain('style="display: none;"');
});

test('lazy component merges custom classes', function () {
    $html = Blade::render('<x-accelade::lazy class="my-custom-class">Content</x-accelade::lazy>');

    expect($html)->toContain('accelade-lazy-wrapper');
    expect($html)->toContain('my-custom-class');
});

test('lazy component renders short last shimmer line', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" :shimmer-lines="3">Content</x-accelade::lazy>');

    expect($html)->toContain('accelade-shimmer-line-short');
});

test('lazy component with single shimmer line has no short line', function () {
    $html = Blade::render('<x-accelade::lazy :shimmer="true" :shimmer-lines="1">Content</x-accelade::lazy>');

    expect($html)->not->toContain('accelade-shimmer-line-short');
});
