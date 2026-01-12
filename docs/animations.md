# Animations

Accelade provides a powerful animation system with built-in presets for smooth enter/leave transitions. Add animations to your toggle components with a single attribute, or use the standalone transition component for more control.

## Quick Start

The simplest way to add animations is with the `animation` attribute on the toggle component:

```blade
<x-accelade::toggle animation="fade">
    <button @click="toggle()">Toggle</button>
    <div a-show="toggled">This content fades in and out!</div>
</x-accelade::toggle>
```

That's it! All `a-show` elements inside the toggle will automatically animate.

## Built-in Presets

| Preset | Description | Best For |
|--------|-------------|----------|
| `default` | Subtle fade with scale (95% to 100%) | Dropdowns, popovers |
| `fade` | Simple opacity fade | General purpose |
| `opacity` | Same as fade (alias) | General purpose |
| `scale` | Scale from center (0% to 100%) | Modals, dialogs |
| `collapse` | Fade only, no translate | Accordions, FAQs |
| `slide-up` | Slide up from bottom | Toasts, notifications |
| `slide-down` | Slide down from top | Menus, dropdowns |
| `slide-left` | Slide in from left | Sidebars, drawers |
| `slide-right` | Slide in from right | Sidebars, drawers |

## Toggle with Animation

Add the `animation` attribute to any toggle component:

```blade
{{-- Fade animation --}}
<x-accelade::toggle animation="fade">
    <button @click="toggle()">Show/Hide</button>
    <div a-show="toggled">Animated content</div>
</x-accelade::toggle>

{{-- Scale animation for modals --}}
<x-accelade::toggle animation="scale">
    <button @click="toggle()">Open Modal</button>
    <div a-show="toggled" class="modal">
        Modal content...
    </div>
</x-accelade::toggle>

{{-- Collapse for accordions (recommended) --}}
<x-accelade::toggle animation="collapse">
    <button @click="toggle()">FAQ Question</button>
    <div a-show="toggled">
        Answer content that fades smoothly without overlap...
    </div>
</x-accelade::toggle>
```

### Accordion Example

For accordions, use the `collapse` preset to avoid content overlapping headers:

```blade
<div class="space-y-2">
    <x-accelade::toggle animation="collapse">
        <div class="border rounded-lg overflow-hidden">
            <button @click="toggle()" class="w-full p-4 text-left flex justify-between">
                <span>What is Accelade?</span>
                <svg a-class="{'rotate-180': toggled}" class="w-5 h-5 transition-transform">...</svg>
            </button>
            <div a-show="toggled" class="p-4 border-t">
                Accelade is a reactive UI library for Laravel Blade...
            </div>
        </div>
    </x-accelade::toggle>

    <x-accelade::toggle animation="collapse">
        <div class="border rounded-lg overflow-hidden">
            <button @click="toggle()" class="w-full p-4 text-left flex justify-between">
                <span>How do I install it?</span>
                <svg a-class="{'rotate-180': toggled}" class="w-5 h-5 transition-transform">...</svg>
            </button>
            <div a-show="toggled" class="p-4 border-t">
                Run: composer require accelade/accelade
            </div>
        </div>
    </x-accelade::toggle>
</div>
```

## Transition Component

For more control, use the standalone `<x-accelade::transition>` component:

```blade
<x-accelade::toggle>
    <button @click="toggle()">Toggle</button>

    <x-accelade::transition show="toggled" animation="scale">
        <div class="modal">
            This uses the transition component directly
        </div>
    </x-accelade::transition>
</x-accelade::toggle>
```

### Custom Classes

Define your own transition using individual class props:

```blade
<x-accelade::transition
    show="toggled"
    enter="transition-all duration-500 ease-out"
    enter-from="opacity-0 translate-y-8 rotate-6"
    enter-to="opacity-100 translate-y-0 rotate-0"
    leave="transition-all duration-300 ease-in"
    leave-from="opacity-100 translate-y-0 rotate-0"
    leave-to="opacity-0 translate-y-8 -rotate-6"
>
    <div>Custom animated content!</div>
</x-accelade::transition>
```

### Props

| Prop | Type | Description |
|------|------|-------------|
| `show` | string | State expression to watch (e.g., "toggled") |
| `animation` | string | Preset name (default, fade, scale, collapse, slide-*) |
| `enter` | string | Classes during enter transition |
| `enter-from` | string | Classes at start of enter |
| `enter-to` | string | Classes at end of enter |
| `leave` | string | Classes during leave transition |
| `leave-from` | string | Classes at start of leave |
| `leave-to` | string | Classes at end of leave |

## Custom Animation Presets

Register your own animation presets in a service provider:

```php
use Accelade\Facades\Animation;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register a custom "bounce" animation
        Animation::new(
            name: 'bounce',
            enter: 'transition ease-bounce duration-300',
            enterFrom: 'opacity-0 scale-50',
            enterTo: 'opacity-100 scale-100',
            leave: 'transition ease-in duration-200',
            leaveFrom: 'opacity-100 scale-100',
            leaveTo: 'opacity-0 scale-50',
        );

        // Register a custom "slide-fade" animation
        Animation::new(
            name: 'slide-fade',
            enter: 'transition-all ease-out duration-300',
            enterFrom: 'opacity-0 -translate-y-4',
            enterTo: 'opacity-100 translate-y-0',
            leave: 'transition-all ease-in duration-200',
            leaveFrom: 'opacity-100 translate-y-0',
            leaveTo: 'opacity-0 -translate-y-4',
        );
    }
}
```

Then use your custom preset:

```blade
<x-accelade::toggle animation="bounce">
    <button @click="toggle()">Bounce!</button>
    <div a-show="toggled">Bouncy content</div>
</x-accelade::toggle>
```

## Animation Facade API

```php
use Accelade\Facades\Animation;

// Register a new preset
Animation::new(
    name: 'custom',
    enter: '...',
    enterFrom: '...',
    enterTo: '...',
    leave: '...',
    leaveFrom: '...',
    leaveTo: '...',
);

// Get a preset
$preset = Animation::get('fade');

// Check if preset exists
if (Animation::has('custom')) {
    // ...
}

// Get all presets
$all = Animation::all();

// Get all as array (for JSON)
$array = Animation::toArray();
```

## Tips

### Slide Animations with Overflow

When using slide animations (`slide-up`, `slide-down`, `slide-left`, `slide-right`), wrap the content in an `overflow-hidden` container to prevent content from appearing outside boundaries:

```blade
<x-accelade::toggle animation="slide-up">
    <button @click="toggle()">Toggle</button>
    <div class="overflow-hidden">
        <div a-show="toggled">
            Content slides up without overflow
        </div>
    </div>
</x-accelade::toggle>
```

### Accordions

For accordions, always use the `collapse` preset instead of `slide-down` to avoid content overlapping with headers during the leave animation.

### Dropdowns

For dropdown menus, the `default` preset works well:

```blade
<x-accelade::toggle animation="default">
    <div class="relative">
        <button @click="toggle()">Menu</button>
        <div a-show="toggled" class="absolute top-full left-0 mt-1 bg-white shadow-lg rounded-lg">
            <a href="#">Profile</a>
            <a href="#">Settings</a>
            <a href="#">Logout</a>
        </div>
    </div>
</x-accelade::toggle>
```

### Modals

For modals, use `scale` for a nice pop effect:

```blade
<x-accelade::toggle animation="scale">
    <button @click="toggle()">Open Modal</button>

    {{-- Backdrop --}}
    <div a-show="toggled" class="fixed inset-0 bg-black/50" @click="setToggle(false)"></div>

    {{-- Modal --}}
    <div a-show="toggled" class="fixed inset-0 flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 max-w-md">
            <h2>Modal Title</h2>
            <p>Modal content...</p>
            <button @click="setToggle(false)">Close</button>
        </div>
    </div>
</x-accelade::toggle>
```

## CSS Requirements

The animation utility classes are automatically included when you use `@acceladeStyles`. No additional CSS configuration is needed.

If you're using Tailwind CSS and want to customize the animations further, ensure your custom classes are included in your content configuration.
