# Flash Component (Session Flash Data)

Accelade provides seamless access to Laravel's session flash data through the Flash component. This enables displaying flash messages without full page reloads, perfect for SPA-like applications.

## Basic Usage

```blade
<x-accelade::flash>
    <div a-if="flash.has('success')" class="alert alert-success">
        <span a-text="flash.success"></span>
    </div>

    <div a-if="flash.has('error')" class="alert alert-danger">
        <span a-text="flash.error"></span>
    </div>
</x-accelade::flash>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `shared` | bool | true | Share flash data globally via `Accelade::share()` |

## The Flash Object

The component exposes a `flash` object with the following methods and properties:

### `flash.has(key)`

Check if a flash key exists and has a truthy value:

```blade
<x-accelade::flash>
    <div a-if="flash.has('message')">
        Message exists!
    </div>
</x-accelade::flash>
```

### `flash.key`

Access flash values directly by key:

```blade
<x-accelade::flash>
    <p a-text="flash.success"></p>
    <p a-text="flash.error"></p>
    <p a-text="flash.message"></p>
</x-accelade::flash>
```

### `flash.get(key, default)`

Get a flash value with an optional default:

```blade
<x-accelade::flash>
    <p a-text="flash.get('message', 'No message')"></p>
</x-accelade::flash>
```

### `flash.all()`

Get all flash data as an object:

```blade
<x-accelade::flash>
    <pre a-text="JSON.stringify(flash.all(), null, 2)"></pre>
</x-accelade::flash>
```

## Setting Flash Data

Flash data is set in your Laravel controllers using the standard session methods:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Create the order...
        $order = Order::create($request->validated());

        // Flash a success message
        session()->flash('success', 'Order created successfully!');

        return redirect()->route('orders.index');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        // Flash multiple messages
        session()->flash('message', 'Order has been deleted.');
        session()->flash('info', 'You can restore it within 30 days.');

        return redirect()->route('orders.index');
    }
}
```

You can also use the `with()` helper when redirecting:

```php
return redirect()->route('orders.index')
    ->with('success', 'Order created successfully!');
```

## Configuration

Configure flash data behavior in `config/accelade.php`:

```php
'flash' => [
    // Enable/disable automatic flash data sharing
    'enabled' => env('ACCELADE_FLASH_ENABLED', true),

    // Specific keys to share (null = all common keys)
    'keys' => null,
],
```

### Limiting Flash Keys

To only share specific flash keys:

```php
'flash' => [
    'enabled' => true,
    'keys' => ['success', 'error', 'warning', 'info', 'message'],
],
```

This is useful for security if you want to prevent accidentally exposing sensitive session data.

## Automatic Sharing

By default, flash data is automatically shared via the `ShareAcceladeData` middleware. This means flash data is available:

1. In the `<x-accelade::flash>` component
2. Via `Accelade::shared()->get('flash')` in views
3. In JavaScript via the shared data system

The middleware automatically collects common flash keys:
- `message`
- `success`
- `error`
- `warning`
- `info`
- `status`
- `notification`
- `alert`

## Framework-Specific Syntax

The Flash component works with all Accelade-supported frameworks:

### Vanilla (Default)

```blade
<x-accelade::flash>
    <div a-if="flash.has('success')">
        <p a-text="flash.success"></p>
    </div>
</x-accelade::flash>
```

### Vue

```blade
<x-accelade::flash>
    <div v-if="flash.has('success')">
        <p v-text="flash.success"></p>
    </div>
</x-accelade::flash>
```

### React

```blade
<x-accelade::flash>
    <div data-state-if="flash.has('success')">
        <p data-state-text="flash.success"></p>
    </div>
</x-accelade::flash>
```

## Notification-Style Flash Messages

Position flash messages as toast notifications:

```blade
<x-accelade::flash class="fixed top-4 right-4 z-50 space-y-2">
    <div
        a-show="flash.has('success')"
        class="p-4 bg-green-100 border border-green-400 rounded-lg shadow-lg"
    >
        <p a-text="flash.success" class="text-green-700"></p>
    </div>

    <div
        a-show="flash.has('error')"
        class="p-4 bg-red-100 border border-red-400 rounded-lg shadow-lg"
    >
        <p a-text="flash.error" class="text-red-700"></p>
    </div>
</x-accelade::flash>
```

## Combining with Custom Scripts

Add custom behavior like auto-dismiss:

```blade
<x-accelade::flash>
    <div a-show="flash.has('message')" class="alert">
        <span a-text="flash.message"></span>
        <button @click="dismiss()">Dismiss</button>
    </div>

    <accelade:script>
        return {
            dismiss() {
                // Custom dismiss logic
                $set('flash', {});
            }
        };
    </accelade:script>
</x-accelade::flash>
```

## SPA Navigation

Flash data works seamlessly with SPA navigation. When navigating between pages:

1. The server sets flash data
2. Accelade fetches the page via AJAX
3. Flash data is included in the response
4. The Flash component reactively updates

No page reload required.

## Disabling Shared Mode

If you don't want flash data shared globally:

```blade
<x-accelade::flash :shared="false">
    {{-- Flash data only available within this component --}}
    <p a-text="flash.message"></p>
</x-accelade::flash>
```

## Complete Example

### Controller

```php
<?php

namespace App\Http\Controllers;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $task = Task::create($request->validated());

        session()->flash('success', "Task '{$task->title}' created!");
        session()->flash('info', 'You can edit it anytime.');

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        session()->flash('message', 'Task deleted.');

        return redirect()->route('tasks.index');
    }
}
```

### Blade Template

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Tasks</title>
    @acceladeStyles
</head>
<body>
    {{-- Flash message area --}}
    <x-accelade::flash class="mb-4">
        <div a-if="flash.has('success')"
             class="p-4 bg-green-100 text-green-700 rounded-lg">
            <strong>Success!</strong>
            <span a-text="flash.success"></span>
        </div>

        <div a-if="flash.has('info')"
             class="p-4 bg-blue-100 text-blue-700 rounded-lg mt-2">
            <span a-text="flash.info"></span>
        </div>

        <div a-if="flash.has('message')"
             class="p-4 bg-gray-100 text-gray-700 rounded-lg">
            <span a-text="flash.message"></span>
        </div>
    </x-accelade::flash>

    {{-- Page content --}}
    <div class="tasks">
        @foreach($tasks as $task)
            <div class="task">{{ $task->title }}</div>
        @endforeach
    </div>

    @acceladeScripts
</body>
</html>
```

## Next Steps

- [Notifications](notifications.md) - Toast notification system
- [Components](components.md) - Reactive components
- [SPA Navigation](spa-navigation.md) - Client-side routing
