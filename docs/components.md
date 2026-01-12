# Components

Accelade components bring reactivity to your Blade templates. This guide covers creating and using reactive components.

## Basic Component

Use the `@accelade` directive to create a reactive block:

```blade
@accelade(['count' => 0, 'name' => 'World'])
    <div>
        <h1>Hello, <span a-text="name">World</span>!</h1>
        <p>Count: <span a-text="count">0</span></p>
        <input a-model="name" placeholder="Enter name">
        <button @click="$set('count', count + 1)">Increment</button>
    </div>
@endaccelade
```

The array passed to `@accelade` defines the initial state.

## Binding Directives

### Text Binding (`a-text`)

Binds element's text content to a state property:

```blade
<span a-text="message">Default text</span>
<p a-text="user.name">Guest</p>
<div a-text="items.length + ' items'">0 items</div>
```

### HTML Binding (`a-html`)

Binds raw HTML content (use with caution):

```blade
<div a-html="htmlContent"></div>
```

### Show/Hide (`a-show`)

Toggles element visibility using CSS display:

```blade
<div a-show="isVisible">This can be toggled</div>
<div a-show="count > 0">Count is positive</div>
<div a-show="!isEmpty">Has content</div>
```

### Conditional Rendering (`a-if`)

Removes/adds element from DOM based on condition:

```blade
<div a-if="isLoggedIn">Welcome back!</div>
<div a-if="items.length === 0">No items found</div>
```

### Two-Way Binding (`a-model`)

Creates two-way data binding for form inputs:

```blade
{{-- Text input --}}
<input a-model="username" type="text">

{{-- Textarea --}}
<textarea a-model="description"></textarea>

{{-- Checkbox --}}
<input a-model="isActive" type="checkbox">

{{-- Select --}}
<select a-model="selectedOption">
    <option value="a">Option A</option>
    <option value="b">Option B</option>
</select>

{{-- Radio buttons --}}
<input a-model="gender" type="radio" value="male"> Male
<input a-model="gender" type="radio" value="female"> Female
```

### Event Handling (`@event`)

Binds event listeners:

```blade
{{-- Click events --}}
<button @click="handleClick()">Click me</button>
<button @click="$set('count', count + 1)">Increment</button>

{{-- Form events --}}
<form @submit.prevent="handleSubmit()">
<input @input="validateField()">
<input @change="onSelectionChange()">

{{-- Keyboard events --}}
<input @keyup.enter="submitForm()">
<input @keydown.escape="cancel()">

{{-- Mouse events --}}
<div @mouseenter="showTooltip()">
<div @mouseleave="hideTooltip()">
```

### Dynamic Classes (`a-class`)

Conditionally applies CSS classes:

```blade
<div a-class="{ active: isActive, disabled: isDisabled }">
    Conditional classes
</div>

<button a-class="{ 'btn-primary': isPrimary, 'btn-lg': isLarge }">
    Button
</button>
```

### Dynamic Styles (`a-style`)

Applies inline styles dynamically:

```blade
<div a-style="{ color: textColor, fontSize: size + 'px' }">
    Dynamic styles
</div>
```

### Server Sync (`a-sync`)

Syncs property changes with the Laravel backend:

```blade
@accelade(['count' => 0])
    <div a-sync="count">
        <span a-text="count">0</span>
        <button @click="$set('count', count + 1)">+1</button>
    </div>
@endaccelade
```

When `count` changes, it's automatically persisted to the server.

## Built-in Functions

Inside `@accelade` blocks, these functions are available:

### `$get(key)`

Get a state value:

```blade
<button @click="alert($get('name'))">Show Name</button>
```

### `$set(key, value)`

Set a state value:

```blade
<button @click="$set('count', 0)">Reset</button>
<button @click="$set('items', [...items, newItem])">Add Item</button>
```

### `$toggle(key)`

Toggle a boolean value:

```blade
<button @click="$toggle('isOpen')">Toggle Menu</button>
```

### `$reset(key)`

Reset a property to its initial value:

```blade
<button @click="$reset('count')">Reset Count</button>
<button @click="$reset()">Reset All</button>
```

## Custom Scripts

Define custom functions using `<accelade:script>`:

```blade
@accelade(['count' => 0, 'step' => 1])
    <div>
        <span a-text="count">0</span>
        <button @click="increment()">+</button>
        <button @click="decrement()">-</button>
        <button @click="double()">Double</button>
    </div>

    <accelade:script>
        return {
            increment() {
                $set('count', $get('count') + $get('step'));
            },
            decrement() {
                $set('count', $get('count') - $get('step'));
            },
            double() {
                $set('count', $get('count') * 2);
            }
        };
    </accelade:script>
@endaccelade
```

## Blade Components

Create reusable components as Blade component classes:

### Counter Component

```php
// app/View/Components/Counter.php
namespace App\View\Components;

use Accelade\Components\AcceladeComponent;

class Counter extends AcceladeComponent
{
    public int $initialCount;
    public ?string $sync;

    public function __construct(int $initialCount = 0, ?string $sync = null)
    {
        $this->initialCount = $initialCount;
        $this->sync = $sync;
    }

    public function render()
    {
        return view('components.counter');
    }
}
```

```blade
{{-- resources/views/components/counter.blade.php --}}
@accelade(['count' => $initialCount])
    <div @if($sync) a-sync="{{ $sync }}" @endif>
        <span a-text="count">{{ $initialCount }}</span>
        <button @click="$set('count', count + 1)">+</button>
        <button @click="$set('count', count - 1)">-</button>
    </div>
@endaccelade
```

Usage:

```blade
<x-counter :initial-count="5" />
<x-counter :initial-count="10" sync="count" />
```

## Built-in Components

Accelade includes pre-built components:

### Link Component

Enhanced navigation with HTTP methods, confirmation dialogs, and SPA routing:

```blade
{{-- Basic SPA navigation --}}
<x-accelade::link href="/dashboard">Dashboard</x-accelade::link>

{{-- With navigation options --}}
<x-accelade::link
    href="/settings"
    class="nav-link"
    :preserve-scroll="true"
    :prefetch="true"
>
    Settings
</x-accelade::link>

{{-- HTTP methods for form-like submissions --}}
<x-accelade::link
    href="/api/items"
    method="POST"
    :data="['name' => 'New Item', 'status' => 'active']"
>
    Create Item
</x-accelade::link>

{{-- DELETE with confirmation dialog --}}
<x-accelade::link
    href="/api/items/123"
    method="DELETE"
    confirm-text="Delete this item permanently?"
    confirm-button="Delete"
    :confirm-danger="true"
>
    Delete Item
</x-accelade::link>

{{-- External link --}}
<x-accelade::link href="https://example.com" :away="true">
    Visit External Site
</x-accelade::link>

{{-- Fully customized confirmation --}}
<x-accelade::link
    href="/account/delete"
    confirm-title="Delete Account"
    confirm-text="This action cannot be undone."
    confirm-button="Yes, delete"
    cancel-button="No, keep my account"
    :confirm-danger="true"
>
    Delete Account
</x-accelade::link>
```

The Link component supports: GET, POST, PUT, PATCH, DELETE methods, request data and headers, confirmation dialogs, prefetching, scroll/state preservation, and history replacement. See [Link Component](link.md) for full documentation.

### Modal Component

Modal dialogs, slideover panels, and bottom sheets with async content loading:

```blade
{{-- Pre-loaded modal with hash link --}}
<x-accelade::link href="#my-modal">Open Modal</x-accelade::link>

<x-accelade::modal name="my-modal">
    <h2>Welcome!</h2>
    <p>Modal content here...</p>
    <button data-modal-close>Close</button>
</x-accelade::modal>

{{-- Async modal (load from URL) --}}
<x-accelade::link href="/users/create" :modal="true">
    Create User
</x-accelade::link>

{{-- Slideover panel --}}
<x-accelade::link href="#settings">Settings</x-accelade::link>

<x-accelade::modal name="settings" :slideover="true" slideover-position="right">
    <nav>Settings navigation...</nav>
</x-accelade::modal>

{{-- Bottom sheet (mobile-friendly) --}}
<x-accelade::link href="#actions">Actions</x-accelade::link>

<x-accelade::modal name="actions" :bottom-sheet="true">
    <button>Option 1</button>
    <button>Option 2</button>
    <button data-modal-close>Cancel</button>
</x-accelade::modal>

{{-- Custom size and position --}}
<x-accelade::modal
    name="large-dialog"
    max-width="4xl"
    position="top"
    :close-explicitly="true"
>
    <p>Important content...</p>
    <button data-modal-close>I Understand</button>
</x-accelade::modal>
```

The Modal component supports: pre-loaded content, async loading from URLs, slideover panels (left/right), bottom sheets for mobile, customizable sizes (sm to 7xl), vertical positioning (top/center/bottom), explicit close mode, and JavaScript API. See [Modal Component](modal.md) for full documentation.

### Counter Component

```blade
<x-accelade::counter :initial-count="0" />
<x-accelade::counter :initial-count="10" sync="count" framework="vue" />
```

### Event Component (Laravel Echo)

Listen to Laravel Echo broadcast events in real-time:

```blade
{{-- Basic event listener --}}
<x-accelade::event channel="orders" listen="OrderCreated">
    <p a-if="subscribed">Listening for orders...</p>
    <p a-if="!subscribed">Connecting...</p>
</x-accelade::event>

{{-- Private channel --}}
<x-accelade::event
    channel="user.{{ auth()->id() }}"
    :private="true"
    listen="MessageReceived"
>
    <span a-text="events.length"></span> new messages
</x-accelade::event>

{{-- Auto-refresh on event --}}
<x-accelade::event
    channel="dashboard"
    listen="DataUpdated"
    :preserve-scroll="true"
/>
```

The component exposes `subscribed` (boolean) and `events` (array) state. See [Event Component](event.md) for full documentation.

### Flash Component (Session Flash Data)

Access Laravel's session flash data in your templates:

```blade
{{-- Basic flash message display --}}
<x-accelade::flash>
    <div a-if="flash.has('success')" class="alert alert-success">
        <span a-text="flash.success"></span>
    </div>

    <div a-if="flash.has('error')" class="alert alert-danger">
        <span a-text="flash.error"></span>
    </div>
</x-accelade::flash>

{{-- Notification-style positioning --}}
<x-accelade::flash class="fixed top-4 right-4 z-50">
    <div a-show="flash.has('message')" class="p-4 bg-white shadow-lg rounded">
        <p a-text="flash.message"></p>
    </div>
</x-accelade::flash>
```

The component exposes a `flash` object with `.has()` method and direct property access. See [Flash Component](flash.md) for full documentation.

### Rehydrate Component (Selective Reloading)

Reload specific sections without full page refresh:

```blade
{{-- Event-triggered reload --}}
<x-accelade::rehydrate on="item-created">
    <ul>
        @foreach($items as $item)
            <li>{{ $item->name }}</li>
        @endforeach
    </ul>
</x-accelade::rehydrate>

{{-- Emit event to trigger reload --}}
<script>
    Accelade.emit('item-created');
</script>

{{-- Multiple events --}}
<x-accelade::rehydrate :on="['created', 'updated', 'deleted']">
    ...
</x-accelade::rehydrate>

{{-- Auto-polling every 5 seconds --}}
<x-accelade::rehydrate :poll="5000">
    Current score: {{ $score }}
</x-accelade::rehydrate>
```

The component supports event-triggered reloading, automatic polling, and JavaScript API for manual control. See [Rehydrate Component](rehydrate.md) for full documentation.

### State Component (Unified Errors, Flash & Shared)

Unified access to validation errors, flash messages, and shared data:

```blade
{{-- Basic validation error display --}}
<x-accelade::state>
    <div a-show="state.hasErrors" class="alert alert-danger">
        Please fix the errors below.
    </div>

    <div a-show="hasError('email')" class="text-red-500">
        <span a-text="getError('email')"></span>
    </div>
</x-accelade::state>

{{-- Flash message display --}}
<x-accelade::state>
    <div a-show="hasFlash('success')" class="alert alert-success">
        <span a-text="getFlash('success')"></span>
    </div>
</x-accelade::state>

{{-- Shared data access --}}
<x-accelade::state>
    <div a-show="hasShared('user')">
        Welcome, <span a-text="getShared('user.name')"></span>!
    </div>
</x-accelade::state>
```

The component exposes `state` object with errors/flash/shared, plus helper methods: `hasError()`, `getError()`, `getErrors()`, `hasFlash()`, `getFlash()`, `hasShared()`, `getShared()`. See [State Component](state.md) for full documentation.

### Teleport Component (DOM Relocation)

Relocate template sections to different DOM nodes while preserving reactivity:

```blade
{{-- Content teleports to footer --}}
<x-accelade::teleport to="#footer">
    <p>This content appears in the footer</p>
</x-accelade::teleport>

<div id="footer"></div>

{{-- Reactive content in teleport --}}
<div data-accelade data-accelade-state='{"search": ""}'>
    <input a-model="search" placeholder="Search...">

    <x-accelade::teleport to="#search-preview">
        <p>Searching for: <span a-text="search"></span></p>
    </x-accelade::teleport>
</div>

<div id="search-preview"></div>

{{-- Disabled teleport (stays in place) --}}
<x-accelade::teleport to="#target" :disabled="true">
    <p>Content stays here</p>
</x-accelade::teleport>
```

The component accepts CSS selectors and maintains parent component reactivity. Useful for modals, notifications, and cross-layout content. See [Teleport Component](teleport.md) for full documentation.

### Toggle Component (Boolean State)

Simplified boolean state management for show/hide toggles and flags:

```blade
{{-- Basic toggle --}}
<x-accelade::toggle>
    <button @click.prevent="toggle()">Show Content</button>
    <div a-show="toggled">
        <p>Toggled content!</p>
        <button @click.prevent="setToggle(false)">Hide</button>
    </div>
</x-accelade::toggle>

{{-- Start with toggled = true --}}
<x-accelade::toggle :data="true">
    <div a-show="toggled">Visible by default</div>
</x-accelade::toggle>

{{-- Multiple named toggles --}}
<x-accelade::toggle data="isCompany, hasVatNumber">
    <button @click.prevent="toggle('isCompany')">Company Mode</button>
    <div a-show="isCompany">Company fields...</div>

    <button @click.prevent="setToggle('hasVatNumber', true)">Enable VAT</button>
    <div a-show="hasVatNumber">VAT fields...</div>
</x-accelade::toggle>
```

The Toggle component exposes `toggle()`, `setToggle()`, and `toggled` (or named keys). Perfect for accordions, dropdowns, and conditional form fields. See [Toggle Component](toggle.md) for full documentation.

## Nested Components

Components can be nested:

```blade
@accelade(['parent' => 'value'])
    <div>
        <p a-text="parent">Parent value</p>

        @accelade(['child' => 'nested'])
            <div>
                <p a-text="child">Child value</p>
            </div>
        @endaccelade
    </div>
@endaccelade
```

Each nested component has its own isolated state.

## Component IDs

Each component gets a unique ID. You can specify a custom ID:

```blade
@accelade(['count' => 0], 'my-counter')
    {{-- Component content --}}
@endaccelade
```

Access the component by ID in JavaScript:

```javascript
const component = window.Accelade.getComponent('my-counter');
component.setState('count', 5);
```

## Best Practices

1. **Keep State Minimal** - Only include state that needs to be reactive
2. **Use Server Sync Sparingly** - Only sync what needs to persist
3. **Prefer Blade Components** - Extract reusable logic into components
4. **Escape User Input** - Always escape when using `a-html`
5. **Name Components** - Use meaningful IDs for debugging

## Next Steps

- [Frameworks](frameworks.md) - Framework-specific syntax
- [Link Component](link.md) - Enhanced navigation
- [Modal Component](modal.md) - Dialogs and slideovers
- [Event Component](event.md) - Laravel Echo integration
- [Flash Component](flash.md) - Session flash data
- [Rehydrate Component](rehydrate.md) - Selective section reloading
- [State Component](state.md) - Unified errors, flash & shared data
- [Teleport Component](teleport.md) - DOM relocation
- [Toggle Component](toggle.md) - Boolean state management
- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Notifications](notifications.md) - Toast notifications
