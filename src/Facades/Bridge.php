<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string $bridgeId, string $componentClass, array $props)
 * @method static array|null get(string $bridgeId)
 * @method static bool has(string $bridgeId)
 * @method static string createPayload(string $bridgeId, string $componentClass, array $props)
 * @method static array|null decodePayload(string $encrypted)
 * @method static \Illuminate\View\Component|null createInstance(array $payload)
 * @method static array all()
 * @method static void clear()
 *
 * @see \Accelade\Bridge\BridgeManager
 */
class Bridge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.bridge';
    }
}
