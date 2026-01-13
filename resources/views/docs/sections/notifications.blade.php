@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="notifications" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: Notifications -->
    <section class="bg-[var(--docs-bg-secondary)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
            <h3 class="text-lg font-semibold">Notifications</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Filament-style notifications from PHP backend or JavaScript frontend.</p>

        <!-- Backend (PHP) Notifications -->
        <div class="mb-4">
            <h4 class="text-sm font-medium mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
                Backend (PHP) - Triggered from server
            </h4>
            <div class="bg-violet-500/10 rounded-lg p-4 border border-violet-500/20">
                <div class="flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('docs.notify', 'success') }}" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition">Success</a>
                    <a href="{{ route('docs.notify', 'info') }}" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">Info</a>
                    <a href="{{ route('docs.notify', 'warning') }}" class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-medium hover:bg-amber-700 transition">Warning</a>
                    <a href="{{ route('docs.notify', 'danger') }}" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition">Danger</a>
                </div>
            </div>
        </div>

        <!-- Frontend (JS) Notifications -->
        <div class="bg-[var(--docs-bg)] rounded-lg p-4 border border-[var(--docs-border)]">
            <h4 class="font-medium mb-3 text-sm flex items-center gap-2">
                <span class="w-2 h-2 bg-cyan-500 rounded-full"></span>
                Frontend (JavaScript)
            </h4>
            <div class="flex flex-wrap gap-2">
                <button
                    onclick="window.Accelade?.notify?.success('Success!', 'Operation completed successfully.')"
                    class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition"
                >
                    Success
                </button>
                <button
                    onclick="window.Accelade?.notify?.info('Info', 'Here is some information.')"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition"
                >
                    Info
                </button>
                <button
                    onclick="window.Accelade?.notify?.warning('Warning', 'Please be careful!')"
                    class="px-3 py-1.5 bg-amber-600 text-white rounded-lg text-xs font-medium hover:bg-amber-700 transition"
                >
                    Warning
                </button>
                <button
                    onclick="window.Accelade?.notify?.danger('Error', 'Something went wrong.')"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition"
                >
                    Danger
                </button>
            </div>
        </div>
    </section>

    <x-accelade::code-block language="javascript" filename="notifications.js">
// JavaScript API
window.Accelade.notify.success('Title', 'Message');
window.Accelade.notify.info('Title', 'Message');
window.Accelade.notify.warning('Title', 'Message');
window.Accelade.notify.danger('Title', 'Message');

// PHP (Backend)
Notify::success('Saved!')->message('Changes saved.');
    </x-accelade::code-block>
</x-accelade::layouts.docs>
