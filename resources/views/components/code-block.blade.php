@props([
    'language' => 'markup',
    'filename' => null,
])

@php
    $id = 'code-' . Str::random(8);

    // Map common language names to Prism language classes
    // Blade files use PHP since they contain PHP, HTML, and JS mixed
    $prismLanguage = match($language) {
        'blade' => 'php',
        'html', 'xml' => 'markup',
        'js' => 'javascript',
        default => $language,
    };
@endphp

<div class="code-block-wrapper rounded-xl overflow-hidden shadow-lg my-4" dir="ltr" style="background:#1e293b;border:1px solid #334155;" data-code-block="{{ $id }}">
    {{-- macOS-style title bar --}}
    <div class="flex items-center justify-between px-4 py-3" style="background:#1e293b;border-bottom:1px solid #334155;">
        <div class="flex items-center gap-4">
            {{-- Traffic lights --}}
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background:#ef4444;"></span>
                <span class="w-3 h-3 rounded-full" style="background:#eab308;"></span>
                <span class="w-3 h-3 rounded-full" style="background:#22c55e;"></span>
            </div>
            {{-- Filename or language label --}}
            <span class="text-xs font-medium uppercase tracking-wider" style="color:#94a3b8;">
                {{ $filename ?? $language }}
            </span>
        </div>
        {{-- Action buttons --}}
        <div class="flex items-center gap-2">
            {{-- Copy button --}}
            <button
                type="button"
                class="code-copy-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all"
                style="color:#94a3b8;background:rgba(51,65,85,0.5);"
                data-code-target="{{ $id }}"
                title="Copy code"
            >
                <span class="copy-icon">
                    <x-accelade::icon name="heroicon-o-clipboard-document" size="sm" :showFallback="false" />
                </span>
                <span class="check-icon hidden">
                    <x-accelade::icon name="heroicon-o-check" size="sm" :showFallback="false" />
                </span>
                <span class="copy-text">Copy</span>
            </button>
            {{-- Download as image button --}}
            <button
                type="button"
                class="code-download-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all"
                style="color:#94a3b8;background:rgba(51,65,85,0.5);"
                data-code-target="{{ $id }}"
                title="Download as image"
            >
                <x-accelade::icon name="heroicon-o-photo" size="sm" :showFallback="false" />
                <span>Image</span>
            </button>
        </div>
    </div>
    {{-- Code content --}}
    <div class="code-content overflow-x-auto" style="background:#1e293b;">
        <pre id="{{ $id }}" class="language-{{ $prismLanguage }}" style="margin:0;border-radius:0;background:#1e293b !important;"><code class="language-{{ $prismLanguage }}" style="background:transparent !important;">{!! $slot !!}</code></pre>
    </div>
</div>
