<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

beforeEach(function () {
    config(['accelade.flash.enabled' => true]);
});

function makeFlashView(array $props = []): string
{
    $defaults = [
        'shared' => true,
        'slot' => new HtmlString(''),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/flash.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders flash component', function () {
    $html = makeFlashView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-flash');
});

it('includes flash data from session in state', function () {
    session()->flash('success', 'Test message');

    $html = makeFlashView();

    expect($html)
        ->toContain('data-flash-data')
        ->toContain('Test message');
});

it('renders with multiple flash messages', function () {
    session()->flash('success', 'Success message');
    session()->flash('error', 'Error message');
    session()->flash('info', 'Info message');

    $html = makeFlashView();

    expect($html)
        ->toContain('Success message')
        ->toContain('Error message')
        ->toContain('Info message');
});

it('generates unique component id', function () {
    $view1 = makeFlashView();
    $view2 = makeFlashView();

    // Extract IDs
    preg_match('/data-accelade-id="([^"]+)"/', $view1, $matches1);
    preg_match('/data-accelade-id="([^"]+)"/', $view2, $matches2);

    expect($matches1[1])->not->toBe($matches2[1]);
});

it('includes cloak attribute for FOUC prevention', function () {
    $html = makeFlashView();

    expect($html)->toContain('data-accelade-cloak');
});

it('respects shared prop', function () {
    session()->flash('message', 'Test');

    $html = makeFlashView(['shared' => false]);

    expect($html)->toContain('data-flash-no-share');
});

it('handles empty flash data gracefully', function () {
    $html = makeFlashView();

    // Should still render with empty flash data
    expect($html)
        ->toContain('data-accelade-flash')
        ->toContain('data-flash-data');
});

it('includes slot content', function () {
    $html = makeFlashView([
        'slot' => new HtmlString('<div class="test-slot">Test Content</div>'),
    ]);

    expect($html)->toContain('Test Content');
});

it('passes additional attributes to wrapper', function () {
    $html = makeFlashView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-custom-class',
            'id' => 'my-flash',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-custom-class"')
        ->toContain('id="my-flash"');
});

it('collects common flash keys from session', function () {
    // Set various common flash keys
    session()->put('message', 'Test message');
    session()->put('success', 'Success!');
    session()->put('warning', 'Warning!');
    session()->put('error', 'Error!');
    session()->put('info', 'Info!');
    session()->put('status', 'Status!');

    $html = makeFlashView();

    // Should collect common flash keys
    expect($html)
        ->toContain('Test message')
        ->toContain('Success!');
});
