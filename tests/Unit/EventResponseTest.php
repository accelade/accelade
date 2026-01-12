<?php

declare(strict_types=1);

use Accelade\Broadcasting\EventResponse;

it('creates redirect response', function () {
    $response = EventResponse::redirect('/orders/123');
    $array = $response->toArray();

    expect($array)->toHaveKey('_accelade');
    expect($array['_accelade'])->toHaveKey('action', 'redirect');
    expect($array['_accelade'])->toHaveKey('url', '/orders/123');
});

it('creates redirect to route response', function () {
    // Mock the route function
    $response = EventResponse::redirect('https://example.com/orders/123');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('redirect');
    expect($array['_accelade']['url'])->toBe('https://example.com/orders/123');
});

it('creates refresh response', function () {
    $response = EventResponse::refresh();
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('refresh');
});

it('creates toast response with default type', function () {
    $response = EventResponse::toast('Hello World');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Hello World');
    expect($array['_accelade']['type'])->toBe('info');
});

it('creates toast response with custom type', function () {
    $response = EventResponse::toast('Success!', 'success');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Success!');
    expect($array['_accelade']['type'])->toBe('success');
});

it('creates success toast shorthand', function () {
    $response = EventResponse::success('Operation successful');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Operation successful');
    expect($array['_accelade']['type'])->toBe('success');
});

it('creates info toast shorthand', function () {
    $response = EventResponse::info('Information message');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Information message');
    expect($array['_accelade']['type'])->toBe('info');
});

it('creates warning toast shorthand', function () {
    $response = EventResponse::warning('Warning message');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Warning message');
    expect($array['_accelade']['type'])->toBe('warning');
});

it('creates danger toast shorthand', function () {
    $response = EventResponse::danger('Error message');
    $array = $response->toArray();

    expect($array['_accelade']['action'])->toBe('toast');
    expect($array['_accelade']['message'])->toBe('Error message');
    expect($array['_accelade']['type'])->toBe('danger');
});

it('adds title to toast response', function () {
    $response = EventResponse::success('Order created successfully')
        ->withTitle('New Order');
    $array = $response->toArray();

    expect($array['_accelade']['title'])->toBe('New Order');
    expect($array['_accelade']['message'])->toBe('Order created successfully');
});

it('adds custom data to response', function () {
    $response = EventResponse::redirect('/orders/123')
        ->with(['order_id' => 123, 'status' => 'paid']);
    $array = $response->toArray();

    expect($array)->toHaveKey('order_id', 123);
    expect($array)->toHaveKey('status', 'paid');
    expect($array)->toHaveKey('_accelade');
});

it('merges multiple custom data', function () {
    $response = EventResponse::toast('Hello')
        ->with(['key1' => 'value1'])
        ->with(['key2' => 'value2']);
    $array = $response->toArray();

    expect($array)->toHaveKey('key1', 'value1');
    expect($array)->toHaveKey('key2', 'value2');
});

it('is json serializable', function () {
    $response = EventResponse::success('Test');
    $json = json_encode($response);
    $decoded = json_decode($json, true);

    expect($decoded)->toHaveKey('_accelade');
    expect($decoded['_accelade']['action'])->toBe('toast');
});

it('implements arrayable interface', function () {
    $response = EventResponse::refresh();

    expect($response)->toBeInstanceOf(\Illuminate\Contracts\Support\Arrayable::class);
    expect($response->toArray())->toBeArray();
});

it('chain methods return self', function () {
    $response = EventResponse::toast('Test')
        ->withTitle('Title')
        ->with(['data' => 'value']);

    expect($response)->toBeInstanceOf(EventResponse::class);
});
