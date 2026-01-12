# Event Component (Laravel Echo)

Accelade provides real-time event handling through Laravel Echo integration. The Event component enables listening to broadcasted events and triggering client-side actions like redirects, page refreshes, and toast notifications.

## Requirements

The Event component requires Laravel Echo to be configured and available as `window.Echo`. Follow the [Laravel Broadcasting documentation](https://laravel.com/docs/broadcasting) to set up Echo with your preferred driver (Pusher, Ably, Soketi, etc.).

## Basic Usage

```blade
<x-accelade::event channel="orders" listen="OrderCreated">
    <p a-if="subscribed">Listening for new orders...</p>
    <p a-if="!subscribed">Not connected to channel.</p>
</x-accelade::event>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `channel` | string | null | The channel name to subscribe to |
| `private` | bool | false | Use a private (authenticated) channel |
| `presence` | bool | false | Use a presence channel |
| `listen` | string | '' | Comma-separated event names to listen for |
| `preserve-scroll` | bool | false | Preserve scroll position on refresh actions |

## Channel Types

### Public Channel

```blade
<x-accelade::event channel="announcements" listen="NewAnnouncement">
    <!-- Anyone can listen -->
</x-accelade::event>
```

### Private Channel

Private channels require authentication. Laravel will verify the user has permission to join.

```blade
<x-accelade::event
    channel="user.{{ auth()->id() }}"
    :private="true"
    listen="MessageReceived,NotificationReceived"
>
    <div a-show="events.length > 0">
        You have <span a-text="events.length"></span> new events
    </div>
</x-accelade::event>
```

### Presence Channel

Presence channels track who is currently subscribed.

```blade
<x-accelade::event
    channel="chat.room.1"
    :presence="true"
    listen="UserJoined,UserLeft,MessageSent"
>
    <!-- Track users in real-time -->
</x-accelade::event>
```

## Exposed State

The component exposes reactive state that you can use in your templates:

| State | Type | Description |
|-------|------|-------------|
| `subscribed` | boolean | Whether successfully subscribed to the channel |
| `events` | array | Array of received events with `name`, `data`, and `timestamp` |

```blade
<x-accelade::event channel="orders" listen="OrderCreated">
    <div a-show="subscribed" class="text-green-600">
        Connected to orders channel
    </div>

    <div a-show="!subscribed" class="text-amber-600">
        Connecting...
    </div>

    <ul>
        <template a-for="event in events">
            <li>
                <span a-text="event.name"></span> at
                <span a-text="new Date(event.timestamp).toLocaleString()"></span>
            </li>
        </template>
    </ul>
</x-accelade::event>
```

## Backend Event Actions

When broadcasting events, you can include Accelade action payloads that automatically trigger client-side behavior.

### Redirect on Event

Navigate the client to a new page when the event is received:

```php
use Accelade\Accelade;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderCreated implements ShouldBroadcast
{
    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->order->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return Accelade::redirectOnEvent(
            route('orders.show', $this->order)
        )->toArray();
    }
}
```

### Redirect to Named Route

```php
public function broadcastWith(): array
{
    return Accelade::redirectToRouteOnEvent(
        'orders.show',
        ['order' => $this->order->id]
    )->toArray();
}
```

### Refresh on Event

Reload the current page when the event is received:

```php
public function broadcastWith(): array
{
    return Accelade::refreshOnEvent()->toArray();
}
```

With scroll preservation (useful for dashboards):

```blade
<x-accelade::event
    channel="dashboard"
    listen="DataUpdated"
    :preserve-scroll="true"
/>
```

### Toast on Event

Show a notification when the event is received:

```php
public function broadcastWith(): array
{
    return Accelade::toastOnEvent(
        'New order received!',
        'success'
    )->toArray();
}
```

#### Toast Shortcuts

```php
// Success toast
Accelade::successOnEvent('Order completed!')

// Info toast
Accelade::infoOnEvent('New update available')

// Warning toast
Accelade::warningOnEvent('Low inventory alert')

// Danger toast
Accelade::dangerOnEvent('Payment failed')
```

#### Toast with Title

```php
Accelade::toastOnEvent('Your order has been shipped!')
    ->withTitle('Order Update')
    ->toArray();
```

### Including Custom Data

Add additional data alongside the action:

```php
public function broadcastWith(): array
{
    return Accelade::redirectOnEvent(route('orders.show', $this->order))
        ->with([
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'total' => $this->order->total,
        ])
        ->toArray();
}
```

## Custom Event Handling

For advanced use cases, listen to the `accelade:echo` custom event:

```blade
<x-accelade::event
    channel="orders"
    listen="OrderCreated"
    id="order-listener"
>
    <!-- Content -->
</x-accelade::event>

<script>
document.getElementById('order-listener').addEventListener('accelade:echo', (e) => {
    console.log('Event received:', e.detail.name, e.detail.data);

    // Custom handling
    if (e.detail.data.priority === 'high') {
        playNotificationSound();
    }
});
</script>
```

## Multiple Events

Listen to multiple events on the same channel:

```blade
<x-accelade::event
    channel="orders"
    listen="OrderCreated,OrderUpdated,OrderCancelled"
>
    <div a-for="event in events">
        <span a-show="event.name === 'OrderCreated'" class="text-green-600">
            New order created
        </span>
        <span a-show="event.name === 'OrderUpdated'" class="text-blue-600">
            Order updated
        </span>
        <span a-show="event.name === 'OrderCancelled'" class="text-red-600">
            Order cancelled
        </span>
    </div>
</x-accelade::event>
```

## Framework-Specific Attributes

The component works with all Accelade-supported frameworks:

```blade
{{-- Vanilla --}}
<x-accelade::event channel="orders" listen="OrderCreated">
    <p a-text="events.length"></p>
</x-accelade::event>

{{-- Vue --}}
<x-accelade::event channel="orders" listen="OrderCreated">
    <p v-text="events.length"></p>
</x-accelade::event>

{{-- React --}}
<x-accelade::event channel="orders" listen="OrderCreated">
    <p data-state-text="events.length"></p>
</x-accelade::event>
```

## Graceful Degradation

If Laravel Echo is not configured, the component:

- Renders without error
- Sets `subscribed` to `false`
- Logs a warning to the console
- All other Accelade features continue working

## Complete Example

### Backend Event

```php
<?php

namespace App\Events;

use App\Models\Order;
use Accelade\Accelade;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderStatusChanged implements ShouldBroadcast
{
    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->order->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        // Show toast and include order data
        return Accelade::successOnEvent("Order #{$this->order->id} is now {$this->newStatus}")
            ->withTitle('Order Status Update')
            ->with([
                'order_id' => $this->order->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
            ])
            ->toArray();
    }
}
```

### Frontend Component

```blade
<x-accelade::event
    channel="user.{{ auth()->id() }}"
    :private="true"
    listen="OrderStatusChanged"
    class="fixed bottom-4 right-4"
>
    {{-- Status indicator --}}
    <div class="flex items-center gap-2 p-3 bg-white rounded-lg shadow">
        <span
            a-class="{ 'bg-green-500': subscribed, 'bg-amber-500': !subscribed }"
            class="w-2 h-2 rounded-full"
        ></span>
        <span a-show="subscribed" class="text-sm text-gray-600">
            Real-time updates active
        </span>
        <span a-show="!subscribed" class="text-sm text-amber-600">
            Connecting...
        </span>
    </div>
</x-accelade::event>
```

## API Reference

### EventResponse Methods

| Method | Description |
|--------|-------------|
| `EventResponse::redirect($url)` | Create redirect action |
| `EventResponse::redirectToRoute($route, $params)` | Redirect to named route |
| `EventResponse::refresh()` | Create page refresh action |
| `EventResponse::toast($message, $type)` | Create toast notification |
| `EventResponse::success($message)` | Success toast shorthand |
| `EventResponse::info($message)` | Info toast shorthand |
| `EventResponse::warning($message)` | Warning toast shorthand |
| `EventResponse::danger($message)` | Danger toast shorthand |
| `->withTitle($title)` | Add title to toast |
| `->with($data)` | Add custom data to response |
| `->toArray()` | Convert to array for broadcasting |

### Accelade Facade Methods

| Method | Description |
|--------|-------------|
| `Accelade::redirectOnEvent($url)` | Create redirect action |
| `Accelade::redirectToRouteOnEvent($route, $params)` | Redirect to named route |
| `Accelade::refreshOnEvent()` | Create page refresh action |
| `Accelade::toastOnEvent($message, $type)` | Create toast notification |
| `Accelade::successOnEvent($message)` | Success toast shorthand |
| `Accelade::infoOnEvent($message)` | Info toast shorthand |
| `Accelade::warningOnEvent($message)` | Warning toast shorthand |
| `Accelade::dangerOnEvent($message)` | Danger toast shorthand |

## Next Steps

- [Notifications](notifications.md) - Toast notification system
- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Components](components.md) - Reactive components
