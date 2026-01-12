<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeModalView(array $props = []): string
{
    $defaults = [
        'name' => null,
        'maxWidth' => null,
        'position' => null,
        'slideoverPosition' => null,
        'closeExplicitly' => false,
        'closeButton' => true,
        'opened' => false,
        'slideover' => false,
        'bottomSheet' => false,
        'slot' => new HtmlString('<p>Modal Content</p>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/modal.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic modal', function () {
    $html = makeModalView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-modal')
        ->toContain('Modal Content');
});

it('renders with name attribute', function () {
    $html = makeModalView(['name' => 'test-modal']);

    expect($html)->toContain('data-modal-name="test-modal"');
});

it('renders as slideover when slideover is true', function () {
    $html = makeModalView(['slideover' => true]);

    expect($html)->toContain('data-slideover');
});

it('renders with custom max-width', function () {
    $html = makeModalView(['maxWidth' => '4xl']);

    expect($html)->toContain('data-max-width="4xl"');
});

it('renders with default max-width 2xl for modal', function () {
    $html = makeModalView();

    expect($html)->toContain('data-max-width="2xl"');
});

it('renders with default max-width md for slideover', function () {
    $html = makeModalView(['slideover' => true]);

    expect($html)->toContain('data-max-width="md"');
});

it('renders with custom position', function () {
    $html = makeModalView(['position' => 'top']);

    expect($html)->toContain('data-position="top"');
});

it('renders with default position center', function () {
    $html = makeModalView();

    expect($html)->toContain('data-position="center"');
});

it('renders with slideover position right by default', function () {
    $html = makeModalView(['slideover' => true]);

    expect($html)->toContain('data-slideover-position="right"');
});

it('renders with slideover position left', function () {
    $html = makeModalView(['slideover' => true, 'slideoverPosition' => 'left']);

    expect($html)->toContain('data-slideover-position="left"');
});

it('renders with close-explicitly attribute', function () {
    $html = makeModalView(['closeExplicitly' => true]);

    expect($html)->toContain('data-close-explicitly');
});

it('does not render close-explicitly when false', function () {
    $html = makeModalView(['closeExplicitly' => false]);

    expect($html)->not->toContain('data-close-explicitly');
});

it('renders without close button when closeButton is false', function () {
    $html = makeModalView(['closeButton' => false]);

    expect($html)->toContain('data-no-close-button');
});

it('does not have no-close-button when closeButton is true', function () {
    $html = makeModalView(['closeButton' => true]);

    expect($html)->not->toContain('data-no-close-button');
});

it('renders with opened attribute', function () {
    $html = makeModalView(['opened' => true]);

    expect($html)->toContain('data-opened');
});

it('does not render opened attribute when false', function () {
    $html = makeModalView(['opened' => false]);

    expect($html)->not->toContain('data-opened');
});

it('renders with initial state', function () {
    $html = makeModalView();

    expect($html)->toContain('data-accelade-state=');
});

it('renders slot content', function () {
    $html = makeModalView([
        'slot' => new HtmlString('<h1>Custom Title</h1><p>Custom content</p>'),
    ]);

    expect($html)
        ->toContain('Custom Title')
        ->toContain('Custom content');
});

it('has display none style by default', function () {
    $html = makeModalView();

    expect($html)->toContain('display: none');
});

it('merges additional attributes', function () {
    $html = makeModalView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'custom-class',
            'id' => 'my-modal',
        ]),
    ]);

    expect($html)
        ->toContain('class="custom-class')
        ->toContain('id="my-modal"');
});

it('generates unique id when not provided', function () {
    $html = makeModalView();

    expect($html)->toMatch('/data-modal-id="modal-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeModalView([
        'attributes' => new ComponentAttributeBag(['id' => 'specific-modal']),
    ]);

    expect($html)->toContain('data-modal-id="specific-modal"');
});

it('renders all max-width options', function () {
    $sizes = ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'];

    foreach ($sizes as $size) {
        $html = makeModalView(['maxWidth' => $size]);
        expect($html)->toContain("data-max-width=\"{$size}\"");
    }
});

it('renders all position options for modal', function () {
    $positions = ['top', 'center', 'bottom'];

    foreach ($positions as $position) {
        $html = makeModalView(['position' => $position]);
        expect($html)->toContain("data-position=\"{$position}\"");
    }
});

it('renders all slideover position options', function () {
    $positions = ['left', 'right'];

    foreach ($positions as $position) {
        $html = makeModalView(['slideover' => true, 'slideoverPosition' => $position]);
        expect($html)->toContain("data-slideover-position=\"{$position}\"");
    }
});

it('renders as bottom sheet when bottomSheet is true', function () {
    $html = makeModalView(['bottomSheet' => true]);

    expect($html)->toContain('data-bottom-sheet');
});

it('does not render bottom-sheet attribute when false', function () {
    $html = makeModalView(['bottomSheet' => false]);

    expect($html)->not->toContain('data-bottom-sheet');
});

it('renders with default max-width 2xl for bottom sheet', function () {
    $html = makeModalView(['bottomSheet' => true]);

    expect($html)->toContain('data-max-width="2xl"');
});

it('renders bottom sheet with custom max-width', function () {
    $html = makeModalView(['bottomSheet' => true, 'maxWidth' => 'lg']);

    expect($html)->toContain('data-max-width="lg"');
});

it('renders bottom sheet without slideover attribute', function () {
    $html = makeModalView(['bottomSheet' => true]);

    // Check that data-slideover is not present (but data-slideover-position is ok)
    expect($html)
        ->toContain('data-bottom-sheet')
        ->not->toMatch('/\sdata-slideover[^-]/');
});
