# Draggable Component

The Accelade Draggable component enables drag and drop functionality for sortable lists, kanban boards, and cross-container item transfers. It provides a declarative way to add drag and drop without external dependencies.

## Basic Usage

Wrap a container with draggable items:

```blade
<x-accelade::draggable>
    <div data-draggable-item>Item 1</div>
    <div data-draggable-item>Item 2</div>
    <div data-draggable-item>Item 3</div>
</x-accelade::draggable>
```

Add the `data-draggable-item` attribute to elements that should be draggable.

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `group` | string | null | Group name for cross-container drag |
| `handle` | string | null | CSS selector for drag handle |
| `animation` | int | 150 | Animation duration in milliseconds |
| `ghostClass` | string | 'opacity-50' | CSS classes for ghost/placeholder element |
| `dragClass` | string | 'shadow-lg' | CSS classes applied to dragged element |
| `disabled` | bool | false | Disable dragging |
| `sortable` | bool | true | Enable sorting within container |
| `dropzone` | bool | false | Act as dropzone only (no draggable items) |
| `accepts` | string | null | Comma-separated group names this dropzone accepts |
| `axis` | string | null | Constrain drag to axis: 'x' or 'y' |

## Drag Handles

Restrict dragging to a specific element within each item:

```blade
<x-accelade::draggable handle=".drag-handle">
    <div data-draggable-item class="flex items-center gap-2">
        <span class="drag-handle cursor-move">⋮⋮</span>
        <span>Item content</span>
        <button>Edit</button>
    </div>
</x-accelade::draggable>
```

The `handle` prop accepts a CSS selector. Only clicking on the handle initiates dragging.

## Cross-Container Drag

Enable dragging between containers using the `group` prop:

```blade
<div class="flex gap-4">
    <x-accelade::draggable group="tasks">
        <div data-draggable-item>Task 1</div>
        <div data-draggable-item>Task 2</div>
    </x-accelade::draggable>

    <x-accelade::draggable group="tasks">
        <div data-draggable-item>Task 3</div>
    </x-accelade::draggable>
</div>
```

Items can only be dragged between containers with the same `group` name.

## Kanban Board Example

Build a complete kanban board:

```blade
<div class="grid grid-cols-3 gap-4">
    <div>
        <h3>To Do</h3>
        <x-accelade::draggable group="kanban" class="min-h-[200px] bg-gray-100 p-2 rounded">
            <div data-draggable-item class="bg-white p-3 rounded shadow mb-2">
                Design homepage
            </div>
            <div data-draggable-item class="bg-white p-3 rounded shadow mb-2">
                Write documentation
            </div>
        </x-accelade::draggable>
    </div>

    <div>
        <h3>In Progress</h3>
        <x-accelade::draggable group="kanban" class="min-h-[200px] bg-gray-100 p-2 rounded">
            <div data-draggable-item class="bg-white p-3 rounded shadow mb-2">
                Implement API
            </div>
        </x-accelade::draggable>
    </div>

    <div>
        <h3>Done</h3>
        <x-accelade::draggable group="kanban" class="min-h-[200px] bg-gray-100 p-2 rounded">
            <div data-draggable-item class="bg-white p-3 rounded shadow mb-2">
                Setup project
            </div>
        </x-accelade::draggable>
    </div>
</div>
```

## Axis Constraint

Restrict dragging to a single axis:

```blade
{{-- Horizontal only --}}
<x-accelade::draggable axis="x" class="flex gap-2">
    <div data-draggable-item class="w-20 h-20">1</div>
    <div data-draggable-item class="w-20 h-20">2</div>
    <div data-draggable-item class="w-20 h-20">3</div>
</x-accelade::draggable>

{{-- Vertical only --}}
<x-accelade::draggable axis="y">
    <div data-draggable-item>Item 1</div>
    <div data-draggable-item>Item 2</div>
</x-accelade::draggable>
```

## Custom Styling

### Ghost Class

The ghost is the placeholder shown while dragging:

```blade
<x-accelade::draggable ghostClass="opacity-30 bg-blue-200 border-2 border-dashed border-blue-500">
    <div data-draggable-item>Item</div>
</x-accelade::draggable>
```

### Drag Class

Applied to the element being dragged:

```blade
<x-accelade::draggable dragClass="shadow-2xl scale-105 rotate-3">
    <div data-draggable-item>Item</div>
</x-accelade::draggable>
```

## Exposed Methods

The Draggable component exposes methods for programmatic control:

| Method | Description |
|--------|-------------|
| `enableDrag()` | Enable dragging |
| `disableDrag()` | Disable dragging |
| `isDragEnabled()` | Check if dragging is enabled |
| `getDragItems()` | Get array of draggable items |
| `moveDragItem(from, to)` | Programmatically move item |
| `refreshDrag()` | Re-scan DOM for items |

### Programmatic Control

```blade
<x-accelade::draggable>
    <div data-draggable-item>Item 1</div>
    <div data-draggable-item>Item 2</div>
</x-accelade::draggable>

<button @click="disableDrag()">Lock Order</button>
<button @click="enableDrag()">Unlock Order</button>
<button @click="moveDragItem(0, 2)">Move First to Last</button>
```

## Dropzone Only

Create a dropzone that accepts items but doesn't have sortable items:

```blade
{{-- Source container --}}
<x-accelade::draggable group="files">
    <div data-draggable-item>Document.pdf</div>
    <div data-draggable-item>Image.jpg</div>
</x-accelade::draggable>

{{-- Dropzone --}}
<x-accelade::draggable
    :dropzone="true"
    accepts="files"
    class="border-2 border-dashed p-8 text-center"
>
    Drop files here to delete
</x-accelade::draggable>
```

## Events

The Draggable component dispatches events during drag operations:

| Event | Description |
|-------|-------------|
| `dragstart` | When dragging begins |
| `dragend` | When dragging ends |
| `dragsort` | When items are reordered within container |
| `dragmove` | When item moves to different container |
| `dragenter` | When dragging enters a container |
| `dragleave` | When dragging leaves a container |
| `dragdrop` | When item is dropped |

### Listening to Events

```blade
<x-accelade::draggable @dragsort="handleSort">
    ...
</x-accelade::draggable>
```

```javascript
// Event detail includes:
// - id: Container ID
// - oldIndex: Original index
// - newIndex: New index
// - item: The dragged element
// - from: Source container
// - to: Target container
// - group: Group name

element.addEventListener('dragsort', (e) => {
    console.log('Moved from', e.detail.oldIndex, 'to', e.detail.newIndex);
});

// Global events
document.addEventListener('accelade:drag:sort', (e) => {
    console.log('Sort in container:', e.detail.id);
});

document.addEventListener('accelade:drag:move', (e) => {
    console.log('Moved between containers');
});
```

## Disabling Drag

Disable dragging statically or dynamically:

```blade
{{-- Statically disabled --}}
<x-accelade::draggable :disabled="true">
    ...
</x-accelade::draggable>

{{-- Conditionally disabled --}}
<x-accelade::draggable :disabled="$isLocked">
    ...
</x-accelade::draggable>
```

## State

The Draggable component maintains the following state:

| State | Type | Description |
|-------|------|-------------|
| `isDragging` | boolean | Whether currently dragging |
| `isDragOver` | boolean | Whether drag is over this container |
| `draggedItem` | HTMLElement | Reference to dragged element |
| `draggedIndex` | number | Index of dragged item |

Use these in reactive bindings:

```blade
<x-accelade::draggable>
    <div a-show="isDragOver" class="bg-blue-100 p-4">
        Drop items here
    </div>
    <div data-draggable-item>Item</div>
</x-accelade::draggable>
```

## Animation

Control the animation speed:

```blade
{{-- Fast animation --}}
<x-accelade::draggable :animation="100">
    ...
</x-accelade::draggable>

{{-- No animation --}}
<x-accelade::draggable :animation="0">
    ...
</x-accelade::draggable>

{{-- Slow animation --}}
<x-accelade::draggable :animation="300">
    ...
</x-accelade::draggable>
```

## Use Cases

### Todo List

```blade
<x-accelade::draggable>
    @foreach($todos as $todo)
        <div data-draggable-item class="flex items-center gap-2 p-2 border-b">
            <span class="cursor-move">⋮⋮</span>
            <input type="checkbox" {{ $todo->completed ? 'checked' : '' }}>
            <span>{{ $todo->title }}</span>
        </div>
    @endforeach
</x-accelade::draggable>
```

### Image Gallery

```blade
<x-accelade::draggable axis="x" class="flex gap-2 overflow-x-auto">
    @foreach($images as $image)
        <div data-draggable-item class="flex-shrink-0">
            <img src="{{ $image->url }}" class="w-32 h-32 object-cover rounded">
        </div>
    @endforeach
</x-accelade::draggable>
```

### Priority Queue

```blade
<x-accelade::draggable handle=".priority-handle">
    @foreach($tickets as $index => $ticket)
        <div data-draggable-item class="flex items-center p-3 border-b">
            <span class="priority-handle cursor-move mr-2">{{ $index + 1 }}.</span>
            <span class="flex-1">{{ $ticket->title }}</span>
            <span class="text-sm text-gray-500">{{ $ticket->priority }}</span>
        </div>
    @endforeach
</x-accelade::draggable>
```

## Best Practices

1. **Add visual feedback** - Use `ghostClass` and `dragClass` to make drag operations clear
2. **Use handles for complex items** - When items contain interactive elements (buttons, inputs)
3. **Provide empty state** - Show placeholder text when a container is empty
4. **Consider mobile** - Touch devices work with drag and drop, but consider touch-friendly sizing
5. **Save order** - Listen to `dragsort` events to persist the new order to your backend
6. **Use animation** - Smooth animations improve the user experience

## Next Steps

- [Tooltip Component](tooltip.md) - Contextual information
- [Toggle Component](toggle.md) - Boolean state management
- [Modal Component](modal.md) - Dialogs and slideovers
