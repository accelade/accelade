<?php

declare(strict_types=1);

namespace Accelade\Tests;

use Accelade\AcceladeServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
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
        $app['config']->set('accelade.framework', 'vanilla');
    }
}
