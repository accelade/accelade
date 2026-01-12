<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeStateView(array $props = []): string
{
    $defaults = [
        'errors' => null,
        'flash' => null,
        'shared' => null,
        'slot' => new HtmlString('<p>State Content</p>'),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/state.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic state component', function () {
    $html = makeStateView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-state-component')
        ->toContain('State Content');
});

it('renders with explicit errors array', function () {
    $errors = [
        'email' => ['The email field is required.'],
        'password' => ['The password must be at least 8 characters.'],
    ];

    $html = makeStateView(['errors' => $errors]);

    expect($html)
        ->toContain('data-state-errors')
        ->toContain('email')
        ->toContain('password');
});

it('renders with flash data', function () {
    $flash = [
        'success' => 'Item created successfully!',
        'info' => 'Welcome back.',
    ];

    $html = makeStateView(['flash' => $flash]);

    expect($html)
        ->toContain('data-state-flash')
        ->toContain('success')
        ->toContain('Item created successfully!');
});

it('renders with shared data', function () {
    $shared = [
        'user' => ['name' => 'John', 'email' => 'john@example.com'],
        'settings' => ['theme' => 'dark'],
    ];

    $html = makeStateView(['shared' => $shared]);

    expect($html)
        ->toContain('data-state-shared')
        ->toContain('user')
        ->toContain('John');
});

it('generates unique id when not provided', function () {
    $html = makeStateView();

    expect($html)->toMatch('/data-state-id="state-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeStateView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-state']),
    ]);

    expect($html)->toContain('data-state-id="my-state"');
});

it('renders slot content', function () {
    $html = makeStateView([
        'slot' => new HtmlString('<div class="custom">Custom Content</div>'),
    ]);

    expect($html)
        ->toContain('custom')
        ->toContain('Custom Content');
});

it('merges additional attributes', function () {
    $html = makeStateView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-class',
            'data-testid' => 'state-test',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-class"')
        ->toContain('data-testid="state-test"');
});

it('includes initial state with errors structure', function () {
    $errors = ['name' => ['Name is required']];

    $html = makeStateView(['errors' => $errors]);

    // HTML entities are escaped, so look for the escaped version
    expect($html)
        ->toContain('data-accelade-state=')
        ->toMatch('/&quot;hasErrors&quot;:true/');
});

it('includes initial state with hasErrors false when no errors', function () {
    $html = makeStateView(['errors' => []]);

    expect($html)
        ->toContain('data-accelade-state=')
        ->toMatch('/&quot;hasErrors&quot;:false/');
});

it('converts raw errors to first-error-only format in state', function () {
    $errors = [
        'email' => ['First error', 'Second error', 'Third error'],
    ];

    $html = makeStateView(['errors' => $errors]);

    // The state should include the rawErrors with all messages
    // HTML entities are escaped
    expect($html)
        ->toMatch('/&quot;rawErrors&quot;/')
        ->toContain('First error');
});

it('renders with all data combined', function () {
    $html = makeStateView([
        'errors' => ['field' => ['Error message']],
        'flash' => ['success' => 'Success message'],
        'shared' => ['key' => 'value'],
        'attributes' => new ComponentAttributeBag(['id' => 'combined-state']),
    ]);

    expect($html)
        ->toContain('data-accelade-state-component')
        ->toContain('data-state-id="combined-state"')
        ->toContain('data-state-errors')
        ->toContain('data-state-flash')
        ->toContain('data-state-shared')
        ->toContain('data-accelade-state=');
});

it('json encodes errors properly', function () {
    $errors = ['field' => ['Message with "quotes" and special chars']];

    $html = makeStateView(['errors' => $errors]);

    // Should be properly escaped
    expect($html)
        ->toContain('data-state-errors=')
        ->not->toContain('Message with "quotes"'); // Should be escaped
});

it('handles empty arrays for all data', function () {
    $html = makeStateView([
        'errors' => [],
        'flash' => [],
        'shared' => [],
    ]);

    expect($html)
        ->toContain('data-state-errors="[]"')
        ->toContain('data-state-flash="[]"')
        ->toContain('data-state-shared="[]"');
});

it('handles nested shared data', function () {
    $shared = [
        'user' => [
            'profile' => [
                'name' => 'John Doe',
                'settings' => ['theme' => 'dark'],
            ],
        ],
    ];

    $html = makeStateView(['shared' => $shared]);

    expect($html)
        ->toContain('data-state-shared')
        ->toContain('profile')
        ->toContain('John Doe');
});

it('handles multiple errors per field', function () {
    $errors = [
        'password' => [
            'The password must be at least 8 characters.',
            'The password must contain at least one uppercase letter.',
            'The password must contain at least one number.',
        ],
    ];

    $html = makeStateView(['errors' => $errors]);

    expect($html)
        ->toContain('data-state-errors')
        ->toContain('8 characters')
        ->toContain('uppercase');
});
