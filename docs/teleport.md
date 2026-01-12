# Teleport Component

The Accelade Teleport component enables relocating template sections to different DOM nodes. Content is "teleported" to a target element specified by a CSS selector while maintaining access to parent component data and reactive properties.

## Basic Usage

Teleport content to a target element:

```blade
{{-- Define teleport source --}}
<x-accelade::teleport to="#footer">
    <p>This content appears in the footer</p>
</x-accelade::teleport>

{{-- Target element (anywhere in the document) --}}
<div id="footer"></div>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `to` | string | null | CSS selector for the target element (required) |
| `disabled` | bool | false | Keep content in original location when true |

## Reactive Data Access

Teleported content maintains access to parent component's reactive state:

```blade
<div data-accelade data-accelade-state='{"search": "", "count": 0}'>
    <input a-model="search" placeholder="Search...">
    <button @click="$set('count', count + 1)">Increment</button>

    {{-- This content teleports but stays reactive --}}
    <x-accelade::teleport to="#preview">
        <div class="preview">
            <p>Search: <span a-text="search"></span></p>
            <p>Count: <span a-text="count"></span></p>
        </div>
    </x-accelade::teleport>
</div>

<div id="preview"></div>
```

Changes to `search` and `count` will update the teleported content in real-time.

## Multiple Teleports to Same Target

Multiple teleport components can send content to the same target:

```blade
{{-- First component --}}
<x-accelade::teleport to="#notifications" id="teleport-1">
    <div class="alert alert-info">First notification</div>
</x-accelade::teleport>

{{-- Second component --}}
<x-accelade::teleport to="#notifications" id="teleport-2">
    <div class="alert alert-success">Second notification</div>
</x-accelade::teleport>

{{-- Target receives both --}}
<div id="notifications"></div>
```

## Disabled Teleport

Keep content in its original location:

```blade
{{-- Content stays in place --}}
<x-accelade::teleport to="#target" :disabled="true">
    <p>This content won't be teleported</p>
</x-accelade::teleport>

{{-- Conditionally disable --}}
<x-accelade::teleport to="#target" :disabled="$isMobile">
    <p>Teleports on desktop, stays in place on mobile</p>
</x-accelade::teleport>
```

## CSS Selector Support

The `to` prop accepts any valid CSS selector:

```blade
{{-- ID selector --}}
<x-accelade::teleport to="#footer">...</x-accelade::teleport>

{{-- Class selector --}}
<x-accelade::teleport to=".modal-body">...</x-accelade::teleport>

{{-- Attribute selector --}}
<x-accelade::teleport to="[data-notifications]">...</x-accelade::teleport>

{{-- Complex selector --}}
<x-accelade::teleport to="#app > .container .notifications">...</x-accelade::teleport>
```

## JavaScript API

### Getting Instances

```javascript
// Get a teleport instance by ID
const instance = Accelade.teleport.get('my-teleport');

// Get all teleport instances
const allInstances = Accelade.teleport.getAll();
```

### Controlling Teleport

```javascript
// Teleport content (if not already teleported)
Accelade.teleport.teleport('my-teleport');

// Return content to original position
Accelade.teleport.return('my-teleport');

// Update target and re-teleport
Accelade.teleport.updateTarget('my-teleport', '#new-target');
```

### Instance Properties

```javascript
const instance = Accelade.teleport.get('my-teleport');

instance.id;              // Teleport ID
instance.config;          // Configuration object
instance.sourceElement;   // Original container element
instance.targetElement;   // Current target element
instance.contentElement;  // The teleported content wrapper
instance.isTeleported;    // Whether content is currently teleported

// Methods
instance.teleport();      // Move content to target
instance.return();        // Return content to source
instance.updateTarget('#selector'); // Change target
instance.dispose();       // Clean up
```

## Events

Listen to teleport events:

```javascript
// Global event
document.addEventListener('accelade:teleport', (e) => {
    console.log('Teleport:', e.detail.id, e.detail.to, e.detail.success);
});

// Element-specific event
element.addEventListener('teleport', (e) => {
    if (e.detail.success) {
        console.log('Content teleported to', e.detail.to);
    } else {
        console.error('Teleport failed:', e.detail.error);
    }
});
```

## Use Cases

### Global Notifications

Teleport form feedback to a global notification area:

```blade
<form data-accelade data-accelade-state='{"message": "", "status": ""}'>
    @csrf
    <input name="email" type="email">
    <button type="submit">Subscribe</button>

    <x-accelade::teleport to="#global-notifications">
        <div a-show="status === 'success'" class="alert alert-success">
            <span a-text="message"></span>
        </div>
        <div a-show="status === 'error'" class="alert alert-danger">
            <span a-text="message"></span>
        </div>
    </x-accelade::teleport>
</form>

{{-- In your layout --}}
<div id="global-notifications" class="fixed top-4 right-4 z-50"></div>
```

### Modal Content

Teleport modal content to avoid z-index issues:

```blade
<div data-accelade data-accelade-state='{"isOpen": false, "title": "Settings"}'>
    <button @click="$set('isOpen', true)">Open Settings</button>

    <x-accelade::teleport to="#modal-container">
        <div a-show="isOpen" class="modal-backdrop">
            <div class="modal">
                <h2 a-text="title"></h2>
                <p>Modal content here...</p>
                <button @click="$set('isOpen', false)">Close</button>
            </div>
        </div>
    </x-accelade::teleport>
</div>

{{-- At end of body --}}
<div id="modal-container"></div>
```

### Search Preview

Show search results in a different location:

```blade
<div data-accelade data-accelade-state='{"query": "", "results": []}'>
    <div class="search-box">
        <input a-model="query" placeholder="Search..." @input="performSearch()">
    </div>

    {{-- Results appear in sidebar --}}
    <x-accelade::teleport to="#search-sidebar">
        <div a-show="query.length > 0">
            <h4>Search Results</h4>
            <p>Searching for: <span a-text="query"></span></p>
        </div>
    </x-accelade::teleport>
</div>
```

### Footer Actions

Teleport form actions to footer:

```blade
<form data-accelade data-accelade-state='{"isDirty": false}'>
    <input @input="$set('isDirty', true)">

    <x-accelade::teleport to="#form-footer">
        <div class="form-actions">
            <button type="button" @click="resetForm()">Cancel</button>
            <button type="submit" a-class="{'opacity-50': !isDirty}" :disabled="!isDirty">
                Save Changes
            </button>
        </div>
    </x-accelade::teleport>
</form>

<footer id="form-footer" class="sticky bottom-0 p-4 bg-white border-t"></footer>
```

## Dynamic Targets

Change the teleport target at runtime:

```blade
<div data-accelade data-accelade-state='{"activePanel": "left"}'>
    <button @click="$set('activePanel', 'left')">Left Panel</button>
    <button @click="$set('activePanel', 'right')">Right Panel</button>

    <x-accelade::teleport to="#left-panel" id="dynamic-content">
        <div class="panel-content">
            <p>This content can move between panels</p>
        </div>
    </x-accelade::teleport>
</div>

<script>
// Watch for panel changes and update teleport target
document.addEventListener('DOMContentLoaded', () => {
    // Example: update target based on state
    Accelade.teleport.updateTarget('dynamic-content', '#right-panel');
});
</script>

<div id="left-panel"></div>
<div id="right-panel"></div>
```

## Best Practices

1. **Ensure target exists** - The target element must exist in the DOM when teleport initializes
2. **Use unique IDs** - Assign explicit IDs when you need JavaScript control
3. **Keep targets accessible** - Place targets where they make semantic sense
4. **Consider z-index** - Teleport helps avoid z-index stacking issues for modals/overlays
5. **Test disabled state** - Use disabled for responsive designs where teleport isn't needed

## Framework Attributes

Works with all framework attribute prefixes:

| Framework | Attributes |
|-----------|------------|
| Vanilla | `a-text`, `a-show`, `a-model` |
| Vue | `v-text`, `v-show`, `v-model` |
| React | `data-state-text`, `data-state-show` |
| Svelte | `s-text`, `s-show`, `s-model` |
| Angular | `ng-text`, `ng-show`, `ng-model` |

## Next Steps

- [Components](components.md) - Reactive components
- [Modal Component](modal.md) - Dialogs and slideovers
- [State Component](state.md) - Errors, flash & shared data
- [Notifications](notifications.md) - Toast notifications
