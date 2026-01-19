<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeTooltipView(array $props = []): string
{
    $defaults = [
        'text' => 'Test tooltip',
        'position' => 'top',
        'trigger' => 'hover',
        'delay' => 0,
        'hideDelay' => 0,
        'arrow' => true,
        'animation' => 'fade',
        'interactive' => false,
        'offset' => 8,
        'maxWidth' => null,
        'slot' => new HtmlString('<button>Hover me</button>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/tooltip.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic tooltip component', function () {
    $html = makeTooltipView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-tooltip')
        ->toContain('Hover me');
});

it('generates unique id when not provided', function () {
    $html = makeTooltipView();

    expect($html)->toMatch('/data-tooltip-id="tooltip-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeTooltipView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-tooltip']),
    ]);

    expect($html)->toContain('data-tooltip-id="my-tooltip"');
});

it('renders with text content', function () {
    $html = makeTooltipView(['text' => 'Hello World']);

    expect($html)->toContain('&quot;text&quot;:&quot;Hello World&quot;');
});

it('renders with position top by default', function () {
    $html = makeTooltipView();

    expect($html)->toContain('&quot;position&quot;:&quot;top&quot;');
});

it('renders with custom position', function () {
    $html = makeTooltipView(['position' => 'bottom']);

    expect($html)->toContain('&quot;position&quot;:&quot;bottom&quot;');
});

it('renders with all position variants', function () {
    $positions = ['top', 'top-start', 'top-end', 'bottom', 'bottom-start', 'bottom-end', 'left', 'left-start', 'left-end', 'right', 'right-start', 'right-end'];

    foreach ($positions as $position) {
        $html = makeTooltipView(['position' => $position]);
        expect($html)->toContain("&quot;position&quot;:&quot;{$position}&quot;");
    }
});

it('renders with hover trigger by default', function () {
    $html = makeTooltipView();

    expect($html)->toContain('&quot;trigger&quot;:&quot;hover&quot;');
});

it('renders with click trigger', function () {
    $html = makeTooltipView(['trigger' => 'click']);

    expect($html)->toContain('&quot;trigger&quot;:&quot;click&quot;');
});

it('renders with focus trigger', function () {
    $html = makeTooltipView(['trigger' => 'focus']);

    expect($html)->toContain('&quot;trigger&quot;:&quot;focus&quot;');
});

it('renders with manual trigger', function () {
    $html = makeTooltipView(['trigger' => 'manual']);

    expect($html)->toContain('&quot;trigger&quot;:&quot;manual&quot;');
});

it('renders with delay', function () {
    $html = makeTooltipView(['delay' => 500]);

    expect($html)->toContain('&quot;delay&quot;:500');
});

it('renders with hide delay', function () {
    $html = makeTooltipView(['hideDelay' => 1000]);

    expect($html)->toContain('&quot;hideDelay&quot;:1000');
});

it('renders with arrow enabled by default', function () {
    $html = makeTooltipView();

    expect($html)->toContain('&quot;arrow&quot;:true');
});

it('renders without arrow', function () {
    $html = makeTooltipView(['arrow' => false]);

    expect($html)->toContain('&quot;arrow&quot;:false');
});

it('renders non-interactive by default', function () {
    $html = makeTooltipView();

    expect($html)->toContain('&quot;interactive&quot;:false');
});

it('renders interactive tooltip', function () {
    $html = makeTooltipView(['interactive' => true]);

    expect($html)->toContain('&quot;interactive&quot;:true');
});

it('renders with custom offset', function () {
    $html = makeTooltipView(['offset' => 12]);

    expect($html)->toContain('&quot;offset&quot;:12');
});

it('renders with max width', function () {
    $html = makeTooltipView(['maxWidth' => '200px']);

    expect($html)->toContain('&quot;maxWidth&quot;:&quot;200px&quot;');
});

it('renders slot content', function () {
    $html = makeTooltipView([
        'slot' => new HtmlString('<span class="custom">Custom Content</span>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Content');
});

it('merges additional attributes', function () {
    $html = makeTooltipView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class',
            'data-testid' => 'tooltip-test',
        ]),
    ]);

    expect($html)
        ->toContain('my-class')
        ->toContain('data-testid="tooltip-test"');
});

it('has relative inline-block class by default', function () {
    $html = makeTooltipView();

    expect($html)->toContain('relative inline-block');
});

it('renders initial state', function () {
    $html = makeTooltipView(['text' => 'Test']);

    expect($html)
        ->toContain('data-accelade-state=')
        ->toContain('&quot;isVisible&quot;:false')
        ->toContain('&quot;tooltipText&quot;:&quot;Test&quot;');
});

it('renders with animation config when animation is specified', function () {
    $html = makeTooltipView(['animation' => 'fade']);

    expect($html)->toContain('data-accelade');
});

it('renders with all props combined', function () {
    $html = makeTooltipView([
        'text' => 'Full test',
        'position' => 'right',
        'trigger' => 'click',
        'delay' => 200,
        'hideDelay' => 500,
        'arrow' => false,
        'interactive' => true,
        'offset' => 10,
        'maxWidth' => '300px',
        'attributes' => new ComponentAttributeBag(['id' => 'full-tooltip', 'class' => 'tooltip-wrapper']),
    ]);

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-tooltip')
        ->toContain('data-tooltip-id="full-tooltip"')
        ->toContain('&quot;text&quot;:&quot;Full test&quot;')
        ->toContain('&quot;position&quot;:&quot;right&quot;')
        ->toContain('&quot;trigger&quot;:&quot;click&quot;')
        ->toContain('&quot;delay&quot;:200')
        ->toContain('&quot;hideDelay&quot;:500')
        ->toContain('&quot;arrow&quot;:false')
        ->toContain('&quot;interactive&quot;:true')
        ->toContain('&quot;offset&quot;:10')
        ->toContain('&quot;maxWidth&quot;:&quot;300px&quot;')
        ->toContain('tooltip-wrapper');
});

it('handles empty text', function () {
    $html = makeTooltipView(['text' => '']);

    expect($html)
        ->toContain('data-accelade-tooltip')
        ->toContain('&quot;text&quot;:&quot;&quot;');
});

it('renders input elements in slot', function () {
    $html = makeTooltipView([
        'slot' => new HtmlString('<input type="text" placeholder="Enter text">'),
        'trigger' => 'focus',
    ]);

    expect($html)
        ->toContain('input')
        ->toContain('placeholder="Enter text"');
});

it('renders complex slot content', function () {
    $html = makeTooltipView([
        'slot' => new HtmlString('<div class="flex items-center gap-2"><svg></svg><span>Help</span></div>'),
    ]);

    expect($html)
        ->toContain('flex items-center gap-2')
        ->toContain('<svg></svg>')
        ->toContain('<span>Help</span>');
});
