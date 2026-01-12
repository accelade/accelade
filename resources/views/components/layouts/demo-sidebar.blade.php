@props([
    'framework' => 'vanilla',
    'section' => 'counter',
])

@php
    $frameworkColors = [
        'vanilla' => ['bg' => 'bg-indigo-600', 'shadow' => 'shadow-indigo-200', 'text' => 'text-indigo-600', 'dot' => 'bg-indigo-500'],
        'vue' => ['bg' => 'bg-green-600', 'shadow' => 'shadow-green-200', 'text' => 'text-green-600', 'dot' => 'bg-green-500'],
        'react' => ['bg' => 'bg-blue-600', 'shadow' => 'shadow-blue-200', 'text' => 'text-blue-600', 'dot' => 'bg-blue-500'],
        'svelte' => ['bg' => 'bg-orange-600', 'shadow' => 'shadow-orange-200', 'text' => 'text-orange-600', 'dot' => 'bg-orange-500'],
        'angular' => ['bg' => 'bg-red-600', 'shadow' => 'shadow-red-200', 'text' => 'text-red-600', 'dot' => 'bg-red-500'],
    ];
    $colors = $frameworkColors[$framework] ?? $frameworkColors['vanilla'];

    $sections = [
        ['id' => 'counter', 'label' => 'Counter', 'icon' => '🔢'],
        ['id' => 'scripts', 'label' => 'Custom Scripts', 'icon' => '📜'],
        ['id' => 'navigation', 'label' => 'SPA Navigation', 'icon' => '🧭'],
        ['id' => 'progress', 'label' => 'Progress Bar', 'icon' => '📊'],
        ['id' => 'notifications', 'label' => 'Notifications', 'icon' => '🔔'],
        ['id' => 'shared-data', 'label' => 'Shared Data', 'icon' => '📤'],
        ['id' => 'lazy', 'label' => 'Lazy Loading', 'icon' => '⏳'],
        ['id' => 'content', 'label' => 'Content', 'icon' => '📄'],
        ['id' => 'data', 'label' => 'Data Component', 'icon' => '💾'],
        ['id' => 'defer', 'label' => 'Defer Component', 'icon' => '⏱️'],
        ['id' => 'errors', 'label' => 'Errors Component', 'icon' => '⚠️'],
        ['id' => 'event', 'label' => 'Event (Echo)', 'icon' => '📡'],
        ['id' => 'flash', 'label' => 'Flash Data', 'icon' => '⚡'],
    ];
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
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
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
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
        }
        .sidebar-link.active:hover {
            transform: translateX(2px);
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .sidebar-link .icon {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .sidebar-link:not(.active) .icon {
            background-color: #f1f5f9;
        }
        .sidebar-link.active .icon {
            background-color: rgba(255, 255, 255, 0.2);
        }
        /* Code block styles - override Prism defaults */
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
        /* Prism token colors for dark theme */
        .code-block-wrapper .token.comment,
        .code-block-wrapper .token.prolog,
        .code-block-wrapper .token.doctype,
        .code-block-wrapper .token.cdata {
            color: #6b7280 !important;
        }
        .code-block-wrapper .token.punctuation {
            color: #94a3b8 !important;
        }
        .code-block-wrapper .token.property,
        .code-block-wrapper .token.tag,
        .code-block-wrapper .token.boolean,
        .code-block-wrapper .token.number,
        .code-block-wrapper .token.constant,
        .code-block-wrapper .token.symbol {
            color: #f472b6 !important;
        }
        .code-block-wrapper .token.selector,
        .code-block-wrapper .token.attr-name,
        .code-block-wrapper .token.string,
        .code-block-wrapper .token.char,
        .code-block-wrapper .token.builtin {
            color: #a5f3fc !important;
        }
        .code-block-wrapper .token.operator,
        .code-block-wrapper .token.entity,
        .code-block-wrapper .token.url,
        .code-block-wrapper .language-css .token.string,
        .code-block-wrapper .style .token.string,
        .code-block-wrapper .token.variable {
            color: #fbbf24 !important;
        }
        .code-block-wrapper .token.atrule,
        .code-block-wrapper .token.attr-value,
        .code-block-wrapper .token.keyword {
            color: #c084fc !important;
        }
        .code-block-wrapper .token.function {
            color: #60a5fa !important;
        }
        .code-block-wrapper .token.regex,
        .code-block-wrapper .token.important {
            color: #fb923c !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-100">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Accelade
                </h1>
                <p class="text-xs text-slate-500 mt-1">Reactive Blade Templates</p>
            </div>

            <!-- Framework Selector -->
            <div class="p-4 border-b border-slate-100">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">Framework</label>
                <div class="flex flex-wrap gap-1">
                    @foreach(['vanilla', 'vue', 'react', 'svelte', 'angular'] as $fw)
                        <a
                            href="{{ route('demo.section', ['framework' => $fw, 'section' => $section]) }}"
                            class="px-2.5 py-1 rounded-md text-xs font-medium transition {{ $framework === $fw ? $frameworkColors[$fw]['bg'] . ' text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            {{ ucfirst($fw) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 overflow-y-auto">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 block">Components</label>
                <div class="space-y-1">
                    @foreach($sections as $s)
                        <a
                            href="{{ route('demo.section', ['framework' => $framework, 'section' => $s['id']]) }}"
                            class="sidebar-link {{ $section === $s['id'] ? 'active' : '' }}"
                        >
                            <span class="icon">{{ $s['icon'] }}</span>
                            <span>{{ $s['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-slate-100 text-xs text-slate-400">
                <p>Laravel {{ app()->version() }}</p>
                <p>Framework: {{ ucfirst($framework) }}</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 px-8 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 {{ $colors['dot'] }} rounded-full"></span>
                        <h2 class="text-lg font-semibold text-slate-800">
                            {{ collect($sections)->firstWhere('id', $section)['label'] ?? ucfirst($section) }}
                        </h2>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full text-xs font-medium text-slate-600">
                        {{ ucfirst($framework) }}
                    </span>
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
