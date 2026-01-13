<?php

declare(strict_types=1);

use Accelade\Facades\Accelade;
use Accelade\Http\Controllers\DocsController;
use Accelade\Http\Controllers\NotifyDemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Documentation Routes
|--------------------------------------------------------------------------
|
| These routes serve the Accelade documentation portal with live demos.
|
*/

// Share demo data for all docs routes (needed for live demos)
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

// Valid sections
$validSections = [
    'getting-started',
    'installation',
    'configuration',
    'counter',
    'data',
    'state',
    'modal',
    'toggle',
    'transition',
    'notifications',
    'code-block',
    'lazy',
    'defer',
    'content',
    'rehydrate',
    'teleport',
    'navigation',
    'link',
    'progress',
    'persistent',
    'event-bus',
    'event',
    'bridge',
    'shared-data',
    'flash',
    'errors',
    'scripts',
    'api-reference',
    'frameworks',
    'architecture',
    'testing',
    'contributing',
    'sponsor',
    'thanks',
];

// Docs section routes
Route::get('/{section}', function (string $section) use ($shareData, $validSections) {
    if (! in_array($section, $validSections)) {
        abort(404);
    }

    $shareData();

    return app(DocsController::class)->show(request(), $section);
})->name('docs.section');

// Search API
Route::get('/api/search', function () {
    return app(DocsController::class)->search(request());
})->name('docs.search');

// Backend notification demo routes
Route::get('/notify/{type}', [NotifyDemoController::class, 'show'])
    ->name('docs.notify');

// Default docs page
Route::get('/', fn () => redirect()->route('docs.section', ['section' => 'getting-started']))
    ->name('docs.index');
