<?php

declare(strict_types=1);

use Accelade\Accelade;
use Accelade\Notification\NotificationManager;

describe('Service Provider Registration', function () {
    it('registers the accelade singleton', function () {
        expect(app()->bound('accelade'))->toBeTrue();
        expect(app('accelade'))->toBeInstanceOf(Accelade::class);
    });

    it('registers the notify singleton', function () {
        expect(app()->bound('accelade.notify'))->toBeTrue();
        expect(app('accelade.notify'))->toBeInstanceOf(NotificationManager::class);
    });

    it('returns same instance for accelade singleton', function () {
        $instance1 = app('accelade');
        $instance2 = app('accelade');

        expect($instance1)->toBe($instance2);
    });

    it('returns same instance for notify singleton', function () {
        $instance1 = app('accelade.notify');
        $instance2 = app('accelade.notify');

        expect($instance1)->toBe($instance2);
    });

    it('loads config from accelade.php', function () {
        expect(config('accelade'))->toBeArray();
        expect(config('accelade.framework'))->toBe('vanilla');
    });
});

describe('Blade Directives', function () {
    it('registers acceladeScripts directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('acceladeScripts');
    });

    it('registers acceladeStyles directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('acceladeStyles');
    });

    it('registers accelade directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('accelade');
    });

    it('registers endaccelade directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('endaccelade');
    });

    it('registers acceladeComponent directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('acceladeComponent');
    });

    it('registers endacceladeComponent directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('endacceladeComponent');
    });

    it('registers acceladeNotifications directive', function () {
        $directives = app('blade.compiler')->getCustomDirectives();

        expect($directives)->toHaveKey('acceladeNotifications');
    });
});

describe('View Loading', function () {
    it('loads views from accelade namespace', function () {
        // Check that accelade view namespace is registered
        $hints = app('view')->getFinder()->getHints();

        expect($hints)->toHaveKey('accelade');
    });

    it('can resolve accelade component views', function () {
        // The component namespace should be registered
        $namespaces = app('blade.compiler')->getClassComponentNamespaces();

        expect($namespaces)->toHaveKey('accelade');
        expect($namespaces['accelade'])->toBe('Accelade\\Components');
    });
});
