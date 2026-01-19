{{-- Calendar Component Section - Full-featured Event Calendar --}}
@props(['prefix' => 'a'])

@php
    $textAttr = match($prefix) {
        'v' => 'v-text',
        'data-state' => 'data-state-text',
        's' => 's-text',
        'ng' => 'ng-text',
        default => 'a-text',
    };

    $showAttr = match($prefix) {
        'v' => 'v-show',
        'data-state' => 'data-state-show',
        's' => 's-show',
        'ng' => 'ng-show',
        default => 'a-show',
    };

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

<!-- Demo: Calendar Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Calendar Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Full-featured event calendar with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::calendar&gt;</code>.
        Supports multiple views, drag-and-drop, and resource scheduling.
    </p>

    <!-- Interactive Calendar Demo -->
    <div class="rounded-xl p-4 border border-blue-500/30 mb-4" style="background: rgba(59, 130, 246, 0.1);">
        <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Interactive</span>
            Event Calendar
        </h4>
        <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
            Try dragging events to reschedule, or click on dates to interact.
            Use the toolbar to switch between views.
        </p>

        <x-accelade::calendar
            id="demo-calendar"
            :events="$sampleEvents"
            view="dayGridMonth"
            height="500px"
            :editable="true"
            :selectable="true"
            darkMode="auto"
            :headerToolbar="[
                'start' => 'prev,next today',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,listWeek',
            ]"
            class="rounded-lg overflow-hidden border border-[var(--docs-border)]"
            style="background: var(--docs-bg);"
        />

        <div class="mt-4 p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <p class="text-sm" style="color: var(--docs-text-muted);">
                <strong class="text-blue-500">Try it:</strong> Drag events to move them, resize to change duration, or click and drag on empty space to create a selection.
            </p>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props</h4>
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
                        <td class="py-2 px-3">Initial view (dayGridMonth, timeGridWeek, listWeek, etc.)</td>
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
                        <td class="py-2 px-3">Array of resource objects for resource views</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">editable</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Enable drag-and-drop and resize</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">selectable</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Enable date/time selection</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">height</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'auto'</td>
                        <td class="py-2 px-3">Calendar height (CSS value)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">headerToolbar</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">[...]</td>
                        <td class="py-2 px-3">Toolbar configuration (start, center, end)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-blue-500">darkMode</code></td>
                        <td class="py-2 px-3">bool|'auto'</td>
                        <td class="py-2 px-3">'auto'</td>
                        <td class="py-2 px-3">Enable dark mode theme</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">Available Views</h5>
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach(['dayGridMonth', 'timeGridWeek', 'timeGridDay', 'listWeek', 'listMonth', 'resourceTimeGridDay'] as $viewType)
                <span class="px-2 py-1 text-xs rounded border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">{{ $viewType }}</span>
            @endforeach
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">JavaScript API</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Method</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">Accelade.calendar.get(id)</code></td>
                        <td class="py-2 px-3">Get calendar instance by ID</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">instance.setView(view)</code></td>
                        <td class="py-2 px-3">Change current view</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-blue-500">instance.addEvent(event)</code></td>
                        <td class="py-2 px-3">Add a new event</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-blue-500">instance.removeEventById(id)</code></td>
                        <td class="py-2 px-3">Remove event by ID</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="calendar-example.blade.php">
{{-- Basic calendar --}}
&lt;x-accelade::calendar
    id="my-calendar"
    :events="$events"
    view="dayGridMonth"
    height="600px"
/&gt;

{{-- Editable calendar with drag-and-drop --}}
&lt;x-accelade::calendar
    id="schedule-calendar"
    :events="$events"
    view="timeGridWeek"
    :editable="true"
    :selectable="true"
    slotMinTime="08:00:00"
    slotMaxTime="18:00:00"
/&gt;

{{-- JavaScript API --}}
&lt;script&gt;
const calendar = Accelade.calendar.get('my-calendar');
calendar.addEvent({
    id: 'new-1',
    title: 'New Event',
    start: '2024-01-15T10:00:00',
});
calendar.setView('timeGridWeek');
&lt;/script&gt;
    </x-accelade::code-block>
</section>
