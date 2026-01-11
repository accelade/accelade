<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Notification\Notification;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Notification title(string $title)
 * @method static Notification success(string $title)
 * @method static Notification info(string $title)
 * @method static Notification warning(string $title)
 * @method static Notification danger(string $title)
 * @method static void push(Notification $notification)
 * @method static \Illuminate\Support\Collection all()
 * @method static \Illuminate\Support\Collection flush()
 * @method static void setDefault(\Closure $callback)
 *
 * @see \Accelade\Notification\NotificationManager
 */
class Notify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.notify';
    }
}
