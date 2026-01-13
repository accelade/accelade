@props([
    'framework' => 'vanilla',
    'section' => 'counter',
])

@php
    $frameworkColors = [
        'vanilla' => ['bg' => 'bg-indigo-600', 'shadow' => 'shadow-indigo-200', 'text' => 'text-indigo-600', 'dot' => 'bg-indigo-500', 'ring' => 'ring-indigo-500'],
        'vue' => ['bg' => 'bg-emerald-600', 'shadow' => 'shadow-emerald-200', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'ring' => 'ring-emerald-500'],
        'react' => ['bg' => 'bg-cyan-600', 'shadow' => 'shadow-cyan-200', 'text' => 'text-cyan-600', 'dot' => 'bg-cyan-500', 'ring' => 'ring-cyan-500'],
        'svelte' => ['bg' => 'bg-orange-600', 'shadow' => 'shadow-orange-200', 'text' => 'text-orange-600', 'dot' => 'bg-orange-500', 'ring' => 'ring-orange-500'],
        'angular' => ['bg' => 'bg-red-600', 'shadow' => 'shadow-red-200', 'text' => 'text-red-600', 'dot' => 'bg-red-500', 'ring' => 'ring-red-500'],
    ];
    $colors = $frameworkColors[$framework] ?? $frameworkColors['vanilla'];

    // Grouped sections for better organization
    $sectionGroups = [
        'Getting Started' => [
            ['id' => 'counter', 'label' => 'Counter', 'icon' => '🔢'],
            ['id' => 'scripts', 'label' => 'Custom Scripts', 'icon' => '📜'],
        ],
        'Data & State' => [
            ['id' => 'data', 'label' => 'Data Component', 'icon' => '💾'],
            ['id' => 'state', 'label' => 'State Component', 'icon' => '🔀'],
            ['id' => 'shared-data', 'label' => 'Shared Data', 'icon' => '📤'],
            ['id' => 'flash', 'label' => 'Flash Data', 'icon' => '⚡'],
            ['id' => 'errors', 'label' => 'Errors Component', 'icon' => '⚠️'],
        ],
        'UI Components' => [
            ['id' => 'modal', 'label' => 'Modal/Slideover', 'icon' => '🪟'],
            ['id' => 'toggle', 'label' => 'Toggle', 'icon' => '🔘'],
            ['id' => 'transition', 'label' => 'Transition', 'icon' => '✨'],
            ['id' => 'notifications', 'label' => 'Notifications', 'icon' => '🔔'],
        ],
        'Content Loading' => [
            ['id' => 'lazy', 'label' => 'Lazy Loading', 'icon' => '⏳'],
            ['id' => 'defer', 'label' => 'Defer Component', 'icon' => '⏱️'],
            ['id' => 'content', 'label' => 'Content', 'icon' => '📄'],
            ['id' => 'rehydrate', 'label' => 'Rehydrate', 'icon' => '🔄'],
            ['id' => 'teleport', 'label' => 'Teleport', 'icon' => '🚀'],
        ],
        'Navigation & Events' => [
            ['id' => 'navigation', 'label' => 'SPA Navigation', 'icon' => '🧭'],
            ['id' => 'link', 'label' => 'Link Component', 'icon' => '🔗'],
            ['id' => 'progress', 'label' => 'Progress Bar', 'icon' => '📊'],
            ['id' => 'persistent', 'label' => 'Persistent Layout', 'icon' => '📌'],
            ['id' => 'event-bus', 'label' => 'Event Bus', 'icon' => '🔊'],
            ['id' => 'event', 'label' => 'Event (Echo)', 'icon' => '📡'],
        ],
        'Backend Integration' => [
            ['id' => 'bridge', 'label' => 'Bridge (PHP)', 'icon' => '🌉'],
        ],
    ];

    // Flatten for finding section info
    $allSections = collect($sectionGroups)->flatten(1)->keyBy('id')->toArray();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="accelade-framework" content="{{ $framework }}">

    <title>Accelade Demo - {{ ucfirst($framework) }} - {{ ucfirst($section) }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Prism.js Syntax Highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @acceladeStyles
    @php
        app('accelade')->setFramework($framework);
    @endphp

    <style>
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all 0.15s ease;
            color: #64748b;
            text-decoration: none;
        }
        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #334155;
            transform: translateX(2px);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 2px 4px -1px rgba(99, 102, 241, 0.3);
        }
        .sidebar-link.active:hover {
            transform: translateX(2px);
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .sidebar-link .icon {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        .group-title {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            color: #94a3b8;
            padding: 0.5rem 0.75rem 0.25rem;
            margin-top: 0.75rem;
        }
        .group-title:first-child {
            margin-top: 0;
        }
        /* Framework buttons */
        .framework-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            background: #f1f5f9;
            border: 2px solid transparent;
        }
        .framework-btn:hover {
            transform: scale(1.1);
            background: #e2e8f0;
        }
        .framework-btn.active {
            border-color: currentColor;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .framework-btn svg {
            width: 20px;
            height: 20px;
        }
        /* Code block styles */
        .code-block-wrapper pre[class*="language-"] {
            margin: 0 !important;
            padding: 1rem !important;
            border-radius: 0 !important;
            background: #1e293b !important;
            font-size: 0.875rem !important;
            line-height: 1.6 !important;
        }
        .code-block-wrapper code[class*="language-"] {
            font-family: 'JetBrains Mono', 'Fira Code', 'SF Mono', 'Menlo', 'Monaco', 'Courier New', monospace !important;
            font-size: 0.875rem !important;
        }
        .code-block-wrapper .token.comment,
        .code-block-wrapper .token.prolog,
        .code-block-wrapper .token.doctype,
        .code-block-wrapper .token.cdata { color: #6b7280 !important; }
        .code-block-wrapper .token.punctuation { color: #94a3b8 !important; }
        .code-block-wrapper .token.property,
        .code-block-wrapper .token.tag,
        .code-block-wrapper .token.boolean,
        .code-block-wrapper .token.number,
        .code-block-wrapper .token.constant,
        .code-block-wrapper .token.symbol { color: #f472b6 !important; }
        .code-block-wrapper .token.selector,
        .code-block-wrapper .token.attr-name,
        .code-block-wrapper .token.string,
        .code-block-wrapper .token.char,
        .code-block-wrapper .token.builtin { color: #a5f3fc !important; }
        .code-block-wrapper .token.operator,
        .code-block-wrapper .token.entity,
        .code-block-wrapper .token.url,
        .code-block-wrapper .language-css .token.string,
        .code-block-wrapper .style .token.string,
        .code-block-wrapper .token.variable { color: #fbbf24 !important; }
        .code-block-wrapper .token.atrule,
        .code-block-wrapper .token.attr-value,
        .code-block-wrapper .token.keyword { color: #c084fc !important; }
        .code-block-wrapper .token.function { color: #60a5fa !important; }
        .code-block-wrapper .token.regex,
        .code-block-wrapper .token.important { color: #fb923c !important; }
        /* Scrollbar styling */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full">
            <!-- Logo -->
            <div class="p-5 border-b border-slate-100">
                <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Accelade
                </h1>
                <p class="text-[10px] text-slate-400 mt-0.5">Reactive Blade Templates</p>
            </div>

            <!-- Framework Selector with Logos -->
            <div class="px-4 py-3 border-b border-slate-100">
                <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2 block">Framework</label>
                <div class="flex items-center justify-between gap-1">
                    {{-- Vanilla JS --}}
                    <a href="{{ route('docs.section', ['framework' => 'vanilla', 'section' => $section]) }}"
                       class="framework-btn {{ $framework === 'vanilla' ? 'active text-indigo-600' : 'text-slate-500' }}"
                       title="Vanilla JavaScript">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M0 0h24v24H0V0zm22.034 18.276c-.175-1.095-.888-2.015-3.003-2.873-.736-.345-1.554-.585-1.797-1.14-.091-.33-.105-.51-.046-.705.15-.646.915-.84 1.515-.66.39.12.75.42.976.9 1.034-.676 1.034-.676 1.755-1.125-.27-.42-.404-.601-.586-.78-.63-.705-1.469-1.065-2.834-1.034l-.705.089c-.676.165-1.32.525-1.71 1.005-1.14 1.291-.811 3.541.569 4.471 1.365 1.02 3.361 1.244 3.616 2.205.24 1.17-.87 1.545-1.966 1.41-.811-.18-1.26-.586-1.755-1.336l-1.83 1.051c.21.48.45.689.81 1.109 1.74 1.756 6.09 1.666 6.871-1.004.029-.09.24-.705.074-1.65l.046.067zm-8.983-7.245h-2.248c0 1.938-.009 3.864-.009 5.805 0 1.232.063 2.363-.138 2.711-.33.689-1.18.601-1.566.48-.396-.196-.597-.466-.83-.855-.063-.105-.11-.196-.127-.196l-1.825 1.125c.305.63.75 1.172 1.324 1.517.855.51 2.004.675 3.207.405.783-.226 1.458-.691 1.811-1.411.51-.93.402-2.07.397-3.346.012-2.054 0-4.109 0-6.179l.004-.056z"/>
                        </svg>
                    </a>

                    {{-- Vue --}}
                    <a href="{{ route('docs.section', ['framework' => 'vue', 'section' => $section]) }}"
                       class="framework-btn {{ $framework === 'vue' ? 'active text-emerald-600' : 'text-slate-500' }}"
                       title="Vue.js">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 1.61h-9.94L12 5.16 9.94 1.61H0l12 20.78L24 1.61zM12 14.08 5.16 2.23h4.43L12 6.41l2.41-4.18h4.43L12 14.08z"/>
                        </svg>
                    </a>

                    {{-- React --}}
                    <a href="{{ route('docs.section', ['framework' => 'react', 'section' => $section]) }}"
                       class="framework-btn {{ $framework === 'react' ? 'active text-cyan-500' : 'text-slate-500' }}"
                       title="React">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14.23 12.004a2.236 2.236 0 0 1-2.235 2.236 2.236 2.236 0 0 1-2.236-2.236 2.236 2.236 0 0 1 2.235-2.236 2.236 2.236 0 0 1 2.236 2.236zm2.648-10.69c-1.346 0-3.107.96-4.888 2.622-1.78-1.653-3.542-2.602-4.887-2.602-.41 0-.783.093-1.106.278-1.375.793-1.683 3.264-.973 6.365C1.98 8.917 0 10.42 0 12.004c0 1.59 1.99 3.097 5.043 4.03-.704 3.113-.39 5.588.988 6.38.32.187.69.275 1.102.275 1.345 0 3.107-.96 4.888-2.624 1.78 1.654 3.542 2.603 4.887 2.603.41 0 .783-.09 1.106-.275 1.374-.792 1.683-3.263.973-6.365C22.02 15.096 24 13.59 24 12.004c0-1.59-1.99-3.097-5.043-4.032.704-3.11.39-5.587-.988-6.38-.318-.184-.688-.277-1.092-.278zm-.005 1.09v.006c.225 0 .406.044.558.127.666.382.955 1.835.73 3.704-.054.46-.142.945-.25 1.44-.96-.236-2.006-.417-3.107-.534-.66-.905-1.345-1.727-2.035-2.447 1.592-1.48 3.087-2.292 4.105-2.295zm-9.77.02c1.012 0 2.514.808 4.11 2.28-.686.72-1.37 1.537-2.02 2.442-1.107.117-2.154.298-3.113.538-.112-.49-.195-.964-.254-1.42-.23-1.868.054-3.32.714-3.707.19-.09.4-.127.563-.132zm4.882 3.05c.455.468.91.992 1.36 1.564-.44-.02-.89-.034-1.345-.034-.46 0-.915.01-1.36.034.44-.572.895-1.096 1.345-1.565zM12 8.1c.74 0 1.477.034 2.202.093.406.582.802 1.203 1.183 1.86.372.64.71 1.29 1.018 1.946-.308.655-.646 1.31-1.013 1.95-.38.66-.773 1.288-1.18 1.87-.728.063-1.466.098-2.21.098-.74 0-1.477-.035-2.202-.093-.406-.582-.802-1.204-1.183-1.86-.372-.64-.71-1.29-1.018-1.946.303-.657.646-1.313 1.013-1.954.38-.66.773-1.286 1.18-1.868.728-.064 1.466-.098 2.21-.098zm-3.635.254c-.24.377-.48.763-.704 1.16-.225.39-.435.782-.635 1.174-.265-.656-.49-1.31-.676-1.947.64-.15 1.315-.283 2.015-.386zm7.26 0c.695.103 1.365.23 2.006.387-.18.632-.405 1.282-.66 1.933-.2-.39-.41-.783-.64-1.174-.225-.392-.465-.774-.705-1.146zm3.063.675c.484.15.944.317 1.375.498 1.732.74 2.852 1.708 2.852 2.476-.005.768-1.125 1.74-2.857 2.475-.42.18-.88.342-1.355.493-.28-.958-.646-1.956-1.1-2.98.45-1.017.81-2.01 1.085-2.964zm-13.395.004c.278.96.645 1.957 1.1 2.98-.45 1.017-.812 2.01-1.086 2.964-.484-.15-.944-.318-1.37-.5-1.732-.737-2.852-1.706-2.852-2.474 0-.768 1.12-1.742 2.852-2.476.42-.18.88-.342 1.356-.494zm11.678 4.28c.265.657.49 1.312.676 1.948-.64.157-1.316.29-2.016.39.24-.375.48-.762.705-1.158.225-.39.435-.788.636-1.18zm-9.945.02c.2.392.41.783.64 1.175.23.39.465.772.705 1.143-.695-.102-1.365-.23-2.006-.386.18-.63.406-1.282.66-1.933zM17.92 16.32c.112.493.2.968.254 1.423.23 1.868-.054 3.32-.714 3.708-.147.09-.338.128-.563.128-1.012 0-2.514-.807-4.11-2.28.686-.72 1.37-1.536 2.02-2.44 1.107-.118 2.154-.3 3.113-.54zm-11.83.01c.96.234 2.006.415 3.107.532.66.905 1.345 1.727 2.035 2.446-1.595 1.483-3.092 2.295-4.11 2.295-.22-.005-.406-.05-.553-.132-.666-.38-.955-1.834-.73-3.703.054-.46.142-.944.25-1.438zm4.56.64c.44.02.89.034 1.345.034.46 0 .915-.01 1.36-.034-.44.572-.895 1.095-1.345 1.565-.455-.47-.91-.993-1.36-1.565z"/>
                        </svg>
                    </a>

                    {{-- Svelte --}}
                    <a href="{{ route('docs.section', ['framework' => 'svelte', 'section' => $section]) }}"
                       class="framework-btn {{ $framework === 'svelte' ? 'active text-orange-600' : 'text-slate-500' }}"
                       title="Svelte">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10.354 21.125a4.44 4.44 0 0 1-4.765-1.767 4.109 4.109 0 0 1-.703-3.107 3.898 3.898 0 0 1 .134-.522l.105-.321.287.21a7.21 7.21 0 0 0 2.186 1.092l.208.063-.02.208a1.253 1.253 0 0 0 .226.83 1.337 1.337 0 0 0 1.435.533 1.231 1.231 0 0 0 .343-.15l5.59-3.562a1.164 1.164 0 0 0 .524-.778 1.242 1.242 0 0 0-.211-.937 1.338 1.338 0 0 0-1.435-.533 1.23 1.23 0 0 0-.343.15l-2.133 1.36a4.078 4.078 0 0 1-1.135.499 4.44 4.44 0 0 1-4.765-1.766 4.108 4.108 0 0 1-.702-3.108 3.855 3.855 0 0 1 1.742-2.582l5.589-3.563a4.072 4.072 0 0 1 1.135-.499 4.44 4.44 0 0 1 4.765 1.767 4.109 4.109 0 0 1 .703 3.107 3.943 3.943 0 0 1-.134.522l-.105.321-.286-.21a7.204 7.204 0 0 0-2.187-1.093l-.208-.063.02-.207a1.255 1.255 0 0 0-.226-.831 1.337 1.337 0 0 0-1.435-.532 1.231 1.231 0 0 0-.343.15L8.62 9.368a1.162 1.162 0 0 0-.524.778 1.24 1.24 0 0 0 .211.937 1.338 1.338 0 0 0 1.435.533 1.235 1.235 0 0 0 .344-.151l2.132-1.36a4.067 4.067 0 0 1 1.135-.498 4.44 4.44 0 0 1 4.765 1.766 4.108 4.108 0 0 1 .702 3.108 3.857 3.857 0 0 1-1.742 2.583l-5.589 3.562a4.072 4.072 0 0 1-1.135.499Z"/>
                        </svg>
                    </a>

                    {{-- Angular --}}
                    <a href="{{ route('docs.section', ['framework' => 'angular', 'section' => $section]) }}"
                       class="framework-btn {{ $framework === 'angular' ? 'active text-red-600' : 'text-slate-500' }}"
                       title="Angular">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.93 12.645h4.134L11.996 7.74M11.996.009L.686 3.988l1.725 14.76 9.585 5.243 9.588-5.238L23.308 3.99 11.996.01zm7.058 18.297h-2.636l-1.42-3.501H8.995l-1.42 3.501H4.937l7.06-15.648 7.057 15.648z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Navigation with Groups -->
            <nav class="flex-1 px-3 py-2 overflow-y-auto sidebar-nav">
                @foreach($sectionGroups as $groupName => $groupSections)
                    <div class="group-title">{{ $groupName }}</div>
                    <div class="space-y-0.5">
                        @foreach($groupSections as $s)
                            <a
                                href="{{ route('docs.section', ['framework' => $framework, 'section' => $s['id']]) }}"
                                class="sidebar-link {{ $section === $s['id'] ? 'active' : '' }}"
                            >
                                <span class="icon">{{ $s['icon'] }}</span>
                                <span>{{ $s['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <!-- Footer -->
            <div class="p-3 border-t border-slate-100 text-[10px] text-slate-400">
                <div class="flex items-center justify-between">
                    <span>Laravel {{ app()->version() }}</span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 {{ $colors['dot'] }} rounded-full"></span>
                        {{ ucfirst($framework) }}
                    </span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 {{ $colors['dot'] }} rounded-full"></span>
                        <h2 class="text-lg font-semibold text-slate-800">
                            {{ $allSections[$section]['label'] ?? ucfirst($section) }}
                        </h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400">
                            @foreach($sectionGroups as $groupName => $groupSections)
                                @if(collect($groupSections)->pluck('id')->contains($section))
                                    {{ $groupName }}
                                    @break
                                @endif
                            @endforeach
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $colors['bg'] }} text-white rounded-full text-xs font-medium">
                            {{ ucfirst($framework) }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8" data-accelade-page>
                {{ $slot }}
            </div>
        </main>
    </div>

    @acceladeScripts
    @acceladeNotifications

    {{-- Prism.js for syntax highlighting --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

    {{-- Code block functionality --}}
    <script>
    (function() {
        // Run Prism highlighting
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }

        // Copy functionality
        document.querySelectorAll('.code-copy-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const targetId = this.dataset.codeTarget;
                const codeEl = document.getElementById(targetId);
                if (!codeEl) return;

                const code = codeEl.textContent;
                try {
                    await navigator.clipboard.writeText(code);

                    // Show success feedback
                    const copyIcon = this.querySelector('.copy-icon');
                    const checkIcon = this.querySelector('.check-icon');
                    const copyText = this.querySelector('.copy-text');

                    copyIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    copyText.textContent = 'Copied!';
                    this.classList.add('text-green-400');

                    setTimeout(() => {
                        copyIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                        copyText.textContent = 'Copy';
                        this.classList.remove('text-green-400');
                    }, 2000);
                } catch (err) {
                    console.error('Failed to copy:', err);
                }
            });
        });

        // Download as image functionality - Pure Canvas approach
        document.querySelectorAll('.code-download-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const targetId = this.dataset.codeTarget;
                const codeEl = document.getElementById(targetId);
                const wrapper = document.querySelector(`[data-code-block="${targetId}"]`);
                if (!codeEl || !wrapper) return;

                try {
                    const code = codeEl.textContent;
                    const lines = code.split('\n').filter(l => l.trim() !== '' || lines.length === 1);
                    const filename = wrapper.querySelector('.text-slate-400')?.textContent?.trim() || 'code';

                    // Canvas settings
                    const paddingX = 24;
                    const paddingY = 20;
                    const headerHeight = 44;
                    const lineHeight = 22;
                    const fontSize = 13;
                    const scale = 2;
                    const borderRadius = 12;

                    // Calculate dimensions
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    ctx.font = `${fontSize}px ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace`;

                    // Find max line width
                    let maxWidth = 350;
                    lines.forEach(line => {
                        const width = ctx.measureText(line).width;
                        if (width > maxWidth) maxWidth = width;
                    });

                    const innerWidth = maxWidth + paddingX * 2;
                    const innerHeight = headerHeight + lines.length * lineHeight + paddingY * 2;
                    const outerPadding = 20;

                    canvas.width = (innerWidth + outerPadding * 2) * scale;
                    canvas.height = (innerHeight + outerPadding * 2) * scale;

                    // Scale for retina
                    ctx.scale(scale, scale);

                    // Outer background (gradient)
                    const gradient = ctx.createLinearGradient(0, 0, canvas.width / scale, canvas.height / scale);
                    gradient.addColorStop(0, '#1e1b4b');
                    gradient.addColorStop(1, '#312e81');
                    ctx.fillStyle = gradient;
                    ctx.fillRect(0, 0, canvas.width / scale, canvas.height / scale);

                    // Draw rounded rectangle for code block
                    const x = outerPadding;
                    const y = outerPadding;
                    const w = innerWidth;
                    const h = innerHeight;
                    const r = borderRadius;

                    ctx.beginPath();
                    ctx.moveTo(x + r, y);
                    ctx.lineTo(x + w - r, y);
                    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
                    ctx.lineTo(x + w, y + h - r);
                    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
                    ctx.lineTo(x + r, y + h);
                    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
                    ctx.lineTo(x, y + r);
                    ctx.quadraticCurveTo(x, y, x + r, y);
                    ctx.closePath();

                    // Code block background
                    ctx.fillStyle = '#1e293b';
                    ctx.fill();

                    // Header background (top rounded corners)
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x + r, y);
                    ctx.lineTo(x + w - r, y);
                    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
                    ctx.lineTo(x + w, y + headerHeight);
                    ctx.lineTo(x, y + headerHeight);
                    ctx.lineTo(x, y + r);
                    ctx.quadraticCurveTo(x, y, x + r, y);
                    ctx.closePath();
                    ctx.fillStyle = '#334155';
                    ctx.fill();
                    ctx.restore();

                    // Traffic lights
                    const trafficLightY = y + headerHeight / 2;
                    const dotRadius = 6;
                    const dotSpacing = 20;
                    const dotStartX = x + 16;

                    ctx.fillStyle = '#ef4444';
                    ctx.beginPath();
                    ctx.arc(dotStartX, trafficLightY, dotRadius, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#facc15';
                    ctx.beginPath();
                    ctx.arc(dotStartX + dotSpacing, trafficLightY, dotRadius, 0, Math.PI * 2);
                    ctx.fill();

                    ctx.fillStyle = '#22c55e';
                    ctx.beginPath();
                    ctx.arc(dotStartX + dotSpacing * 2, trafficLightY, dotRadius, 0, Math.PI * 2);
                    ctx.fill();

                    // Filename
                    ctx.fillStyle = '#94a3b8';
                    ctx.font = `11px -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`;
                    ctx.fillText(filename.toUpperCase(), dotStartX + dotSpacing * 3 + 10, trafficLightY + 4);

                    // Code text
                    ctx.fillStyle = '#e2e8f0';
                    ctx.font = `${fontSize}px ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace`;

                    lines.forEach((line, i) => {
                        ctx.fillText(line, x + paddingX, y + headerHeight + paddingY + (i + 1) * lineHeight - 6);
                    });

                    // Border
                    ctx.strokeStyle = '#475569';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(x + r, y);
                    ctx.lineTo(x + w - r, y);
                    ctx.quadraticCurveTo(x + w, y, x + w, y + r);
                    ctx.lineTo(x + w, y + h - r);
                    ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
                    ctx.lineTo(x + r, y + h);
                    ctx.quadraticCurveTo(x, y + h, x, y + h - r);
                    ctx.lineTo(x, y + r);
                    ctx.quadraticCurveTo(x, y, x + r, y);
                    ctx.closePath();
                    ctx.stroke();

                    // Download
                    const link = document.createElement('a');
                    link.download = `${filename.toLowerCase().replace(/[^a-z0-9]/g, '-')}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                } catch (err) {
                    console.error('Failed to download:', err);
                    alert('Failed to generate image.');
                }
            });
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
