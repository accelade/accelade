<?php

declare(strict_types=1);

namespace Accelade\Exceptions;

use Closure;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * ExceptionHandler - Custom exception handling for Accelade requests.
 *
 * Provides graceful error handling for AJAX requests from Bridge components,
 * Defer loading, and other Accelade features.
 */
class ExceptionHandler
{
    /**
     * Create an exception handler for Accelade requests.
     *
     * Usage in app/Exceptions/Handler.php:
     *
     * ```php
     * use Accelade\Exceptions\ExceptionHandler;
     *
     * public function register(): void
     * {
     *     $this->renderable(ExceptionHandler::handle($this));
     * }
     * ```
     *
     * With custom handler:
     *
     * ```php
     * $this->renderable(ExceptionHandler::handle($this, function ($e, $request) {
     *     if ($e instanceof CustomException) {
     *         return response()->json(['error' => 'Custom error'], 500);
     *     }
     * }));
     * ```
     */
    public static function handle(Handler $handler, ?Closure $customHandler = null): Closure
    {
        return function (Throwable $e, Request $request) use ($customHandler) {
            // Only handle Accelade AJAX requests
            if (! static::isAcceladeRequest($request)) {
                return null;
            }

            // Let custom handler process first
            if ($customHandler !== null) {
                $result = $customHandler($e, $request);
                if ($result !== null) {
                    return $result;
                }
            }

            // Handle validation exceptions specially
            if ($e instanceof ValidationException) {
                return static::handleValidationException($e, $request);
            }

            // Handle HTTP exceptions (404, 403, 419, etc.)
            if ($e instanceof HttpExceptionInterface) {
                return static::handleHttpException($e, $request);
            }

            // Handle generic exceptions
            return static::handleGenericException($e, $request);
        };
    }

    /**
     * Check if this is an Accelade AJAX request.
     */
    public static function isAcceladeRequest(Request $request): bool
    {
        // Check for XHR request
        if (! $request->ajax() && ! $request->wantsJson()) {
            return false;
        }

        // Check for Accelade-specific routes or headers
        $path = $request->path();

        // Accelade route patterns
        $acceladePatterns = [
            'accelade/',
            '_accelade/',
        ];

        foreach ($acceladePatterns as $pattern) {
            if (str_starts_with($path, $pattern)) {
                return true;
            }
        }

        // Check for X-Accelade header
        if ($request->hasHeader('X-Accelade')) {
            return true;
        }

        // Check for Accept header with accelade
        $accept = $request->header('Accept', '');
        if (str_contains($accept, 'accelade')) {
            return true;
        }

        return false;
    }

    /**
     * Handle validation exceptions.
     */
    protected static function handleValidationException(ValidationException $e, Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => $e->errors(),
            '_accelade' => [
                'type' => 'validation',
                'toast' => [
                    'type' => 'danger',
                    'title' => __('Validation Error'),
                    'body' => $e->getMessage(),
                ],
            ],
        ], 422);
    }

    /**
     * Handle HTTP exceptions (404, 403, 419, etc.).
     */
    protected static function handleHttpException(HttpExceptionInterface $e, Request $request): JsonResponse
    {
        $statusCode = $e->getStatusCode();
        $message = static::getHttpExceptionMessage($statusCode, $e);

        $response = [
            'success' => false,
            'message' => $message,
            '_accelade' => [
                'type' => 'http',
                'status' => $statusCode,
            ],
        ];

        // Handle specific status codes
        switch ($statusCode) {
            case 419: // CSRF token mismatch
                $response['_accelade']['action'] = 'refresh';
                $response['_accelade']['toast'] = [
                    'type' => 'warning',
                    'title' => __('Session Expired'),
                    'body' => __('Your session has expired. Please refresh the page.'),
                ];
                break;

            case 401: // Unauthorized
                $response['_accelade']['action'] = 'redirect';
                $response['_accelade']['url'] = route('login');
                $response['_accelade']['toast'] = [
                    'type' => 'info',
                    'title' => __('Authentication Required'),
                    'body' => __('Please log in to continue.'),
                ];
                break;

            case 403: // Forbidden
                $response['_accelade']['toast'] = [
                    'type' => 'danger',
                    'title' => __('Access Denied'),
                    'body' => $message,
                ];
                break;

            case 404: // Not Found
                $response['_accelade']['toast'] = [
                    'type' => 'warning',
                    'title' => __('Not Found'),
                    'body' => $message,
                ];
                break;

            case 429: // Too Many Requests
                $response['_accelade']['toast'] = [
                    'type' => 'warning',
                    'title' => __('Too Many Requests'),
                    'body' => __('Please slow down and try again later.'),
                ];
                break;

            case 500: // Server Error
            case 503: // Service Unavailable
            default:
                $response['_accelade']['toast'] = [
                    'type' => 'danger',
                    'title' => __('Server Error'),
                    'body' => App::environment('production')
                        ? __('An unexpected error occurred. Please try again.')
                        : $message,
                ];
                break;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Handle generic exceptions.
     */
    protected static function handleGenericException(Throwable $e, Request $request): JsonResponse
    {
        $isDebug = config('app.debug', false);

        $response = [
            'success' => false,
            'message' => $isDebug ? $e->getMessage() : __('An unexpected error occurred.'),
            '_accelade' => [
                'type' => 'exception',
                'toast' => [
                    'type' => 'danger',
                    'title' => __('Error'),
                    'body' => $isDebug ? $e->getMessage() : __('An unexpected error occurred. Please try again.'),
                ],
            ],
        ];

        // Include debug info in non-production
        if ($isDebug) {
            $response['_accelade']['debug'] = [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(10)->map(function ($frame) {
                    return [
                        'file' => $frame['file'] ?? null,
                        'line' => $frame['line'] ?? null,
                        'function' => $frame['function'] ?? null,
                        'class' => $frame['class'] ?? null,
                    ];
                })->all(),
            ];
        }

        return response()->json($response, 500);
    }

    /**
     * Get a human-readable message for HTTP status codes.
     */
    protected static function getHttpExceptionMessage(int $statusCode, HttpExceptionInterface $e): string
    {
        // Use the exception message if available
        if ($e->getMessage()) {
            return $e->getMessage();
        }

        // Default messages for common status codes
        return match ($statusCode) {
            400 => __('Bad Request'),
            401 => __('Unauthorized'),
            403 => __('Forbidden'),
            404 => __('Not Found'),
            405 => __('Method Not Allowed'),
            408 => __('Request Timeout'),
            419 => __('Page Expired'),
            422 => __('Unprocessable Entity'),
            429 => __('Too Many Requests'),
            500 => __('Internal Server Error'),
            502 => __('Bad Gateway'),
            503 => __('Service Unavailable'),
            504 => __('Gateway Timeout'),
            default => __('HTTP Error :code', ['code' => $statusCode]),
        };
    }
}
