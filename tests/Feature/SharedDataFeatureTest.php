<?php

declare(strict_types=1);

use Accelade\Facades\Accelade;

describe('Accelade Shared Data Feature', function () {
    describe('facade share method', function () {
        it('can share data via facade', function () {
            Accelade::share('appName', 'Test App');

            expect(Accelade::getShared('appName'))->toBe('Test App');
        });

        it('can share multiple values via facade', function () {
            Accelade::share([
                'key1' => 'value1',
                'key2' => 'value2',
            ]);

            expect(Accelade::getShared('key1'))->toBe('value1')
                ->and(Accelade::getShared('key2'))->toBe('value2');
        });

        it('can get all shared data via facade', function () {
            Accelade::share('test', 'value');

            $all = Accelade::allShared();

            expect($all)->toHaveKey('test')
                ->and($all['test'])->toBe('value');
        });

        it('can access SharedData instance via facade', function () {
            $shared = Accelade::shared();

            expect($shared)->toBeInstanceOf(\Accelade\Support\SharedData::class);
        });

        it('returns fluent interface from share', function () {
            $result = Accelade::share('key', 'value');

            expect($result)->toBeInstanceOf(\Accelade\Accelade::class);
        });
    });

    describe('scripts output', function () {
        it('includes shared data in scripts output', function () {
            Accelade::share('testKey', 'testValue');

            $scripts = Accelade::scripts();

            expect($scripts)->toContain('shared')
                ->and($scripts)->toContain('testKey')
                ->and($scripts)->toContain('testValue');
        });

        it('includes nested shared data in scripts output', function () {
            Accelade::share('user', [
                'name' => 'John',
                'email' => 'john@example.com',
            ]);

            $scripts = Accelade::scripts();

            expect($scripts)->toContain('John')
                ->and($scripts)->toContain('john@example.com');
        });

        it('includes shared key in config when no data', function () {
            // Clear any existing shared data
            Accelade::shared()->flush();

            $scripts = Accelade::scripts();

            // Check that shared is in the AcceladeConfig section (before the JS bundle)
            expect($scripts)->toContain('AcceladeConfig')
                ->and($scripts)->toContain('shared');
        });
    });

    describe('lazy shared data', function () {
        it('resolves closures when getting scripts', function () {
            $called = false;
            Accelade::share('lazy', function () use (&$called) {
                $called = true;

                return 'resolved';
            });

            expect($called)->toBeFalse();

            $scripts = Accelade::scripts();

            expect($called)->toBeTrue()
                ->and($scripts)->toContain('resolved');
        });
    });
});
