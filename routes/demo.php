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

// Share demo data for all demo routes
$shareData = function () {
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
};

// Framework prefix mapping
$prefixMap = [
    'vanilla' => 'a',
    'vue' => 'v',
    'react' => 'data-state',
    'svelte' => 's',
    'angular' => 'ng',
];

// Valid frameworks and sections
$validFrameworks = ['vanilla', 'vue', 'react', 'svelte', 'angular'];
$validSections = ['counter', 'scripts', 'navigation', 'progress', 'notifications', 'shared-data', 'lazy', 'content', 'data', 'defer', 'rehydrate', 'errors', 'event', 'flash', 'link', 'modal', 'state'];

// Section-based demo routes (new structure)
Route::get('/{framework}/{section}', function (string $framework, string $section) use ($shareData, $prefixMap, $validFrameworks, $validSections) {
    if (! in_array($framework, $validFrameworks)) {
        abort(404);
    }
    if (! in_array($section, $validSections)) {
        abort(404);
    }

    $shareData();

    $prefix = $prefixMap[$framework] ?? 'a';

    return view("accelade::demo.sections.{$section}", [
        'framework' => $framework,
        'prefix' => $prefix,
    ]);
})->name('demo.section');

// Legacy framework-specific demo pages (redirect to new structure)
Route::get('/vanilla', fn () => redirect()->route('demo.section', ['framework' => 'vanilla', 'section' => 'counter']))
    ->name('demo.vanilla');

Route::get('/vue', fn () => redirect()->route('demo.section', ['framework' => 'vue', 'section' => 'counter']))
    ->name('demo.vue');

Route::get('/react', fn () => redirect()->route('demo.section', ['framework' => 'react', 'section' => 'counter']))
    ->name('demo.react');

Route::get('/svelte', fn () => redirect()->route('demo.section', ['framework' => 'svelte', 'section' => 'counter']))
    ->name('demo.svelte');

Route::get('/angular', fn () => redirect()->route('demo.section', ['framework' => 'angular', 'section' => 'counter']))
    ->name('demo.angular');

// Backend notification demo routes
Route::get('/notify/{type}', [NotifyDemoController::class, 'show'])
    ->name('demo.notify');

// Default demo (redirects to vanilla counter)
Route::get('/', fn () => redirect()->route('demo.section', ['framework' => 'vanilla', 'section' => 'counter']))
    ->name('demo.index');
