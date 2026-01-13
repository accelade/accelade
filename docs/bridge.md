# Bridge Components

Bridge Components provide two-way binding between PHP Blade Components and JavaScript. Access public PHP properties as reactive `props`, call PHP methods from the frontend, and receive responses like toasts, redirects, and events.

**No trait required!** Any Blade component works with Bridge out of the box.

## Quick Start

### 1. Create a Component (Plain PHP)

```php
<?php

namespace App\View\Components;

use Accelade\Bridge\BridgeResponse;
use Illuminate\View\Component;

class Counter extends Component
{
    // No trait needed - just define public properties and methods!

    public int $count = 0;
    public int $step = 1;
    public string $name = '';

    public function __construct(int $count = 0, int $step = 1, string $name = '')
    {
        $this->count = $count;
        $this->step = $step;
        $this->name = $name;
    }

    public function increment(): BridgeResponse
    {
        $this->count += $this->step;

        return BridgeResponse::success()
            ->toastSuccess('Incremented!', "Count is now {$this->count}");
    }

    public function decrement(): BridgeResponse
    {
        $this->count -= $this->step;

        return BridgeResponse::success()
            ->toastInfo('Decremented!');
    }

    public function reset(): BridgeResponse
    {
        $this->count = 0;

        return BridgeResponse::success()
            ->toastWarning('Counter reset');
    }

    public function render()
    {
        return view('components.counter');
    }
}
```

### 2. Use the Bridge Component

```blade
@php
    $counter = new App\View\Components\Counter(count: 10, step: 5, name: 'World');
@endphp

<x-accelade::bridge :component="$counter">
    <div class="counter">
        <div class="display" a-text="props.count">{{ $counter->count }}</div>

        <input type="text" a-model="props.name" placeholder="Your name" />
        <p a-show="props.name">Hello, <span a-text="props.name"></span>!</p>

        <input type="number" a-model="props.step" min="1" />

        <button @click="decrement()">-</button>
        <button @click="increment()">+</button>
        <button @click="reset()">Reset</button>
    </div>
</x-accelade::bridge>
```

## How It Works

1. **Props Access**: Public PHP properties are serialized and accessible via `props.propertyName` in templates
2. **Method Calls**: Public PHP methods can be called from `@click` handlers using `methodName()`
3. **AJAX Communication**: Method calls are sent to the server via AJAX and return `BridgeResponse` objects
4. **State Encryption**: Component state is encrypted for security between requests

## BridgeResponse API

Methods can return a `BridgeResponse` to control what happens after the call:

### Toast Notifications

```php
public function save(): BridgeResponse
{
    // Save logic...

    return BridgeResponse::success()
        ->toastSuccess('Saved!', 'Your changes have been saved.')
        // or
        ->toastInfo('Info', 'Optional body text')
        ->toastWarning('Warning!', 'Something to watch')
        ->toastDanger('Error!', 'Something went wrong');
}
```

### Redirects

```php
public function submit(): BridgeResponse
{
    // Submit logic...

    return BridgeResponse::success()
        ->redirectTo('/dashboard');
        // or
        ->redirectToRoute('orders.show', ['order' => $order->id]);
}
```

### Page Refresh

```php
public function reload(): BridgeResponse
{
    return BridgeResponse::success()
        ->refresh();
        // or preserve scroll position
        ->refresh(preserveScroll: true);
}
```

### Emit Events

```php
public function complete(): BridgeResponse
{
    return BridgeResponse::success()
        ->emit('task-completed', ['taskId' => $this->taskId])
        ->emit('refresh-list');
}
```

Listen for these events in other components:

```blade
<x-accelade::data :default="['completed' => false]">
    <div a-show="completed">Task completed!</div>
    <accelade:script>
        $on('task-completed', (data) => {
            $set('completed', true);
            console.log('Task ID:', data.taskId);
        });
    </accelade:script>
</x-accelade::data>
```

### Static Constructors

```php
// Success response with data
BridgeResponse::success(['key' => 'value']);

// Error response
BridgeResponse::error('Something went wrong');

// Redirect response
BridgeResponse::redirect('/path');

// Data response
BridgeResponse::data(['users' => $users]);
```

## Two-Way Binding

Use `a-model` (or your framework prefix) to bind to props:

```blade
{{-- Changes sync to PHP property --}}
<input a-model="props.name" />

{{-- Works with all input types --}}
<select a-model="props.status">
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
</select>

<input type="checkbox" a-model="props.enabled" />

<textarea a-model="props.description"></textarea>
```

## Method Arguments

Pass arguments to PHP methods:

```php
// PHP
public function setStatus(string $status): BridgeResponse
{
    $this->status = $status;

    return BridgeResponse::success()
        ->toastInfo("Status set to: {$status}");
}

public function addItem(string $name, int $quantity = 1): BridgeResponse
{
    // Add item logic...

    return BridgeResponse::success();
}
```

```blade
{{-- Blade template --}}
<button @click="setStatus('active')">Set Active</button>
<button @click="setStatus('inactive')">Set Inactive</button>

<button @click="addItem('Widget', 5)">Add 5 Widgets</button>
```

## Hiding Properties

Pass hidden properties to the Bridge component:

```blade
@php
    $user = new UserProfile(name: 'John', email: 'john@example.com', secretKey: 'abc123');
@endphp

{{-- Hide sensitive properties --}}
<x-accelade::bridge :component="$user" :hidden="['secretKey', 'internalId']">
    <input a-model="props.name" />
    <input a-model="props.email" />
</x-accelade::bridge>
```

## Working with Eloquent Models

```php
class EditUser extends Component
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function save(): BridgeResponse
    {
        $this->user->save();

        return BridgeResponse::success()
            ->toastSuccess('User saved!');
    }
}
```

> **Security**: Always use Eloquent's `$hidden` property on your models to prevent sensitive fields from being exposed:

```php
class User extends Model
{
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];
}
```

## JavaScript API

Access bridge instances from JavaScript:

```javascript
// Get a specific bridge instance
const bridge = window.Accelade.bridge.get('bridge-abc123');

// Call a method
const result = await bridge.call('increment');

// Access props
console.log(bridge.props.count);

// Get all bridge instances
const allBridges = window.Accelade.bridge.getAll();
```

## Framework Prefixes

Bridge components work with all Accelade framework prefixes:

| Framework | Prefix | Model | Text |
|-----------|--------|-------|------|
| Vanilla JS | `a-` | `a-model` | `a-text` |
| Vue | `v-` | `v-model` | `v-text` |
| React | `data-state-` | `data-state-model` | `data-state-text` |
| Svelte | `s-` | `s-model` | `s-text` |
| Angular | `ng-` | `ng-model` | `ng-text` |

```blade
{{-- Vue style --}}
<x-accelade::bridge :component="$counter">
    <div v-text="props.count"></div>
    <input v-model="props.name" />
    <button @click="increment()">+</button>
</x-accelade::bridge>

{{-- React style --}}
<x-accelade::bridge :component="$counter">
    <div data-state-text="props.count"></div>
    <input data-state-model="props.name" />
    <button @click="increment()">+</button>
</x-accelade::bridge>
```

## Renderless Components

Create components without a render method for maximum flexibility:

```php
class DataManager extends Component
{
    public array $items = [];

    public function addItem(string $name): BridgeResponse
    {
        $this->items[] = ['name' => $name, 'id' => uniqid()];

        return BridgeResponse::success();
    }

    public function removeItem(string $id): BridgeResponse
    {
        $this->items = array_filter($this->items, fn($item) => $item['id'] !== $id);

        return BridgeResponse::success();
    }

    // No render() method - use slot content
}
```

```blade
@php
    $manager = new App\View\Components\DataManager(items: $initialItems);
@endphp

<x-accelade::bridge :component="$manager">
    <input type="text" id="newItem" />
    <button @click="addItem(document.getElementById('newItem').value)">Add</button>

    <ul>
        <template a-for="item in props.items">
            <li>
                <span a-text="item.name"></span>
                <button @click="removeItem(item.id)">Remove</button>
            </li>
        </template>
    </ul>
</x-accelade::bridge>
```

## Security Considerations

1. **Public Properties**: All public properties are serialized and sent to the browser. Never expose sensitive data.
2. **Encrypted State**: Component state is encrypted using Laravel's encryption. The app key must be secure.
3. **Method Validation**: Only public methods defined in your component class can be called.
4. **CSRF Protection**: All AJAX requests include CSRF tokens automatically.
5. **Hidden Properties**: Use the `:hidden` prop to exclude sensitive properties.

## Comparison with Livewire

| Feature | Bridge Components | Livewire |
|---------|------------------|----------|
| Server Rendering | Yes (initial) | Yes (always) |
| JS Required | Yes | Optional |
| Build Step | None | None |
| Trait Required | **No** | Yes |
| Real-time Updates | Via events | Yes |
| File Uploads | Not built-in | Built-in |
| Polling | Manual | Built-in |
| Bundle Size | Included in Accelade | Separate |

Bridge Components are ideal when you want:
- Quick PHP method calls from the frontend
- Simpler state management than Livewire
- Integration with other Accelade features
- No additional dependencies
- **Works with any existing component** - no trait required!

## Next Steps

- [Data Component](data.md) - Client-side reactive data
- [Event Bus](event-bus.md) - Component communication
- [Notifications](notifications.md) - Toast notification system
