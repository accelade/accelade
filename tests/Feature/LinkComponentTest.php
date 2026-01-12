<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeLinkView(array $props = []): string
{
    $defaults = [
        'href' => '/test',
        'method' => 'GET',
        'data' => null,
        'headers' => null,
        'spa' => true,
        'away' => false,
        'activeClass' => 'active',
        'prefetch' => false,
        'preserveScroll' => false,
        'preserveState' => false,
        'replace' => false,
        'confirm' => null,
        'confirmText' => null,
        'confirmTitle' => null,
        'confirmButton' => null,
        'cancelButton' => null,
        'confirmDanger' => false,
        'modal' => false,
        'slideover' => false,
        'bottomSheet' => false,
        'modalMaxWidth' => null,
        'modalPosition' => null,
        'slideoverPosition' => null,
        'slot' => new HtmlString('Test Link'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/link.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic link', function () {
    $html = makeLinkView();

    expect($html)
        ->toContain('href="/test"')
        ->toContain('Test Link')
        ->toContain('a-link');
});

it('renders with SPA attributes for vanilla framework', function () {
    config(['accelade.framework' => 'vanilla']);

    $html = makeLinkView();

    expect($html)->toContain('a-link');
});

it('renders with SPA attributes for vue framework', function () {
    config(['accelade.framework' => 'vue']);

    $html = makeLinkView();

    expect($html)->toContain('data-accelade-link');
});

it('renders with SPA attributes for react framework', function () {
    config(['accelade.framework' => 'react']);

    $html = makeLinkView();

    expect($html)->toContain('data-spa-link');
});

it('includes HTTP method attribute for non-GET methods', function () {
    $html = makeLinkView(['method' => 'POST']);

    expect($html)->toContain('data-method="POST"');
});

it('includes data attribute when data is provided', function () {
    $html = makeLinkView(['data' => ['name' => 'test', 'id' => 123]]);

    expect($html)
        ->toContain('data-data=')
        ->toContain('name')
        ->toContain('test');
});

it('includes headers attribute when headers are provided', function () {
    $html = makeLinkView(['headers' => ['X-Custom' => 'value']]);

    expect($html)
        ->toContain('data-headers=')
        ->toContain('X-Custom');
});

it('includes prefetch attribute when enabled', function () {
    $html = makeLinkView(['prefetch' => true]);

    expect($html)->toContain('data-prefetch');
});

it('includes preserve-scroll attribute when enabled', function () {
    $html = makeLinkView(['preserveScroll' => true]);

    expect($html)->toContain('data-preserve-scroll');
});

it('includes preserve-state attribute when enabled', function () {
    $html = makeLinkView(['preserveState' => true]);

    expect($html)->toContain('data-preserve-state');
});

it('includes replace attribute when enabled', function () {
    $html = makeLinkView(['replace' => true]);

    expect($html)->toContain('data-replace');
});

it('includes confirm attribute when confirm is true', function () {
    $html = makeLinkView(['confirm' => true]);

    expect($html)->toContain('data-confirm=');
});

it('includes confirm attribute with custom text', function () {
    $html = makeLinkView(['confirmText' => 'Are you sure?']);

    expect($html)->toContain('data-confirm="Are you sure?"');
});

it('includes confirm-title attribute when provided', function () {
    $html = makeLinkView([
        'confirm' => true,
        'confirmTitle' => 'Confirm Action',
    ]);

    expect($html)->toContain('data-confirm-title="Confirm Action"');
});

it('includes confirm-button attribute when provided', function () {
    $html = makeLinkView([
        'confirm' => true,
        'confirmButton' => 'Yes, do it',
    ]);

    expect($html)->toContain('data-confirm-button="Yes, do it"');
});

it('includes cancel-button attribute when provided', function () {
    $html = makeLinkView([
        'confirm' => true,
        'cancelButton' => 'No, cancel',
    ]);

    expect($html)->toContain('data-cancel-button="No, cancel"');
});

it('includes confirm-danger attribute when enabled', function () {
    $html = makeLinkView([
        'confirm' => true,
        'confirmDanger' => true,
    ]);

    expect($html)->toContain('data-confirm-danger');
});

it('includes away attribute for external links', function () {
    $html = makeLinkView(['away' => true]);

    expect($html)->toContain('data-away');
});

it('adds active class when on current URL', function () {
    // Mock the request to be on /test
    request()->merge(['_url' => '/test']);

    $html = makeLinkView(['href' => request()->url()]);

    expect($html)->toContain('active');
});

it('renders without SPA attributes when spa is false', function () {
    $html = makeLinkView(['spa' => false]);

    expect($html)
        ->not->toContain('a-link')
        ->not->toContain('data-accelade-link')
        ->not->toContain('data-spa-link');
});

it('merges additional attributes', function () {
    $html = makeLinkView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'custom-class',
            'id' => 'my-link',
        ]),
    ]);

    expect($html)
        ->toContain('class="custom-class')
        ->toContain('id="my-link"');
});

it('renders slot content', function () {
    $html = makeLinkView([
        'slot' => new HtmlString('Click Here'),
    ]);

    expect($html)->toContain('Click Here');
});

it('renders DELETE method link', function () {
    $html = makeLinkView([
        'method' => 'DELETE',
        'confirmDanger' => true,
        'confirmText' => 'Delete this item?',
    ]);

    expect($html)
        ->toContain('data-method="DELETE"')
        ->toContain('data-confirm="Delete this item?"')
        ->toContain('data-confirm-danger');
});

it('renders PUT method link', function () {
    $html = makeLinkView(['method' => 'PUT']);

    expect($html)->toContain('data-method="PUT"');
});

it('renders PATCH method link', function () {
    $html = makeLinkView(['method' => 'PATCH']);

    expect($html)->toContain('data-method="PATCH"');
});

it('renders modal link with data-modal attribute', function () {
    $html = makeLinkView(['modal' => true]);

    expect($html)->toContain('data-modal');
});

it('renders slideover link with data-slideover attribute', function () {
    $html = makeLinkView(['slideover' => true]);

    expect($html)->toContain('data-slideover');
});

it('renders modal link with custom max-width', function () {
    $html = makeLinkView(['modal' => true, 'modalMaxWidth' => '4xl']);

    expect($html)
        ->toContain('data-modal')
        ->toContain('data-modal-max-width="4xl"');
});

it('renders modal link with custom position', function () {
    $html = makeLinkView(['modal' => true, 'modalPosition' => 'top']);

    expect($html)
        ->toContain('data-modal')
        ->toContain('data-modal-position="top"');
});

it('renders slideover link with custom position', function () {
    $html = makeLinkView(['slideover' => true, 'slideoverPosition' => 'left']);

    expect($html)
        ->toContain('data-slideover')
        ->toContain('data-slideover-position="left"');
});

it('modal link does not have SPA attributes', function () {
    $html = makeLinkView(['modal' => true]);

    expect($html)
        ->toContain('data-modal')
        ->not->toContain('a-link')
        ->not->toContain('data-accelade-link')
        ->not->toContain('data-spa-link');
});

it('slideover link does not have SPA attributes', function () {
    $html = makeLinkView(['slideover' => true]);

    expect($html)
        ->toContain('data-slideover')
        ->not->toContain('a-link')
        ->not->toContain('data-accelade-link')
        ->not->toContain('data-spa-link');
});

it('renders bottom sheet link with data-bottom-sheet attribute', function () {
    $html = makeLinkView(['bottomSheet' => true]);

    expect($html)->toContain('data-bottom-sheet');
});

it('bottom sheet link does not have SPA attributes', function () {
    $html = makeLinkView(['bottomSheet' => true]);

    expect($html)
        ->toContain('data-bottom-sheet')
        ->not->toContain('a-link')
        ->not->toContain('data-accelade-link')
        ->not->toContain('data-spa-link');
});

it('renders bottom sheet link with custom max-width', function () {
    $html = makeLinkView(['bottomSheet' => true, 'modalMaxWidth' => 'lg']);

    expect($html)
        ->toContain('data-bottom-sheet')
        ->toContain('data-modal-max-width="lg"');
});

it('bottom sheet link does not have modal or slideover attributes', function () {
    $html = makeLinkView(['bottomSheet' => true]);

    expect($html)
        ->toContain('data-bottom-sheet')
        ->not->toContain('data-modal')
        ->not->toContain('data-slideover');
});
