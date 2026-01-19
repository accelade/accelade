@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);

    // Sample events for demo
    $sampleEvents = [
        [
            'id' => 1,
            'title' => 'Team Meeting',
            'start' => now()->format('Y-m-d') . 'T09:00:00',
            'end' => now()->format('Y-m-d') . 'T10:00:00',
            'backgroundColor' => '#3b82f6',
        ],
        [
            'id' => 2,
            'title' => 'Product Launch',
            'start' => now()->addDays(2)->format('Y-m-d'),
            'allDay' => true,
            'backgroundColor' => '#22c55e',
        ],
        [
            'id' => 3,
            'title' => 'Client Call',
            'start' => now()->addDays(1)->format('Y-m-d') . 'T14:00:00',
            'end' => now()->addDays(1)->format('Y-m-d') . 'T15:30:00',
            'backgroundColor' => '#f59e0b',
        ],
        [
            'id' => 4,
            'title' => 'Workshop',
            'start' => now()->addDays(3)->format('Y-m-d') . 'T10:00:00',
            'end' => now()->addDays(3)->format('Y-m-d') . 'T16:00:00',
            'backgroundColor' => '#8b5cf6',
        ],
        [
            'id' => 5,
            'title' => 'Sprint Review',
            'start' => now()->addDays(5)->format('Y-m-d') . 'T11:00:00',
            'end' => now()->addDays(5)->format('Y-m-d') . 'T12:00:00',
            'backgroundColor' => '#ec4899',
        ],
    ];
@endphp

<x-accelade::layouts.docs :framework="$framework" section="calendar" :documentation="$documentation" :hasDemo="$hasDemo">
    {{-- Live Demo --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Calendar Component</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Full-featured event calendar powered by @event-calendar/core. Supports multiple views, drag-and-drop editing, and dark mode. Use the toolbar buttons to switch views.
        </p>

        <div class="mb-4 p-4 rounded-xl border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <x-accelade::calendar
                id="demo-calendar"
                :events="$sampleEvents"
                view="dayGridMonth"
                height="450px"
                :editable="true"
                :selectable="true"
                darkMode="auto"
                :headerToolbar="[
                    'start' => 'prev,next today',
                    'center' => 'title',
                    'end' => 'dayGridMonth,timeGridWeek,listWeek',
                ]"
                class="rounded-lg overflow-hidden"
                style="--ec-bg-color: var(--docs-bg); --ec-text-color: var(--docs-text); --ec-border-color: var(--docs-border);"
            />
        </div>

        <div class="p-3 rounded-lg border border-blue-500/30 mb-4" style="background: rgba(59, 130, 246, 0.1);">
            <p class="text-sm" style="color: var(--docs-text-muted);">
                <strong class="text-blue-500">Try it:</strong> Drag events to reschedule, resize to change duration, or click and drag on empty space to create a selection.
            </p>
        </div>

        <x-accelade::code-block language="blade" filename="calendar-basic.blade.php">
{{-- Basic Calendar --}}
&lt;x-accelade::calendar
    id="my-calendar"
    :events="$events"
    view="dayGridMonth"
    height="500px"
    :editable="true"
    :selectable="true"
/&gt;

{{-- Events array structure --}}
@php
$events = [
    [
        'id' => 1,
        'title' => 'Team Meeting',
        'start' => '2024-01-15T09:00:00',
        'end' => '2024-01-15T10:00:00',
        'backgroundColor' => '#3788d8',
    ],
    [
        'id' => 2,
        'title' => 'All Day Event',
        'start' => '2024-01-16',
        'allDay' => true,
    ],
];
@endphp
        </x-accelade::code-block>
    </section>

    {{-- JavaScript API --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">JavaScript API</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            Access calendar instances programmatically to navigate, add events, or change views.
        </p>

        <x-accelade::code-block language="javascript" filename="calendar-api.js">
// Get calendar instance
const calendar = Accelade.calendar.get('my-calendar');

// Navigation
calendar.prev();
calendar.next();
calendar.today();
calendar.gotoDate('2024-06-15');

// Views
calendar.setView('timeGridWeek');
const currentView = calendar.getView();

// Events
calendar.addEvent({
    id: 'new-1',
    title: 'New Meeting',
    start: '2024-01-20T14:00:00',
    end: '2024-01-20T15:00:00',
});

calendar.updateEvent({ id: 'new-1', title: 'Updated Title' });
calendar.removeEventById('new-1');
calendar.refetchEvents();
        </x-accelade::code-block>
    </section>

    {{-- DOM Events --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">DOM Events</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">
            The calendar dispatches custom events for user interactions.
        </p>

        <x-accelade::code-block language="javascript" filename="calendar-events.js">
// Event clicked
document.addEventListener('calendar:eventClick', (e) => {
    console.log('Event:', e.detail.event);
});

// Date clicked
document.addEventListener('calendar:dateClick', (e) => {
    console.log('Date:', e.detail.date);
});

// Date range selected
document.addEventListener('calendar:select', (e) => {
    console.log('Selection:', e.detail.start, 'to', e.detail.end);
});

// Event dragged
document.addEventListener('calendar:eventDrop', (e) => {
    console.log('Moved:', e.detail.event);
});

// Event resized
document.addEventListener('calendar:eventResize', (e) => {
    console.log('Resized:', e.detail.event);
});
        </x-accelade::code-block>
    </section>

    {{-- Props Reference --}}
    <section class="bg-[var(--docs-bg-alt)] rounded-xl p-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-slate-500 rounded-full"></span>
            <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Props Reference</h3>
        </div>

        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">view</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'dayGridMonth'</td>
                        <td class="py-2 px-3">Initial calendar view</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">events</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">[]</td>
                        <td class="py-2 px-3">Array of event objects</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">resources</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">[]</td>
                        <td class="py-2 px-3">Resources for resource views</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">editable</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Enable drag-and-drop</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">selectable</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Enable date selection</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">height</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'auto'</td>
                        <td class="py-2 px-3">Calendar height (CSS)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">headerToolbar</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">[...]</td>
                        <td class="py-2 px-3">Toolbar config (start, center, end)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">slotMinTime</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'00:00:00'</td>
                        <td class="py-2 px-3">Earliest time slot</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">slotMaxTime</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'24:00:00'</td>
                        <td class="py-2 px-3">Latest time slot</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-blue-500">darkMode</code></td>
                        <td class="py-2 px-3">bool|'auto'</td>
                        <td class="py-2 px-3">'auto'</td>
                        <td class="py-2 px-3">Dark mode theme</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h4 class="font-medium mb-2" style="color: var(--docs-text);">Available Views</h4>
        <div class="flex flex-wrap gap-2">
            @foreach(['dayGridMonth', 'timeGridWeek', 'timeGridDay', 'listWeek', 'listMonth', 'resourceTimeGridDay'] as $viewType)
                <span class="px-2 py-1 text-xs rounded border border-[var(--docs-border)]" style="background: var(--docs-bg); color: var(--docs-text-muted);">{{ $viewType }}</span>
            @endforeach
        </div>
    </section>
</x-accelade::layouts.docs>
