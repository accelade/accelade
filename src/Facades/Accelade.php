<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Support\SharedData;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string scripts()
 * @method static string styles()
 * @method static void startComponent(array $state = [])
 * @method static string endComponent()
 * @method static string framework()
 * @method static \Accelade\Accelade share(array|string $key, mixed $value = null)
 * @method static mixed getShared(string $key, mixed $default = null)
 * @method static array allShared()
 * @method static SharedData shared()
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
