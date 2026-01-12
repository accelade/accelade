# Toggle Component

The Accelade Toggle component is a streamlined variant designed specifically for managing boolean values. It provides a simple interface for toggling state without the complexity of more comprehensive data management.

## Basic Usage

Create a simple show/hide toggle:

```blade
<x-accelade::toggle>
    <button @click.prevent="toggle()">Show Content</button>

    <div a-show="toggled">
        <p>This content can be toggled!</p>
        <button @click.prevent="setToggle(false)">Hide</button>
    </div>
</x-accelade::toggle>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | bool\|string\|array | false | Default value or comma-separated key names |

## Exposed Properties

The Toggle component exposes three properties to its slot:

| Property | Type | Description |
|----------|------|-------------|
| `toggled` | boolean | Current toggle state (single mode) |
| `toggle(key?)` | function | Toggle the current state |
| `setToggle(value)` | function | Set a specific state value |

## Setting Default State

Use the `data` prop to set the initial state:

```blade
{{-- Start with toggled = true --}}
<x-accelade::toggle :data="true">
    <div a-show="toggled">Visible by default!</div>
    <button @click.prevent="toggle()">Toggle</button>
</x-accelade::toggle>
```

## Multiple Toggles

Manage multiple boolean values by passing comma-separated keys:

```blade
<x-accelade::toggle data="isCompany, hasVatNumber, wantsNewsletter">
    {{-- Toggle specific keys --}}
    <button @click.prevent="toggle('isCompany')">Switch Account Type</button>

    {{-- Show/hide based on keys --}}
    <div a-show="isCompany">
        <input name="company_name" placeholder="Company Name">
    </div>

    {{-- Set specific values --}}
    <button @click.prevent="setToggle('hasVatNumber', true)">Enable VAT</button>

    <div a-show="hasVatNumber">
        <input name="vat_number" placeholder="VAT Number">
    </div>

    {{-- Toggle newsletter --}}
    <label>
        <input type="checkbox" @change="toggle('wantsNewsletter')">
        Subscribe to newsletter
    </label>
</x-accelade::toggle>
```

With multiple toggles, the `toggle()` and `setToggle()` methods require a key parameter:

- `toggle('keyName')` - Toggle a specific key
- `setToggle('keyName', true)` - Set a specific key to a value

## Array Data with Default Values

Pass an array to set different default values for each key:

```blade
<x-accelade::toggle :data="['showSidebar' => true, 'showFooter' => false]">
    <div a-show="showSidebar">Sidebar content...</div>
    <div a-show="showFooter">Footer content...</div>
</x-accelade::toggle>
```

## Use Cases

### Accordion

Build an accordion with multiple Toggle components:

```blade
<div class="accordion">
    <x-accelade::toggle>
        <div class="accordion-item">
            <button @click.prevent="toggle()" class="accordion-header">
                <span>Section 1</span>
                <span a-text="toggled ? '−' : '+'">+</span>
            </button>
            <div a-show="toggled" class="accordion-content">
                Content for section 1...
            </div>
        </div>
    </x-accelade::toggle>

    <x-accelade::toggle>
        <div class="accordion-item">
            <button @click.prevent="toggle()" class="accordion-header">
                <span>Section 2</span>
                <span a-text="toggled ? '−' : '+'">+</span>
            </button>
            <div a-show="toggled" class="accordion-content">
                Content for section 2...
            </div>
        </div>
    </x-accelade::toggle>
</div>
```

### Conditional Form Fields

Show/hide form fields based on user selection:

```blade
<x-accelade::toggle data="isCompany, needsShipping">
    <form>
        <input name="name" placeholder="Your name">

        <label>
            <input type="checkbox" @change="toggle('isCompany')">
            I'm registering as a company
        </label>

        <div a-show="isCompany" class="company-fields">
            <input name="company_name" placeholder="Company Name">
            <input name="vat_number" placeholder="VAT Number">
        </div>

        <label>
            <input type="checkbox" @change="toggle('needsShipping')">
            I need shipping
        </label>

        <div a-show="needsShipping" class="shipping-fields">
            <textarea name="address" placeholder="Shipping address"></textarea>
        </div>

        <button type="submit">Submit</button>
    </form>
</x-accelade::toggle>
```

### Dropdown Menu

Create a simple dropdown:

```blade
<x-accelade::toggle>
    <div class="dropdown">
        <button @click.prevent="toggle()">
            Menu
        </button>

        <div a-show="toggled" class="dropdown-menu">
            <a href="/profile">Profile</a>
            <a href="/settings">Settings</a>
            <button @click.prevent="setToggle(false)">Close</button>
        </div>
    </div>
</x-accelade::toggle>
```

### Modal Dialog

Simple modal implementation:

```blade
<x-accelade::toggle>
    <button @click.prevent="setToggle(true)">Open Modal</button>

    <div a-show="toggled" class="modal-backdrop">
        <div class="modal">
            <h2>Modal Title</h2>
            <p>Modal content goes here...</p>
            <button @click.prevent="setToggle(false)">Close</button>
        </div>
    </div>
</x-accelade::toggle>
```

### Expandable Card

Create an expandable card component:

```blade
<x-accelade::toggle>
    <div class="card">
        <div class="card-header">
            <h3>Card Title</h3>
            <button @click.prevent="toggle()">
                <span a-show="!toggled">Expand</span>
                <span a-show="toggled">Collapse</span>
            </button>
        </div>

        <div a-show="toggled" class="card-body">
            <p>Expanded content with more details...</p>
        </div>
    </div>
</x-accelade::toggle>
```

## Events

The Toggle component dispatches events when state changes:

```javascript
// Listen on element
element.addEventListener('toggle', (e) => {
    console.log('Key:', e.detail.key);
    console.log('Value:', e.detail.value);
    console.log('ID:', e.detail.id);
});

// Listen globally
document.addEventListener('accelade:toggle', (e) => {
    console.log('Toggle changed:', e.detail);
});
```

## Framework Attributes

Works with all framework attribute prefixes:

| Framework | Show Attribute | Text Attribute |
|-----------|---------------|----------------|
| Vanilla | `a-show` | `a-text` |
| Vue | `v-show` | `v-text` |
| React | `data-state-show` | `data-state-text` |
| Svelte | `s-show` | `s-text` |
| Angular | `ng-show` | `ng-text` |

## Comparison with Data Component

| Feature | Toggle | Data Component |
|---------|--------|----------------|
| Boolean values | ✅ Optimized | ✅ Supported |
| Multiple data types | ❌ | ✅ |
| Nested objects | ❌ | ✅ |
| Simple API | ✅ | ➖ |
| Form integration | ➖ | ✅ |

Use Toggle when you only need boolean state management. Use the Data component for more complex state needs.

## Best Practices

1. **Use for booleans only** - For complex state, use the Data component
2. **Name your keys clearly** - Use descriptive names like `isVisible`, `hasError`, `showDetails`
3. **Keep toggles focused** - One toggle component per logical unit
4. **Use `a-show` for visibility** - More performant than `a-if` for frequent toggles

## Next Steps

- [Components](components.md) - Reactive components
- [State Component](state.md) - Errors, flash & shared data
- [Modal Component](modal.md) - Dialogs and slideovers
