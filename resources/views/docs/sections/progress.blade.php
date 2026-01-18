@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="progress" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: Progress Bar Settings -->
    <section class="bg-[var(--docs-bg-secondary)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></span>
            <h3 class="text-lg font-semibold">Progress Bar Settings</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Customize the progress bar appearance and test it with a simulated request.</p>

        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Settings -->
            <div class="bg-[var(--docs-bg)] rounded-lg p-4 border border-[var(--docs-border)]">
                <h4 class="font-medium mb-4 text-sm">Appearance</h4>

                <div class="space-y-4">
                    <!-- Color -->
                    <div>
                        <label class="block text-xs font-medium text-[var(--docs-text-muted)] mb-1.5">Color</label>
                        <div class="flex gap-2">
                            <input
                                type="color"
                                id="progress-color"
                                value="#6366f1"
                                onchange="updateProgressSettings()"
                                class="w-10 h-10 rounded-lg cursor-pointer border border-[var(--docs-border)]"
                            />
                            <input
                                type="text"
                                id="progress-color-text"
                                value="#6366f1"
                                onchange="document.getElementById('progress-color').value = this.value; updateProgressSettings()"
                                class="flex-1 px-3 py-2 text-xs rounded-lg border border-[var(--docs-border)] bg-[var(--docs-bg-secondary)]"
                                placeholder="#6366f1"
                            />
                        </div>
                    </div>

                    <!-- Height -->
                    <div>
                        <label class="block text-xs font-medium text-[var(--docs-text-muted)] mb-1.5">
                            Height: <span id="height-value">3</span>px
                        </label>
                        <input
                            type="range"
                            id="progress-height"
                            min="1"
                            max="10"
                            value="3"
                            onchange="document.getElementById('height-value').textContent = this.value; updateProgressSettings()"
                            class="w-full accent-indigo-600"
                        />
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-xs font-medium text-[var(--docs-text-muted)] mb-1.5">Position</label>
                        <select
                            id="progress-position"
                            onchange="updateProgressSettings()"
                            class="w-full px-3 py-2 text-xs rounded-lg border border-[var(--docs-border)] bg-[var(--docs-bg-secondary)]"
                        >
                            <option value="top">Top</option>
                            <option value="bottom">Bottom</option>
                        </select>
                    </div>

                    <!-- Spinner -->
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="progress-spinner"
                            checked
                            onchange="updateProgressSettings()"
                            class="w-4 h-4 rounded accent-indigo-600"
                        />
                        <label for="progress-spinner" class="text-xs font-medium text-[var(--docs-text-muted)]">Show Spinner</label>
                    </div>

                    <!-- Gradient -->
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="progress-gradient"
                            checked
                            onchange="updateProgressSettings()"
                            class="w-4 h-4 rounded accent-indigo-600"
                        />
                        <label for="progress-gradient" class="text-xs font-medium text-[var(--docs-text-muted)]">Use Gradient</label>
                    </div>
                </div>
            </div>

            <!-- Test Area -->
            <div class="bg-[var(--docs-bg)] rounded-lg p-4 border border-[var(--docs-border)]">
                <h4 class="font-medium mb-4 text-sm">Test Progress Bar</h4>

                <div class="space-y-3">
                    <button
                        onclick="testProgressBar()"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Run Demo (2 seconds)
                    </button>

                    <p id="request-status" class="text-xs text-center text-[var(--docs-text-muted)]">Click the button to see the progress bar in action</p>
                </div>

                <!-- Color Presets -->
                <div class="mt-4 pt-4 border-t border-[var(--docs-border)]">
                    <label class="block text-xs font-medium text-[var(--docs-text-muted)] mb-2">Color Presets</label>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="setPresetColor('#6366f1')" class="w-8 h-8 rounded-lg bg-indigo-500 hover:ring-2 ring-offset-2 ring-indigo-500 transition" title="Indigo"></button>
                        <button onclick="setPresetColor('#8b5cf6')" class="w-8 h-8 rounded-lg bg-violet-500 hover:ring-2 ring-offset-2 ring-violet-500 transition" title="Violet"></button>
                        <button onclick="setPresetColor('#ec4899')" class="w-8 h-8 rounded-lg bg-pink-500 hover:ring-2 ring-offset-2 ring-pink-500 transition" title="Pink"></button>
                        <button onclick="setPresetColor('#ef4444')" class="w-8 h-8 rounded-lg bg-red-500 hover:ring-2 ring-offset-2 ring-red-500 transition" title="Red"></button>
                        <button onclick="setPresetColor('#f97316')" class="w-8 h-8 rounded-lg bg-orange-500 hover:ring-2 ring-offset-2 ring-orange-500 transition" title="Orange"></button>
                        <button onclick="setPresetColor('#eab308')" class="w-8 h-8 rounded-lg bg-yellow-500 hover:ring-2 ring-offset-2 ring-yellow-500 transition" title="Yellow"></button>
                        <button onclick="setPresetColor('#22c55e')" class="w-8 h-8 rounded-lg bg-green-500 hover:ring-2 ring-offset-2 ring-green-500 transition" title="Green"></button>
                        <button onclick="setPresetColor('#06b6d4')" class="w-8 h-8 rounded-lg bg-cyan-500 hover:ring-2 ring-offset-2 ring-cyan-500 transition" title="Cyan"></button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function updateProgressSettings() {
                const color = document.getElementById('progress-color').value;
                const height = parseInt(document.getElementById('progress-height').value);
                const position = document.getElementById('progress-position').value;
                const includeSpinner = document.getElementById('progress-spinner').checked;
                const useGradient = document.getElementById('progress-gradient').checked;

                // Update text input to match color picker
                document.getElementById('progress-color-text').value = color;

                // Apply settings
                window.Accelade?.progress?.configure({
                    color: color,
                    height: height,
                    position: position,
                    includeSpinner: includeSpinner,
                    useGradient: useGradient
                });
            }

            function setPresetColor(color) {
                document.getElementById('progress-color').value = color;
                document.getElementById('progress-color-text').value = color;
                updateProgressSettings();
            }

            function testProgressBar() {
                const status = document.getElementById('request-status');
                status.textContent = 'Loading...';
                status.classList.add('text-indigo-500');

                window.Accelade?.progress?.start();

                setTimeout(() => {
                    status.textContent = 'Complete!';
                    status.classList.remove('text-indigo-500');
                    status.classList.add('text-green-500');
                    window.Accelade?.progress?.done();

                    setTimeout(() => {
                        status.textContent = 'Click the button to see the progress bar in action';
                        status.classList.remove('text-green-500');
                    }, 1500);
                }, 2000);
            }
        </script>
    </section>

    <x-accelade::code-block language="javascript" filename="progress.js">
// Configure progress bar appearance
window.Accelade.progress.configure({
    color: '#6366f1',      // Primary color
    height: 3,             // Height in pixels (1-10)
    position: 'top',       // 'top' or 'bottom'
    includeSpinner: true,  // Show spinner indicator
    useGradient: true,     // Use gradient effect
    delay: 0,              // Delay before showing (ms)
    speed: 200,            // Animation speed (ms)
    trickleSpeed: 200      // Auto-increment speed (ms)
});

// The progress bar automatically shows during SPA navigation
// You can also control it manually:
window.Accelade.progress.start();  // Start progress
window.Accelade.progress.done();   // Complete progress
    </x-accelade::code-block>
</x-accelade::layouts.docs>
