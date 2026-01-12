<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    // Set up the view path for testing
    $this->app['config']->set('view.paths', [
        __DIR__.'/../../resources/views',
    ]);
});

it('renders with no errors', function () {
    // Create empty error bag
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-id');
    expect($html)->toContain('data-accelade-errors');
    expect($html)->toContain('Content');
});

it('includes errors in state', function () {
    // Create error bag with errors
    $messageBag = new MessageBag([
        'name' => ['The name field is required.'],
        'email' => ['The email field is required.', 'The email must be valid.'],
    ]);
    $errors = new ViewErrorBag;
    $errors->put('default', $messageBag);
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    expect($html)->toContain('data-accelade-state');

    // Extract and verify state
    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state)->toHaveKey('errors');
    expect($state['errors'])->toHaveKey('name');
    expect($state['errors'])->toHaveKey('email');
    expect($state['errors']['name'])->toBe(['The name field is required.']);
    expect($state['errors']['email'])->toBe(['The email field is required.', 'The email must be valid.']);
});

it('generates unique IDs for each component', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html1 = Blade::render('<x-accelade::errors>Content 1</x-accelade::errors>');
    $html2 = Blade::render('<x-accelade::errors>Content 2</x-accelade::errors>');

    preg_match('/data-accelade-id="([^"]*)"/', $html1, $matches1);
    preg_match('/data-accelade-id="([^"]*)"/', $html2, $matches2);

    expect($matches1[1])->not->toBe($matches2[1]);
});

it('includes cloak attribute', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    expect($html)->toContain('data-accelade-cloak');
});

it('passes additional attributes to wrapper element', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors class="my-errors" id="form-errors">Content</x-accelade::errors>');

    expect($html)->toContain('class="my-errors"');
    expect($html)->toContain('id="form-errors"');
});

it('renders slot content correctly', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('
        <x-accelade::errors>
            <div class="error-container">
                <p>Error messages here</p>
            </div>
        </x-accelade::errors>
    ');

    expect($html)->toContain('<div class="error-container">');
    expect($html)->toContain('<p>Error messages here</p>');
});

it('includes script with error helper methods', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    expect($html)->toContain('accelade:script');
    expect($html)->toContain('errors');
    expect($html)->toContain('has(key)');
    expect($html)->toContain('first(key)');
});

it('supports custom error bag', function () {
    // Create custom error bag
    $defaultBag = new MessageBag(['name' => ['Default error']]);
    $customBag = new MessageBag(['title' => ['Custom bag error']]);

    $errors = new ViewErrorBag;
    $errors->put('default', $defaultBag);
    $errors->put('custom', $customBag);
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors bag="custom">Content</x-accelade::errors>');

    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state['errors'])->toHaveKey('title');
    expect($state['errors'])->not->toHaveKey('name');
    expect($state['errors']['title'])->toBe(['Custom bag error']);
});

it('handles empty error bag gracefully', function () {
    $errors = new ViewErrorBag;
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state['errors'])->toBe([]);
});

it('handles special characters in error messages', function () {
    $messageBag = new MessageBag([
        'field' => ['Error with "quotes" and <html> & special chars'],
    ]);
    $errors = new ViewErrorBag;
    $errors->put('default', $messageBag);
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    // Should be properly JSON encoded
    expect($html)->toContain('data-accelade-state');

    // Verify it can be decoded
    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state['errors']['field'][0])->toContain('quotes');
    expect($state['errors']['field'][0])->toContain('special chars');
});

it('handles nested field names', function () {
    $messageBag = new MessageBag([
        'user.name' => ['The user name is required.'],
        'user.email' => ['The user email is required.'],
        'items.0.name' => ['Item name is required.'],
    ]);
    $errors = new ViewErrorBag;
    $errors->put('default', $messageBag);
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state['errors'])->toHaveKey('user.name');
    expect($state['errors'])->toHaveKey('user.email');
    expect($state['errors'])->toHaveKey('items.0.name');
});

it('handles multiple errors per field', function () {
    $messageBag = new MessageBag([
        'password' => [
            'The password must be at least 8 characters.',
            'The password must contain a number.',
            'The password must contain a special character.',
        ],
    ]);
    $errors = new ViewErrorBag;
    $errors->put('default', $messageBag);
    view()->share('errors', $errors);

    $html = Blade::render('<x-accelade::errors>Content</x-accelade::errors>');

    preg_match('/data-accelade-state="([^"]*)"/', $html, $matches);
    $state = json_decode(html_entity_decode($matches[1]), true);

    expect($state['errors']['password'])->toHaveCount(3);
    expect($state['errors']['password'][0])->toContain('8 characters');
    expect($state['errors']['password'][1])->toContain('number');
    expect($state['errors']['password'][2])->toContain('special character');
});
