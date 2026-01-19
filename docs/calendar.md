# Calendar Component

The Accelade Calendar component provides a full-featured event calendar using [@event-calendar/core](https://github.com/vkurko/calendar). It supports multiple views, drag-and-drop editing, resource scheduling, and dark mode.

## Basic Usage

```blade
<x-accelade::calendar
    id="my-calendar"
    :events="$events"
/>
```

## Views

The calendar supports multiple view types:

### Day Grid Views

```blade
{{-- Month view (default) --}}
<x-accelade::calendar view="dayGridMonth" :events="$events" />

{{-- Week view --}}
<x-accelade::calendar view="dayGridWeek" :events="$events" />

{{-- Day view --}}
<x-accelade::calendar view="dayGridDay" :events="$events" />
```

### Time Grid Views

```blade
{{-- Week with time slots --}}
<x-accelade::calendar
    view="timeGridWeek"
    :events="$events"
    slotMinTime="08:00:00"
    slotMaxTime="18:00:00"
/>

{{-- Day with time slots --}}
<x-accelade::calendar
    view="timeGridDay"
    :events="$events"
/>
```

### List Views

```blade
{{-- List view for agenda-style display --}}
<x-accelade::calendar view="listWeek" :events="$events" />
<x-accelade::calendar view="listMonth" :events="$events" />
<x-accelade::calendar view="listYear" :events="$events" />
```

### Resource Views

```blade
{{-- Resource time grid --}}
<x-accelade::calendar
    view="resourceTimeGridDay"
    :events="$events"
    :resources="$resources"
/>

{{-- Resource timeline --}}
<x-accelade::calendar
    view="resourceTimelineWeek"
    :events="$events"
    :resources="$resources"
/>
```

## Events

Events are passed as an array with the following structure:

```php
$events = [
    [
        'id' => 1,
        'title' => 'Team Meeting',
        'start' => '2024-01-15T09:00:00',
        'end' => '2024-01-15T10:00:00',
        'backgroundColor' => '#3788d8',
        'textColor' => '#ffffff',
    ],
    [
        'id' => 2,
        'title' => 'All Day Event',
        'start' => '2024-01-16',
        'allDay' => true,
        'backgroundColor' => '#22c55e',
    ],
];
```

### Event Properties

| Property | Type | Description |
|----------|------|-------------|
| `id` | string/number | Unique identifier |
| `title` | string | Event title |
| `start` | Date/string | Start date/time |
| `end` | Date/string | End date/time (optional) |
| `allDay` | boolean | All-day event flag |
| `backgroundColor` | string | Background color |
| `textColor` | string | Text color |
| `editable` | boolean | Per-event editability |
| `resourceId` | string/number | Resource assignment |
| `resourceIds` | array | Multiple resource assignments |
| `extendedProps` | object | Custom properties |

## Resources

Resources are used for scheduling views (rooms, staff, equipment):

```php
$resources = [
    ['id' => 1, 'title' => 'Room A', 'eventBackgroundColor' => '#3788d8'],
    ['id' => 2, 'title' => 'Room B', 'eventBackgroundColor' => '#22c55e'],
    ['id' => 3, 'title' => 'Room C', 'eventBackgroundColor' => '#f59e0b'],
];

$events = [
    [
        'id' => 1,
        'title' => 'Meeting',
        'start' => '2024-01-15T09:00:00',
        'end' => '2024-01-15T11:00:00',
        'resourceId' => 1,  // Assigned to Room A
    ],
];
```

```blade
<x-accelade::calendar
    view="resourceTimeGridDay"
    :events="$events"
    :resources="$resources"
/>
```

## Interactive Features

### Editable Calendar

Enable drag-and-drop to move and resize events:

```blade
<x-accelade::calendar
    :events="$events"
    :editable="true"
/>
```

### Selectable Calendar

Enable date/time selection:

```blade
<x-accelade::calendar
    :events="$events"
    :selectable="true"
/>
```

### Both Editable and Selectable

```blade
<x-accelade::calendar
    :events="$events"
    :editable="true"
    :selectable="true"
/>
```

## Configuration Options

### Time Slots

```blade
<x-accelade::calendar
    view="timeGridWeek"
    slotDuration="00:30:00"     {{-- 30 minute slots --}}
    slotMinTime="07:00:00"      {{-- Start at 7 AM --}}
    slotMaxTime="20:00:00"      {{-- End at 8 PM --}}
    scrollTime="08:00:00"       {{-- Initial scroll position --}}
    :slotHeight="24"            {{-- Slot height in pixels --}}
/>
```

### Week Configuration

```blade
<x-accelade::calendar
    :firstDay="1"              {{-- Start week on Monday --}}
    :hiddenDays="[0, 6]"       {{-- Hide Sunday and Saturday --}}
/>
```

### Header Toolbar

```blade
<x-accelade::calendar
    :headerToolbar="[
        'start' => 'prev,next today',
        'center' => 'title',
        'end' => 'dayGridMonth,timeGridWeek,timeGridDay',
    ]"
/>
```

### Date Constraints

```blade
<x-accelade::calendar
    :validRange="[
        'start' => '2024-01-01',
        'end' => '2024-12-31',
    ]"
/>
```

## Theming

### Dark Mode

```blade
{{-- Auto-detect from system/page --}}
<x-accelade::calendar darkMode="auto" />

{{-- Force dark mode --}}
<x-accelade::calendar :darkMode="true" />

{{-- Force light mode --}}
<x-accelade::calendar :darkMode="false" />
```

### Custom Theme

```blade
<x-accelade::calendar
    :theme="[
        'primaryColor' => '#ec4899',
        'todayColor' => 'rgba(236, 72, 153, 0.2)',
        'backgroundColor' => '#1a1a2e',
        'textColor' => '#eaeaea',
        'borderColor' => '#333',
    ]"
    eventBackgroundColor="#ec4899"
    eventTextColor="#ffffff"
/>
```

### Theme Properties

| Property | Description |
|----------|-------------|
| `textColor` | Calendar text color |
| `backgroundColor` | Calendar background |
| `primaryColor` | Primary highlight color |
| `borderColor` | Border color |
| `todayColor` | Today highlight color |
| `eventBackgroundColor` | Default event background |
| `eventTextColor` | Default event text color |

## Event Sources

For dynamic event loading from an API:

```blade
<x-accelade::calendar
    :eventSources="[
        [
            'url' => '/api/events',
            'method' => 'GET',
            'extraParams' => [
                'category' => 'meetings',
            ],
        ],
    ]"
/>
```

## JavaScript API

### Get Calendar Instance

```javascript
const calendar = Accelade.calendar.get('my-calendar');
```

### Navigation

```javascript
// Navigate between periods
calendar.prev();
calendar.next();
calendar.today();

// Go to specific date
calendar.gotoDate('2024-06-15');
calendar.gotoDate(new Date(2024, 5, 15));

// Get current date
const date = calendar.getDate();
```

### Views

```javascript
// Change view
calendar.setView('timeGridWeek');
calendar.setView('dayGridMonth');

// Get current view
const view = calendar.getView();
```

### Event Management

```javascript
// Add event
calendar.addEvent({
    id: 'new-1',
    title: 'New Meeting',
    start: '2024-01-20T14:00:00',
    end: '2024-01-20T15:00:00',
    backgroundColor: '#3788d8',
});

// Update event
calendar.updateEvent({
    id: 'new-1',
    title: 'Updated Title',
    start: '2024-01-20T15:00:00',
    end: '2024-01-20T16:00:00',
});

// Remove event
calendar.removeEventById('new-1');

// Get all events
const events = calendar.getEvents();

// Get event by ID
const event = calendar.getEventById('new-1');

// Refetch from sources
calendar.refetchEvents();
```

### Options

```javascript
// Get option
const isEditable = calendar.getOption('editable');

// Set option
calendar.setOption('editable', true);
calendar.setOption('selectable', false);
```

### Cleanup

```javascript
// Dispose calendar instance
calendar.dispose();
```

## Events

The calendar emits custom DOM events:

### Event Click

```javascript
document.addEventListener('calendar:eventClick', (e) => {
    console.log('Event clicked:', e.detail.event);
    console.log('Calendar ID:', e.detail.id);
});
```

### Date Click

```javascript
document.addEventListener('calendar:dateClick', (e) => {
    console.log('Date clicked:', e.detail.date);
    console.log('All day:', e.detail.allDay);
});
```

### Date Select

```javascript
document.addEventListener('calendar:select', (e) => {
    console.log('Selection:', e.detail.start, 'to', e.detail.end);
    console.log('All day:', e.detail.allDay);
});
```

### Event Drop (Drag)

```javascript
document.addEventListener('calendar:eventDrop', (e) => {
    console.log('Event moved:', e.detail.event);
    console.log('Old event:', e.detail.oldEvent);

    // Revert the change if needed
    e.detail.revert();
});
```

### Event Resize

```javascript
document.addEventListener('calendar:eventResize', (e) => {
    console.log('Event resized:', e.detail.event);
    console.log('Old event:', e.detail.oldEvent);
});
```

### View Change

```javascript
document.addEventListener('calendar:viewChange', (e) => {
    console.log('View changed to:', e.detail.view);
    console.log('Date range:', e.detail.start, 'to', e.detail.end);
});
```

## Full Example

```blade
@php
$events = [
    [
        'id' => 1,
        'title' => 'Team Standup',
        'start' => now()->format('Y-m-d') . 'T09:00:00',
        'end' => now()->format('Y-m-d') . 'T09:30:00',
        'backgroundColor' => '#3788d8',
    ],
    [
        'id' => 2,
        'title' => 'Sprint Planning',
        'start' => now()->addDays(1)->format('Y-m-d') . 'T10:00:00',
        'end' => now()->addDays(1)->format('Y-m-d') . 'T12:00:00',
        'backgroundColor' => '#22c55e',
    ],
    [
        'id' => 3,
        'title' => 'Conference',
        'start' => now()->addDays(3)->format('Y-m-d'),
        'end' => now()->addDays(5)->format('Y-m-d'),
        'allDay' => true,
        'backgroundColor' => '#f59e0b',
    ],
];
@endphp

<x-accelade::calendar
    id="team-calendar"
    :events="$events"
    view="timeGridWeek"
    height="600px"
    :editable="true"
    :selectable="true"
    :firstDay="1"
    slotMinTime="08:00:00"
    slotMaxTime="18:00:00"
    :headerToolbar="[
        'start' => 'prev,next today',
        'center' => 'title',
        'end' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    ]"
    darkMode="auto"
    class="rounded-lg shadow-lg"
/>

<script>
document.addEventListener('calendar:select', (e) => {
    if (confirm('Create event from ' + e.detail.startStr + ' to ' + e.detail.endStr + '?')) {
        const calendar = Accelade.calendar.get('team-calendar');
        calendar.addEvent({
            id: 'new-' + Date.now(),
            title: 'New Event',
            start: e.detail.start,
            end: e.detail.end,
            allDay: e.detail.allDay,
        });
    }
});

document.addEventListener('calendar:eventDrop', async (e) => {
    try {
        await fetch('/api/events/' + e.detail.event.id, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                start: e.detail.event.start,
                end: e.detail.event.end,
            }),
        });
    } catch (error) {
        e.detail.revert();
        alert('Failed to save changes');
    }
});
</script>
```

## All Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | auto | Unique calendar identifier |
| `view` | string | 'dayGridMonth' | Initial view |
| `date` | Date/string | now | Initial date to display |
| `height` | string | 'auto' | Calendar height |
| `events` | array | [] | Event objects |
| `eventSources` | array | [] | Dynamic event sources |
| `resources` | array | [] | Resource objects |
| `headerToolbar` | array | [...] | Toolbar configuration |
| `editable` | boolean | false | Enable drag/resize |
| `selectable` | boolean | false | Enable selection |
| `nowIndicator` | boolean | true | Show current time |
| `allDaySlot` | boolean | true | Show all-day section |
| `firstDay` | integer | 0 | First day of week |
| `locale` | string | 'en' | Locale for formatting |
| `slotDuration` | string | '00:30:00' | Time slot duration |
| `slotMinTime` | string | '00:00:00' | Earliest slot |
| `slotMaxTime` | string | '24:00:00' | Latest slot |
| `scrollTime` | string | '06:00:00' | Initial scroll |
| `slotHeight` | integer | 24 | Slot height in px |
| `validRange` | array | null | Navigation limits |
| `hiddenDays` | array | [] | Days to hide |
| `eventBackgroundColor` | string | '#3788d8' | Default event bg |
| `eventTextColor` | string | '#ffffff' | Default event text |
| `theme` | array | [] | Theme configuration |
| `darkMode` | bool/'auto' | 'auto' | Dark mode setting |
| `pointer` | boolean | true | Show pointer on events |
| `lazyFetching` | boolean | true | Lazy fetch events |

## Next Steps

- [Draggable Component](draggable.md) - Drag and drop lists
- [Charts](charts.md) - Data visualization
- [Toggle Component](toggle.md) - Boolean state management
