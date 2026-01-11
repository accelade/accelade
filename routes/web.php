<?php

declare(strict_types=1);

use Accelade\Http\Controllers\AcceladeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accelade Routes
|--------------------------------------------------------------------------
|
| Core routes for Accelade package functionality.
|
*/

// Core Accelade routes
Route::get('/accelade-v2.js', [AcceladeController::class, 'script'])
    ->name('accelade.script');

Route::post('/update', [AcceladeController::class, 'update'])
    ->name('accelade.update');

Route::post('/batch-update', [AcceladeController::class, 'batchUpdate'])
    ->name('accelade.batch-update');
