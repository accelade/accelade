# Custom Scripts

Define custom JavaScript functions with full access to component state and actions using the `<x-accelade::script>` component.

---

## Basic Usage

The script component allows you to add custom methods to your Accelade components:

```blade
@accelade(['count' => 0, 'items' => []])
    <button @click="customIncrement()">Add 5</button>
    <button @click="addItem('New Item')">Add Item</button>

    <x-accelade::script>
        return {
            customIncrement() {
                $set('count', $get('count') + 5);
            },
            addItem(name) {
                const items = $get('items');
                items.push({ name, id: Date.now() });
                $set('items', items);
            }
        };
    </x-accelade::script>
@endaccelade
```

---

## Available Functions

Inside the script component, you have access to these built-in functions:

| Function | Description |
|----------|-------------|
| `$set(key, value)` | Set a state value |
| `$get(key)` | Get a state value |
| `$toggle(key)` | Toggle a boolean state value |
| `$reset(key?)` | Reset state to initial values (all or specific key) |
| `$navigate(url, options?)` | Navigate to a URL using SPA |
| `$watch(key, callback)` | Watch a state property for changes (Vue only) |
| `increment(key, amount?)` | Increment a numeric state value |
| `decrement(key, amount?)` | Decrement a numeric state value |
| `state` | Direct access to the reactive state object |
| `actions` | Access to built-in action methods |

---

## Framework Support

The component automatically renders the correct script attribute based on your configured framework:

| Framework | Output |
|-----------|--------|
| Vanilla JS | `<script a-script>...</script>` |
| Vue.js | `<script v-script>...</script>` |
| React | `<script state-script>...</script>` |
| Svelte | `<script state-script>...</script>` |
| Angular | `<script state-script>...</script>` |

---

## Examples

### Form Validation

Create custom validation logic for forms:

```blade
@accelade(['email' => '', 'password' => '', 'errors' => []])
    <form @submit.prevent="validate()">
        <input a-model="email" type="email" placeholder="Email">
        <p a-show="errors.email" a-text="errors.email" class="text-red-500"></p>

        <input a-model="password" type="password" placeholder="Password">
        <p a-show="errors.password" a-text="errors.password" class="text-red-500"></p>

        <button type="submit">Login</button>
    </form>

    <x-accelade::script>
        return {
            validate() {
                const errors = {};
                const email = $get('email');
                const password = $get('password');

                if (!email) {
                    errors.email = 'Email is required';
                } else if (!email.includes('@')) {
                    errors.email = 'Invalid email format';
                }

                if (!password) {
                    errors.password = 'Password is required';
                } else if (password.length < 8) {
                    errors.password = 'Password must be at least 8 characters';
                }

                $set('errors', errors);

                if (Object.keys(errors).length === 0) {
                    this.submitForm();
                }
            },
            async submitForm() {
                // Submit to server...
                $navigate('/dashboard');
            }
        };
    </x-accelade::script>
@endaccelade
```

### Async Data Loading

Load data from an API with loading and error states:

```blade
@accelade(['loading' => false, 'users' => [], 'error' => null])
    <button @click="loadUsers()" a-show="!loading">Load Users</button>
    <span a-show="loading">Loading...</span>

    <ul a-show="users.length">
        <template a-for="user in users">
            <li a-text="user.name"></li>
        </template>
    </ul>

    <p a-show="error" a-text="error" class="text-red-500"></p>

    <x-accelade::script>
        return {
            async loadUsers() {
                $set('loading', true);
                $set('error', null);

                try {
                    const response = await fetch('/api/users');
                    if (!response.ok) throw new Error('Failed to load users');

                    const data = await response.json();
                    $set('users', data);
                } catch (e) {
                    $set('error', e.message);
                } finally {
                    $set('loading', false);
                }
            }
        };
    </x-accelade::script>
@endaccelade
```

### Shopping Cart

Manage a shopping cart with add, remove, and total calculation:

```blade
@accelade(['cart' => [], 'total' => 0])
    <div class="cart">
        <template a-for="item in cart">
            <div class="cart-item">
                <span a-text="item.name"></span>
                <span a-text="'$' + item.price.toFixed(2)"></span>
                <button @click="removeItem(item.id)">Remove</button>
            </div>
        </template>

        <div class="cart-total">
            Total: <span a-text="'$' + total.toFixed(2)">$0.00</span>
        </div>
    </div>

    <x-accelade::script>
        return {
            addItem(product) {
                const cart = $get('cart');
                const existing = cart.find(i => i.id === product.id);

                if (existing) {
                    existing.quantity++;
                } else {
                    cart.push({ ...product, quantity: 1 });
                }

                $set('cart', cart);
                this.updateTotal();
            },
            removeItem(id) {
                const cart = $get('cart').filter(i => i.id !== id);
                $set('cart', cart);
                this.updateTotal();
            },
            updateTotal() {
                const cart = $get('cart');
                const total = cart.reduce((sum, item) => {
                    return sum + (item.price * item.quantity);
                }, 0);
                $set('total', total);
            }
        };
    </x-accelade::script>
@endaccelade
```

### Debounced Search

Implement debounced search with custom timing:

```blade
@accelade(['query' => '', 'results' => [], 'searching' => false])
    <input
        a-model="query"
        @input="debouncedSearch()"
        placeholder="Search..."
    >

    <div a-show="searching">Searching...</div>

    <ul a-show="results.length && !searching">
        <template a-for="result in results">
            <li a-text="result.title"></li>
        </template>
    </ul>

    <x-accelade::script>
        let searchTimeout = null;

        return {
            debouncedSearch() {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    this.search();
                }, 300);
            },
            async search() {
                const query = $get('query');
                if (!query.trim()) {
                    $set('results', []);
                    return;
                }

                $set('searching', true);

                try {
                    const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                    const data = await response.json();
                    $set('results', data.results);
                } finally {
                    $set('searching', false);
                }
            }
        };
    </x-accelade::script>
@endaccelade
```

---

## Best Practices

### Keep Scripts Focused

Each script should handle related functionality. For complex components, consider breaking them into smaller, focused components.

### Use Async/Await

For API calls and async operations, use async/await for cleaner code:

```blade
<x-accelade::script>
    return {
        async saveData() {
            try {
                const response = await fetch('/api/save', {
                    method: 'POST',
                    body: JSON.stringify($get('formData'))
                });
                // Handle response
            } catch (error) {
                $set('error', error.message);
            }
        }
    };
</x-accelade::script>
```

### Leverage State Functions

Use the built-in state functions instead of direct manipulation:

```blade
{{-- Good --}}
$set('count', $get('count') + 1);

{{-- Better - use increment helper --}}
increment('count');
```

---

## Related

- [State Management](state.md) — Managing component state
- [Bridge](bridge.md) — Call PHP methods from JavaScript
- [API Reference](api-reference.md) — Complete API documentation
