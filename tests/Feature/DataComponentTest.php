<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    // Set up the view path for testing
    $this->app['config']->set('view.paths', [
        __DIR__.'/../../resources/views',
    ]);
});

it('renders with default empty state', function () {
    $html = Blade::render('<x-accelade::data>Content</x-accelade::data>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-id');
    expect($html)->toContain('data-accelade-state="[]"');
    expect($html)->toContain('Content');
});

it('renders with initial data from array', function () {
    $html = Blade::render('<x-accelade::data :default="$data">Content</x-accelade::data>', [
        'data' => ['count' => 0, 'name' => 'John'],
    ]);

    expect($html)->toContain('data-accelade-state');
    // The state should be JSON encoded
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('count', 0);
    expect($decoded)->toHaveKey('name', 'John');
});

it('renders with initial data from collection', function () {
    $collection = new Collection(['count' => 5, 'active' => true]);

    $html = Blade::render('<x-accelade::data :default="$data">Content</x-accelade::data>', [
        'data' => $collection,
    ]);

    expect($html)->toContain('data-accelade-state');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('count', 5);
    expect($decoded)->toHaveKey('active', true);
});

it('renders with javascript object notation string', function () {
    $html = Blade::render('<x-accelade::data :default="$data">Content</x-accelade::data>', [
        'data' => '{ count: 0, items: [] }',
    ]);

    expect($html)->toContain('data-accelade-state-js');
    expect($html)->toContain('{ count: 0, items: [] }');
});

it('sets remember attribute for session storage', function () {
    $html = Blade::render('<x-accelade::data remember="my-form-data">Content</x-accelade::data>');

    expect($html)->toContain('data-accelade-remember="my-form-data"');
});

it('sets local-storage attribute for localStorage', function () {
    $html = Blade::render('<x-accelade::data local-storage="user-preferences">Content</x-accelade::data>');

    expect($html)->toContain('data-accelade-local-storage="user-preferences"');
});

it('sets store attribute for global stores', function () {
    $html = Blade::render('<x-accelade::data store="cart">Content</x-accelade::data>');

    expect($html)->toContain('data-accelade-store="cart"');
});

it('rejects reserved store names', function () {
    $reservedNames = ['data', 'form', 'toggle', 'state', 'store'];

    foreach ($reservedNames as $name) {
        $html = Blade::render('<x-accelade::data :store="$name">Content</x-accelade::data>', [
            'name' => $name,
        ]);

        expect($html)->not->toContain('data-accelade-store');
    }
});

it('allows non-reserved store names', function () {
    $html = Blade::render('<x-accelade::data store="myStore">Content</x-accelade::data>');

    expect($html)->toContain('data-accelade-store="myStore"');
});

it('generates unique IDs for each component', function () {
    $html1 = Blade::render('<x-accelade::data>Content 1</x-accelade::data>');
    $html2 = Blade::render('<x-accelade::data>Content 2</x-accelade::data>');

    preg_match('/data-accelade-id="([^"]*)"/', $html1, $matches1);
    preg_match('/data-accelade-id="([^"]*)"/', $html2, $matches2);

    expect($matches1[1])->not->toBe($matches2[1]);
});

it('passes additional attributes to wrapper element', function () {
    $html = Blade::render('<x-accelade::data class="my-class" id="my-component">Content</x-accelade::data>');

    expect($html)->toContain('class="my-class"');
    expect($html)->toContain('id="my-component"');
});

it('includes cloak attribute for hiding until ready', function () {
    $html = Blade::render('<x-accelade::data>Content</x-accelade::data>');

    expect($html)->toContain('data-accelade-cloak');
});

it('renders slot content correctly', function () {
    $html = Blade::render('
        <x-accelade::data :default="$data">
            <button>Click me</button>
            <span>Count: 0</span>
        </x-accelade::data>
    ', [
        'data' => ['count' => 0],
    ]);

    expect($html)->toContain('<button>Click me</button>');
    expect($html)->toContain('<span>Count: 0</span>');
});

it('can combine remember and default data', function () {
    $html = Blade::render('<x-accelade::data :default="$data" remember="form-data">Content</x-accelade::data>', [
        'data' => ['name' => '', 'email' => ''],
    ]);

    expect($html)->toContain('data-accelade-remember="form-data"');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('name');
    expect($decoded)->toHaveKey('email');
});

it('can combine localStorage and default data', function () {
    $html = Blade::render('<x-accelade::data :default="$data" local-storage="settings">Content</x-accelade::data>', [
        'data' => ['theme' => 'dark', 'language' => 'en'],
    ]);

    expect($html)->toContain('data-accelade-local-storage="settings"');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('theme', 'dark');
    expect($decoded)->toHaveKey('language', 'en');
});

it('can combine store and default data', function () {
    $html = Blade::render('<x-accelade::data :default="$data" store="cartStore">Content</x-accelade::data>', [
        'data' => ['items' => [], 'total' => 0],
    ]);

    expect($html)->toContain('data-accelade-store="cartStore"');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('items');
    expect($decoded)->toHaveKey('total', 0);
});

it('handles empty string for remember attribute', function () {
    $html = Blade::render('<x-accelade::data remember="">Content</x-accelade::data>');

    // Empty string should not set the attribute
    expect($html)->not->toContain('data-accelade-remember=""');
});

it('handles empty string for localStorage attribute', function () {
    $html = Blade::render('<x-accelade::data local-storage="">Content</x-accelade::data>');

    // Empty string should not set the attribute
    expect($html)->not->toContain('data-accelade-local-storage=""');
});

it('handles nested array data', function () {
    $html = Blade::render('<x-accelade::data :default="$data">Content</x-accelade::data>', [
        'data' => [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com',
            ],
            'preferences' => [
                'theme' => 'dark',
                'notifications' => true,
            ],
        ],
    ]);

    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);

    expect($decoded)->toHaveKey('user');
    expect($decoded['user'])->toHaveKey('name', 'John');
    expect($decoded)->toHaveKey('preferences');
    expect($decoded['preferences'])->toHaveKey('theme', 'dark');
});

it('renders with data attributes for styling', function () {
    $html = Blade::render('<x-accelade::data data-testid="test-component">Content</x-accelade::data>');

    expect($html)->toContain('data-testid="test-component"');
});
