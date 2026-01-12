# Modal Component

The Accelade Modal component provides modal dialogs, slideover panels, and bottom sheets with async content loading, customizable appearance, and pre-loaded content support.

## Basic Usage

### Pre-loaded Modal

Use a hash link to open a modal with pre-loaded content:

```blade
{{-- Link to open modal --}}
<x-accelade::link href="#my-modal">Open Modal</x-accelade::link>

{{-- Modal component (hidden until triggered) --}}
<x-accelade::modal name="my-modal">
    <h2>Modal Title</h2>
    <p>Modal content goes here...</p>
    <button data-modal-close>Close</button>
</x-accelade::modal>
```

### Async Modal (Load from URL)

Load modal content asynchronously from a URL:

```blade
<x-accelade::link href="/users/create" :modal="true">
    Create User
</x-accelade::link>
```

The content from `/users/create` will be loaded into the modal when clicked.

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | null | Modal name for hash-based opening |
| `slideover` | bool | false | Render as slideover instead of modal |
| `bottomSheet` | bool | false | Render as bottom sheet (mobile-friendly) |
| `maxWidth` | string | 2xl / md | Max width (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl) |
| `position` | string | center | Vertical position (top, center, bottom) |
| `slideoverPosition` | string | right | Slideover horizontal position (left, right) |
| `closeExplicitly` | bool | false | Disable ESC key and outside click closing |
| `closeButton` | bool | true | Show the X close button |
| `opened` | bool | false | Open immediately on page load |

## Slideovers

Slideovers are side panels that slide in from the left or right:

```blade
{{-- Right slideover (default) --}}
<x-accelade::link href="#settings">Settings</x-accelade::link>

<x-accelade::modal name="settings" :slideover="true">
    <h2>Settings</h2>
    <nav>...</nav>
</x-accelade::modal>

{{-- Left slideover --}}
<x-accelade::modal name="navigation" :slideover="true" slideover-position="left">
    <nav>...</nav>
</x-accelade::modal>
```

### Async Slideover

```blade
<x-accelade::link href="/settings" :slideover="true">
    Open Settings
</x-accelade::link>
```

## Bottom Sheets

Bottom sheets are mobile-friendly panels that slide up from the bottom of the screen. They're perfect for action sheets, quick selections, and mobile navigation:

```blade
{{-- Pre-loaded bottom sheet --}}
<x-accelade::link href="#actions">Actions</x-accelade::link>

<x-accelade::modal name="actions" :bottom-sheet="true">
    <h2>Choose an Action</h2>
    <button>Edit</button>
    <button>Share</button>
    <button>Delete</button>
    <button data-modal-close>Cancel</button>
</x-accelade::modal>
```

Bottom sheets include a drag handle at the top for visual affordance.

### Async Bottom Sheet

Load bottom sheet content from a URL:

```blade
<x-accelade::link href="/share-options" :bottom-sheet="true">
    Share
</x-accelade::link>
```

### Bottom Sheet Features

- Slides up from the bottom of the screen
- Full width with rounded top corners
- Includes a drag handle indicator
- Max height of 90vh with scroll support
- Perfect for mobile-first interfaces

## Max Width Options

Control the width of modals and slideovers:

| Value | Width | Description |
|-------|-------|-------------|
| `sm` | 24rem | Small |
| `md` | 28rem | Medium (slideover default) |
| `lg` | 32rem | Large |
| `xl` | 36rem | Extra large |
| `2xl` | 42rem | 2X large (modal default) |
| `3xl` | 48rem | 3X large |
| `4xl` | 56rem | 4X large |
| `5xl` | 64rem | 5X large |
| `6xl` | 72rem | 6X large |
| `7xl` | 80rem | 7X large |

```blade
{{-- Small modal --}}
<x-accelade::modal name="confirm" max-width="sm">
    <p>Are you sure?</p>
</x-accelade::modal>

{{-- Large modal for tables/forms --}}
<x-accelade::modal name="data-table" max-width="5xl">
    <table>...</table>
</x-accelade::modal>
```

## Position Options

### Modal Positions

Position modals vertically within the viewport:

```blade
{{-- Top of screen --}}
<x-accelade::modal name="alert" position="top">
    Alert content...
</x-accelade::modal>

{{-- Center (default) --}}
<x-accelade::modal name="dialog" position="center">
    Dialog content...
</x-accelade::modal>

{{-- Bottom of screen --}}
<x-accelade::modal name="action-sheet" position="bottom">
    Action sheet...
</x-accelade::modal>
```

### Slideover Positions

```blade
{{-- Right side (default) --}}
<x-accelade::modal name="details" :slideover="true" slideover-position="right">
    Detail panel...
</x-accelade::modal>

{{-- Left side --}}
<x-accelade::modal name="menu" :slideover="true" slideover-position="left">
    Navigation menu...
</x-accelade::modal>
```

## Closing Modals

### Close Button

By default, modals have an X button in the top-right corner. Disable it with:

```blade
<x-accelade::modal name="custom" :close-button="false">
    <button data-modal-close>Custom Close Button</button>
</x-accelade::modal>
```

### Data Attribute Close

Add `data-modal-close` to any element to make it close the modal:

```blade
<x-accelade::modal name="dialog">
    <p>Modal content...</p>
    <button data-modal-close>Cancel</button>
    <button data-modal-close>Save & Close</button>
</x-accelade::modal>
```

### Close Explicitly

Prevent closing via ESC key and outside click:

```blade
<x-accelade::modal name="important" :close-explicitly="true">
    <h2>Important Notice</h2>
    <p>You must acknowledge this message.</p>
    <button data-modal-close>I Understand</button>
</x-accelade::modal>
```

## Link Component Props

Use these props on `<x-accelade::link>` to open modals:

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modal` | bool | false | Open link in modal |
| `slideover` | bool | false | Open link in slideover |
| `bottomSheet` | bool | false | Open link in bottom sheet |
| `modalMaxWidth` | string | null | Override modal max-width |
| `modalPosition` | string | null | Override modal position |
| `slideoverPosition` | string | null | Override slideover position |

```blade
{{-- Modal link --}}
<x-accelade::link href="/form" :modal="true">
    Open Form
</x-accelade::link>

{{-- Modal link with options --}}
<x-accelade::link
    href="/details"
    :modal="true"
    modal-max-width="4xl"
    modal-position="top"
>
    View Details
</x-accelade::link>

{{-- Slideover link --}}
<x-accelade::link
    href="/settings"
    :slideover="true"
    slideover-position="left"
>
    Settings
</x-accelade::link>

{{-- Bottom sheet link --}}
<x-accelade::link href="/share" :bottom-sheet="true">
    Share Options
</x-accelade::link>
```

## JavaScript API

### Events

Listen to modal events:

```javascript
// When any modal opens
document.addEventListener('accelade:modal-open', (e) => {
    console.log('Modal opened:', e.detail.id, e.detail.name);
});

// When any modal closes
document.addEventListener('accelade:modal-close', (e) => {
    console.log('Modal closed:', e.detail.id);
});
```

### Programmatic Control

Access the modal manager:

```javascript
// Open a named modal
window.Accelade.modal.openNamed('my-modal');

// Open modal from URL
await window.Accelade.modal.openUrl('/users/create', {
    maxWidth: '4xl',
    position: 'top',
});

// Open slideover from URL
await window.Accelade.modal.openUrl('/settings', {
    type: 'slideover',
    slideoverPosition: 'left',
});

// Open bottom sheet from URL
await window.Accelade.modal.openUrl('/actions', {
    type: 'bottom-sheet',
    maxWidth: 'lg',
});

// Open with HTML content
window.Accelade.modal.open({
    content: '<h2>Hello</h2><p>World</p>',
    maxWidth: 'md',
});

// Close all modals
window.Accelade.modal.closeAll();

// Close the topmost modal
window.Accelade.modal.closeLast();

// Check if any modal is open
if (window.Accelade.modal.hasOpen()) {
    // ...
}
```

### Modal Instance

Get a reference to a modal instance:

```javascript
const modal = window.Accelade.modal.getByName('my-modal');

// Control the modal
modal.open();
modal.close();
modal.setIsOpen(true);

// Check state
console.log(modal.isOpen);
```

## Complete Examples

### Confirmation Modal

```blade
<x-accelade::link href="#confirm-delete">
    Delete Item
</x-accelade::link>

<x-accelade::modal name="confirm-delete" max-width="sm" :close-explicitly="true">
    <h3 class="text-lg font-semibold text-red-600">Delete Item?</h3>
    <p class="text-slate-600 my-4">
        This action cannot be undone. Are you sure you want to delete this item?
    </p>
    <div class="flex gap-3 justify-end">
        <button data-modal-close class="px-4 py-2 bg-slate-200 rounded">
            Cancel
        </button>
        <x-accelade::link
            href="/items/123"
            method="DELETE"
            class="px-4 py-2 bg-red-500 text-white rounded"
        >
            Delete
        </x-accelade::link>
    </div>
</x-accelade::modal>
```

### Form in Modal

```blade
<x-accelade::link href="/users/create" :modal="true" modal-max-width="lg">
    Create User
</x-accelade::link>
```

In your `/users/create` view, wrap the form with the modal component:

```blade
{{-- resources/views/users/create.blade.php --}}
<x-accelade::modal>
    <h2 class="text-xl font-semibold mb-4">Create User</h2>

    <form action="/users" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Name">
        <input type="email" name="email" placeholder="Email">

        <div class="flex gap-3 justify-end mt-4">
            <button type="button" data-modal-close>Cancel</button>
            <button type="submit">Create</button>
        </div>
    </form>
</x-accelade::modal>
```

### Navigation Sidebar

```blade
<x-accelade::link href="#nav-menu">
    <svg><!-- menu icon --></svg>
</x-accelade::link>

<x-accelade::modal name="nav-menu" :slideover="true" slideover-position="left">
    <nav class="space-y-2">
        <x-accelade::link href="/dashboard" data-modal-close>
            Dashboard
        </x-accelade::link>
        <x-accelade::link href="/users" data-modal-close>
            Users
        </x-accelade::link>
        <x-accelade::link href="/settings" data-modal-close>
            Settings
        </x-accelade::link>
    </nav>
</x-accelade::modal>
```

### Auto-open Modal

Open a modal immediately when the page loads:

```blade
<x-accelade::modal name="welcome" :opened="true">
    <h2>Welcome!</h2>
    <p>Thanks for visiting our site.</p>
    <button data-modal-close>Got it</button>
</x-accelade::modal>
```

### Action Sheet (Bottom Sheet)

Create a mobile-friendly action sheet:

```blade
<x-accelade::link href="#photo-options">
    <svg><!-- camera icon --></svg>
    Add Photo
</x-accelade::link>

<x-accelade::modal name="photo-options" :bottom-sheet="true">
    <div class="space-y-2">
        <button class="w-full p-4 text-left hover:bg-slate-100 rounded">
            Take Photo
        </button>
        <button class="w-full p-4 text-left hover:bg-slate-100 rounded">
            Choose from Gallery
        </button>
        <button class="w-full p-4 text-left hover:bg-slate-100 rounded">
            Browse Files
        </button>
    </div>
    <button data-modal-close class="w-full p-4 text-center text-slate-500">
        Cancel
    </button>
</x-accelade::modal>
```

## Styling

### CSS Variables

Customize modal appearance with CSS:

```css
/* Modal overlay background */
.accelade-modal-overlay {
    background: rgba(0, 0, 0, 0.75);
}

/* Modal panel */
.accelade-modal-panel {
    border-radius: 1rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

/* Close button */
.accelade-modal-close:hover {
    background: #fee2e2;
    color: #dc2626;
}
```

### Dark Mode

```css
@media (prefers-color-scheme: dark) {
    .accelade-modal-panel {
        background: #1f2937;
        color: #f9fafb;
    }

    .accelade-modal-close {
        color: #9ca3af;
    }

    .accelade-modal-close:hover {
        background: #374151;
        color: #f9fafb;
    }
}
```

## Next Steps

- [Link Component](link.md) - Enhanced navigation
- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Components](components.md) - Reactive components
