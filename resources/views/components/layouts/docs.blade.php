@props([
    'section' => 'getting-started',
    'framework' => 'vanilla',
    'documentation' => null,
    'hasDemo' => true,
    'navigation' => null,
])

@php
    // Framework configuration
    $frameworks = [
        'vanilla' => ['label' => 'Vanilla JS', 'color' => '#f7df1e'],
        'vue' => ['label' => 'Vue.js', 'color' => '#42b883'],
        'react' => ['label' => 'React', 'color' => '#61dafb'],
        'svelte' => ['label' => 'Svelte', 'color' => '#ff3e00'],
        'angular' => ['label' => 'Angular', 'color' => '#dd0031'],
    ];
    $currentFramework = $frameworks[$framework] ?? $frameworks['vanilla'];

    // Framework prefixes
    $frameworkPrefixes = [
        'vanilla' => 'a',
        'vue' => 'v',
        'react' => 'data-state',
        'svelte' => 's',
        'angular' => 'ng',
    ];
    $prefix = $frameworkPrefixes[$framework] ?? 'a';

    // Icon mapping for section slugs
    $sectionIcons = [
        'getting-started' => '👋',
        'installation' => '📦',
        'configuration' => '⚙️',
        'frameworks' => '🏗️',
        'architecture' => '🏛️',
        'testing' => '🧪',
        'counter' => '🔢',
        'data' => '💾',
        'state' => '🔀',
        'scripts' => '📜',
        'modal' => '🪟',
        'toggle' => '🔘',
        'transition' => '✨',
        'notifications' => '🔔',
        'code-block' => '💻',
        'lazy' => '💤',
        'defer' => '⏱️',
        'content' => '📄',
        'rehydrate' => '🔄',
        'teleport' => '🚀',
        'navigation' => '🧭',
        'link' => '🔗',
        'progress' => '📊',
        'persistent' => '📌',
        'event-bus' => '📡',
        'event' => '📻',
        'bridge' => '🌉',
        'shared-data' => '📤',
        'flash' => '⚡',
        'errors' => '⚠️',
        'api-reference' => '📚',
        'contributing' => '🤝',
        'sponsor' => '💖',
        'thanks' => '🙏',
    ];

    // Use navigation from registry if provided, otherwise get from app
    $navData = $navigation ?? app('accelade.docs')->getNavigation();

    // Build section groups from navigation data
    $sectionGroups = [];
    $allSections = [];
    foreach ($navData as $group) {
        $groupSections = [];
        foreach ($group['items'] as $item) {
            // Use section's icon if set, otherwise fallback to emoji mapping
            $itemIcon = $item['icon'] ?? $sectionIcons[$item['slug']] ?? '📄';
            $sectionData = [
                'id' => $item['slug'],
                'label' => $item['label'],
                'icon' => $itemIcon,
                'demo' => $item['hasDemo'],
                'keywords' => '',
            ];
            $groupSections[] = $sectionData;
            $allSections[$item['slug']] = $sectionData;
        }
        $sectionGroups[$group['label']] = $groupSections;
    }

    // Current section info
    $currentSection = $allSections[$section] ?? ['label' => ucfirst($section)];

    // Get doc file from registry for GitHub edit URL
    $registry = app('accelade.docs');
    $docSection = $registry->getSection($section);
    $docFile = $docSection?->markdownFile ?? 'getting-started.md';
    $githubRepo = config('accelade.docs.github_repo', 'accelade/accelade');
    $githubEditUrl = "https://github.com/{$githubRepo}/edit/master/docs/{$docFile}";

    // Extract headings from documentation for TOC
    $tocItems = [];
    if ($documentation) {
        preg_match_all('/<h([2-3])[^>]*id="([^"]*)"[^>]*>([^<]*)<\/h[2-3]>/i', $documentation, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $tocItems[] = [
                'level' => (int) $match[1],
                'id' => $match[2],
                'text' => strip_tags($match[3]),
            ];
        }
        // If no IDs found, try matching without IDs and we'll generate them
        if (empty($tocItems)) {
            preg_match_all('/<h([2-3])[^>]*>([^<]*)<\/h[2-3]>/i', $documentation, $matches, PREG_SET_ORDER);
            foreach ($matches as $i => $match) {
                $id = \Illuminate\Support\Str::slug($match[2]);
                $tocItems[] = [
                    'level' => (int) $match[1],
                    'id' => $id,
                    'text' => strip_tags($match[2]),
                ];
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="accelade-framework" content="{{ $framework }}">
    <meta name="color-scheme" content="light dark">

    <title>{{ $currentSection['label'] }} - Accelade Documentation</title>
    <meta name="description" content="Learn about {{ $currentSection['label'] }} in Accelade - a reactive Blade template library for Laravel. Build dynamic UIs without complex JavaScript frameworks.">
    <meta name="keywords" content="accelade, laravel, blade, reactive, {{ strtolower($currentSection['label']) }}, php, frontend">
    <meta name="author" content="Accelade">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Accelade Documentation">
    <meta property="og:title" content="{{ $currentSection['label'] }} - Accelade Docs">
    <meta property="og:description" content="Learn about {{ $currentSection['label'] }} in Accelade - a reactive Blade template library for Laravel.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $currentSection['label'] }} - Accelade Docs">
    <meta name="twitter:description" content="Learn about {{ $currentSection['label'] }} in Accelade - a reactive Blade template library for Laravel.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                safelist: [
                    // Light mode
                    'bg-white',
                    'bg-slate-50',
                    'bg-slate-100',
                    'border-slate-100',
                    'border-slate-200',
                    'text-slate-800',
                    'text-slate-700',
                    'text-slate-600',
                    'text-slate-500',
                    'text-slate-400',
                    'text-green-600',
                    'text-red-600',
                    // Dark mode
                    'dark:bg-slate-800',
                    'dark:bg-slate-700',
                    'dark:bg-slate-900',
                    'dark:border-slate-700',
                    'dark:border-slate-600',
                    'dark:text-slate-100',
                    'dark:text-slate-200',
                    'dark:text-slate-300',
                    'dark:text-slate-400',
                    'dark:text-slate-500',
                    'dark:text-green-400',
                    'dark:text-red-400',
                    'dark:hidden',
                    'dark:block',
                ]
            }
        </script>
    @endif

    @acceladeStyles
    @php app('accelade')->setFramework($framework); @endphp

    <style>
        :root {
            --docs-bg: #ffffff;
            --docs-bg-alt: #f8fafc;
            --docs-text: #0f172a;
            --docs-text-muted: #64748b;
            --docs-border: #e2e8f0;
            --docs-accent: #ea7023;
            --docs-primary: #18395c;
        }
        .dark {
            --docs-bg: #0f172a;
            --docs-bg-alt: #1e293b;
            --docs-text: #f1f5f9;
            --docs-text-muted: #94a3b8;
            --docs-border: #334155;
            --docs-accent: #fb923c;
            --docs-primary: #60a5fa;
        }
        body { background: var(--docs-bg); color: var(--docs-text); font-family: 'Inter', sans-serif; }

        /* Global scrollbar styling */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--docs-bg); }
        ::-webkit-scrollbar-thumb { background: var(--docs-border); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--docs-text-muted); }
        * { scrollbar-width: thin; scrollbar-color: var(--docs-border) var(--docs-bg); }

        /* Sidebar */
        .sidebar { background: var(--docs-bg-alt); border-color: var(--docs-border); }

        /* Sidebar scrollbar styling */
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--docs-border); border-radius: 3px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: var(--docs-text-muted); }
        .sidebar { scrollbar-width: thin; scrollbar-color: var(--docs-border) transparent; }

        .sidebar-group { margin-bottom: 1.25rem; }
        .sidebar-group-title {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--docs-text-muted);
            padding-inline: 0.75rem;
            margin-bottom: 0.375rem;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
            color: var(--docs-text-muted);
            border-radius: 0.375rem;
            transition: all 0.15s;
        }
        .sidebar-link:hover { color: var(--docs-text); background: var(--docs-border); }
        .sidebar-link.active { color: var(--docs-accent); font-weight: 500; }
        .sidebar-link .icon { font-size: 0.875rem; width: 1.25rem; text-align: center; }

        /* Mobile sidebar - RTL aware */
        @media (max-width: 1023px) {
            .sidebar {
                position: fixed;
                z-index: 50;
                height: 100vh;
                width: 280px;
                transition: transform 0.2s;
                inset-inline-start: 0;
            }
            [dir="ltr"] .sidebar { transform: translateX(-100%); }
            [dir="rtl"] .sidebar { transform: translateX(100%); }
            .sidebar.mobile-open { transform: translateX(0) !important; }
        }

        /* Right Sidebar (TOC) - RTL aware */
        .toc { position: sticky; top: 5rem; }
        .toc-link {
            display: block;
            padding: 0.25rem 0;
            font-size: 0.8125rem;
            color: var(--docs-text-muted);
            transition: color 0.15s;
            border-inline-start: 2px solid transparent;
            padding-inline-start: 0.75rem;
            margin-inline-start: -2px;
        }
        .toc-link:hover { color: var(--docs-text); }
        .toc-link.active { color: var(--docs-accent); border-color: var(--docs-accent); }
        .toc-link.level-3 { padding-inline-start: 1.5rem; font-size: 0.75rem; }

        /* Header */
        .docs-header { background: var(--docs-bg); border-color: var(--docs-border); }

        /* Tabs */
        .tab-btn { padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-bottom: 2px solid transparent; color: var(--docs-text-muted); transition: all 0.15s; }
        .tab-btn:hover { color: var(--docs-text); }
        .tab-btn.active { color: var(--docs-accent); border-color: var(--docs-accent); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Cloak pattern - hide until JS is ready */
        [data-cloak] { display: none !important; }

        /* Documentation prose - GitHub-style markdown */
        .docs-prose {
            font-size: 1rem;
            line-height: 1.75;
            color: var(--docs-text);
            word-wrap: break-word;
        }

        /* Headings */
        .docs-prose h1,
        .docs-prose h2,
        .docs-prose h3,
        .docs-prose h4,
        .docs-prose h5,
        .docs-prose h6 {
            margin-top: 1.5em;
            margin-bottom: 1rem;
            font-weight: 600;
            line-height: 1.25;
            color: var(--docs-text);
        }
        .docs-prose h1 { font-size: 2em; padding-bottom: 0.3em; border-bottom: 1px solid var(--docs-border); margin-top: 0; }
        .docs-prose h2 { font-size: 1.5em; padding-bottom: 0.3em; border-bottom: 1px solid var(--docs-border); scroll-margin-top: 5rem; }
        .docs-prose h3 { font-size: 1.25em; scroll-margin-top: 5rem; }
        .docs-prose h4 { font-size: 1em; }
        .docs-prose h5 { font-size: 0.875em; }
        .docs-prose h6 { font-size: 0.85em; color: var(--docs-text-muted); }

        /* Paragraphs */
        .docs-prose p {
            margin-top: 0;
            margin-bottom: 1em;
        }

        /* Links */
        .docs-prose a {
            color: var(--docs-accent);
            text-decoration: none;
        }
        .docs-prose a:hover {
            text-decoration: underline;
        }

        /* Bold and emphasis */
        .docs-prose strong { font-weight: 600; color: var(--docs-text); }
        .docs-prose em { font-style: italic; }

        /* Inline code */
        .docs-prose code {
            padding: 0.2em 0.4em;
            margin: 0;
            font-size: 85%;
            font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, monospace;
            background-color: var(--docs-bg-alt);
            border-radius: 6px;
            border: 1px solid var(--docs-border);
        }
        .docs-prose pre code {
            padding: 0;
            margin: 0;
            font-size: 100%;
            background-color: transparent;
            border: none;
            border-radius: 0;
        }

        /* Code blocks */
        .docs-prose pre {
            padding: 1rem;
            overflow: auto;
            font-size: 0.875rem;
            line-height: 1.45;
            background-color: var(--docs-bg-alt);
            border-radius: 6px;
            margin-top: 0;
            margin-bottom: 1rem;
        }

        /* Lists */
        .docs-prose ul,
        .docs-prose ol {
            margin-top: 0;
            margin-bottom: 1rem;
            padding-inline-start: 2em;
        }
        .docs-prose ul { list-style-type: disc; }
        .docs-prose ol { list-style-type: decimal; }
        .docs-prose ul ul,
        .docs-prose ol ol,
        .docs-prose ul ol,
        .docs-prose ol ul {
            margin-top: 0.25em;
            margin-bottom: 0;
        }
        .docs-prose li {
            margin-bottom: 0.25em;
        }
        .docs-prose li + li {
            margin-top: 0.25em;
        }
        .docs-prose li > p {
            margin-top: 1em;
        }
        .docs-prose li > ul,
        .docs-prose li > ol {
            margin-top: 0.25em;
        }

        /* Task lists (checkboxes) */
        .docs-prose input[type="checkbox"] {
            margin-inline-end: 0.5em;
            vertical-align: middle;
        }

        /* Blockquotes */
        .docs-prose blockquote {
            margin: 0 0 1rem 0;
            padding: 0 1em;
            color: var(--docs-text-muted);
            border-inline-start: 0.25em solid var(--docs-border);
        }
        .docs-prose blockquote > :first-child { margin-top: 0; }
        .docs-prose blockquote > :last-child { margin-bottom: 0; }

        /* Horizontal rules */
        .docs-prose hr {
            height: 2px;
            padding: 0;
            margin: 2rem 0;
            background: linear-gradient(to right, transparent, var(--docs-border), transparent);
            border: 0;
        }
        .dark .docs-prose hr {
            background: linear-gradient(to right, transparent, var(--docs-text-muted), transparent);
        }

        /* Tables */
        .docs-prose table {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow: auto;
            border-spacing: 0;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 1rem;
        }
        .docs-prose table th,
        .docs-prose table td {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--docs-border);
        }
        .docs-prose table th {
            font-weight: 600;
            background-color: var(--docs-bg-alt);
            text-align: start;
        }
        .docs-prose table tr {
            background-color: var(--docs-bg);
            border-top: 1px solid var(--docs-border);
        }
        .docs-prose table tr:nth-child(2n) {
            background-color: var(--docs-bg-alt);
        }

        /* Images */
        .docs-prose img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 1rem 0;
        }

        /* Definition lists */
        .docs-prose dl {
            padding: 0;
            margin-bottom: 1rem;
        }
        .docs-prose dl dt {
            padding: 0;
            margin-top: 1rem;
            font-weight: 600;
        }
        .docs-prose dl dd {
            padding: 0 1rem;
            margin-bottom: 1rem;
            margin-inline-start: 0;
        }

        /* Keyboard keys */
        .docs-prose kbd {
            display: inline-block;
            padding: 0.2em 0.4em;
            font-size: 0.875em;
            font-family: 'JetBrains Mono', monospace;
            line-height: 1;
            color: var(--docs-text);
            vertical-align: middle;
            background-color: var(--docs-bg-alt);
            border: 1px solid var(--docs-border);
            border-radius: 6px;
            box-shadow: inset 0 -1px 0 var(--docs-border);
        }

        /* Abbreviations */
        .docs-prose abbr[title] {
            text-decoration: underline dotted;
            cursor: help;
            border-bottom: none;
        }

        /* First child and last child cleanup */
        .docs-prose > :first-child { margin-top: 0 !important; }
        .docs-prose > :last-child { margin-bottom: 0 !important; }

        /* Note/Warning/Info boxes - for custom callouts */
        .docs-prose .note,
        .docs-prose .warning,
        .docs-prose .info,
        .docs-prose .tip {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border-inline-start: 4px solid;
        }
        .docs-prose .note {
            background-color: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        .docs-prose .warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-color: #f59e0b;
        }
        .docs-prose .info {
            background-color: rgba(99, 102, 241, 0.1);
            border-color: #6366f1;
        }
        .docs-prose .tip {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }

        /* Framework selector */
        .framework-btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: 1px solid var(--docs-border); transition: all 0.15s; }
        .framework-btn:hover { border-color: var(--docs-accent); }
        .framework-dropdown { background: var(--docs-bg); border: 1px solid var(--docs-border); border-radius: 0.5rem; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2); }

        /* Code block styling - handled by code-block component */
        .docs-prose .code-block-wrapper { margin: 1.5rem 0; }
        .docs-prose pre code {
            font-size: 0.875rem;
            line-height: 1.7;
        }
    </style>
</head>
<body class="min-h-screen antialiased" data-section="{{ $section }}">
    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleMobileSidebar()"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-60 border-e flex flex-col shrink-0 lg:sticky lg:top-0 lg:h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-[var(--docs-border)]">
                <x-accelade::link href="{{ route('docs.section', ['section' => 'getting-started']) }}" class="flex items-center gap-3" activeClass="">
                    <img src="{{ asset('vendor/accelade/logo-dark.png') }}" alt="Accelade" class="h-7 w-auto dark:hidden">
                    <img src="{{ asset('vendor/accelade/logo-light.png') }}" alt="Accelade" class="h-7 w-auto hidden dark:block">
                    <span class="font-bold text-lg">Accelade</span>
                </x-accelade::link>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-3 overflow-y-auto" id="docs-sidebar-nav">
                @foreach($sectionGroups as $groupName => $sections)
                    <div class="sidebar-group">
                        <div class="sidebar-group-title">{{ $groupName }}</div>
                        @foreach($sections as $s)
                            <x-accelade::link
                                href="{{ route('docs.section', ['section' => $s['id'], 'framework' => $framework]) }}"
                                :preserveScroll="true"
                                class="sidebar-link {{ $section === $s['id'] ? 'active' : '' }}"
                                activeClass="">
                                <span class="icon">
                                    @if(preg_match('/^[a-z][a-z0-9-]*$/i', $s['icon']))
                                        <x-accelade::icon :name="$s['icon']" class="w-4 h-4" />
                                    @else
                                        {{ $s['icon'] }}
                                    @endif
                                </span>
                                <span>{{ $s['label'] }}</span>
                            </x-accelade::link>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <!-- Version -->
            <div class="p-3 border-t border-[var(--docs-border)] text-xs text-[var(--docs-text-muted)]">
                v1.0.0
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="docs-header sticky top-0 z-30 border-b px-4 lg:px-6">
                <div class="h-14 flex items-center justify-between gap-4">
                    <!-- Mobile menu -->
                    <button onclick="toggleMobileSidebar()" class="p-2 -ms-2 rounded-lg hover:bg-[var(--docs-bg-alt)] lg:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Framework Selector -->
                    <div class="relative">
                        <button onclick="toggleFrameworkDropdown()" class="framework-btn">
                            @include('accelade::components.layouts.partials.framework-icon', ['fw' => $framework, 'size' => 'w-5 h-5'])
                            <span class="hidden sm:inline">{{ $currentFramework['label'] }}</span>
                            <svg class="w-4 h-4 text-[var(--docs-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="framework-dropdown" class="framework-dropdown hidden absolute start-0 mt-2 w-48 py-1 z-50">
                            @foreach($frameworks as $fw => $config)
                                <a href="{{ route('docs.section', ['section' => $section, 'framework' => $fw]) }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-[var(--docs-bg-alt)] {{ $framework === $fw ? 'text-[var(--docs-accent)] font-medium' : '' }}">
                                    @include('accelade::components.layouts.partials.framework-icon', ['fw' => $fw, 'size' => 'w-5 h-5'])
                                    {{ $config['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Search -->
                    <button onclick="openSearch()" class="flex-1 max-w-md flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--docs-bg-alt)] border border-[var(--docs-border)] text-[var(--docs-text-muted)] text-sm hover:border-[var(--docs-accent)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span class="hidden sm:inline">Search...</span>
                        <kbd class="ms-auto px-1.5 py-0.5 text-xs rounded bg-[var(--docs-bg)] border border-[var(--docs-border)] hidden sm:inline">⌘K</kbd>
                    </button>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                        <a href="https://github.com/{{ $githubRepo }}" target="_blank" class="p-2 rounded-lg hover:bg-[var(--docs-bg-alt)] text-[var(--docs-text-muted)]" title="GitHub">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="https://acceladephp.com" target="_blank" class="p-2 rounded-lg hover:bg-[var(--docs-bg-alt)] text-[var(--docs-text-muted)] hidden sm:block" title="Website">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </a>
                        <button onclick="toggleTheme()" class="p-2 rounded-lg hover:bg-[var(--docs-bg-alt)] text-[var(--docs-text-muted)]" title="Toggle theme">
                            <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 flex justify-center" data-accelade-page>
                <!-- Main Content -->
                <main class="flex-1 px-4 lg:px-8 py-6 min-w-0 max-w-4xl">
                    @if($hasDemo && $documentation)
                        <!-- Tabs container - hidden by data-cloak until JS sets correct state -->
                        <div id="tabs-container" data-cloak>
                            <!-- Tabs -->
                            <div class="border-b border-[var(--docs-border)] mb-6">
                                <div class="flex gap-4">
                                    <button class="tab-btn active" data-tab="docs">Documentation</button>
                                    <button class="tab-btn" data-tab="demo">
                                        Live Demo
                                        <span class="ms-1 px-1.5 py-0.5 text-xs rounded" style="background: {{ $currentFramework['color'] }}20; color: {{ $currentFramework['color'] }}">
                                            {{ $currentFramework['label'] }}
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Docs Tab -->
                            <div id="tab-docs" class="tab-content active">
                                <div class="docs-prose" id="docs-content">
                                    {!! $documentation !!}
                                </div>
                            </div>

                            <!-- Demo Tab -->
                            <div id="tab-demo" class="tab-content">
                                {{ $slot }}
                            </div>
                        </div>
                        <script>
                            (function() {
                                var container = document.getElementById('tabs-container');
                                if (localStorage.getItem('docs-tab-{{ $section }}') === 'demo') {
                                    document.getElementById('tab-docs').classList.remove('active');
                                    document.getElementById('tab-demo').classList.add('active');
                                    container.querySelectorAll('.tab-btn')[0].classList.remove('active');
                                    container.querySelectorAll('.tab-btn')[1].classList.add('active');
                                }
                                container.removeAttribute('data-cloak');
                            })();
                        </script>
                    @elseif($documentation)
                        <div class="docs-prose" id="docs-content">
                            {!! $documentation !!}
                        </div>
                    @else
                        {{ $slot }}
                    @endif
                </main>

                <!-- TOC Sidebar -->
                @if(!empty($tocItems))
                <aside class="hidden xl:block w-56 shrink-0 pe-4">
                    <div class="toc py-6">
                        <div class="text-xs font-semibold uppercase tracking-wider text-[var(--docs-text-muted)] mb-3 px-3">
                            On this page
                        </div>
                        <nav class="border-s border-[var(--docs-border)]">
                            @foreach($tocItems as $item)
                                <a href="#{{ $item['id'] }}" class="toc-link {{ $item['level'] === 3 ? 'level-3' : '' }}" data-toc-link>
                                    {{ $item['text'] }}
                                </a>
                            @endforeach
                        </nav>

                        <!-- Edit on GitHub -->
                        <div class="mt-6 pt-4 border-t border-[var(--docs-border)]">
                            <a href="{{ $githubEditUrl }}" target="_blank" class="flex items-center gap-2 text-xs text-[var(--docs-text-muted)] hover:text-[var(--docs-accent)]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit this page on GitHub
                            </a>
                        </div>
                    </div>
                </aside>
                @endif
            </div>

            <!-- Footer -->
            <footer class="border-t border-[var(--docs-border)] py-6 px-4 lg:px-8">
                <div class="max-w-3xl flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-[var(--docs-text-muted)]">
                    <div class="flex items-center gap-4">
                        <a href="https://acceladephp.com" target="_blank" class="hover:text-[var(--docs-accent)]">Website</a>
                        <a href="https://github.com/{{ $githubRepo }}" target="_blank" class="hover:text-[var(--docs-accent)]">GitHub</a>
                        <a href="https://github.com/{{ $githubRepo }}/issues" target="_blank" class="hover:text-[var(--docs-accent)]">Issues</a>
                    </div>
                    <div>Built with Accelade & Laravel</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeSearch()"></div>
        <div class="fixed inset-x-4 top-[15%] sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-lg">
            <div class="rounded-xl border border-[var(--docs-border)] bg-[var(--docs-bg)] shadow-2xl overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-3 border-b border-[var(--docs-border)]">
                    <svg class="w-5 h-5 text-[var(--docs-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="search-input" class="flex-1 bg-transparent outline-none" placeholder="Search documentation..." oninput="handleSearch(this.value)">
                    <kbd class="px-1.5 py-0.5 text-xs rounded bg-[var(--docs-bg-alt)] border border-[var(--docs-border)] text-[var(--docs-text-muted)]">ESC</kbd>
                </div>
                <div id="search-results" class="max-h-80 overflow-y-auto p-2">
                    @foreach($sectionGroups as $groupName => $sections)
                        <div class="search-group" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}">
                            <div class="search-group-title px-2 py-1.5 text-xs font-medium text-[var(--docs-text-muted)] uppercase">{{ $groupName }}</div>
                            @foreach($sections as $s)
                                <x-accelade::link
                                    href="{{ route('docs.section', ['section' => $s['id'], 'framework' => $framework]) }}"
                                    class="search-item flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-[var(--docs-bg-alt)]"
                                    activeClass=""
                                    data-search="{{ strtolower($s['label'] . ' ' . $groupName . ' ' . ($s['keywords'] ?? '')) }}"
                                    data-group="{{ \Illuminate\Support\Str::slug($groupName) }}">
                                    <span>
                                        @if(preg_match('/^[a-z][a-z0-9-]*$/i', $s['icon']))
                                            <x-accelade::icon :name="$s['icon']" class="w-4 h-4" />
                                        @else
                                            {{ $s['icon'] }}
                                        @endif
                                    </span>
                                    <span class="font-medium">{{ $s['label'] }}</span>
                                </x-accelade::link>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @acceladeScripts
    @acceladeNotifications

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup-templating.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>

    <script>
        // Theme initialization
        (function() {
            var stored = localStorage.getItem('docs-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', stored === 'dark' || (!stored && prefersDark));
        })();

        function toggleTheme() {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('docs-theme', isDark ? 'dark' : 'light');
        }

        function toggleMobileSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        function toggleFrameworkDropdown() {
            document.getElementById('framework-dropdown').classList.toggle('hidden');
        }

        function openSearch() {
            document.getElementById('search-modal').classList.remove('hidden');
            document.getElementById('search-input').focus();
            document.body.style.overflow = 'hidden';
        }

        function closeSearch() {
            document.getElementById('search-modal').classList.add('hidden');
            document.getElementById('search-input').value = '';
            document.body.style.overflow = '';
            document.querySelectorAll('.search-item, .search-group').forEach(function(el) {
                el.style.display = '';
            });
        }

        function handleSearch(query) {
            var q = query.toLowerCase().trim();
            var groupVisibility = {};

            document.querySelectorAll('.search-item').forEach(function(el) {
                var matches = !q || el.dataset.search.includes(q);
                el.style.display = matches ? '' : 'none';
                groupVisibility[el.dataset.group] = groupVisibility[el.dataset.group] || matches;
            });

            document.querySelectorAll('.search-group').forEach(function(group) {
                group.style.display = groupVisibility[group.dataset.group] ? '' : 'none';
            });
        }

        function updateTocActive() {
            var docsTab = document.getElementById('tab-docs');
            if (!docsTab || !docsTab.classList.contains('active')) {
                return;
            }

            var currentId = '';
            document.querySelectorAll('.docs-prose h2[id], .docs-prose h3[id]').forEach(function(h) {
                if (h.getBoundingClientRect().top <= 120) {
                    currentId = h.id;
                }
            });

            document.querySelectorAll('[data-toc-link]').forEach(function(link) {
                link.classList.toggle('active', link.getAttribute('href') === '#' + currentId);
            });
        }

        window.switchTab = function(tab) {
            document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
            document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });

            var tabBtn = document.querySelector('.tab-btn[data-tab="' + tab + '"]');
            var tabContent = document.getElementById('tab-' + tab);
            if (tabBtn) { tabBtn.classList.add('active'); }
            if (tabContent) { tabContent.classList.add('active'); }

            var section = document.body.dataset.section || 'default';
            localStorage.setItem('docs-tab-' + section, tab);
        };

        // Markdown filename to section slug mapping
        var docToSection = {
            'getting-started.md': 'getting-started', 'installation.md': 'installation',
            'configuration.md': 'configuration', 'components.md': 'counter',
            'data.md': 'data', 'state.md': 'state', 'modal.md': 'modal',
            'toggle.md': 'toggle', 'animations.md': 'transition',
            'notifications.md': 'notifications', 'code-block.md': 'code-block',
            'lazy-loading.md': 'lazy', 'content.md': 'content', 'rehydrate.md': 'rehydrate',
            'teleport.md': 'teleport', 'spa-navigation.md': 'navigation', 'link.md': 'link',
            'persistent-layout.md': 'persistent', 'event-bus.md': 'event-bus',
            'event.md': 'event', 'bridge.md': 'bridge', 'shared-data.md': 'shared-data',
            'flash.md': 'flash', 'exception-handling.md': 'errors', 'scripts.md': 'scripts',
            'api-reference.md': 'api-reference', 'frameworks.md': 'frameworks',
            'architecture.md': 'architecture', 'testing.md': 'testing',
            'contributing.md': 'contributing', 'sponsor.md': 'sponsor', 'thanks.md': 'thanks'
        };

        function fixDocLinks() {
            var docsContent = document.getElementById('docs-content');
            if (!docsContent) {
                return;
            }

            docsContent.querySelectorAll('a[href]').forEach(function(link) {
                var href = link.getAttribute('href');
                if (!href || href.startsWith('http') || href.startsWith('#')) {
                    return;
                }

                var filename = href.split('/').pop();
                if (filename.endsWith('.md')) {
                    var sectionId = docToSection[filename];
                    if (sectionId) {
                        link.setAttribute('href', '/docs/' + sectionId + '?framework={{ $framework }}');
                        link.setAttribute('a-navigate', '');
                    }
                }
            });
        }

        function initDocsPage() {
            if (typeof Prism !== 'undefined') {
                Prism.highlightAll();
            }

            fixDocLinks();
            updateTocActive();

            document.querySelectorAll('.docs-prose h2, .docs-prose h3').forEach(function(h) {
                if (!h.id) {
                    h.id = h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
                }
            });

            var tabsContainer = document.getElementById('tabs-container');
            if (tabsContainer) {
                var section = document.body.dataset.section || 'default';
                if (localStorage.getItem('docs-tab-' + section) === 'demo') {
                    window.switchTab('demo');
                }
                tabsContainer.removeAttribute('data-cloak');
            }
        }

        // Sidebar scroll preservation
        (function() {
            var SCROLL_KEY = 'accelade-sidebar-scroll';
            var nav = null;

            function getNav() {
                if (!nav) nav = document.getElementById('docs-sidebar-nav');
                return nav;
            }

            // Save scroll on any link click inside sidebar
            document.addEventListener('click', function(e) {
                var link = e.target.closest('#docs-sidebar-nav a');
                if (link) {
                    var n = getNav();
                    if (n) sessionStorage.setItem(SCROLL_KEY, String(n.scrollTop));
                }
            }, true);

            // Restore scroll after SPA navigation
            function restoreScroll() {
                var saved = sessionStorage.getItem(SCROLL_KEY);
                if (saved !== null) {
                    var n = getNav();
                    if (n) n.scrollTop = parseInt(saved, 10);
                }
            }

            // Listen for Accelade navigation events
            document.addEventListener('accelade:navigated', restoreScroll);

            // Also use MutationObserver as fallback
            var lastHref = location.href;
            var observer = new MutationObserver(function() {
                if (location.href !== lastHref) {
                    lastHref = location.href;
                    restoreScroll();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        })();

        // Global click handler using event delegation
        document.addEventListener('click', function(e) {
            // Close framework dropdown when clicking outside
            var dropdown = document.getElementById('framework-dropdown');
            if (dropdown && !e.target.closest('.framework-btn') && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }

            // Tab switching
            var tabBtn = e.target.closest('.tab-btn');
            if (tabBtn && tabBtn.dataset.tab) {
                window.switchTab(tabBtn.dataset.tab);
            }

            // TOC link click - switch to docs tab and scroll
            var tocLink = e.target.closest('[data-toc-link]');
            if (tocLink) {
                e.preventDefault();
                var targetId = tocLink.getAttribute('href').substring(1);
                var target = document.getElementById(targetId);
                if (target) {
                    window.switchTab('docs');
                    setTimeout(function() {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 50);
                }
            }

            // Code copy button
            var copyBtn = e.target.closest('.code-copy-btn');
            if (copyBtn) {
                handleCodeCopy(copyBtn);
            }

            // Code download button
            var downloadBtn = e.target.closest('.code-download-btn');
            if (downloadBtn) {
                handleCodeDownload(downloadBtn);
            }

        }, true);

        function handleCodeCopy(btn) {
            var targetId = btn.dataset.codeTarget;
            var pre = document.getElementById(targetId);
            if (!pre) {
                return;
            }

            var code = pre.querySelector('code') || pre;
            navigator.clipboard.writeText(code.textContent).then(function() {
                var copyIcon = btn.querySelector('.copy-icon');
                var checkIcon = btn.querySelector('.check-icon');
                var copyText = btn.querySelector('.copy-text');

                if (copyIcon) { copyIcon.classList.add('hidden'); }
                if (checkIcon) { checkIcon.classList.remove('hidden'); }
                if (copyText) { copyText.textContent = 'Copied!'; }

                setTimeout(function() {
                    if (copyIcon) { copyIcon.classList.remove('hidden'); }
                    if (checkIcon) { checkIcon.classList.add('hidden'); }
                    if (copyText) { copyText.textContent = 'Copy'; }
                }, 2000);
            });
        }

        function handleCodeDownload(btn) {
            var targetId = btn.dataset.codeTarget;
            var wrapper = document.querySelector('[data-code-block="' + targetId + '"]');
            if (!wrapper) {
                return;
            }

            var originalText = btn.querySelector('span:last-child');
            var originalContent = originalText ? originalText.textContent : 'Image';
            if (originalText) {
                originalText.textContent = 'Loading...';
            }

            try {
                var pre = document.getElementById(targetId);
                var codeEl = pre && pre.querySelector('code') || pre;
                var langLabel = wrapper.querySelector('.text-xs.font-medium.uppercase');
                langLabel = langLabel ? langLabel.textContent.trim() : 'CODE';

                var coloredLines = extractColoredLines(codeEl);
                var canvas = renderCodeToCanvas(coloredLines, langLabel);

                var link = document.createElement('a');
                link.download = 'code-snippet.png';
                link.href = canvas.toDataURL('image/png');
                link.click();

                if (originalText) {
                    originalText.textContent = 'Done!';
                }
                setTimeout(function() {
                    if (originalText) {
                        originalText.textContent = originalContent;
                    }
                }, 1500);
            } catch (error) {
                console.error('Failed to generate image:', error);
                if (originalText) {
                    originalText.textContent = originalContent;
                }
                alert('Failed to generate image: ' + error.message);
            }
        }

        function extractColoredLines(element) {
            var tokens = [];

            function walk(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    var text = node.textContent;
                    if (text) {
                        var color = '#e2e8f0';
                        var parent = node.parentElement;
                        if (parent && parent.classList.contains('token')) {
                            color = window.getComputedStyle(parent).color;
                        }
                        tokens.push({ text: text, color: color });
                    }
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    for (var i = 0; i < node.childNodes.length; i++) {
                        walk(node.childNodes[i]);
                    }
                }
            }
            walk(element);

            var lines = [];
            var currentLine = [];
            tokens.forEach(function(token) {
                var parts = token.text.split('\n');
                parts.forEach(function(part, i) {
                    if (i > 0) {
                        lines.push(currentLine);
                        currentLine = [];
                    }
                    if (part) {
                        currentLine.push({ text: part, color: token.color });
                    }
                });
            });
            if (currentLine.length > 0) {
                lines.push(currentLine);
            }

            return lines;
        }

        function renderCodeToCanvas(coloredLines, langLabel) {
            var config = {
                padding: 40, headerHeight: 48, fontSize: 14, lineHeight: 1.6,
                font: 'JetBrains Mono, SF Mono, Monaco, Consolas, monospace',
                bgColor: '#1e293b', outerBgColor: '#0f172a', borderColor: '#334155',
                textColor: '#e2e8f0', labelColor: '#94a3b8',
                dotColors: ['#ef4444', '#eab308', '#22c55e'], borderRadius: 12
            };

            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            var scale = 2;

            ctx.font = config.fontSize + 'px ' + config.font;
            var maxWidth = 400;
            coloredLines.forEach(function(line) {
                var lineText = line.map(function(t) { return t.text; }).join('');
                var width = ctx.measureText(lineText).width;
                if (width > maxWidth) {
                    maxWidth = width;
                }
            });

            var contentWidth = Math.min(maxWidth + config.padding, 900);
            var codeHeight = coloredLines.length * config.fontSize * config.lineHeight;
            var contentHeight = codeHeight + config.padding;
            var totalWidth = contentWidth + config.padding * 2;
            var totalHeight = contentHeight + config.headerHeight + config.padding * 2;

            canvas.width = totalWidth * scale;
            canvas.height = totalHeight * scale;
            ctx.scale(scale, scale);

            // Outer background gradient
            var gradient = ctx.createLinearGradient(0, 0, totalWidth, totalHeight);
            gradient.addColorStop(0, config.outerBgColor);
            gradient.addColorStop(1, '#1a1a2e');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, totalWidth, totalHeight);

            var blockX = config.padding;
            var blockY = config.padding;
            var blockW = contentWidth;
            var blockH = contentHeight + config.headerHeight;

            // Shadow and container
            ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
            ctx.shadowBlur = 20;
            ctx.shadowOffsetY = 10;
            ctx.fillStyle = config.bgColor;
            ctx.beginPath();
            ctx.roundRect(blockX, blockY, blockW, blockH, config.borderRadius);
            ctx.fill();

            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetY = 0;

            // Border
            ctx.strokeStyle = config.borderColor;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.roundRect(blockX, blockY, blockW, blockH, config.borderRadius);
            ctx.stroke();

            // Header with bottom border
            ctx.fillStyle = config.bgColor;
            ctx.beginPath();
            ctx.roundRect(blockX, blockY, blockW, config.headerHeight, [config.borderRadius, config.borderRadius, 0, 0]);
            ctx.fill();

            ctx.strokeStyle = config.borderColor;
            ctx.beginPath();
            ctx.moveTo(blockX, blockY + config.headerHeight);
            ctx.lineTo(blockX + blockW, blockY + config.headerHeight);
            ctx.stroke();

            // Traffic light dots
            var dotY = blockY + config.headerHeight / 2;
            var dotStartX = blockX + 16;
            config.dotColors.forEach(function(color, i) {
                ctx.fillStyle = color;
                ctx.beginPath();
                ctx.arc(dotStartX + (i * 20), dotY, 6, 0, Math.PI * 2);
                ctx.fill();
            });

            // Language label
            ctx.fillStyle = config.labelColor;
            ctx.font = '500 11px ' + config.font;
            ctx.textAlign = 'left';
            ctx.fillText(langLabel.toUpperCase(), dotStartX + 70, dotY + 4);

            // Syntax-highlighted code
            ctx.font = config.fontSize + 'px ' + config.font;
            ctx.textBaseline = 'top';

            var codeStartX = blockX + config.padding / 2;
            var codeStartY = blockY + config.headerHeight + config.padding / 2;

            coloredLines.forEach(function(line, lineIndex) {
                var x = codeStartX;
                var y = codeStartY + (lineIndex * config.fontSize * config.lineHeight);

                line.forEach(function(segment) {
                    ctx.fillStyle = segment.color;
                    ctx.fillText(segment.text, x, y);
                    x += ctx.measureText(segment.text).width;
                });
            });

            return canvas;
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                openSearch();
            }
            if (e.key === 'Escape') {
                closeSearch();
            }
        });

        window.addEventListener('scroll', updateTocActive);

        // SPA navigation handling
        var lastUrl = location.href;
        var observer = new MutationObserver(function() {
            if (location.href !== lastUrl) {
                lastUrl = location.href;
                requestAnimationFrame(function() {
                    initDocsPage();
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            initDocsPage();
            observer.observe(document.body, { childList: true, subtree: true });
        });

        window.addEventListener('popstate', function() {
            requestAnimationFrame(function() {
                initDocsPage();
            });
        });
    </script>
</body>
</html>
