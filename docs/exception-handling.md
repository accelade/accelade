# Exception Handling

Accelade provides custom exception handling for AJAX requests, ensuring graceful error handling with toast notifications, redirects, and proper error responses.

## Quick Start

Register the Accelade exception handler in your `app/Exceptions/Handler.php`:

```php
<?php

namespace App\Exceptions;

use Accelade\Facades\Accelade;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(Accelade::exceptionHandler($this));
    }
}
```

That's it! Accelade will now handle exceptions for all AJAX requests automatically.

## How It Works

### Server-Side

When an exception occurs during an Accelade AJAX request (Bridge calls, Defer loading, etc.), the exception handler:

1. Detects if the request is an Accelade request (XHR with Accelade routes or headers)
2. Converts the exception to a JSON response with:
   - Error message
   - Toast notification configuration
   - Optional action (redirect, refresh)
   - Debug info (in development mode)

### Client-Side

The JavaScript error handler:

1. Receives the error response
2. Shows a toast notification
3. Executes any actions (redirect, refresh)
4. Logs errors to the console (configurable)

## Custom Exception Handling

Handle specific exceptions differently by passing a closure:

```php
$this->renderable(Accelade::exceptionHandler($this, function ($e, $request) {
    // Handle CSRF token mismatch with custom redirect
    if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
        if ($e->getStatusCode() === 419) {
            return redirect()->route('login', ['reason' => 'timeout']);
        }
    }

    // Handle custom exceptions
    if ($e instanceof \App\Exceptions\PaymentException) {
        return response()->json([
            'success' => false,
            'message' => 'Payment failed',
            '_accelade' => [
                'type' => 'payment',
                'toast' => [
                    'type' => 'danger',
                    'title' => 'Payment Error',
                    'body' => $e->getMessage(),
                ],
            ],
        ], 402);
    }

    // Return null to let Accelade handle other exceptions
    return null;
}));
```

## Error Response Format

Accelade error responses follow this structure:

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    },
    "_accelade": {
        "type": "validation|http|exception",
        "status": 422,
        "action": "refresh|redirect",
        "url": "/redirect/url",
        "toast": {
            "type": "danger",
            "title": "Error Title",
            "body": "Error details"
        },
        "debug": {
            "exception": "RuntimeException",
            "message": "Detailed error message",
            "file": "/path/to/file.php",
            "line": 42,
            "trace": [...]
        }
    }
}
```

## HTTP Status Code Handling

Different HTTP status codes trigger different behaviors:

| Status | Behavior |
|--------|----------|
| 401 | Redirect to login page |
| 403 | Show "Access Denied" toast |
| 404 | Show "Not Found" toast |
| 419 | Refresh page (CSRF expired) |
| 422 | Show validation errors |
| 429 | Show "Too Many Requests" toast |
| 500+ | Show "Server Error" toast |

## Configuration

Configure error handling behavior in `config/accelade.php`:

```php
'errors' => [
    // In production, suppress JS errors that may cause blank pages
    'suppress_errors' => env('ACCELADE_SUPPRESS_ERRORS', env('APP_ENV') === 'production'),

    // Show toast notifications for errors
    'show_toasts' => env('ACCELADE_ERROR_TOASTS', true),

    // Log errors to browser console
    'log_errors' => env('ACCELADE_LOG_ERRORS', true),

    // Include debug information (uses APP_DEBUG)
    'debug' => env('APP_DEBUG', false),
],
```

### suppress_errors

When `true`, JavaScript errors that might cause blank pages are caught and shown as toast notifications instead. This is enabled by default in production.

### show_toasts

When `true`, error toast notifications are automatically shown for AJAX failures. Users see friendly error messages instead of silent failures.

### log_errors

When `true`, errors are logged to the browser console for debugging. Useful during development.

### debug

When `true`, detailed error information (exception class, file, line, trace) is included in the response. This uses Laravel's `APP_DEBUG` setting by default.

## JavaScript API

Access error handling from JavaScript:

```javascript
// Initialize with custom config
Accelade.errors.init({
    suppressErrors: true,
    showToasts: true,
    logErrors: true,
    debug: false,
});

// Handle a generic error
Accelade.errors.handle(error, 'Context');

// Handle an AJAX error response
Accelade.errors.handleAjax(response, statusCode);

// Access the full ErrorHandler module
const ErrorHandler = Accelade.errors.handler;
```

## Validation Errors

Validation errors are handled specially:

```php
// In your controller
public function store(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'name' => 'required|min:3',
    ]);

    // ... handle valid request
}
```

When validation fails, Accelade automatically:

1. Returns a 422 response
2. Shows a "Validation Error" toast
3. Includes the error messages in the response

You can access validation errors in JavaScript:

```javascript
const result = await bridge.call('store', formData);

if (!result.success && result.errors) {
    // result.errors contains validation error messages
    console.log(result.errors.email); // ['The email field is required.']
}
```

## Network Error Handling

Network errors (offline, timeout, etc.) are handled gracefully:

- **Offline**: Shows "No Connection" toast
- **Timeout/Abort**: Shows "Request Cancelled" toast
- **Network Error**: Shows "Network Error" toast

## Security Considerations

1. **Debug Info**: Debug information is only included when `APP_DEBUG=true`. Never enable debug mode in production.

2. **Error Messages**: In production, generic error messages are shown to users. Detailed messages are only shown in development.

3. **Stack Traces**: Stack traces are truncated to 10 frames to prevent exposing too much application structure.

## Comparison with Splade

| Feature | Accelade | Splade |
|---------|----------|--------|
| Exception Handler | `Accelade::exceptionHandler()` | `SpladeCore::exceptionHandler()` |
| Custom Handler | Closure as 2nd argument | Closure as 2nd argument |
| Error Suppression | `suppress_errors` config | `suppress_compile_errors` config |
| Toast Notifications | Built-in | Built-in |
| Debug Mode | Uses `APP_DEBUG` | Uses `APP_DEBUG` |

## Next Steps

- [Bridge Components](bridge.md) - PHP/JS two-way binding
- [Notifications](notifications.md) - Toast notification system
- [Defer Component](defer.md) - Lazy loading content
