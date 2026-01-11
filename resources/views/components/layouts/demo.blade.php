<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="accelade-framework" content="{{ $framework }}">

    <title>Accelade Demo - {{ ucfirst($framework) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS - Try Vite first, fallback to CDN -->
    @hasSection('vite')
        @yield('vite')
    @else
        @if(file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    @endif

    @acceladeStyles
    @php
        // Set the framework for this page so the correct JS bundle is loaded
        app('accelade')->setFramework($framework);
    @endphp
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen font-sans antialiased">
    <div class="max-w-4xl mx-auto py-12 px-4">
        <!-- Header -->
        <header class="text-center mb-8">
            <h1 class="text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-3">
                Accelade
            </h1>
            <p class="text-xl text-slate-600 mb-4">Reactive Blade templates with your favorite framework</p>
        </header>

        <!-- Framework Tabs (Full Page Reload - different JS runtimes) -->
        <nav class="flex justify-center gap-2 mb-10 flex-wrap">
            <a
                href="{{ route('demo.vanilla') }}"
                class="px-5 py-2.5 rounded-xl font-medium transition {{ $framework === 'vanilla' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                Vanilla
            </a>
            <a
                href="{{ route('demo.vue') }}"
                class="px-5 py-2.5 rounded-xl font-medium transition {{ $framework === 'vue' ? 'bg-green-600 text-white shadow-lg shadow-green-200' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                Vue
            </a>
            <a
                href="{{ route('demo.react') }}"
                class="px-5 py-2.5 rounded-xl font-medium transition {{ $framework === 'react' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                React
            </a>
            <a
                href="{{ route('demo.svelte') }}"
                class="px-5 py-2.5 rounded-xl font-medium transition {{ $framework === 'svelte' ? 'bg-orange-600 text-white shadow-lg shadow-orange-200' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                Svelte
            </a>
            <a
                href="{{ route('demo.angular') }}"
                class="px-5 py-2.5 rounded-xl font-medium transition {{ $framework === 'angular' ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}"
            >
                Angular
            </a>
        </nav>

        <!-- Current Framework Badge -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full border border-slate-200 shadow-sm">
                @if($framework === 'vanilla')
                    <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                    <span class="text-sm font-medium text-slate-600">Using Vanilla JavaScript</span>
                @elseif($framework === 'vue')
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                    <span class="text-sm font-medium text-slate-600">Using Vue.js 3</span>
                @elseif($framework === 'react')
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-sm font-medium text-slate-600">Using React 18</span>
                @elseif($framework === 'svelte')
                    <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                    <span class="text-sm font-medium text-slate-600">Using Svelte 5</span>
                @elseif($framework === 'angular')
                    <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                    <span class="text-sm font-medium text-slate-600">Using Angular 17+</span>
                @endif
            </span>
        </div>

        <!-- Main Content Area (SPA replaces this) -->
        <main data-accelade-page>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="text-center text-slate-400 text-sm mt-12">
            <p class="mb-1">
                <span class="font-semibold text-slate-600">Accelade</span> — Accelerate your Blade templates
            </p>
            <p>
                Built with Laravel {{ app()->version() }} | Framework: {{ $framework }}
            </p>
        </footer>
    </div>

    @acceladeScripts
    @acceladeNotifications
</body>
</html>
