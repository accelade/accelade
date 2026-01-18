<?php

declare(strict_types=1);

namespace Accelade\Tests;

use Accelade\AcceladeServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            AcceladeServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Accelade' => \Accelade\Facades\Accelade::class,
            'Notify' => \Accelade\Facades\Notify::class,
            'SEO' => \Accelade\Facades\SEO::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('accelade.framework', 'vanilla');
    }
}
