<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

describe('Core Routes', function () {
    it('registers the script route', function () {
        expect(Route::has('accelade.script'))->toBeTrue();
    });

    it('registers the update route', function () {
        expect(Route::has('accelade.update'))->toBeTrue();
    });

    it('registers the batch-update route', function () {
        expect(Route::has('accelade.batch-update'))->toBeTrue();
    });
});

describe('Demo Routes with Config', function () {
    it('demo routes registration depends on config', function () {
        // The demo routes are registered based on config at boot time
        // By default in TestCase, demo.enabled is not set (false)
        // This test documents the expected behavior
        $demoEnabled = config('accelade.demo.enabled', false);

        if ($demoEnabled) {
            expect(Route::has('demo.vanilla'))->toBeTrue();
            expect(Route::has('demo.vue'))->toBeTrue();
            expect(Route::has('demo.react'))->toBeTrue();
            expect(Route::has('demo.svelte'))->toBeTrue();
            expect(Route::has('demo.angular'))->toBeTrue();
            expect(Route::has('demo.notify'))->toBeTrue();
        } else {
            // When demo is disabled, routes should not exist
            expect(Route::has('demo.vanilla'))->toBeFalse();
        }
    });

    it('demo is enabled in non-production environments', function () {
        // Demo is enabled by default in non-production environments
        // env('APP_ENV') !== 'production' evaluates to true in testing
        $isProduction = app()->environment('production');
        $demoEnabled = config('accelade.demo.enabled');

        if ($isProduction) {
            expect($demoEnabled)->toBeFalse();
        } else {
            expect($demoEnabled)->toBeTrue();
        }
    });

    it('demo prefix defaults to demo', function () {
        expect(config('accelade.demo.prefix', 'demo'))->toBe('demo');
    });

    it('demo middleware defaults to web', function () {
        expect(config('accelade.demo.middleware', ['web']))->toBe(['web']);
    });
});
