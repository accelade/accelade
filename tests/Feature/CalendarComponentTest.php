<?php

declare(strict_types=1);

use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\View\ComponentAttributeBag;

function makeCalendarView(array $props = []): string
{
    $defaults = [
        'view' => 'dayGridMonth',
        'date' => null,
        'height' => 'auto',
        'events' => [],
        'eventSources' => [],
        'resources' => [],
        'headerToolbar' => null,
        'editable' => false,
        'selectable' => false,
        'nowIndicator' => true,
        'allDaySlot' => true,
        'firstDay' => 0,
        'locale' => 'en',
        'slotDuration' => '00:30:00',
        'slotMinTime' => '00:00:00',
        'slotMaxTime' => '24:00:00',
        'scrollTime' => '06:00:00',
        'slotHeight' => 24,
        'validRange' => null,
        'hiddenDays' => [],
        'duration' => null,
        'eventBackgroundColor' => '#3788d8',
        'eventTextColor' => '#ffffff',
        'theme' => [],
        'customButtons' => [],
        'buttonText' => [],
        'darkMode' => 'auto',
        'pointer' => true,
        'lazyFetching' => true,
        'slot' => new HtmlString(''),
        'attributes' => new ComponentAttributeBag([]),
    ];

    return View::file(
        __DIR__.'/../../resources/views/components/calendar.blade.php',
        array_merge($defaults, $props)
    )->render();
}

it('renders basic calendar component', function () {
    $html = makeCalendarView();

    expect($html)
        ->toContain('data-accelade')
        ->toContain('data-accelade-calendar');
});

it('generates unique id when not provided', function () {
    $html = makeCalendarView();

    expect($html)->toMatch('/data-calendar-id="calendar-[a-f0-9]+"/');
});

it('uses provided id attribute', function () {
    $html = makeCalendarView([
        'attributes' => new ComponentAttributeBag(['id' => 'my-calendar']),
    ]);

    expect($html)->toContain('data-calendar-id="my-calendar"');
});

it('renders with default view', function () {
    $html = makeCalendarView();

    expect($html)->toContain('&quot;view&quot;:&quot;dayGridMonth&quot;');
});

it('renders with custom view', function () {
    $html = makeCalendarView(['view' => 'timeGridWeek']);

    expect($html)->toContain('&quot;view&quot;:&quot;timeGridWeek&quot;');
});

it('renders with events array', function () {
    $events = [
        [
            'id' => 1,
            'title' => 'Test Event',
            'start' => '2024-01-15T09:00:00',
        ],
    ];

    $html = makeCalendarView(['events' => $events]);

    expect($html)
        ->toContain('&quot;id&quot;:1')
        ->toContain('&quot;title&quot;:&quot;Test Event&quot;')
        ->toContain('&quot;start&quot;:&quot;2024-01-15T09:00:00&quot;');
});

it('renders with multiple events', function () {
    $events = [
        ['id' => 1, 'title' => 'Event 1', 'start' => '2024-01-15'],
        ['id' => 2, 'title' => 'Event 2', 'start' => '2024-01-16'],
        ['id' => 3, 'title' => 'Event 3', 'start' => '2024-01-17'],
    ];

    $html = makeCalendarView(['events' => $events]);

    expect($html)
        ->toContain('&quot;title&quot;:&quot;Event 1&quot;')
        ->toContain('&quot;title&quot;:&quot;Event 2&quot;')
        ->toContain('&quot;title&quot;:&quot;Event 3&quot;');
});

it('renders with resources', function () {
    $resources = [
        ['id' => 1, 'title' => 'Room A'],
        ['id' => 2, 'title' => 'Room B'],
    ];

    $html = makeCalendarView(['resources' => $resources]);

    expect($html)
        ->toContain('&quot;title&quot;:&quot;Room A&quot;')
        ->toContain('&quot;title&quot;:&quot;Room B&quot;');
});

it('renders with editable true', function () {
    $html = makeCalendarView(['editable' => true]);

    expect($html)->toContain('&quot;editable&quot;:true');
});

it('renders with editable false', function () {
    $html = makeCalendarView(['editable' => false]);

    expect($html)->toContain('&quot;editable&quot;:false');
});

it('renders with selectable true', function () {
    $html = makeCalendarView(['selectable' => true]);

    expect($html)->toContain('&quot;selectable&quot;:true');
});

it('renders with custom height', function () {
    $html = makeCalendarView(['height' => '600px']);

    expect($html)->toContain('&quot;height&quot;:&quot;600px&quot;');
});

it('renders with custom slot times', function () {
    $html = makeCalendarView([
        'slotMinTime' => '08:00:00',
        'slotMaxTime' => '18:00:00',
    ]);

    expect($html)
        ->toContain('&quot;slotMinTime&quot;:&quot;08:00:00&quot;')
        ->toContain('&quot;slotMaxTime&quot;:&quot;18:00:00&quot;');
});

it('renders with custom first day', function () {
    $html = makeCalendarView(['firstDay' => 1]);

    expect($html)->toContain('&quot;firstDay&quot;:1');
});

it('renders with hidden days', function () {
    $html = makeCalendarView(['hiddenDays' => [0, 6]]);

    expect($html)->toContain('&quot;hiddenDays&quot;:[0,6]');
});

it('renders with custom header toolbar', function () {
    $html = makeCalendarView([
        'headerToolbar' => [
            'start' => 'prev,next',
            'center' => 'title',
            'end' => 'dayGridMonth,timeGridWeek',
        ],
    ]);

    expect($html)
        ->toContain('&quot;start&quot;:&quot;prev,next&quot;')
        ->toContain('&quot;center&quot;:&quot;title&quot;')
        ->toContain('&quot;end&quot;:&quot;dayGridMonth,timeGridWeek&quot;');
});

it('renders with custom event colors', function () {
    $html = makeCalendarView([
        'eventBackgroundColor' => '#ff0000',
        'eventTextColor' => '#000000',
    ]);

    expect($html)
        ->toContain('&quot;eventBackgroundColor&quot;:&quot;#ff0000&quot;')
        ->toContain('&quot;eventTextColor&quot;:&quot;#000000&quot;');
});

it('renders with theme configuration', function () {
    $html = makeCalendarView([
        'theme' => [
            'primaryColor' => '#ec4899',
            'todayColor' => 'rgba(236, 72, 153, 0.2)',
        ],
    ]);

    expect($html)
        ->toContain('&quot;primaryColor&quot;:&quot;#ec4899&quot;')
        ->toContain('todayColor');
});

it('renders with dark mode auto', function () {
    $html = makeCalendarView(['darkMode' => 'auto']);

    expect($html)->toContain('&quot;darkMode&quot;:&quot;auto&quot;');
});

it('renders with dark mode true', function () {
    $html = makeCalendarView(['darkMode' => true]);

    expect($html)->toContain('&quot;darkMode&quot;:true');
});

it('renders with dark mode false', function () {
    $html = makeCalendarView(['darkMode' => false]);

    expect($html)->toContain('&quot;darkMode&quot;:false');
});

it('renders with valid range', function () {
    $html = makeCalendarView([
        'validRange' => [
            'start' => '2024-01-01',
            'end' => '2024-12-31',
        ],
    ]);

    expect($html)
        ->toContain('&quot;start&quot;:&quot;2024-01-01&quot;')
        ->toContain('&quot;end&quot;:&quot;2024-12-31&quot;');
});

it('renders with slot duration', function () {
    $html = makeCalendarView(['slotDuration' => '00:15:00']);

    expect($html)->toContain('&quot;slotDuration&quot;:&quot;00:15:00&quot;');
});

it('renders with scroll time', function () {
    $html = makeCalendarView(['scrollTime' => '09:00:00']);

    expect($html)->toContain('&quot;scrollTime&quot;:&quot;09:00:00&quot;');
});

it('renders with slot height', function () {
    $html = makeCalendarView(['slotHeight' => 30]);

    expect($html)->toContain('&quot;slotHeight&quot;:30');
});

it('renders with now indicator', function () {
    $html = makeCalendarView(['nowIndicator' => true]);

    expect($html)->toContain('&quot;nowIndicator&quot;:true');
});

it('renders with all day slot', function () {
    $html = makeCalendarView(['allDaySlot' => false]);

    expect($html)->toContain('&quot;allDaySlot&quot;:false');
});

it('renders with locale', function () {
    $html = makeCalendarView(['locale' => 'de']);

    expect($html)->toContain('&quot;locale&quot;:&quot;de&quot;');
});

it('renders with pointer option', function () {
    $html = makeCalendarView(['pointer' => false]);

    expect($html)->toContain('&quot;pointer&quot;:false');
});

it('renders with lazy fetching option', function () {
    $html = makeCalendarView(['lazyFetching' => false]);

    expect($html)->toContain('&quot;lazyFetching&quot;:false');
});

it('merges additional attributes', function () {
    $html = makeCalendarView([
        'attributes' => new ComponentAttributeBag([
            'class' => 'my-calendar-class',
            'data-testid' => 'calendar-test',
        ]),
    ]);

    expect($html)
        ->toContain('class="my-calendar-class"')
        ->toContain('data-testid="calendar-test"');
});

it('renders initial state with current view', function () {
    $html = makeCalendarView(['view' => 'timeGridWeek']);

    expect($html)->toContain('&quot;currentView&quot;:&quot;timeGridWeek&quot;');
});

it('renders initial state with empty events', function () {
    $html = makeCalendarView();

    expect($html)->toContain('&quot;events&quot;:[]');
});

it('renders initial state with is loading false', function () {
    $html = makeCalendarView();

    expect($html)->toContain('&quot;isLoading&quot;:false');
});

it('renders event with all day flag', function () {
    $events = [
        [
            'id' => 1,
            'title' => 'All Day Event',
            'start' => '2024-01-15',
            'allDay' => true,
        ],
    ];

    $html = makeCalendarView(['events' => $events]);

    expect($html)
        ->toContain('&quot;allDay&quot;:true');
});

it('renders event with resource id', function () {
    $events = [
        [
            'id' => 1,
            'title' => 'Resource Event',
            'start' => '2024-01-15T09:00:00',
            'resourceId' => 2,
        ],
    ];

    $html = makeCalendarView(['events' => $events]);

    expect($html)->toContain('&quot;resourceId&quot;:2');
});

it('renders with custom button text', function () {
    $html = makeCalendarView([
        'buttonText' => [
            'today' => 'Today',
            'month' => 'Month',
            'week' => 'Week',
        ],
    ]);

    expect($html)
        ->toContain('&quot;today&quot;:&quot;Today&quot;')
        ->toContain('&quot;month&quot;:&quot;Month&quot;')
        ->toContain('&quot;week&quot;:&quot;Week&quot;');
});

it('renders with all calendar views', function () {
    $views = [
        'dayGridDay',
        'dayGridWeek',
        'dayGridMonth',
        'timeGridDay',
        'timeGridWeek',
        'listDay',
        'listWeek',
        'listMonth',
        'listYear',
        'resourceTimeGridDay',
        'resourceTimeGridWeek',
        'resourceTimelineDay',
        'resourceTimelineWeek',
        'resourceTimelineMonth',
    ];

    foreach ($views as $view) {
        $html = makeCalendarView(['view' => $view]);
        expect($html)->toContain('&quot;view&quot;:&quot;'.$view.'&quot;');
    }
});
