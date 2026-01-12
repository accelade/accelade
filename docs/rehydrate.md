# Rehydrate Component

The Accelade Rehydrate component enables selective section reloading in Blade templates without refreshing the entire page. Content can be updated via events or automatic polling.

## Basic Usage

### Event-Triggered Rehydration

Reload a section when an event is emitted:

```blade
{{-- List that updates when an item is added --}}
<x-accelade::rehydrate on="item-added">
    <ul>
        @foreach($items as $item)
            <li>{{ $item->name }}</li>
        @endforeach
    </ul>
</x-accelade::rehydrate>
```

Trigger the event from JavaScript:

```javascript
// After creating a new item
Accelade.emit('item-added');

// Or with event data
Accelade.emit('item-added', { id: 123 });
```

### Polling (Auto-Refresh)

Automatically refresh content at intervals:

```blade
{{-- Updates every 5 seconds --}}
<x-accelade::rehydrate :poll="5000">
    Current score: {{ $score }}
</x-accelade::rehydrate>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `on` | string\|array | null | Event name(s) that trigger rehydration |
| `poll` | int | null | Polling interval in milliseconds |
| `url` | string | current page | URL to fetch content from |
| `preserveScroll` | bool | true | Preserve scroll position after reload |

## Multiple Events

Listen to multiple events with a single component:

```blade
<x-accelade::rehydrate :on="['created', 'updated', 'deleted']">
    <table>
        @foreach($records as $record)
            <tr>{{ $record->name }}</tr>
        @endforeach
    </table>
</x-accelade::rehydrate>
```

## Integration with Forms

Emit events after form submission:

```blade
{{-- Form that emits event on success --}}
<form method="POST" action="/items">
    @csrf
    <input name="name" required>
    <button type="submit">Add Item</button>
</form>

{{-- List that reloads when item is added --}}
<x-accelade::rehydrate on="item-added">
    @foreach($items as $item)
        <div>{{ $item->name }}</div>
    @endforeach
</x-accelade::rehydrate>

<script>
// In your form submission handler
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    await fetch('/items', { method: 'POST', body: new FormData(form) });
    Accelade.emit('item-added');
});
</script>
```

## Custom URL

Fetch content from a different URL:

```blade
<x-accelade::rehydrate on="refresh" url="/partials/stats">
    @include('partials.stats')
</x-accelade::rehydrate>
```

## JavaScript API

### Emitting Events

```javascript
// Emit an event
Accelade.emit('event-name');

// With data payload
Accelade.emit('user-updated', { userId: 123 });

// Alternative via rehydrate namespace
Accelade.rehydrate.emit('event-name');
```

### Manual Rehydration

```javascript
// Reload a specific component by ID
Accelade.rehydrate.reload('component-id');

// Reload all rehydrate components
Accelade.rehydrate.reloadAll();
```

### Getting Instances

```javascript
// Get a specific instance
const instance = Accelade.rehydrate.get('component-id');

// Get all instances
const instances = Accelade.rehydrate.getAll();

// Trigger rehydration directly
if (instance) {
    await instance.rehydrate();
}
```

### Instance Properties

```javascript
const instance = Accelade.rehydrate.get('my-component');

instance.id;           // Component ID
instance.config;       // Configuration object
instance.element;      // DOM element
instance.isLoading;    // Whether currently loading

instance.rehydrate();  // Trigger rehydration
instance.startPolling(); // Start polling (if configured)
instance.stopPolling();  // Stop polling
instance.dispose();    // Clean up
```

## Events

Listen to rehydration events:

```javascript
// When rehydration completes
document.addEventListener('accelade:rehydrate', (e) => {
    console.log('Rehydrated:', e.detail.id, e.detail.success);
});

// Custom event emission
document.addEventListener('accelade:emit', (e) => {
    console.log('Event emitted:', e.detail.event, e.detail.data);
});
```

## Best Practices

### Place Conditionals Inside

Always place conditionals inside the rehydrate component:

```blade
{{-- Good: Conditional inside --}}
<x-accelade::rehydrate on="data-loaded">
    @if($hasData)
        <div>Data here</div>
    @else
        <div>No data</div>
    @endif
</x-accelade::rehydrate>

{{-- Bad: Wrapping with conditional --}}
@if($hasData)
    <x-accelade::rehydrate on="data-loaded">
        <div>Data here</div>
    </x-accelade::rehydrate>
@endif
```

### Use Unique IDs

For multiple rehydrate components, use explicit IDs:

```blade
<x-accelade::rehydrate on="users-updated" id="users-list">
    ...
</x-accelade::rehydrate>

<x-accelade::rehydrate on="orders-updated" id="orders-list">
    ...
</x-accelade::rehydrate>
```

### Combine with Forms

Use with the `stay` behavior on forms to keep the form visible:

```blade
<x-accelade::rehydrate on="team-member-added">
    <ul>
        @foreach($team->members as $member)
            <li>{{ $member->name }}</li>
        @endforeach
    </ul>
</x-accelade::rehydrate>

{{-- Form stays visible after submission --}}
<form @submit.prevent="submitForm" @success="Accelade.emit('team-member-added')">
    <input name="name">
    <button>Add Member</button>
</form>
```

## Complete Examples

### Live Dashboard Stats

```blade
{{-- Stats that refresh every 10 seconds --}}
<x-accelade::rehydrate :poll="10000" id="dashboard-stats">
    <div class="grid grid-cols-3 gap-4">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p>{{ $totalUsers }}</p>
        </div>
        <div class="stat-card">
            <h3>Active Sessions</h3>
            <p>{{ $activeSessions }}</p>
        </div>
        <div class="stat-card">
            <h3>Revenue Today</h3>
            <p>${{ $revenueToday }}</p>
        </div>
    </div>
</x-accelade::rehydrate>
```

### Notification Counter

```blade
{{-- Badge that updates when notifications arrive --}}
<x-accelade::rehydrate on="notification-received" id="notification-badge">
    @if($unreadCount > 0)
        <span class="badge">{{ $unreadCount }}</span>
    @endif
</x-accelade::rehydrate>

<script>
// When a WebSocket message arrives
Echo.channel('notifications')
    .listen('NewNotification', () => {
        Accelade.emit('notification-received');
    });
</script>
```

### Chat Messages

```blade
{{-- Messages list that polls for new messages --}}
<x-accelade::rehydrate :poll="2000" :on="['message-sent']" id="messages">
    <div class="messages">
        @foreach($messages as $message)
            <div class="message">
                <strong>{{ $message->user->name }}</strong>
                <p>{{ $message->content }}</p>
            </div>
        @endforeach
    </div>
</x-accelade::rehydrate>
```

## Styling

The component adds a loading class during rehydration:

```css
/* Style during rehydration */
.accelade-rehydrating {
    opacity: 0.5;
    pointer-events: none;
}

/* Or add a loading indicator */
.accelade-rehydrating::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}
```

## Next Steps

- [Components](components.md) - Reactive components
- [Event Component](event.md) - Laravel Echo integration
- [Defer Component](defer.md) - Async data loading
- [SPA Navigation](spa-navigation.md) - Client-side routing
