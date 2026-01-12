<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeTeleportView(array $props = []): string
{
    $defaults = [
        'to' => null,
        'disabled' => false,
        'slot' => new HtmlString('<p>Teleport Content</p>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/teleport.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic teleport component', function () {
    $html = makeTeleportView(['to' => '#footer']);

    expect($html)
        ->toContain('data-accelade-teleport')
        ->toContain('data-teleport-to="#footer"')
        ->toContain('Teleport Content');
});

it('renders with target selector', function () {
    $html = makeTeleportView(['to' => '.modal-container']);

    expect($html)->toContain('data-teleport-to=".modal-container"');
});

it('renders with complex CSS selector', function () {
    $html = makeTeleportView(['to' => '#app > .notifications']);

    expect($html)->toContain('data-teleport-to="#app &gt; .notifications"');
});

it('renders without target when to is null', function () {
    $html = makeTeleportView(['to' => null]);

    expect($html)
        ->toContain('data-accelade-teleport')
        ->not->toContain('data-teleport-to');
});

it('renders with disabled attribute', function () {
    $html = makeTeleportView(['to' => '#target', 'disabled' => true]);

    expect($html)->toContain('data-teleport-disabled="true"');
});

it('renders without disabled attribute when false', function () {
    $html = makeTeleportView(['to' => '#target', 'disabled' => false]);

    expect($html)->not->toContain('data-teleport-disabled');
});

it('generates unique id when not provided', function () {
    $html = makeTeleportView(['to' => '#target']);

    expect($html)->toMatch('/data-teleport-id="teleport-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeTeleportView([
        'to' => '#target',
        'attributes' => new ComponentAttributeBag(['id' => 'my-teleport']),
    ]);

    expect($html)->toContain('data-teleport-id="my-teleport"');
});

it('renders slot content', function () {
    $html = makeTeleportView([
        'to' => '#target',
        'slot' => new HtmlString('<div class="custom">Custom Content</div>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Content');
});

it('merges additional attributes', function () {
    $html = makeTeleportView([
        'to' => '#target',
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class',
            'data-testid' => 'teleport-test',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-class"')
        ->toContain('data-testid="teleport-test"');
});

it('renders with all props combined', function () {
    $html = makeTeleportView([
        'to' => '#my-target',
        'disabled' => true,
        'attributes' => new ComponentAttributeBag(['id' => 'full-teleport', 'class' => 'teleport-wrapper']),
    ]);

    expect($html)
        ->toContain('data-accelade-teleport')
        ->toContain('data-teleport-id="full-teleport"')
        ->toContain('data-teleport-to="#my-target"')
        ->toContain('data-teleport-disabled="true"')
        ->toContain('class="teleport-wrapper"');
});

it('renders with reactive content', function () {
    $html = makeTeleportView([
        'to' => '#target',
        'slot' => new HtmlString('<span a-text="dynamicValue">Default</span>'),
    ]);

    expect($html)
        ->toContain('a-text="dynamicValue"')
        ->toContain('Default');
});

it('handles special characters in target selector', function () {
    $html = makeTeleportView(['to' => '#target-123']);

    expect($html)->toContain('data-teleport-to="#target-123"');
});

it('handles ID selector', function () {
    $html = makeTeleportView(['to' => '#notification-area']);

    expect($html)->toContain('data-teleport-to="#notification-area"');
});

it('handles class selector', function () {
    $html = makeTeleportView(['to' => '.modal-body']);

    expect($html)->toContain('data-teleport-to=".modal-body"');
});

it('handles multiple class selector', function () {
    $html = makeTeleportView(['to' => '.container.main']);

    expect($html)->toContain('data-teleport-to=".container.main"');
});
