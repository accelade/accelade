# Data Component

The Data component provides a reactive data container that automatically handles state initialization, storage persistence, and global stores. It's an alternative to `@accelade` directive with additional features for data management.

## Quick Start

```blade
<x-accelade::data :default="['count' => 0]">
    <button @click="$set('count', count + 1)">
        Count: <span a-text="count">0</span>
    </button>
</x-accelade::data>
```

## Basic Usage

### With Array Data

```blade
<x-accelade::data :default="['name' => '', 'email' => '']">
    <input a-model="name" placeholder="Name">
    <input a-model="email" type="email" placeholder="Email">
    <p a-show="name">Hello, <span a-text="name"></span>!</p>
</x-accelade::data>
```

### With PHP Variables

```blade
{{-- In your controller --}}
return view('profile', [
    'user' => auth()->user()->only('name', 'email'),
]);

{{-- In your view --}}
<x-accelade::data :default="$user">
    <input a-model="name" placeholder="Name">
    <input a-model="email" type="email" placeholder="Email">
</x-accelade::data>
```

### With Eloquent Models

The component automatically converts Eloquent models to arrays:

```blade
<x-accelade::data :default="$user">
    <span a-text="name"></span>
    <span a-text="email"></span>
</x-accelade::data>
```

### With Collections

```blade
<x-accelade::data :default="collect(['count' => 0, 'items' => []])">
    {{-- Your content --}}
</x-accelade::data>
```

## Session Storage (Remember)

Use the `remember` attribute to persist state in session storage. The state will be restored when the user navigates back to the page during the same browser session:

```blade
<x-accelade::data :default="['step' => 1, 'formData' => []]" remember="wizard-form">
    <div a-show="step === 1">
        Step 1 content...
        <button @click="$set('step', 2)">Next</button>
    </div>
    <div a-show="step === 2">
        Step 2 content...
        <button @click="$set('step', 1)">Back</button>
    </div>
</x-accelade::data>
```

The state is stored with the key `accelade:wizard-form` in sessionStorage.

## Local Storage

Use the `local-storage` attribute to persist state in localStorage. The state will be restored even after the browser is closed:

```blade
<x-accelade::data
    :default="['theme' => 'light', 'fontSize' => 16]"
    local-storage="user-preferences"
>
    <select a-model="theme">
        <option value="light">Light</option>
        <option value="dark">Dark</option>
    </select>
    <input type="range" a-model="fontSize" min="12" max="24">
</x-accelade::data>
```

The state is stored with the key `accelade:user-preferences` in localStorage.

## Global Stores

Use the `store` attribute to create shared state across multiple components:

```blade
{{-- First component - Cart icon in header --}}
<x-accelade::data :default="['items' => [], 'count' => 0]" store="cart">
    <span a-text="count">0</span> items in cart
</x-accelade::data>

{{-- Second component - Product list --}}
<x-accelade::data :default="['items' => [], 'count' => 0]" store="cart">
    <button @click="$set('count', count + 1); $set('items', [...items, 'Product'])">
        Add to Cart
    </button>
</x-accelade::data>
```

When `count` is updated in one component, all components using the same store will automatically update.

### Reserved Store Names

The following names are reserved and cannot be used as store names:
- `data`
- `form`
- `toggle`
- `state`
- `store`

## JavaScript Object Notation

You can pass JavaScript object notation directly as a string:

```blade
<x-accelade::data :default="'{ count: 0, items: [], isActive: true }'">
    {{-- Your content --}}
</x-accelade::data>
```

This is useful when you want to use JavaScript-specific syntax that isn't valid PHP.

## Accessing Stores from JavaScript

You can access global stores from JavaScript:

```javascript
// Get a specific store
const cart = window.Accelade.stores.get('cart');
console.log(cart.items);

// Check if a store exists
if (window.Accelade.stores.has('cart')) {
    // ...
}

// Get all store names
const names = window.Accelade.stores.names();

// Get all stores
const allStores = window.Accelade.stores.all();
```

## Combining Features

You can combine storage persistence with global stores:

```blade
{{-- Persist cart to localStorage AND share across components --}}
<x-accelade::data
    :default="['items' => [], 'total' => 0]"
    store="cart"
    local-storage="shopping-cart"
>
    {{-- Your content --}}
</x-accelade::data>
```

## Component Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `default` | array\|object\|string | `[]` | Initial state data |
| `remember` | string | `null` | Session storage key for persistence |
| `local-storage` | string | `null` | Local storage key for persistence |
| `store` | string | `null` | Global store name for shared state |
| `class` | string | - | CSS classes for the wrapper |
| `id` | string | - | ID for the wrapper |
| `*` | - | - | Any other HTML attributes |

## Comparison with @accelade Directive

The `<x-accelade::data>` component and `@accelade` directive are functionally similar, with the component offering additional storage features:

```blade
{{-- Using @accelade directive --}}
@accelade(['count' => 0])
    <button @click="$set('count', count + 1)">
        Count: <span a-text="count">0</span>
    </button>
@endaccelade

{{-- Using x-accelade::data component --}}
<x-accelade::data :default="['count' => 0]">
    <button @click="$set('count', count + 1)">
        Count: <span a-text="count">0</span>
    </button>
</x-accelade::data>

{{-- With storage persistence (only available with component) --}}
<x-accelade::data :default="['count' => 0]" remember="counter">
    <button @click="$set('count', count + 1)">
        Count: <span a-text="count">0</span>
    </button>
</x-accelade::data>
```

### When to Use What

| Use Case | Recommended |
|----------|-------------|
| Simple reactive state | `@accelade` |
| State persistence (session/local) | `<x-accelade::data>` |
| Global shared state | `<x-accelade::data>` with `store` |
| Dynamic PHP data | Either works |

## Examples

### Multi-Step Form with Persistence

```blade
<x-accelade::data
    :default="[
        'step' => 1,
        'name' => '',
        'email' => '',
        'phone' => '',
    ]"
    remember="contact-form"
>
    <div a-show="step === 1">
        <h2>Step 1: Basic Info</h2>
        <input a-model="name" placeholder="Name">
        <input a-model="email" type="email" placeholder="Email">
        <button @click="$set('step', 2)">Next</button>
    </div>

    <div a-show="step === 2">
        <h2>Step 2: Contact</h2>
        <input a-model="phone" placeholder="Phone">
        <button @click="$set('step', 1)">Back</button>
        <button @click="submit()">Submit</button>
    </div>
</x-accelade::data>
```

### Theme Switcher with Local Storage

```blade
<x-accelade::data
    :default="['theme' => 'light']"
    local-storage="theme-preference"
>
    <button @click="$set('theme', theme === 'light' ? 'dark' : 'light')">
        Toggle Theme (Current: <span a-text="theme">light</span>)
    </button>
</x-accelade::data>
```

### Shopping Cart with Global Store

```blade
{{-- Header cart indicator --}}
<x-accelade::data
    :default="['items' => [], 'total' => 0]"
    store="cart"
    local-storage="shopping-cart"
>
    <a href="/cart">
        Cart (<span a-text="items.length">0</span>)
        - $<span a-text="total">0.00</span>
    </a>
</x-accelade::data>

{{-- Product page --}}
<x-accelade::data
    :default="['items' => [], 'total' => 0]"
    store="cart"
>
    @foreach($products as $product)
        <div class="product">
            <h3>{{ $product->name }}</h3>
            <p>${{ $product->price }}</p>
            <button @click="$set('items', [...items, {{ json_encode($product->only('id', 'name', 'price')) }}]); $set('total', total + {{ $product->price }})">
                Add to Cart
            </button>
        </div>
    @endforeach
</x-accelade::data>
```

## Next Steps

- [Components](components.md) - Building reactive components
- [Shared Data](shared-data.md) - Share data from PHP to JavaScript
- [API Reference](api-reference.md) - Complete API documentation
