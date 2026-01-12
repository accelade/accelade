<?php

declare(strict_types=1);

namespace Accelade\Http\Middleware;

use Accelade\Facades\Accelade;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareAcceladeData
{
    /**
     * Handle an incoming request.
     *
     * This middleware allows applications to define a callback that shares
     * data across all requests. Register this middleware in your application
     * and define the callback in a service provider.
     *
     * Example in AppServiceProvider:
     *
     *     Accelade::share('user', fn() => auth()->user()?->only('id', 'name', 'email'));
     *     Accelade::share('appName', config('app.name'));
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Share common data that's useful across all requests
        if (! Accelade::shared()->has('csrf_token')) {
            Accelade::share('csrf_token', fn () => csrf_token());
        }

        // Share flash data if enabled
        if (config('accelade.flash.enabled', true)) {
            Accelade::share('flash', fn () => $this->getFlashData());
        }

        return $next($request);
    }

    /**
     * Get flash data from session.
     *
     * @return array<string, mixed>
     */
    protected function getFlashData(): array
    {
        $flashData = [];
        $session = session();

        // Get allowed keys from config (null means all keys)
        $allowedKeys = config('accelade.flash.keys');

        // Collect flash data from session's old flash keys
        $oldFlashKeys = $session->get('_flash.old', []);
        foreach ($oldFlashKeys as $key) {
            if ($session->has($key)) {
                // If allowedKeys is set, filter by it
                if ($allowedKeys === null || in_array($key, $allowedKeys, true)) {
                    $flashData[$key] = $session->get($key);
                }
            }
        }

        // Also check common flash keys that might be in new flash
        $commonFlashKeys = ['message', 'success', 'error', 'warning', 'info', 'status', 'notification', 'alert'];

        // If allowedKeys is set, use only those
        $keysToCheck = $allowedKeys ?? $commonFlashKeys;

        foreach ($keysToCheck as $key) {
            if ($session->has($key) && ! isset($flashData[$key])) {
                $flashData[$key] = $session->get($key);
            }
        }

        return $flashData;
    }
}
