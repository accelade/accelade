<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeToggleView(array $props = []): string
{
    $defaults = [
        'data' => false,
        'slot' => new HtmlString('<p>Toggle Content</p>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/toggle.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic toggle component', function () {
    $html = makeToggleView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-toggle')
        ->toContain('Toggle Content');
});

it('generates unique id when not provided', function () {
    $html = makeToggleView();

    expect($html)->toMatch('/data-toggle-id="toggle-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeToggleView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-toggle']),
    ]);

    expect($html)->toContain('data-toggle-id="my-toggle"');
});

it('renders with default false state', function () {
    $html = makeToggleView(['data' => false]);

    // Debug: dump the HTML to see what's actually rendered
    // dump($html);

    expect($html)
        ->toContain('data-toggle-default="false"')
        ->toContain('&quot;toggled&quot;:false');
});

it('renders with default true state', function () {
    $html = makeToggleView(['data' => true]);

    expect($html)
        ->toContain('data-toggle-default="true"')
        ->toContain('&quot;toggled&quot;:true');
});

it('renders with string true data', function () {
    $html = makeToggleView(['data' => 'true']);

    expect($html)
        ->toContain('data-toggle-data="true"')
        ->toContain('&quot;toggled&quot;:true');
});

it('renders with string false data', function () {
    $html = makeToggleView(['data' => 'false']);

    expect($html)
        ->toContain('data-toggle-data="false"')
        ->toContain('&quot;toggled&quot;:false');
});

it('renders with single named key', function () {
    $html = makeToggleView(['data' => 'isVisible']);

    expect($html)
        ->toContain('data-toggle-data="isVisible"')
        ->toContain('&quot;isVisible&quot;:false');
});

it('renders with multiple named keys', function () {
    $html = makeToggleView(['data' => 'isCompany, hasVatNumber']);

    expect($html)
        ->toContain('data-toggle-data="isCompany, hasVatNumber"')
        ->toContain('&quot;isCompany&quot;:false')
        ->toContain('&quot;hasVatNumber&quot;:false');
});

it('renders with multiple keys and spaces', function () {
    $html = makeToggleView(['data' => 'key1,  key2,key3']);

    expect($html)
        ->toContain('&quot;key1&quot;:false')
        ->toContain('&quot;key2&quot;:false')
        ->toContain('&quot;key3&quot;:false');
});

it('renders slot content', function () {
    $html = makeToggleView([
        'slot' => new HtmlString('<div class="custom">Custom Content</div>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Content');
});

it('merges additional attributes', function () {
    $html = makeToggleView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class',
            'data-testid' => 'toggle-test',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-class"')
        ->toContain('data-testid="toggle-test"');
});

it('renders with all props combined', function () {
    $html = makeToggleView([
        'data' => 'showPanel, enableFeature',
        'attributes' => new ComponentAttributeBag(['id' => 'full-toggle', 'class' => 'toggle-wrapper']),
    ]);

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-toggle')
        ->toContain('data-toggle-id="full-toggle"')
        ->toContain('data-toggle-data="showPanel, enableFeature"')
        ->toContain('class="toggle-wrapper"')
        ->toContain('&quot;showPanel&quot;:false')
        ->toContain('&quot;enableFeature&quot;:false');
});

it('renders with reactive content in slot', function () {
    $html = makeToggleView([
        'slot' => new HtmlString('<div a-show="toggled">Visible when toggled</div>'),
    ]);

    expect($html)
        ->toContain('a-show="toggled"')
        ->toContain('Visible when toggled');
});

it('handles array data with boolean values', function () {
    $html = makeToggleView([
        'data' => ['isOpen' => true, 'isActive' => false],
    ]);

    expect($html)
        ->toContain('&quot;isOpen&quot;:true')
        ->toContain('&quot;isActive&quot;:false');
});

it('renders toggle with event handlers in slot', function () {
    $html = makeToggleView([
        'slot' => new HtmlString('<button @click.prevent="toggle()">Toggle</button>'),
    ]);

    expect($html)
        ->toContain('@click.prevent="toggle()"')
        ->toContain('Toggle');
});

it('renders multi-toggle with keyed event handlers', function () {
    $html = makeToggleView([
        'data' => 'panel1, panel2',
        'slot' => new HtmlString('<button @click.prevent="toggle(\'panel1\')">Toggle Panel 1</button>'),
    ]);

    expect($html)
        ->toContain('@click.prevent="toggle(\'panel1\')"')
        ->toContain('Toggle Panel 1');
});

it('renders setToggle usage in slot', function () {
    $html = makeToggleView([
        'slot' => new HtmlString('<button @click.prevent="setToggle(true)">Show</button>'),
    ]);

    expect($html)
        ->toContain('@click.prevent="setToggle(true)"')
        ->toContain('Show');
});

it('handles empty string data', function () {
    $html = makeToggleView(['data' => '']);

    expect($html)
        ->toContain('data-accelade-toggle')
        ->toContain('&quot;toggled&quot;:false');
});
