@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => false])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="api-reference" :documentation="$documentation" :hasDemo="false">
    {{-- API Reference page has no interactive demo --}}
    <div class="text-center py-8 text-[var(--docs-text-muted)]">
        <p>Complete API documentation for Accelade.</p>
        <div class="mt-4 flex flex-wrap justify-center gap-3">
            <a href="/docs/scripts" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--docs-bg-alt)] border border-[var(--docs-border)] hover:border-[var(--docs-accent)] transition">
                <span>📜</span>
                Custom Scripts
            </a>
            <a href="/docs/bridge" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--docs-bg-alt)] border border-[var(--docs-border)] hover:border-[var(--docs-accent)] transition">
                <span>🌉</span>
                Bridge
            </a>
            <a href="/docs/notifications" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--docs-bg-alt)] border border-[var(--docs-border)] hover:border-[var(--docs-accent)] transition">
                <span>🔔</span>
                Notifications
            </a>
        </div>
    </div>
</x-accelade::layouts.docs>
