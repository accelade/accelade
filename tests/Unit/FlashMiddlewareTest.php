<?php

declare(strict_types=1);

use Accelade\Facades\Accelade;
use Accelade\Http\Middleware\ShareAcceladeData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

beforeEach(function () {
    Accelade::shared()->flush();
});

it('shares csrf token', function () {
    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    expect(Accelade::shared()->has('csrf_token'))->toBeTrue();
});

it('shares flash data when enabled', function () {
    config(['accelade.flash.enabled' => true]);

    session()->flash('success', 'Test flash');

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    expect(Accelade::shared()->has('flash'))->toBeTrue();
});

it('does not share flash data when disabled', function () {
    config(['accelade.flash.enabled' => false]);

    session()->flash('success', 'Test flash');

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    expect(Accelade::shared()->has('flash'))->toBeFalse();
});

it('respects flash keys config', function () {
    config([
        'accelade.flash.enabled' => true,
        'accelade.flash.keys' => ['success', 'error'],
    ]);

    session()->put('success', 'Success message');
    session()->put('info', 'Info message');
    session()->put('custom', 'Custom message');

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    $flashData = Accelade::shared()->get('flash');

    // Should only include configured keys
    expect($flashData)
        ->toHaveKey('success')
        ->not->toHaveKey('info')
        ->not->toHaveKey('custom');
});

it('collects all common flash keys when keys config is null', function () {
    config([
        'accelade.flash.enabled' => true,
        'accelade.flash.keys' => null,
    ]);

    session()->put('message', 'Message');
    session()->put('success', 'Success');
    session()->put('error', 'Error');
    session()->put('warning', 'Warning');
    session()->put('info', 'Info');
    session()->put('status', 'Status');
    session()->put('notification', 'Notification');
    session()->put('alert', 'Alert');

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    $flashData = Accelade::shared()->get('flash');

    expect($flashData)
        ->toHaveKey('message')
        ->toHaveKey('success')
        ->toHaveKey('error')
        ->toHaveKey('warning')
        ->toHaveKey('info')
        ->toHaveKey('status')
        ->toHaveKey('notification')
        ->toHaveKey('alert');
});

it('continues middleware chain', function () {
    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $responseBody = 'Test Response';
    $response = $middleware->handle($request, fn () => new Response($responseBody));

    expect($response->getContent())->toBe($responseBody);
});

it('handles missing session gracefully', function () {
    config(['accelade.flash.enabled' => true]);

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $response = $middleware->handle($request, fn () => new Response);

    // Should not throw exception
    expect($response)->toBeInstanceOf(Response::class);
});

it('uses lazy loading for flash data', function () {
    config(['accelade.flash.enabled' => true]);

    $middleware = new ShareAcceladeData;
    $request = Request::create('/test', 'GET');

    $middleware->handle($request, fn () => new Response);

    // Flash should be registered but not evaluated until accessed
    expect(Accelade::shared()->has('flash'))->toBeTrue();

    // Now access it to trigger evaluation
    $flash = Accelade::shared()->get('flash');
    expect($flash)->toBeArray();
});
