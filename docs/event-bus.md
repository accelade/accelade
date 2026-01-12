# Event Bus

Accelade includes a built-in event bus for decoupled communication between components. Components can emit and listen to events without needing direct references to each other.

## Quick Start

```blade
{{-- Component A: Emit an event --}}
<button @click="$emit('user-selected', { id: 123 })">
    Select User
</button>

{{-- Component B: Listen for the event --}}
<x-accelade::data :default="['userId' => null]">
    <div a-show="userId">Selected: <span a-text="userId"></span></div>
    <accelade:script>
        $on('user-selected', (data) => {
            $set('userId', data.id);
        });
    </accelade:script>
</x-accelade::data>
```

## API

### Global Methods

Access the event bus from anywhere via `window.Accelade`:

```javascript
// Emit an event with data
Accelade.emit('event-name', { foo: 'bar' });

// Listen for an event
const unsubscribe = Accelade.on('event-name', (data) => {
    console.log('Received:', data);
});

// Stop listening
unsubscribe();

// Listen once (auto-removes after first call)
Accelade.once('init-complete', () => {
    console.log('Initialization done!');
});

// Remove a specific listener
Accelade.off('event-name', callbackFunction);
```

### Component Context Methods

Inside Accelade components, use the `$` prefixed methods:

| Method | Description |
|--------|-------------|
| `$emit(event, data?)` | Emit an event with optional data |
| `$on(event, callback)` | Listen for an event, returns unsubscribe function |
| `$once(event, callback)` | Listen once, auto-removes after first call |
| `$off(event, callback)` | Remove a specific listener |

### Events Object

For advanced usage, access the full events API:

```javascript
// Full events API
Accelade.events.emit('event', data);
Accelade.events.on('event', callback);
Accelade.events.once('event', callback);
Accelade.events.off('event', callback);

// Additional methods
Accelade.events.clear('event');        // Remove all listeners for an event
Accelade.events.clear();               // Remove ALL listeners
Accelade.events.hasListeners('event'); // Check if event has listeners
Accelade.events.listenerCount('event'); // Get number of listeners
Accelade.events.eventNames();          // Get all registered event names
```

## Examples

### Form Validation Feedback

```blade
{{-- Form component --}}
<x-accelade::data :default="['email' => '']">
    <form @submit.prevent="validateAndSubmit()">
        <input type="email" a-model="email" />
        <button type="submit">Submit</button>
    </form>
    <accelade:script>
        function validateAndSubmit() {
            if (!email.includes('@')) {
                $emit('form-error', { field: 'email', message: 'Invalid email' });
                return;
            }
            $emit('form-success', { email });
        }
    </accelade:script>
</x-accelade::data>

{{-- Toast notification listener --}}
<x-accelade::data :default="['message' => '', 'type' => '']">
    <div a-show="message" a-class="{ 'bg-red-100': type === 'error', 'bg-green-100': type === 'success' }">
        <span a-text="message"></span>
    </div>
    <accelade:script>
        $on('form-error', (data) => {
            $set('message', data.message);
            $set('type', 'error');
        });

        $on('form-success', () => {
            $set('message', 'Form submitted successfully!');
            $set('type', 'success');
        });
    </accelade:script>
</x-accelade::data>
```

### Shopping Cart Updates

```blade
{{-- Product card --}}
<x-accelade::data :default="['product' => $product]">
    <div class="product-card">
        <h3 a-text="product.name"></h3>
        <button @click="$emit('add-to-cart', product)">
            Add to Cart
        </button>
    </div>
</x-accelade::data>

{{-- Cart counter in header --}}
<x-accelade::data :default="['count' => 0]">
    <span class="cart-count" a-text="count">0</span>
    <accelade:script>
        $on('add-to-cart', () => {
            $set('count', $get('count') + 1);
        });

        $on('remove-from-cart', () => {
            $set('count', Math.max(0, $get('count') - 1));
        });

        $on('clear-cart', () => {
            $set('count', 0);
        });
    </accelade:script>
</x-accelade::data>
```

### Modal Communication

```blade
{{-- Open modal button --}}
<button @click="$emit('open-modal', { type: 'confirm', title: 'Delete Item?' })">
    Delete
</button>

{{-- Modal component --}}
<x-accelade::data :default="['isOpen' => false, 'title' => '', 'type' => '']">
    <div a-show="isOpen" class="modal-overlay">
        <div class="modal">
            <h2 a-text="title"></h2>
            <button @click="$emit('modal-confirm'); $set('isOpen', false)">Confirm</button>
            <button @click="$emit('modal-cancel'); $set('isOpen', false)">Cancel</button>
        </div>
    </div>
    <accelade:script>
        $on('open-modal', (data) => {
            $set('isOpen', true);
            $set('title', data.title);
            $set('type', data.type);
        });

        $on('close-modal', () => {
            $set('isOpen', false);
        });
    </accelade:script>
</x-accelade::data>
```

### One-Time Initialization

```blade
<x-accelade::data :default="['ready' => false]">
    <div a-show="!ready">Loading...</div>
    <div a-show="ready">App is ready!</div>
    <accelade:script>
        // Only fires once
        $once('app-initialized', () => {
            $set('ready', true);
        });
    </accelade:script>
</x-accelade::data>

{{-- Somewhere else in your app --}}
<script>
    // After all components are loaded
    window.Accelade.emit('app-initialized');
</script>
```

### Cleanup on Component Destroy

```blade
<x-accelade::data :default="['messages' => []]">
    <ul>
        <template a-for="msg in messages">
            <li a-text="msg"></li>
        </template>
    </ul>
    <accelade:script>
        // Store unsubscribe function
        const unsubscribe = $on('new-message', (data) => {
            $set('messages', [...$get('messages'), data.text]);
        });

        // Clean up when component is removed
        // (This is automatically handled by Accelade's component lifecycle)
    </accelade:script>
</x-accelade::data>
```

## Using with JavaScript

```javascript
// In your app.js or any script
document.addEventListener('DOMContentLoaded', () => {
    // Subscribe to events
    window.Accelade.on('user-action', (data) => {
        analytics.track('user_action', data);
    });

    // Emit from anywhere
    document.querySelector('.special-button').addEventListener('click', () => {
        window.Accelade.emit('special-action', {
            timestamp: Date.now(),
            source: 'button'
        });
    });
});
```

## Best Practices

### 1. Use Descriptive Event Names

```javascript
// Good
$emit('user-profile-updated', { userId: 123 });
$emit('cart-item-added', { product, quantity });
$emit('checkout-completed', { orderId });

// Avoid
$emit('update');
$emit('click');
$emit('data');
```

### 2. Keep Payloads Simple

```javascript
// Good - simple, serializable data
$emit('user-selected', { id: 123, name: 'John' });

// Avoid - complex objects, functions
$emit('user-selected', userInstance); // May have methods, circular refs
```

### 3. Unsubscribe When Done

```javascript
// Store the unsubscribe function
const unsubscribe = $on('event', callback);

// Later, when no longer needed
unsubscribe();
```

### 4. Use `$once` for Initialization

```javascript
// Only need to know when app is ready once
$once('app-ready', () => {
    initializeFeature();
});
```

### 5. Namespace Events for Large Apps

```javascript
// Namespace by feature
$emit('auth:login-success', user);
$emit('auth:logout');
$emit('cart:item-added', item);
$emit('cart:cleared');
```

### 6. Use `$get()` in Callbacks

When accessing current state values inside event callbacks, always use `$get()` instead of the variable name directly. This is because callbacks execute later when the original scope is no longer available.

```javascript
// Good - use $get() to access current state
$on('increment', (data) => {
    $set('count', $get('count') + data.amount);
});

// Bad - will throw "count is not defined" error
$on('increment', (data) => {
    $set('count', count + data.amount);
});
```

## TypeScript Support

The event bus is fully typed:

```typescript
import { emit, on, once, off, EventCallback } from '@accelade/accelade';

interface UserData {
    id: number;
    name: string;
}

// Typed callback
const callback: EventCallback<UserData> = (data) => {
    console.log(data.id, data.name); // TypeScript knows the shape
};

on<UserData>('user-selected', callback);
emit<UserData>('user-selected', { id: 1, name: 'John' });
```

## Next Steps

- [Components](components.md) - Building reactive components
- [Rehydrate](rehydrate.md) - Server-triggered updates
- [Notifications](notifications.md) - Toast notification system
