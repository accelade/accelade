# Tooltip Directive

The Accelade Tooltip directive provides a simple way to display contextual information when users hover, click, or focus on elements. Inspired by FilamentPHP, tooltips support light/dark themes, RTL languages, and persistent settings.

## Basic Usage

Add the `a-tooltip` attribute to any element:

```html
<!-- Simple text -->
<button a-tooltip="This is helpful information">Hover me</button>

<!-- With configuration -->
<button a-tooltip='{"content": "Welcome!", "theme": "light"}'>Hover me</button>
```

## Configuration Options

Pass a JSON object for full configuration:

```html
<button a-tooltip='{
    "content": "Tooltip text",
    "position": "bottom",
    "theme": "light",
    "trigger": "hover",
    "delay": 200,
    "maxWidth": "300px"
}'>
    Configured tooltip
</button>
```

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `content` | string | '' | The tooltip content |
| `position` | string | 'top' | Position relative to trigger element |
| `trigger` | string | 'hover' | How to trigger the tooltip |
| `theme` | string | from storage | Theme: 'light', 'dark', or 'auto' |
| `delay` | int | 0 | Delay before showing (milliseconds) |
| `hideDelay` | int | 0 | Delay before hiding (milliseconds) |
| `arrow` | bool | true | Show arrow pointer |
| `interactive` | bool | false | Allow hovering over tooltip content |
| `offset` | int | 8 | Distance from trigger element (pixels) |
| `maxWidth` | string | '320px' | Maximum width ('none' for full width) |
| `rtl` | bool | auto-detect | Enable RTL support |

## Themes

Tooltips support three themes that match your application's appearance:

### Dark Theme (Default)

```html
<button a-tooltip='{"content": "Dark theme", "theme": "dark"}'>
    Dark tooltip
</button>
```

### Light Theme

```html
<button a-tooltip='{"content": "Light theme", "theme": "light"}'>
    Light tooltip
</button>
```

### Auto Theme

Automatically follows system preference or page's dark mode class:

```html
<button a-tooltip='{"content": "Follows system theme", "theme": "auto"}'>
    Auto theme
</button>
```

## Position Options

Tooltips support 12 position options:

| Position | Description |
|----------|-------------|
| `top` | Above, centered |
| `top-start` | Above, aligned left |
| `top-end` | Above, aligned right |
| `bottom` | Below, centered |
| `bottom-start` | Below, aligned left |
| `bottom-end` | Below, aligned right |
| `left` | Left side, centered |
| `left-start` | Left side, aligned top |
| `left-end` | Left side, aligned bottom |
| `right` | Right side, centered |
| `right-start` | Right side, aligned top |
| `right-end` | Right side, aligned bottom |

```html
<button a-tooltip='{"content": "Appears on right", "position": "right"}'>
    Right tooltip
</button>

<button a-tooltip='{"content": "Bottom left", "position": "bottom-start"}'>
    Bottom start
</button>
```

## Trigger Options

### Hover (Default)

Tooltip appears when hovering over the element:

```html
<button a-tooltip="Appears on hover">Hover me</button>
```

### Click

Tooltip appears when clicking the element:

```html
<button a-tooltip='{"content": "Click again to close", "trigger": "click"}'>
    Click me
</button>
```

### Focus

Ideal for form fields - appears when element receives focus:

```html
<input
    type="email"
    placeholder="Email"
    a-tooltip='{"content": "Enter your email address", "trigger": "focus", "position": "right"}'
>
```

### Manual

Programmatically control the tooltip via JavaScript:

```html
<button id="my-tooltip" a-tooltip='{"content": "Controlled tooltip", "trigger": "manual"}'>
    Manual control
</button>

<script>
const instance = Accelade.tooltip.get('my-tooltip');
instance.show();  // Show tooltip
instance.hide();  // Hide tooltip
instance.toggle(); // Toggle visibility
</script>
```

## RTL Support

Tooltips automatically detect RTL from your document's direction and mirror positions accordingly:

```html
<!-- Auto-detects from document.dir or CSS direction -->
<button a-tooltip='{"content": "مرحبا بك", "position": "left"}'>
    Arabic text
</button>

<!-- Explicit RTL -->
<button a-tooltip='{"content": "مرحبا", "rtl": true, "position": "left"}'>
    Explicit RTL
</button>
```

When RTL is enabled:
- `left` becomes `right`
- `right` becomes `left`
- `top-start` becomes `top-end`
- `bottom-end` becomes `bottom-start`

## Width Control

### Default Max Width

Tooltips default to `320px` max width:

```html
<button a-tooltip="Standard width tooltip">Default</button>
```

### Custom Width

Set a specific max width:

```html
<button a-tooltip='{"content": "Narrow tooltip", "maxWidth": "150px"}'>
    Narrow
</button>

<button a-tooltip='{"content": "Wide tooltip for longer content", "maxWidth": "500px"}'>
    Wide
</button>
```

### Full Width

Use `"none"` for no width restriction:

```html
<button a-tooltip='{"content": "This tooltip can be as wide as needed without wrapping", "maxWidth": "none"}'>
    Full width
</button>
```

## Global Settings (Persistence)

Tooltip settings can be stored in localStorage and applied to all tooltips without explicit configuration:

```javascript
// Set global defaults (persisted to localStorage)
Accelade.tooltip.setSettings({
    theme: 'light',
    position: 'bottom',
    delay: 200,
    maxWidth: '400px'
});

// Get current global settings
const settings = Accelade.tooltip.getSettings();
console.log(settings); // { theme: 'light', position: 'bottom', ... }
```

**Priority Order:**
1. Explicit attribute value (highest priority)
2. Stored global settings from localStorage
3. Default configuration (lowest priority)

## Interactive Tooltips

Allow users to hover over the tooltip itself:

```html
<button a-tooltip='{"content": "You can hover over this tooltip", "interactive": true}'>
    Interactive tooltip
</button>
```

Useful for tooltips containing links or selectable text.

## Delayed Tooltips

Add delays to prevent accidental triggering:

```html
<!-- Show after 500ms -->
<button a-tooltip='{"content": "Delayed tooltip", "delay": 500}'>
    Delayed show
</button>

<!-- Stay visible for 1 second after mouse leaves -->
<button a-tooltip='{"content": "Lingers", "hideDelay": 1000}'>
    Delayed hide
</button>
```

## Without Arrow

Remove the arrow for a cleaner look:

```html
<button a-tooltip='{"content": "No arrow", "arrow": false}'>
    Clean tooltip
</button>
```

## JavaScript API

### Get Tooltip Instance

```javascript
// Get by element ID
const instance = Accelade.tooltip.get('my-tooltip-id');

// Available methods
instance.show();                      // Show tooltip
instance.hide();                      // Hide tooltip
instance.toggle();                    // Toggle visibility
instance.setContent('New text');      // Update content
instance.setTheme('light');           // Change theme
instance.setPosition('bottom');       // Change position
instance.isVisible();                 // Check visibility
instance.dispose();                   // Cleanup and remove
```

### Re-initialize Tooltips

After dynamically adding elements:

```javascript
// Initialize all tooltips on page
Accelade.tooltip.initAll();
```

### Global Settings

```javascript
// Set defaults for all tooltips
Accelade.tooltip.setSettings({
    theme: 'dark',
    delay: 100
});

// Get current settings
const settings = Accelade.tooltip.getSettings();
```

## Events

The tooltip dispatches events for integration:

```javascript
// On trigger element
element.addEventListener('tooltip', (e) => {
    console.log('Type:', e.detail.type); // 'show' or 'hide'
    console.log('ID:', e.detail.id);
});

// Global events
document.addEventListener('accelade:tooltip:show', (e) => {
    console.log('Tooltip shown:', e.detail.id);
});

document.addEventListener('accelade:tooltip:hide', (e) => {
    console.log('Tooltip hidden:', e.detail.id);
});
```

## Use Cases

### Help Icons

```html
<label class="flex items-center gap-2">
    Email Address
    <span a-tooltip="We'll never share your email with anyone" class="cursor-help">
        <svg class="w-4 h-4 text-gray-400">...</svg>
    </span>
</label>
<input type="email" name="email">
```

### Form Field Hints

```html
<input
    type="password"
    name="password"
    placeholder="Password"
    a-tooltip='{"content": "Minimum 8 characters", "trigger": "focus", "position": "right"}'
>
```

### Icon Buttons

```html
<button type="submit" a-tooltip="Save your changes">
    <svg>...</svg>
</button>
```

### Truncated Text

```blade
<span
    class="truncate"
    a-tooltip="{{ $fullText }}"
>
    {{ Str::limit($fullText, 30) }}
</span>
```

### Theme Switcher

```html
<button onclick="Accelade.tooltip.setSettings({ theme: 'light' })">
    Light tooltips
</button>
<button onclick="Accelade.tooltip.setSettings({ theme: 'dark' })">
    Dark tooltips
</button>
```

## Accessibility

The tooltip directive is designed with accessibility in mind:

- Tooltips have `role="tooltip"` attribute
- Direction is set via `dir` attribute for RTL content
- Focus triggers ensure keyboard accessibility for form fields
- Tooltip content is readable by screen readers

## Best Practices

1. **Keep it brief** - Tooltips should provide short, helpful hints
2. **Use appropriate triggers** - Hover for information, click for actions, focus for forms
3. **Consider mobile** - Click/focus triggers work better on touch devices
4. **Don't hide essential info** - Tooltips should enhance, not replace critical content
5. **Use delays wisely** - Short delays prevent accidental triggering
6. **Position thoughtfully** - Ensure tooltips don't cover important UI elements
7. **Match your theme** - Use light tooltips on dark backgrounds and vice versa
8. **Store preferences** - Use global settings for consistent user experience

## Next Steps

- [Toggle Component](toggle.md) - Boolean state management
- [Modal Component](modal.md) - Dialogs and slideovers
- [Notifications](notifications.md) - Toast notifications
