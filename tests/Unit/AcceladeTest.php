<?php

declare(strict_types=1);

use Accelade\Accelade;

describe('Accelade Scripts', function () {
    it('returns script tag', function () {
        $accelade = app('accelade');
        $scripts = $accelade->scripts();

        expect($scripts)->toContain('<script');
        expect($scripts)->toContain('</script>');
    });

    it('includes framework configuration', function () {
        $accelade = app('accelade');
        $scripts = $accelade->scripts();

        expect($scripts)->toContain('AcceladeConfig');
        expect($scripts)->toContain('framework');
    });
});

describe('Accelade Styles', function () {
    it('returns style tag with CSS', function () {
        $accelade = app('accelade');
        $styles = $accelade->styles();

        expect($styles)->toContain('<style');
        expect($styles)->toContain('</style>');
    });

    it('includes notification styles', function () {
        $accelade = app('accelade');
        $styles = $accelade->styles();

        expect($styles)->toContain('accelade-notifications');
    });

    it('includes CSS variables for customization', function () {
        $accelade = app('accelade');
        $styles = $accelade->styles();

        expect($styles)->toContain('--accelade-notif');
    });
});

describe('Accelade Configuration', function () {
    it('uses configured framework', function () {
        config(['accelade.framework' => 'vue']);
        $accelade = new Accelade(app());

        expect(config('accelade.framework'))->toBe('vue');
    });

    it('uses configured prefix', function () {
        config(['accelade.prefix' => 'custom-prefix']);

        expect(config('accelade.prefix'))->toBe('custom-prefix');
    });
});
