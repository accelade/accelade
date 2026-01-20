<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($framework)
        <meta name="accelade-framework" content="{{ $framework }}">
    @endif

    <title>{{ $title ?? config('app.name', 'Accelade') }}</title>
    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @hasSection('vite')
        @yield('vite')
    @else
        @if(file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    @endif

    @acceladeStyles

    @if($framework)
        @php
            app('accelade')->setFramework($framework);
        @endphp
    @endif

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-sans antialiased dark:bg-gray-900">
    @if($header)
        @hasSection('header')
            @yield('header')
        @else
            {{ $headerSlot ?? '' }}
        @endif
    @endif

    <div class="min-h-full">
        @if($sidebar)
            <div class="flex">
                @hasSection('sidebar')
                    <aside class="hidden w-64 shrink-0 lg:block">
                        @yield('sidebar')
                    </aside>
                @else
                    {{ $sidebarSlot ?? '' }}
                @endif

                <main class="flex-1" data-accelade-page>
                    {{ $slot }}
                </main>
            </div>
        @else
            <main data-accelade-page>
                {{ $slot }}
            </main>
        @endif
    </div>

    @if($footer)
        @hasSection('footer')
            @yield('footer')
        @else
            {{ $footerSlot ?? '' }}
        @endif
    @endif

    @acceladeScripts
    @acceladeNotifications

    @stack('scripts')
</body>
</html>
