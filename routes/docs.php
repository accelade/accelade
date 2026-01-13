<?php

declare(strict_types=1);

use Accelade\Docs\DocsRegistry;
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
| Other packages can register their documentation sections via the
| DocsRegistry in their service providers.
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

// Docs section routes - validates against registered sections
Route::get('/{section}', function (string $section) use ($shareData) {
    /** @var DocsRegistry $registry */
    $registry = app('accelade.docs');

    if (! $registry->hasSection($section)) {
        abort(404);
    }

    $shareData();

    return app(DocsController::class)->show(request(), $section);
})->name('docs.section');

// Search API
Route::get('/api/search', function () {
    return app(DocsController::class)->search(request());
})->name('docs.search');

// Navigation API - returns sidebar structure
Route::get('/api/navigation', function () {
    return app(DocsController::class)->navigation();
})->name('docs.navigation');

// Backend notification demo routes
Route::get('/notify/{type}', [NotifyDemoController::class, 'show'])
    ->name('docs.notify');

// Default docs page
Route::get('/', fn () => redirect()->route('docs.section', ['section' => 'getting-started']))
    ->name('docs.index');
