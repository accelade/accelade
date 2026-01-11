<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    // Set up the view path for testing
    $this->app['config']->set('view.paths', [
        __DIR__.'/../../resources/views',
    ]);
});

it('renders with required url attribute', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-id');
    expect($html)->toContain('data-accelade-defer');
    expect($html)->toContain('data-defer-url="/api/data"');
    expect($html)->toContain('Content');
});

it('uses GET method by default', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-method="GET"');
});

it('can specify HTTP method', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" method="POST">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-method="POST"');
});

it('uppercases HTTP method', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" method="post">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-method="POST"');
});

it('uses application/json accept header by default', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-accept="application/json"');
});

it('can specify custom accept header', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" accept-header="text/html">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-accept="text/html"');
});

it('can specify request data as array', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" method="POST" :request="$data">Content</x-accelade::defer>', [
        'data' => ['name' => 'John', 'email' => 'john@example.com'],
    ]);

    expect($html)->toContain('data-defer-request');
    // Verify the JSON is present
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-defer-request="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('name', 'John');
    expect($decoded)->toHaveKey('email', 'john@example.com');
});

it('can specify custom headers', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" :headers="$headers">Content</x-accelade::defer>', [
        'headers' => ['Authorization' => 'Bearer token123', 'X-Custom' => 'value'],
    ]);

    expect($html)->toContain('data-defer-headers');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-defer-headers="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('Authorization', 'Bearer token123');
    expect($decoded)->toHaveKey('X-Custom', 'value');
});

it('can enable polling with interval', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" poll="5000">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-poll="5000"');
});

it('can be set to manual mode', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" :manual="true">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-manual="true"');
});

it('can watch a value for changes', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" watch-value="form.amount">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-watch="form.amount"');
});

it('can specify watch debounce time', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" watch-value="search" :watch-debounce="300">Content</x-accelade::defer>');

    expect($html)->toContain('data-defer-watch="search"');
    expect($html)->toContain('data-defer-watch-debounce="300"');
});

it('does not include debounce attribute when using default value', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" watch-value="search">Content</x-accelade::defer>');

    expect($html)->not->toContain('data-defer-watch-debounce');
});

it('includes cloak attribute for hiding until ready', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    expect($html)->toContain('data-accelade-cloak');
});

it('generates unique IDs for each component', function () {
    $html1 = Blade::render('<x-accelade::defer url="/api/data1">Content 1</x-accelade::defer>');
    $html2 = Blade::render('<x-accelade::defer url="/api/data2">Content 2</x-accelade::defer>');

    preg_match('/data-accelade-id="([^"]*)"/', $html1, $matches1);
    preg_match('/data-accelade-id="([^"]*)"/', $html2, $matches2);

    expect($matches1[1])->not->toBe($matches2[1]);
});

it('passes additional attributes to wrapper element', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" class="my-class" id="my-defer">Content</x-accelade::defer>');

    expect($html)->toContain('class="my-class"');
    expect($html)->toContain('id="my-defer"');
});

it('renders slot content correctly', function () {
    $html = Blade::render('
        <x-accelade::defer url="/api/quote">
            <p>Loading...</p>
            <button>Reload</button>
        </x-accelade::defer>
    ');

    expect($html)->toContain('<p>Loading...</p>');
    expect($html)->toContain('<button>Reload</button>');
});

it('includes initial state with processing response and error', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    expect($html)->toContain('data-accelade-state');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('processing');
    expect($decoded)->toHaveKey('response');
    expect($decoded)->toHaveKey('error');
});

it('sets processing to true when not manual', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data">Content</x-accelade::defer>');

    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded['processing'])->toBeTrue();
});

it('sets processing to false when manual', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" :manual="true">Content</x-accelade::defer>');

    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded['processing'])->toBeFalse();
});

it('supports all HTTP methods', function () {
    $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    foreach ($methods as $method) {
        $html = Blade::render('<x-accelade::defer url="/api/data" :method="$method">Content</x-accelade::defer>', [
            'method' => $method,
        ]);

        expect($html)->toContain("data-defer-method=\"{$method}\"");
    }
});

it('can combine multiple features', function () {
    $html = Blade::render('
        <x-accelade::defer
            url="/api/search"
            method="POST"
            :request="$data"
            :headers="$headers"
            poll="10000"
            watch-value="query"
            :watch-debounce="500"
            class="search-defer"
        >
            Content
        </x-accelade::defer>
    ', [
        'data' => ['q' => ''],
        'headers' => ['X-API-Key' => 'secret'],
    ]);

    expect($html)->toContain('data-defer-url="/api/search"');
    expect($html)->toContain('data-defer-method="POST"');
    expect($html)->toContain('data-defer-request');
    expect($html)->toContain('data-defer-headers');
    expect($html)->toContain('data-defer-poll="10000"');
    expect($html)->toContain('data-defer-watch="query"');
    expect($html)->toContain('data-defer-watch-debounce="500"');
    expect($html)->toContain('class="search-defer"');
});

it('handles collection as request data', function () {
    $html = Blade::render('<x-accelade::defer url="/api/data" method="POST" :request="$data">Content</x-accelade::defer>', [
        'data' => collect(['items' => [1, 2, 3]]),
    ]);

    expect($html)->toContain('data-defer-request');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-defer-request="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('items');
    expect($decoded['items'])->toBe([1, 2, 3]);
});
