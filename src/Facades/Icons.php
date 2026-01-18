<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Icons\BladeIconsRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getSets()
 * @method static array|null getSet(string $nameOrPrefix)
 * @method static array getIcons(string $setName, int $offset = 0, int $limit = 50, ?string $search = null)
 * @method static array searchIcons(string $query, ?array $sets = null, int $offset = 0, int $limit = 50)
 * @method static string|null getSvg(string $iconName, string $class = '', array $attributes = [])
 * @method static array getSetsSummary()
 * @method static bool hasSet(string $nameOrPrefix)
 * @method static void clearCache()
 *
 * @see \Accelade\Icons\BladeIconsRegistry
 */
class Icons extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BladeIconsRegistry::class;
    }
}
