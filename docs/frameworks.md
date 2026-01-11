# Framework Adapters

Accelade supports multiple frontend frameworks. Each framework has its own binding syntax and reactivity system.

## Choosing a Framework

Set your framework in configuration:

```php
// config/accelade.php
'framework' => env('ACCELADE_FRAMEWORK', 'vanilla'),
```

Or via environment variable:

```env
ACCELADE_FRAMEWORK=vue
```

## Available Frameworks

| Framework | Value | Description |
|-----------|-------|-------------|
| Vanilla JS | `vanilla` | No dependencies, Proxy-based reactivity |
| Vue 3 | `vue` | Vue Composition API with reactive() |
| React 18 | `react` | React hooks with useState |
| Svelte 4 | `svelte` | Svelte stores with writable() |
| Angular 17+ | `angular` | Angular signals |

---

## Vanilla JavaScript

The default framework with zero dependencies.

### Binding Syntax

```blade
@accelade(['count' => 0, 'name' => ''])
    <span a-text="count">0</span>
    <input a-model="name">
    <div a-show="count > 0">Positive</div>
    <button @click="$set('count', count + 1)">+1</button>
@endaccelade
```

### Directive Prefix: `a-`

| Directive | Usage |
|-----------|-------|
| `a-text` | Text binding |
| `a-html` | HTML binding |
| `a-show` | Show/hide |
| `a-if` | Conditional |
| `a-model` | Two-way binding |
| `@event` | Event handler |
| `a-class` | Dynamic classes |
| `a-style` | Dynamic styles |
| `a-sync` | Server sync |

### Reactivity

Uses JavaScript Proxy for change detection:

```javascript
// Internal implementation
const state = new Proxy(initialState, {
    set(target, key, value) {
        target[key] = value;
        updateDOM();
        return true;
    }
});
```

---

## Vue 3

For Vue.js applications using Composition API.

### Binding Syntax

```blade
@accelade(['count' => 0, 'name' => ''])
    <span v-text="count">0</span>
    <input v-model="name">
    <div v-show="count > 0">Positive</div>
    <button @click="count++">+1</button>
@endaccelade
```

### Directive Prefix: `v-`

| Directive | Usage |
|-----------|-------|
| `v-text` | Text binding |
| `v-html` | HTML binding |
| `v-show` | Show/hide |
| `v-if` | Conditional |
| `v-model` | Two-way binding |
| `@event` | Event handler |
| `v-bind:class` | Dynamic classes |
| `v-bind:style` | Dynamic styles |

### Reactivity

Uses Vue's `reactive()` and `watch()`:

```javascript
import { reactive, watch } from 'vue';

const state = reactive(initialState);
watch(state, () => updateDOM(), { deep: true });
```

### Vue-Specific Features

```blade
{{-- Short syntax for events --}}
<button @click="count++">+1</button>

{{-- Short syntax for bindings --}}
<div :class="{ active: isActive }">

{{-- Computed-like expressions --}}
<span v-text="firstName + ' ' + lastName"></span>
```

---

## React 18

For React applications using hooks.

### Binding Syntax

```blade
@accelade(['count' => 0, 'name' => ''])
    <span state:text="count">0</span>
    <input state:model="name">
    <div state:show="count > 0">Positive</div>
    <button state:onClick="() => setCount(count + 1)">+1</button>
@endaccelade
```

### Directive Prefix: `state:`

| Directive | Usage |
|-----------|-------|
| `state:text` | Text binding |
| `state:html` | HTML binding |
| `state:show` | Show/hide |
| `state:if` | Conditional |
| `state:model` | Two-way binding |
| `state:onClick` | Click handler |
| `state:className` | Dynamic classes |
| `state:style` | Dynamic styles |

### Reactivity

Uses React's `useState` hooks:

```jsx
const [state, setState] = useState(initialState);

// Update function
const updateState = (key, value) => {
    setState(prev => ({ ...prev, [key]: value }));
};
```

### React-Specific Features

```blade
{{-- JSX-style event handlers --}}
<button state:onClick="handleClick">Click</button>

{{-- Callback pattern --}}
<button state:onClick="() => setCount(c => c + 1)">+1</button>
```

---

## Svelte 4

For Svelte applications using stores.

### Binding Syntax

```blade
@accelade(['count' => 0, 'name' => ''])
    <span bind:text="count">0</span>
    <input bind:value="name">
    <div bind:show="count > 0">Positive</div>
    <button on:click="() => count++">+1</button>
@endaccelade
```

### Directive Prefix: `bind:` / `on:`

| Directive | Usage |
|-----------|-------|
| `bind:text` | Text binding |
| `bind:html` | HTML binding |
| `bind:show` | Show/hide |
| `bind:if` | Conditional |
| `bind:value` | Two-way binding |
| `on:click` | Click handler |
| `class:name` | Dynamic classes |

### Reactivity

Uses Svelte's `writable` stores:

```javascript
import { writable } from 'svelte/store';

const state = writable(initialState);

state.subscribe(value => updateDOM(value));
```

### Svelte-Specific Features

```blade
{{-- Class directive --}}
<div class:active="isActive">

{{-- Store auto-subscription --}}
<span>{$count}</span>
```

---

## Angular 17+

For Angular applications using signals.

### Binding Syntax

```blade
@accelade(['count' => 0, 'name' => ''])
    <span ng-text="count">0</span>
    <input ng-model="name">
    <div ng-show="count > 0">Positive</div>
    <button @click="count = count + 1">+1</button>
@endaccelade
```

### Directive Prefix: `ng-`

| Directive | Usage |
|-----------|-------|
| `ng-text` | Text binding |
| `ng-html` | HTML binding |
| `ng-show` | Show/hide |
| `ng-if` | Conditional |
| `ng-model` | Two-way binding |
| `@click` | Click handler |
| `ng-class` | Dynamic classes |
| `ng-style` | Dynamic styles |

### Reactivity

Uses Angular's signals:

```typescript
import { signal, effect } from '@angular/core';

const count = signal(0);

effect(() => {
    updateDOM(count());
});
```

### Angular-Specific Features

```blade
{{-- Event binding with $event --}}
<input @input="handleInput($event)">

{{-- Two-way binding --}}
<input [(ngModel)]="name">
```

---

## Framework Comparison

| Feature | Vanilla | Vue | React | Svelte | Angular |
|---------|---------|-----|-------|--------|---------|
| Bundle Size | Smallest | Medium | Medium | Small | Large |
| Learning Curve | Easy | Medium | Medium | Easy | Hard |
| Reactivity | Proxy | reactive() | useState | writable | signal |
| Dependencies | None | Vue 3 | React 18 | Svelte 4 | Angular 17 |

## Switching Frameworks

When navigating between pages with different frameworks, a full page reload occurs to load the correct adapter.

```blade
{{-- On Vue page --}}
<x-accelade::link href="/react-page">
    Go to React
</x-accelade::link>
{{-- Full reload happens --}}
```

## Custom Framework Adapter

Create a custom adapter:

```typescript
// resources/js/custom/accelade.ts
import { createAdapter } from '../core/adapter';

export const customAdapter = createAdapter({
    name: 'custom',
    prefix: 'x-',

    createState(initial) {
        // Return reactive state
    },

    bindText(element, expression, state) {
        // Implement text binding
    },

    bindEvent(element, event, handler, state) {
        // Implement event binding
    },

    // ... other bindings
});
```

Register in Vite config:

```typescript
// vite.config.ts
build: {
    rollupOptions: {
        input: {
            'accelade-custom': 'resources/js/custom/accelade.ts'
        }
    }
}
```

## Best Practices

1. **Match Your Stack** - Use the framework you're already using
2. **Vanilla for Simple Sites** - No extra dependencies
3. **Consider Bundle Size** - Vanilla is smallest
4. **SSR Considerations** - All frameworks support SSR

## Next Steps

- [Components](components.md) - Creating components
- [Architecture](architecture.md) - How adapters work
- [API Reference](api-reference.md) - Complete API
