@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="progress">
    <!-- Demo: Progress Bar Testing -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Progress Bar Demo</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Test the progress bar with manual controls and simulated requests.</p>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Manual Controls -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Manual Controls</h3>
                <div class="flex flex-wrap gap-3">
                    <button
                        onclick="window.Accelade?.progress?.start()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition text-sm"
                    >
                        Start Progress
                    </button>
                    <button
                        onclick="window.Accelade?.progress?.done()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition text-sm"
                    >
                        Complete
                    </button>
                    <button
                        onclick="window.Accelade?.progress?.done(true)"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm"
                    >
                        Force Complete
                    </button>
                </div>
            </div>

            <!-- Simulated Request -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Simulated Server Request</h3>
                <div class="flex flex-wrap gap-3">
                    <button
                        onclick="testServerRequest()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm"
                    >
                        Test Sync Request
                    </button>
                    <button
                        onclick="testSlowRequest()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm"
                    >
                        Test Slow Request (2s)
                    </button>
                </div>
                <p id="request-status" class="text-sm text-slate-500 mt-3"></p>
            </div>
        </div>

        <!-- Progress Config -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4">Configure Progress Bar</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Color</label>
                    <input
                        type="color"
                        id="progress-color"
                        value="#6366f1"
                        onchange="updateProgressConfig()"
                        class="w-full h-10 rounded-lg cursor-pointer"
                    />
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Height (px)</label>
                    <input
                        type="range"
                        id="progress-height"
                        min="2"
                        max="10"
                        value="3"
                        onchange="updateProgressConfig()"
                        class="w-full"
                    />
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Position</label>
                    <select
                        id="progress-position"
                        onchange="updateProgressConfig()"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm"
                    >
                        <option value="top">Top</option>
                        <option value="bottom">Bottom</option>
                    </select>
                </div>
            </div>
        </div>

        <script>
            function testServerRequest() {
                const status = document.getElementById('request-status');
                status.textContent = 'Sending request...';

                window.Accelade?.progress?.start();

                fetch('/accelade/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        component: 'demo-test',
                        property: 'testValue',
                        value: Date.now()
                    })
                })
                .then(res => res.json())
                .then(data => {
                    status.textContent = 'Success! Response: ' + JSON.stringify(data);
                    window.Accelade?.progress?.done();
                })
                .catch(err => {
                    status.textContent = 'Error: ' + err.message;
                    window.Accelade?.progress?.done();
                });
            }

            function testSlowRequest() {
                const status = document.getElementById('request-status');
                status.textContent = 'Starting slow request (2 seconds)...';

                window.Accelade?.progress?.start();

                setTimeout(() => {
                    status.textContent = 'Slow request completed!';
                    window.Accelade?.progress?.done();
                }, 2000);
            }

            function updateProgressConfig() {
                const config = {
                    color: document.getElementById('progress-color').value,
                    height: parseInt(document.getElementById('progress-height').value),
                    position: document.getElementById('progress-position').value
                };
                window.Accelade?.progress?.configure(config);
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
</x-accelade::layouts.demo-sidebar>
