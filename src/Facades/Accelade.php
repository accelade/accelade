<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string scripts()
 * @method static string styles()
 * @method static void startComponent(array $state = [])
 * @method static string endComponent()
 * @method static string framework()
 *
 * @see \Accelade\Accelade
 */
class Accelade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade';
    }
}
