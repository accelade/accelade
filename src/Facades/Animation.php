<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Animation\AnimationPreset;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Accelade\Animation\AnimationManager new(string $name, string $enter, string $enterFrom, string $enterTo, string $leave, string $leaveFrom, string $leaveTo)
 * @method static AnimationPreset|null get(string $name)
 * @method static bool has(string $name)
 * @method static array<string, AnimationPreset> all()
 * @method static array<string, array<string, string>> toArray()
 *
 * @see \Accelade\Animation\AnimationManager
 */
class Animation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.animation';
    }
}
