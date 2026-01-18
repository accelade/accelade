# Icon Component

The Icon component provides a unified way to render icons from Blade Icons packages (like Heroicons) throughout your application. It includes fallback support, configurable sizes, and seamless integration with any Blade Icons package.

## Basic Usage

Use the `x-accelade::icon` component to display an icon:

```blade
<x-accelade::icon name="heroicon-o-home" />
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | The icon name (e.g., `heroicon-o-home`) |
| `size` | string | `'base'` | Icon size: `xs`, `sm`, `md`, `base`, `lg`, `xl`, `2xl`, `3xl`, `4xl` |
| `fallback` | string | `'heroicon-o-question-mark-circle'` | Fallback icon if the primary icon is not found |
| `showFallback` | bool | `true` | Whether to show fallback icon when primary is not found |

## Icon Sizes

The component supports multiple predefined sizes:

| Size | Pixels | Tailwind Class |
|------|--------|----------------|
| `xs` | 12px | `w-3 h-3` |
| `sm` | 16px | `w-4 h-4` |
| `md` | 20px | `w-5 h-5` |
| `base` | 24px | `w-6 h-6` |
| `lg` | 32px | `w-8 h-8` |
| `xl` | 40px | `w-10 h-10` |
| `2xl` | 48px | `w-12 h-12` |
| `3xl` | 64px | `w-16 h-16` |
| `4xl` | 80px | `w-20 h-20` |

```blade
{{-- Small icon --}}
<x-accelade::icon name="heroicon-o-home" size="sm" />

{{-- Large icon --}}
<x-accelade::icon name="heroicon-o-home" size="xl" />
```

## Heroicons Variants

When using Heroicons, three variants are available:

| Variant | Prefix | Description |
|---------|--------|-------------|
| Outline | `heroicon-o-` | 24x24 outline style |
| Solid | `heroicon-s-` | 24x24 filled style |
| Mini | `heroicon-m-` | 20x20 compact style |

```blade
{{-- Outline (default) --}}
<x-accelade::icon name="heroicon-o-heart" />

{{-- Solid --}}
<x-accelade::icon name="heroicon-s-heart" />

{{-- Mini --}}
<x-accelade::icon name="heroicon-m-heart" />
```

## Color Styling

Apply colors using Tailwind's text color classes:

```blade
{{-- Primary color --}}
<x-accelade::icon name="heroicon-o-star" class="text-primary-500" />

{{-- Red color --}}
<x-accelade::icon name="heroicon-o-heart" class="text-red-500" />

{{-- Gradient effect --}}
<x-accelade::icon name="heroicon-o-sparkles" class="text-purple-500" />
```

## Fallback Icons

The component gracefully handles missing icons with fallback support:

```blade
{{-- Uses default fallback (question mark) if icon not found --}}
<x-accelade::icon name="nonexistent-icon" />

{{-- Custom fallback icon --}}
<x-accelade::icon name="custom-icon" fallback="heroicon-o-photo" />

{{-- No fallback - renders nothing if icon not found --}}
<x-accelade::icon name="maybe-missing-icon" :showFallback="false" />
```

## Common Icons

Here are some commonly used Heroicons:

### Navigation
- `heroicon-o-home` - Home
- `heroicon-o-bars-3` - Menu
- `heroicon-o-x-mark` - Close
- `heroicon-o-chevron-right` - Chevron right
- `heroicon-o-arrow-left` - Arrow left

### Actions
- `heroicon-o-plus` - Add
- `heroicon-o-minus` - Remove
- `heroicon-o-pencil` - Edit
- `heroicon-o-trash` - Delete
- `heroicon-o-check` - Check/Confirm

### Status
- `heroicon-o-check-circle` - Success
- `heroicon-o-exclamation-circle` - Warning
- `heroicon-o-x-circle` - Error
- `heroicon-o-information-circle` - Info

### Media
- `heroicon-o-photo` - Image
- `heroicon-o-document` - Document
- `heroicon-o-folder` - Folder
- `heroicon-o-clipboard-document` - Copy

### Communication
- `heroicon-o-envelope` - Email
- `heroicon-o-bell` - Notification
- `heroicon-o-chat-bubble-left` - Message
- `heroicon-o-phone` - Phone

## Installation

The Icon component requires the Blade Icons package. Install Heroicons:

```bash
composer require blade-ui-kit/blade-heroicons
```

For other icon sets, see the [Blade Icons documentation](https://blade-ui-kit.com/blade-icons).

## Examples

### Button with Icon

```blade
<button class="flex items-center gap-2 px-4 py-2 bg-primary-500 text-white rounded-lg">
    <x-accelade::icon name="heroicon-o-plus" size="sm" />
    Add Item
</button>
```

### Icon in Alert

```blade
<div class="flex items-start gap-3 p-4 bg-green-50 rounded-lg">
    <x-accelade::icon name="heroicon-o-check-circle" class="text-green-500" />
    <p class="text-green-700">Operation completed successfully!</p>
</div>
```

### Icon Grid

```blade
<div class="grid grid-cols-4 gap-4">
    <x-accelade::icon name="heroicon-o-home" size="lg" class="text-gray-600" />
    <x-accelade::icon name="heroicon-o-user" size="lg" class="text-gray-600" />
    <x-accelade::icon name="heroicon-o-cog-6-tooth" size="lg" class="text-gray-600" />
    <x-accelade::icon name="heroicon-o-bell" size="lg" class="text-gray-600" />
</div>
```

## Tips

1. **Use consistent sizing** - Stick to the predefined sizes for visual consistency
2. **Choose the right variant** - Use outline for UI, solid for emphasis, mini for tight spaces
3. **Leverage fallbacks** - Always consider what happens if an icon is missing
4. **Combine with Tailwind** - Use Tailwind utilities for colors, transitions, and hover states
