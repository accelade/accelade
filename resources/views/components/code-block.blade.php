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
                <svg class="copy-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <svg class="check-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
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
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Image</span>
            </button>
        </div>
    </div>
    {{-- Code content --}}
    <div class="code-content overflow-x-auto" style="background:#1e293b;">
        <pre id="{{ $id }}" class="language-{{ $prismLanguage }}" style="margin:0;border-radius:0;background:#1e293b !important;"><code class="language-{{ $prismLanguage }}" style="background:transparent !important;">{!! $slot !!}</code></pre>
    </div>
</div>
