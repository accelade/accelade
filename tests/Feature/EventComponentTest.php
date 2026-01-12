<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    // Set up the view path for testing
    $this->app['config']->set('view.paths', [
        __DIR__.'/../../resources/views',
    ]);
});

it('renders with channel attribute', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content</x-accelade::event>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-id');
    expect($html)->toContain('data-accelade-echo');
    expect($html)->toContain('data-echo-channel="orders"');
    expect($html)->toContain('Content');
});

it('includes listen attribute', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated,OrderUpdated">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-listen="OrderCreated,OrderUpdated"');
});

it('defaults to public channel', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-private="false"');
    expect($html)->toContain('data-echo-presence="false"');
});

it('can specify private channel', function () {
    $html = Blade::render('<x-accelade::event channel="user.1" :private="true" listen="MessageReceived">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-private="true"');
    expect($html)->toContain('data-echo-presence="false"');
});

it('can specify presence channel', function () {
    $html = Blade::render('<x-accelade::event channel="chat.room.1" :presence="true" listen="UserJoined">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-presence="true"');
});

it('defaults preserve scroll to false', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-preserve-scroll="false"');
});

it('can enable preserve scroll', function () {
    $html = Blade::render('<x-accelade::event channel="dashboard" :preserve-scroll="true" listen="DataUpdated">Content</x-accelade::event>');

    expect($html)->toContain('data-echo-preserve-scroll="true"');
});

it('includes cloak attribute for hiding until ready', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content</x-accelade::event>');

    expect($html)->toContain('data-accelade-cloak');
});

it('generates unique IDs for each component', function () {
    $html1 = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content 1</x-accelade::event>');
    $html2 = Blade::render('<x-accelade::event channel="users" listen="UserCreated">Content 2</x-accelade::event>');

    preg_match('/data-accelade-id="([^"]*)"/', $html1, $matches1);
    preg_match('/data-accelade-id="([^"]*)"/', $html2, $matches2);

    expect($matches1[1])->not->toBe($matches2[1]);
});

it('passes additional attributes to wrapper element', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated" class="my-class" id="my-event">Content</x-accelade::event>');

    expect($html)->toContain('class="my-class"');
    expect($html)->toContain('id="my-event"');
});

it('renders slot content correctly', function () {
    $html = Blade::render('
        <x-accelade::event channel="orders" listen="OrderCreated">
            <p>Listening for events...</p>
            <span>Status indicator</span>
        </x-accelade::event>
    ');

    expect($html)->toContain('<p>Listening for events...</p>');
    expect($html)->toContain('<span>Status indicator</span>');
});

it('includes initial state with subscribed and events', function () {
    $html = Blade::render('<x-accelade::event channel="orders" listen="OrderCreated">Content</x-accelade::event>');

    expect($html)->toContain('data-accelade-state');
    $decoded = json_decode(html_entity_decode(
        preg_match('/data-accelade-state="([^"]*)"/', $html, $matches) ? $matches[1] : '{}'
    ), true);
    expect($decoded)->toHaveKey('subscribed');
    expect($decoded)->toHaveKey('events');
    expect($decoded['subscribed'])->toBeFalse();
    expect($decoded['events'])->toBe([]);
});

it('can combine multiple features', function () {
    $html = Blade::render('
        <x-accelade::event
            channel="user.123"
            :private="true"
            listen="MessageReceived,NotificationReceived"
            :preserve-scroll="true"
            class="event-listener"
        >
            Content
        </x-accelade::event>
    ');

    expect($html)->toContain('data-echo-channel="user.123"');
    expect($html)->toContain('data-echo-private="true"');
    expect($html)->toContain('data-echo-listen="MessageReceived,NotificationReceived"');
    expect($html)->toContain('data-echo-preserve-scroll="true"');
    expect($html)->toContain('class="event-listener"');
});

it('renders without channel attribute', function () {
    $html = Blade::render('<x-accelade::event listen="GlobalEvent">Content</x-accelade::event>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-echo');
    expect($html)->not->toContain('data-echo-channel=""');
});

it('renders without listen attribute', function () {
    $html = Blade::render('<x-accelade::event channel="orders">Content</x-accelade::event>');

    expect($html)->toContain('data-accelade');
    expect($html)->toContain('data-accelade-echo');
    expect($html)->not->toContain('data-echo-listen=""');
});
