<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeRehydrateView(array $props = []): string
{
    $defaults = [
        'on' => null,
        'poll' => null,
        'url' => null,
        'preserveScroll' => true,
        'slot' => new HtmlString('<p>Rehydrate Content</p>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/rehydrate.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic rehydrate component', function () {
    $html = makeRehydrateView();

    expect($html)
        ->toContain('data-accelade-rehydrate')
        ->toContain('Rehydrate Content');
});

it('renders with single event listener', function () {
    $html = makeRehydrateView(['on' => 'item-created']);

    expect($html)->toContain('data-rehydrate-on="item-created"');
});

it('renders with multiple event listeners as array', function () {
    $html = makeRehydrateView(['on' => ['item-created', 'item-updated', 'item-deleted']]);

    expect($html)
        ->toContain('data-rehydrate-on=')
        ->toContain('item-created')
        ->toContain('item-updated')
        ->toContain('item-deleted');
});

it('renders with poll interval', function () {
    $html = makeRehydrateView(['poll' => 5000]);

    expect($html)->toContain('data-rehydrate-poll="5000"');
});

it('renders without poll attribute when poll is null', function () {
    $html = makeRehydrateView(['poll' => null]);

    expect($html)->not->toContain('data-rehydrate-poll');
});

it('renders with custom URL', function () {
    $html = makeRehydrateView(['url' => '/api/data']);

    expect($html)->toContain('data-rehydrate-url="/api/data"');
});

it('renders without url attribute when url is null', function () {
    $html = makeRehydrateView(['url' => null]);

    expect($html)->not->toContain('data-rehydrate-url');
});

it('renders with preserve scroll attribute', function () {
    $html = makeRehydrateView(['preserveScroll' => true]);

    expect($html)->toContain('data-rehydrate-preserve-scroll');
});

it('renders without preserve scroll attribute when false', function () {
    $html = makeRehydrateView(['preserveScroll' => false]);

    expect($html)->not->toContain('data-rehydrate-preserve-scroll');
});

it('generates unique id when not provided', function () {
    $html = makeRehydrateView();

    expect($html)->toMatch('/data-rehydrate-id="rehydrate-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeRehydrateView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-rehydrate']),
    ]);

    expect($html)->toContain('data-rehydrate-id="my-rehydrate"');
});

it('renders slot content', function () {
    $html = makeRehydrateView([
        'slot' => new HtmlString('<div class="custom">Custom Content</div>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Content');
});

it('merges additional attributes', function () {
    $html = makeRehydrateView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class',
            'data-testid' => 'rehydrate-test',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-class"')
        ->toContain('data-testid="rehydrate-test"');
});

it('renders with both event and poll', function () {
    $html = makeRehydrateView([
        'on' => 'data-updated',
        'poll' => 10000,
    ]);

    expect($html)
        ->toContain('data-rehydrate-on="data-updated"')
        ->toContain('data-rehydrate-poll="10000"');
});

it('renders with all props combined', function () {
    $html = makeRehydrateView([
        'on' => ['created', 'updated'],
        'poll' => 3000,
        'url' => '/refresh',
        'preserveScroll' => true,
        'attributes' => new ComponentAttributeBag(['id' => 'full-rehydrate']),
    ]);

    expect($html)
        ->toContain('data-accelade-rehydrate')
        ->toContain('data-rehydrate-id="full-rehydrate"')
        ->toContain('data-rehydrate-on=')
        ->toContain('data-rehydrate-poll="3000"')
        ->toContain('data-rehydrate-url="/refresh"')
        ->toContain('data-rehydrate-preserve-scroll');
});
