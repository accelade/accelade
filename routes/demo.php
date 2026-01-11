<?php

declare(strict_types=1);

use Accelade\Facades\Accelade;
use Accelade\Http\Controllers\NotifyDemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Demo Routes
|--------------------------------------------------------------------------
|
| These routes are only registered when demo mode is enabled in config.
|
*/

// Framework-specific demo pages
Route::get('/vanilla', fn () => view('accelade::demo.vanilla', ['framework' => 'vanilla']))
    ->name('demo.vanilla');

Route::get('/vue', fn () => view('accelade::demo.vue', ['framework' => 'vue']))
    ->name('demo.vue');

Route::get('/react', fn () => view('accelade::demo.react', ['framework' => 'react']))
    ->name('demo.react');

Route::get('/svelte', fn () => view('accelade::demo.svelte', ['framework' => 'svelte']))
    ->name('demo.svelte');

Route::get('/angular', fn () => view('accelade::demo.angular', ['framework' => 'angular']))
    ->name('demo.angular');

// Backend notification demo routes
Route::get('/notify/{type}', [NotifyDemoController::class, 'show'])
    ->name('demo.notify');

// Shared data demo
Route::get('/shared-data', function () {
    // Share some example data
    Accelade::share('appName', config('app.name', 'Accelade Demo'));
    Accelade::share('currentTime', now()->toIso8601String());
    Accelade::share('user', [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    Accelade::share('settings', [
        'theme' => 'dark',
        'language' => 'en',
        'notifications' => true,
    ]);

    return view('accelade::demo.shared-data');
})->name('demo.shared-data');

// Default demo (redirects to vanilla)
Route::get('/', fn () => redirect()->route('demo.vanilla'))
    ->name('demo.index');
