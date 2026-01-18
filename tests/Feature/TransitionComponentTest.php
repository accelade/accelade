<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

beforeEach(function () {
    // Reset animation manager between tests
    app()->forgetInstance('accelade.animation');
});

it('renders basic transition component', function () {
    $view = View::make('accelade::components.transition', [
        'slot' => 'Hello World',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-accelade-transition')
        ->toContain('data-transition-show="true"')
        ->toContain('Hello World');
});

it('renders with show expression', function () {
    $view = View::make('accelade::components.transition', [
        'show' => 'toggled',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-show="toggled"');
});

it('renders with animation preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'fade',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter="transition-opacity ease-out duration-150"')
        ->toContain('data-transition-enter-from="opacity-0"')
        ->toContain('data-transition-enter-to="opacity-100"')
        ->toContain('data-transition-leave="transition-opacity ease-in duration-100"')
        ->toContain('data-transition-leave-from="opacity-100"')
        ->toContain('data-transition-leave-to="opacity-0"');
});

it('renders with slide-left preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'slide-left',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter="transition ease-out duration-150"')
        ->toContain('data-transition-enter-from="opacity-0 -translate-x-full"')
        ->toContain('data-transition-enter-to="opacity-100 translate-x-0"');
});

it('renders with slide-right preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'slide-right',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter-from="opacity-0 translate-x-full"');
});

it('renders with custom classes', function () {
    $view = View::make('accelade::components.transition', [
        'enter' => 'custom-enter',
        'enterFrom' => 'custom-enter-from',
        'enterTo' => 'custom-enter-to',
        'leave' => 'custom-leave',
        'leaveFrom' => 'custom-leave-from',
        'leaveTo' => 'custom-leave-to',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter="custom-enter"')
        ->toContain('data-transition-enter-from="custom-enter-from"')
        ->toContain('data-transition-enter-to="custom-enter-to"')
        ->toContain('data-transition-leave="custom-leave"')
        ->toContain('data-transition-leave-from="custom-leave-from"')
        ->toContain('data-transition-leave-to="custom-leave-to"');
});

it('custom classes override preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'fade',
        'enter' => 'custom-enter-override',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter="custom-enter-override"')
        ->toContain('data-transition-enter-from="opacity-0"'); // Still from preset
});

it('generates unique id when not provided', function () {
    $view = View::make('accelade::components.transition', [
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)->toContain('data-transition-id="transition-');
});

it('uses provided id attribute', function () {
    $view = View::make('accelade::components.transition', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag(['id' => 'my-transition']),
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)->toContain('data-transition-id="my-transition"');
});

it('renders slot content', function () {
    $view = View::make('accelade::components.transition', [
        'slot' => new HtmlString('<div class="nested">Nested content</div>'),
    ]);

    $html = $view->render();

    expect($html)->toContain('<div class="nested">Nested content</div>');
});

it('merges additional attributes', function () {
    $view = View::make('accelade::components.transition', [
        'attributes' => new \Illuminate\View\ComponentAttributeBag([
            'class' => 'my-class',
            'data-custom' => 'value',
        ]),
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('class="my-class"')
        ->toContain('data-custom="value"');
});

it('renders with default preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'default',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter="transition ease-out duration-150"')
        ->toContain('data-transition-enter-from="opacity-0 scale-95"')
        ->toContain('data-transition-enter-to="opacity-100 scale-100"');
});

it('renders with scale preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'scale',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    expect($html)
        ->toContain('data-transition-enter-from="opacity-0 scale-0"')
        ->toContain('data-transition-enter-to="opacity-100 scale-100"');
});

it('falls back to defaults for unknown preset', function () {
    $view = View::make('accelade::components.transition', [
        'animation' => 'nonexistent-preset',
        'slot' => 'Content',
    ]);

    $html = $view->render();

    // Should use default values since preset doesn't exist
    expect($html)
        ->toContain('data-transition-enter="transition ease-out duration-200"')
        ->toContain('data-transition-enter-from="opacity-0"');
});
