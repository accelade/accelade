@props([
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
])

@php
    $framework = config('accelade.framework', 'vanilla');
    $id = $attributes->get('id', 'calendar-' . uniqid());

    // Build calendar configuration
    $calendarConfig = [
        'view' => $view,
        'date' => $date ?? now()->toIso8601String(),
        'height' => $height,
        'events' => $events,
        'eventSources' => $eventSources,
        'resources' => $resources,
        'headerToolbar' => $headerToolbar ?? [
            'start' => 'prev,next today',
            'center' => 'title',
            'end' => 'dayGridMonth,timeGridWeek,timeGridDay',
        ],
        'editable' => (bool) $editable,
        'selectable' => (bool) $selectable,
        'nowIndicator' => (bool) $nowIndicator,
        'allDaySlot' => (bool) $allDaySlot,
        'firstDay' => (int) $firstDay,
        'locale' => $locale,
        'slotDuration' => $slotDuration,
        'slotMinTime' => $slotMinTime,
        'slotMaxTime' => $slotMaxTime,
        'scrollTime' => $scrollTime,
        'slotHeight' => (int) $slotHeight,
        'validRange' => $validRange,
        'hiddenDays' => $hiddenDays,
        'duration' => $duration,
        'eventBackgroundColor' => $eventBackgroundColor,
        'eventTextColor' => $eventTextColor,
        'theme' => $theme,
        'customButtons' => $customButtons,
        'buttonText' => $buttonText,
        'darkMode' => $darkMode,
        'pointer' => (bool) $pointer,
        'lazyFetching' => (bool) $lazyFetching,
    ];

    // Build initial state
    $initialState = [
        'currentView' => $view,
        'currentDate' => $date ?? now()->toIso8601String(),
        'selectedRange' => null,
        'events' => $events,
        'isLoading' => false,
    ];
@endphp

<div
    data-accelade
    data-accelade-calendar
    data-calendar-id="{{ $id }}"
    data-calendar-config="{{ json_encode($calendarConfig) }}"
    data-accelade-state="{{ json_encode($initialState) }}"
    {{ $attributes->except(['id', 'view', 'date', 'height', 'events', 'eventSources', 'resources', 'headerToolbar', 'editable', 'selectable', 'nowIndicator', 'allDaySlot', 'firstDay', 'locale', 'slotDuration', 'slotMinTime', 'slotMaxTime', 'scrollTime', 'slotHeight', 'validRange', 'hiddenDays', 'duration', 'eventBackgroundColor', 'eventTextColor', 'theme', 'customButtons', 'buttonText', 'darkMode', 'pointer', 'lazyFetching']) }}
>{{ $slot }}</div>
