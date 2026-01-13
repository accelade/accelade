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

describe('Docs Routes with Config', function () {
    it('docs routes registration depends on config', function () {
        // The docs routes are registered based on config at boot time
        $docsEnabled = config('accelade.docs.enabled', false);

        if ($docsEnabled) {
            expect(Route::has('docs.section'))->toBeTrue();
            expect(Route::has('docs.index'))->toBeTrue();
            expect(Route::has('docs.search'))->toBeTrue();
            expect(Route::has('docs.notify'))->toBeTrue();
        } else {
            // When docs is disabled, routes should not exist
            expect(Route::has('docs.section'))->toBeFalse();
        }
    });

    it('docs is enabled in non-production environments', function () {
        // Docs is enabled by default in non-production environments
        // env('APP_ENV') !== 'production' evaluates to true in testing
        $isProduction = app()->environment('production');
        $docsEnabled = config('accelade.docs.enabled');

        if ($isProduction) {
            expect($docsEnabled)->toBeFalse();
        } else {
            expect($docsEnabled)->toBeTrue();
        }
    });

    it('docs prefix defaults to docs', function () {
        expect(config('accelade.docs.prefix', 'docs'))->toBe('docs');
    });

    it('docs middleware defaults to web', function () {
        expect(config('accelade.docs.middleware', ['web']))->toBe(['web']);
    });

    it('docs github repo is configured', function () {
        expect(config('accelade.docs.github_repo'))->toBe('accelade/accelade');
    });
});
