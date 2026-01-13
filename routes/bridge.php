<?php

declare(strict_types=1);

use Accelade\Http\Controllers\BridgeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bridge Routes
|--------------------------------------------------------------------------
|
| These routes handle Bridge component AJAX requests for method calls
| and property synchronization.
|
*/

Route::prefix('accelade/bridge')->middleware('web')->group(function () {
    Route::post('/call', [BridgeController::class, 'call'])->name('accelade.bridge.call');
    Route::post('/sync', [BridgeController::class, 'sync'])->name('accelade.bridge.sync');
});
