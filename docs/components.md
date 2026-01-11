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
        <button a-on:click="$set('count', count + 1)">Increment</button>
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

### Event Handling (`a-on:event`)

Binds event listeners:

```blade
{{-- Click events --}}
<button a-on:click="handleClick()">Click me</button>
<button a-on:click="$set('count', count + 1)">Increment</button>

{{-- Form events --}}
<form a-on:submit.prevent="handleSubmit()">
<input a-on:input="validateField()">
<input a-on:change="onSelectionChange()">

{{-- Keyboard events --}}
<input a-on:keyup.enter="submitForm()">
<input a-on:keydown.escape="cancel()">

{{-- Mouse events --}}
<div a-on:mouseenter="showTooltip()">
<div a-on:mouseleave="hideTooltip()">
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
        <button a-on:click="$set('count', count + 1)">+1</button>
    </div>
@endaccelade
```

When `count` changes, it's automatically persisted to the server.

## Built-in Functions

Inside `@accelade` blocks, these functions are available:

### `$get(key)`

Get a state value:

```blade
<button a-on:click="alert($get('name'))">Show Name</button>
```

### `$set(key, value)`

Set a state value:

```blade
<button a-on:click="$set('count', 0)">Reset</button>
<button a-on:click="$set('items', [...items, newItem])">Add Item</button>
```

### `$toggle(key)`

Toggle a boolean value:

```blade
<button a-on:click="$toggle('isOpen')">Toggle Menu</button>
```

### `$reset(key)`

Reset a property to its initial value:

```blade
<button a-on:click="$reset('count')">Reset Count</button>
<button a-on:click="$reset()">Reset All</button>
```

## Custom Scripts

Define custom functions using `<accelade:script>`:

```blade
@accelade(['count' => 0, 'step' => 1])
    <div>
        <span a-text="count">0</span>
        <button a-on:click="increment()">+</button>
        <button a-on:click="decrement()">-</button>
        <button a-on:click="double()">Double</button>
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
        <button a-on:click="$set('count', count + 1)">+</button>
        <button a-on:click="$set('count', count - 1)">-</button>
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

```blade
<x-accelade::link href="/dashboard">Dashboard</x-accelade::link>

<x-accelade::link
    href="/settings"
    class="nav-link"
    :preserveScroll="true"
    :preserveState="true"
>
    Settings
</x-accelade::link>
```

### Counter Component

```blade
<x-accelade::counter :initial-count="0" />
<x-accelade::counter :initial-count="10" sync="count" framework="vue" />
```

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
- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Notifications](notifications.md) - Toast notifications
