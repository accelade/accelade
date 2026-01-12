# Link Component

The Accelade Link component provides enhanced navigation with HTTP methods, confirmation dialogs, and SPA routing. It wraps standard anchor elements to enable asynchronous page loading without full browser refreshes.

## Basic Usage

```blade
<x-accelade::link href="/dashboard">Dashboard</x-accelade::link>

<x-accelade::link href="/users">All Users</x-accelade::link>
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | - | Target URL (required) |
| `method` | string | GET | HTTP method (GET, POST, PUT, PATCH, DELETE) |
| `data` | array | null | Request payload data |
| `headers` | array | null | Custom HTTP headers |
| `spa` | bool | true | Enable SPA navigation |
| `away` | bool | false | Treat as external link (full page navigation) |
| `activeClass` | string | 'active' | CSS class added when on current URL |
| `prefetch` | bool | false | Prefetch page on hover |
| `preserveScroll` | bool | false | Keep scroll position after navigation |
| `preserveState` | bool | false | Preserve component state |
| `replace` | bool | false | Replace history instead of push |
| `confirm` | bool/string | null | Show confirmation dialog |
| `confirmText` | string | null | Custom confirmation message |
| `confirmTitle` | string | null | Confirmation dialog title |
| `confirmButton` | string | 'Confirm' | Confirm button label |
| `cancelButton` | string | 'Cancel' | Cancel button label |
| `confirmDanger` | bool | false | Render confirm button in red (danger) style |

## Confirmation Dialogs

Show a confirmation dialog before navigation:

```blade
{{-- Simple confirmation --}}
<x-accelade::link href="/action" :confirm="true">
    Do Something
</x-accelade::link>

{{-- Custom confirmation text --}}
<x-accelade::link
    href="/delete"
    confirm-text="Are you sure you want to delete this item?"
>
    Delete
</x-accelade::link>

{{-- Fully customized confirmation --}}
<x-accelade::link
    href="/account/delete"
    confirm-title="Delete Account"
    confirm-text="This will permanently delete your account and all data. This action cannot be undone."
    confirm-button="Yes, delete my account"
    cancel-button="No, keep my account"
    :confirm-danger="true"
>
    Delete Account
</x-accelade::link>
```

### Danger Confirmation

For destructive actions, use the `confirm-danger` prop to render the confirm button in red:

```blade
<x-accelade::link
    href="/api/items/123"
    method="DELETE"
    confirm-text="Delete this item?"
    confirm-button="Delete"
    :confirm-danger="true"
>
    Delete Item
</x-accelade::link>
```

## HTTP Methods

Support POST, PUT, PATCH, and DELETE methods for form-like submissions:

```blade
{{-- POST request --}}
<x-accelade::link
    href="/api/items"
    method="POST"
    :data="['name' => 'New Item', 'status' => 'active']"
>
    Create Item
</x-accelade::link>

{{-- PUT request --}}
<x-accelade::link
    href="/api/items/123"
    method="PUT"
    :data="['name' => 'Updated Item']"
>
    Update Item
</x-accelade::link>

{{-- PATCH request --}}
<x-accelade::link
    href="/api/items/123/toggle"
    method="PATCH"
>
    Toggle Status
</x-accelade::link>

{{-- DELETE request --}}
<x-accelade::link
    href="/api/items/123"
    method="DELETE"
    :confirm-danger="true"
    confirm-text="Delete this item permanently?"
>
    Delete
</x-accelade::link>
```

### Request Data

Pass data with your request:

```blade
<x-accelade::link
    href="/api/orders"
    method="POST"
    :data="[
        'product_id' => $product->id,
        'quantity' => 1,
    ]"
>
    Add to Cart
</x-accelade::link>
```

### Custom Headers

Add custom headers to requests:

```blade
<x-accelade::link
    href="/api/resource"
    method="POST"
    :headers="['X-Custom-Header' => 'value']"
>
    Submit
</x-accelade::link>
```

## External Links

Use the `away` attribute for external links:

```blade
{{-- Simple external link --}}
<x-accelade::link href="https://example.com" :away="true">
    Visit Example
</x-accelade::link>

{{-- External link with confirmation --}}
<x-accelade::link
    href="https://external-site.com"
    :away="true"
    confirm-text="You are leaving this site. Continue?"
>
    External Link
</x-accelade::link>
```

## Navigation Options

### Preserve Scroll

Keep the scroll position after navigation:

```blade
<x-accelade::link href="/page" :preserve-scroll="true">
    Update (keep scroll)
</x-accelade::link>
```

Useful when:
- Updating content in the middle of a long page
- Navigating back from a form submission
- Pagination with server-side redirects

### Preserve State

Maintain component state across navigation:

```blade
<x-accelade::link href="/next-page" :preserve-state="true">
    Next (keep state)
</x-accelade::link>
```

### Prefetch

Prefetch the page on hover for faster navigation:

```blade
<x-accelade::link href="/dashboard" :prefetch="true">
    Dashboard
</x-accelade::link>
```

### Replace History

Replace the current history entry instead of pushing a new one:

```blade
<x-accelade::link href="/redirect-target" :replace="true">
    Redirect
</x-accelade::link>
```

## Active State

Links automatically receive an `active` class when on the current URL:

```blade
{{-- Default active class --}}
<x-accelade::link href="/current" class="nav-link">
    Current Page
</x-accelade::link>

{{-- Custom active class --}}
<x-accelade::link href="/page" active-class="is-active">
    Page
</x-accelade::link>
```

Style active links with CSS:

```css
.nav-link.active {
    font-weight: bold;
    color: blue;
}
```

## Non-SPA Links

Disable SPA behavior for specific links:

```blade
<x-accelade::link href="/download" :spa="false">
    Download File
</x-accelade::link>
```

## JavaScript API

### Events

Listen to link events:

```javascript
// Before link is handled
document.addEventListener('accelade:link-before', (e) => {
    console.log('Navigating to:', e.detail.href);
    // Set e.detail.cancelled = true to prevent navigation
});

// After response received
document.addEventListener('accelade:link-response', (e) => {
    console.log('Response:', e.detail.response);
});

// On error
document.addEventListener('accelade:link-error', (e) => {
    console.error('Error:', e.detail.error);
});
```

### Programmatic Confirmation

Use the confirmation dialog programmatically:

```javascript
import { confirm, confirmDanger, showConfirmDialog } from 'accelade';

// Simple confirm
const result = await confirm('Are you sure?');
if (result) {
    // User confirmed
}

// Danger confirm
const deleted = await confirmDanger('Delete this item?', 'Delete');
if (deleted) {
    // User confirmed deletion
}

// Full options
const result = await showConfirmDialog({
    title: 'Confirm Action',
    text: 'This will modify your data.',
    confirmButton: 'Proceed',
    cancelButton: 'Cancel',
    danger: false,
});

if (result.confirmed) {
    // User confirmed
}
```

## Complete Example

### Navigation Menu

```blade
<nav class="flex gap-4">
    <x-accelade::link
        href="{{ route('dashboard') }}"
        class="px-4 py-2 rounded hover:bg-gray-100"
        active-class="bg-blue-100 text-blue-700"
    >
        Dashboard
    </x-accelade::link>

    <x-accelade::link
        href="{{ route('users.index') }}"
        class="px-4 py-2 rounded hover:bg-gray-100"
        active-class="bg-blue-100 text-blue-700"
        :prefetch="true"
    >
        Users
    </x-accelade::link>

    <x-accelade::link
        href="{{ route('settings') }}"
        class="px-4 py-2 rounded hover:bg-gray-100"
        active-class="bg-blue-100 text-blue-700"
    >
        Settings
    </x-accelade::link>
</nav>
```

### Action Buttons

```blade
<div class="flex gap-2">
    {{-- Edit action --}}
    <x-accelade::link
        href="{{ route('items.edit', $item) }}"
        class="px-3 py-1 bg-blue-500 text-white rounded"
    >
        Edit
    </x-accelade::link>

    {{-- Duplicate action --}}
    <x-accelade::link
        href="{{ route('items.duplicate', $item) }}"
        method="POST"
        class="px-3 py-1 bg-gray-500 text-white rounded"
    >
        Duplicate
    </x-accelade::link>

    {{-- Delete action --}}
    <x-accelade::link
        href="{{ route('items.destroy', $item) }}"
        method="DELETE"
        confirm-text="Delete '{{ $item->name }}'?"
        confirm-button="Delete"
        :confirm-danger="true"
        class="px-3 py-1 bg-red-500 text-white rounded"
    >
        Delete
    </x-accelade::link>
</div>
```

## Framework-Specific Attributes

The Link component automatically uses the correct attributes for each framework:

| Framework | Attribute |
|-----------|-----------|
| Vanilla | `a-link` |
| Vue | `data-accelade-link` |
| React | `data-spa-link` |
| Svelte | `data-spa-link` |
| Angular | `data-spa-link` |

## Next Steps

- [SPA Navigation](spa-navigation.md) - Client-side routing
- [Components](components.md) - Reactive components
- [Flash Component](flash.md) - Session flash data
