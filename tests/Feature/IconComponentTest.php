<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentAttributeBag;

function makeIconView(array $props = []): string
{
    $defaults = [
        'name' => null,
        'size' => 'base',
        'fallback' => null,
        'showFallback' => true,
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/icon.blade.php',
        array_merge($defaults, $props)
    )->render();
}

describe('Icon Component', function () {
    it('renders an icon with valid heroicon name', function () {
        $html = makeIconView(['name' => 'heroicon-o-home']);

        expect($html)->toContain('<svg');
        expect($html)->toContain('</svg>');
    });

    it('renders nothing when name is null and showFallback is false', function () {
        $html = makeIconView(['name' => null, 'showFallback' => false]);

        expect(trim($html))->toBe('');
    });

    it('renders fallback when name is null and showFallback is true', function () {
        $html = makeIconView(['name' => null, 'showFallback' => true]);

        expect($html)->toContain('<svg');
    });

    it('uses base size by default', function () {
        $html = makeIconView(['name' => 'heroicon-o-home']);

        expect($html)->toContain('w-6 h-6');
    });

    it('applies xs size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'xs']);

        expect($html)->toContain('w-3 h-3');
    });

    it('applies sm size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'sm']);

        expect($html)->toContain('w-4 h-4');
    });

    it('applies md size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'md']);

        expect($html)->toContain('w-5 h-5');
    });

    it('applies lg size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'lg']);

        expect($html)->toContain('w-7 h-7');
    });

    it('applies xl size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'xl']);

        expect($html)->toContain('w-8 h-8');
    });

    it('applies 2xl size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => '2xl']);

        expect($html)->toContain('w-10 h-10');
    });

    it('applies 3xl size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => '3xl']);

        expect($html)->toContain('w-12 h-12');
    });

    it('applies 4xl size class', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => '4xl']);

        expect($html)->toContain('w-16 h-16');
    });

    it('falls back to base size for invalid size', function () {
        $html = makeIconView(['name' => 'heroicon-o-home', 'size' => 'invalid']);

        expect($html)->toContain('w-6 h-6');
    });

    it('passes additional classes to the icon', function () {
        $html = makeIconView([
            'name' => 'heroicon-o-home',
            'attributes' => new ComponentAttributeBag(['class' => 'text-red-500']),
        ]);

        expect($html)->toContain('text-red-500');
    });

    it('renders outline heroicons', function () {
        $html = makeIconView(['name' => 'heroicon-o-star']);

        expect($html)->toContain('<svg');
    });

    it('renders solid heroicons', function () {
        $html = makeIconView(['name' => 'heroicon-s-star']);

        expect($html)->toContain('<svg');
    });

    it('renders mini heroicons', function () {
        $html = makeIconView(['name' => 'heroicon-m-star']);

        expect($html)->toContain('<svg');
    });

    it('uses fallback icon when main icon not found', function () {
        $html = makeIconView(['name' => 'nonexistent-icon', 'fallback' => 'heroicon-o-photo']);

        expect($html)->toContain('<svg');
    });

    it('uses default fallback SVG when icon not found and no custom fallback', function () {
        $html = makeIconView(['name' => 'nonexistent-icon']);

        expect($html)->toContain('<svg');
        expect($html)->toContain('circle');
    });

    it('shows nothing when icon not found and showFallback is false', function () {
        $html = makeIconView(['name' => 'nonexistent-icon', 'showFallback' => false]);

        expect(trim($html))->toBe('');
    });

    it('combines size and custom classes', function () {
        $html = makeIconView([
            'name' => 'heroicon-o-home',
            'size' => 'lg',
            'attributes' => new ComponentAttributeBag(['class' => 'text-blue-600 hover:text-blue-800']),
        ]);

        expect($html)->toContain('w-7 h-7');
        expect($html)->toContain('text-blue-600');
        expect($html)->toContain('hover:text-blue-800');
    });
});

describe('Icon Component with different icons', function () {
    it('renders home icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-home']);

        expect($html)->toContain('<svg');
    });

    it('renders user icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-user']);

        expect($html)->toContain('<svg');
    });

    it('renders cog icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-cog-6-tooth']);

        expect($html)->toContain('<svg');
    });

    it('renders bell icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-bell']);

        expect($html)->toContain('<svg');
    });

    it('renders check icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-check']);

        expect($html)->toContain('<svg');
    });

    it('renders x-mark icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-x-mark']);

        expect($html)->toContain('<svg');
    });

    it('renders plus icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-plus']);

        expect($html)->toContain('<svg');
    });

    it('renders minus icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-minus']);

        expect($html)->toContain('<svg');
    });

    it('renders magnifying glass icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-magnifying-glass']);

        expect($html)->toContain('<svg');
    });

    it('renders heart icon', function () {
        $html = makeIconView(['name' => 'heroicon-o-heart']);

        expect($html)->toContain('<svg');
    });
});
