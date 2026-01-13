@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="progress" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: Progress Bar Testing -->
    <section class="bg-[var(--docs-bg-secondary)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></span>
            <h3 class="text-lg font-semibold">Progress Bar Demo</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Test the progress bar with manual controls.</p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Manual Controls -->
            <div class="bg-[var(--docs-bg)] rounded-lg p-4 border border-[var(--docs-border)]">
                <h4 class="font-medium mb-3 text-sm">Manual Controls</h4>
                <div class="flex flex-wrap gap-2">
                    <button
                        onclick="window.Accelade?.progress?.start()"
                        class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition"
                    >
                        Start Progress
                    </button>
                    <button
                        onclick="window.Accelade?.progress?.done()"
                        class="px-3 py-1.5 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition"
                    >
                        Complete
                    </button>
                    <button
                        onclick="window.Accelade?.progress?.done(true)"
                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition"
                    >
                        Force Complete
                    </button>
                </div>
            </div>

            <!-- Simulated Request -->
            <div class="bg-[var(--docs-bg)] rounded-lg p-4 border border-[var(--docs-border)]">
                <h4 class="font-medium mb-3 text-sm">Simulated Request</h4>
                <button
                    onclick="testSlowRequest()"
                    class="px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs font-medium hover:bg-purple-700 transition"
                >
                    Test Slow Request (2s)
                </button>
                <p id="request-status" class="text-xs text-[var(--docs-text-muted)] mt-2"></p>
            </div>
        </div>

        <script>
            function testSlowRequest() {
                const status = document.getElementById('request-status');
                status.textContent = 'Starting slow request (2 seconds)...';
                window.Accelade?.progress?.start();
                setTimeout(() => {
                    status.textContent = 'Slow request completed!';
                    window.Accelade?.progress?.done();
                }, 2000);
            }
        </script>
    </section>

    <x-accelade::code-block language="javascript" filename="progress.js">
// Manual controls
window.Accelade.progress.start();
window.Accelade.progress.done();

// Configure progress bar
window.Accelade.progress.configure({
    color: '#6366f1',
    height: 3,
    position: 'top'
});
    </x-accelade::code-block>
</x-accelade::layouts.docs>
