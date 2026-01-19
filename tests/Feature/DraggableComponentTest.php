<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeDraggableView(array $props = []): string
{
    $defaults = [
        'group' => null,
        'handle' => null,
        'animation' => 150,
        'ghostClass' => 'opacity-50',
        'dragClass' => 'shadow-lg',
        'disabled' => false,
        'sortable' => true,
        'dropzone' => false,
        'accepts' => null,
        'axis' => null,
        'slot' => new HtmlString('<div data-draggable-item>Item 1</div><div data-draggable-item>Item 2</div>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/draggable.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic draggable component', function () {
    $html = makeDraggableView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-draggable')
        ->toContain('Item 1')
        ->toContain('Item 2');
});

it('generates unique id when not provided', function () {
    $html = makeDraggableView();

    expect($html)->toMatch('/data-draggable-id="draggable-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeDraggableView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-draggable']),
    ]);

    expect($html)->toContain('data-draggable-id="my-draggable"');
});

it('renders with group name', function () {
    $html = makeDraggableView(['group' => 'tasks']);

    expect($html)->toContain('&quot;group&quot;:&quot;tasks&quot;');
});

it('renders without group by default', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;group&quot;:null');
});

it('renders with handle selector', function () {
    $html = makeDraggableView(['handle' => '.drag-handle']);

    expect($html)->toContain('&quot;handle&quot;:&quot;.drag-handle&quot;');
});

it('renders with default animation duration', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;animation&quot;:150');
});

it('renders with custom animation duration', function () {
    $html = makeDraggableView(['animation' => 300]);

    expect($html)->toContain('&quot;animation&quot;:300');
});

it('renders with default ghost class', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;ghostClass&quot;:&quot;opacity-50&quot;');
});

it('renders with custom ghost class', function () {
    $html = makeDraggableView(['ghostClass' => 'opacity-30 bg-blue-200']);

    expect($html)->toContain('&quot;ghostClass&quot;:&quot;opacity-30 bg-blue-200&quot;');
});

it('renders with default drag class', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;dragClass&quot;:&quot;shadow-lg&quot;');
});

it('renders with custom drag class', function () {
    $html = makeDraggableView(['dragClass' => 'shadow-xl scale-105']);

    expect($html)->toContain('&quot;dragClass&quot;:&quot;shadow-xl scale-105&quot;');
});

it('renders enabled by default', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;disabled&quot;:false');
});

it('renders disabled when specified', function () {
    $html = makeDraggableView(['disabled' => true]);

    expect($html)->toContain('&quot;disabled&quot;:true');
});

it('renders sortable by default', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;sortable&quot;:true');
});

it('renders non-sortable when specified', function () {
    $html = makeDraggableView(['sortable' => false]);

    expect($html)->toContain('&quot;sortable&quot;:false');
});

it('renders non-dropzone by default', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;dropzone&quot;:false');
});

it('renders as dropzone when specified', function () {
    $html = makeDraggableView(['dropzone' => true]);

    expect($html)->toContain('&quot;dropzone&quot;:true');
});

it('renders with accepts filter', function () {
    $html = makeDraggableView(['accepts' => 'group1,group2']);

    expect($html)->toContain('&quot;accepts&quot;:&quot;group1,group2&quot;');
});

it('renders without axis constraint by default', function () {
    $html = makeDraggableView();

    expect($html)->toContain('&quot;axis&quot;:null');
});

it('renders with x axis constraint', function () {
    $html = makeDraggableView(['axis' => 'x']);

    expect($html)->toContain('&quot;axis&quot;:&quot;x&quot;');
});

it('renders with y axis constraint', function () {
    $html = makeDraggableView(['axis' => 'y']);

    expect($html)->toContain('&quot;axis&quot;:&quot;y&quot;');
});

it('renders slot content', function () {
    $html = makeDraggableView([
        'slot' => new HtmlString('<div data-draggable-item class="custom">Custom Item</div>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Item');
});

it('renders initial state', function () {
    $html = makeDraggableView();

    expect($html)
        ->toContain('data-accelade-state=')
        ->toContain('&quot;isDragging&quot;:false')
        ->toContain('&quot;isDragOver&quot;:false');
});

it('merges additional attributes', function () {
    $html = makeDraggableView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class space-y-2',
            'data-testid' => 'draggable-test',
        ]),
    ]);

    expect($html)
        ->toContain('my-class space-y-2')
        ->toContain('data-testid="draggable-test"');
});

it('renders with all props combined', function () {
    $html = makeDraggableView([
        'group' => 'kanban',
        'handle' => '.handle',
        'animation' => 200,
        'ghostClass' => 'opacity-25',
        'dragClass' => 'shadow-2xl',
        'disabled' => false,
        'sortable' => true,
        'dropzone' => false,
        'axis' => 'y',
        'attributes' => new ComponentAttributeBag(['id' => 'full-draggable', 'class' => 'draggable-wrapper']),
    ]);

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-draggable')
        ->toContain('data-draggable-id="full-draggable"')
        ->toContain('&quot;group&quot;:&quot;kanban&quot;')
        ->toContain('&quot;handle&quot;:&quot;.handle&quot;')
        ->toContain('&quot;animation&quot;:200')
        ->toContain('&quot;ghostClass&quot;:&quot;opacity-25&quot;')
        ->toContain('&quot;dragClass&quot;:&quot;shadow-2xl&quot;')
        ->toContain('&quot;axis&quot;:&quot;y&quot;')
        ->toContain('draggable-wrapper');
});

it('renders multiple items in slot', function () {
    $html = makeDraggableView([
        'slot' => new HtmlString('
            <div data-draggable-item>Item 1</div>
            <div data-draggable-item>Item 2</div>
            <div data-draggable-item>Item 3</div>
        '),
    ]);

    expect($html)
        ->toContain('Item 1')
        ->toContain('Item 2')
        ->toContain('Item 3');
});

it('renders complex slot content with handle', function () {
    $html = makeDraggableView([
        'handle' => '.drag-handle',
        'slot' => new HtmlString('
            <div data-draggable-item class="flex items-center">
                <span class="drag-handle">⋮⋮</span>
                <span>Content</span>
                <button>Edit</button>
            </div>
        '),
    ]);

    expect($html)
        ->toContain('drag-handle')
        ->toContain('⋮⋮')
        ->toContain('Content')
        ->toContain('Edit');
});

it('renders dropzone configuration', function () {
    $html = makeDraggableView([
        'dropzone' => true,
        'accepts' => 'files,images',
    ]);

    expect($html)
        ->toContain('&quot;dropzone&quot;:true')
        ->toContain('&quot;accepts&quot;:&quot;files,images&quot;');
});

it('handles empty slot', function () {
    $html = makeDraggableView([
        'slot' => new HtmlString(''),
    ]);

    expect($html)
        ->toContain('data-accelade-draggable')
        ->toContain('data-draggable-config');
});
