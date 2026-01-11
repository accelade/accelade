<x-accelade::layouts.demo :framework="'vanilla'">
    <!-- Demo: Counter (Client-side) -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Counter Component</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">A simple reactive counter using pure vanilla JavaScript. No build tools required!</p>

        <div class="flex justify-center mb-6">
            <x-accelade::counter :initial-count="0" />
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <code class="text-sm font-mono text-slate-700">
                &lt;x-accelade.counter :initial-count="0" /&gt;
            </code>
        </div>
    </section>

    <!-- Demo: Counter with Server Sync -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-indigo-500 rounded-full animate-pulse"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Counter with Server Sync</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Counter that syncs state with the server (persisted across requests).</p>

        <div class="flex justify-center mb-6">
            <x-accelade::counter :initial-count="10" sync="count" />
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <code class="text-sm font-mono text-slate-700">
                &lt;x-accelade.counter :initial-count="10" sync="count" /&gt;
            </code>
        </div>
    </section>

    <!-- Demo: Custom Script Functions -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Custom Script Functions</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Define custom functions using &lt;accelade:script&gt; that can access state.</p>

        @accelade(['count' => 0, 'message' => 'Click the buttons!'])
            <div class="text-center p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl mb-6">
                <p class="text-2xl text-slate-800 mb-4 font-medium" a-text="message">
                    Click the buttons!
                </p>

                <p class="text-4xl font-bold text-indigo-600 mb-4" a-text="count">0</p>

                <div class="flex gap-2 justify-center">
                    <button
                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
                        @click="addFive()"
                    >
                        +5
                    </button>
                    <button
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition shadow-lg shadow-purple-200"
                        @click="double()"
                    >
                        Double
                    </button>
                    <button
                        class="px-6 py-3 bg-slate-600 text-white rounded-xl font-medium hover:bg-slate-700 transition shadow-lg shadow-slate-200"
                        @click="reset('count')"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <accelade:script>
                return {
                    addFive() {
                        $set('count', $get('count') + 5);
                        $set('message', 'Added 5!');
                    },
                    double() {
                        const current = $get('count');
                        $set('count', current * 2);
                        $set('message', 'Doubled to ' + (current * 2) + '!');
                    }
                };
            </accelade:script>
        @endaccelade

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">&lt;accelade:script&gt;
    return {
        addFive() {
            $set('count', $get('count') + 5);
        },
        double() {
            $set('count', $get('count') * 2);
        }
    };
&lt;/accelade:script&gt;</pre>
        </div>
    </section>

    <!-- Demo: SPA Navigation -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">SPA Navigation</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Navigate without full page reloads using &lt;x-accelade::link&gt;.</p>

        <!-- SPA Demo (Same Framework - No Page Reload) -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">SPA Navigation (Same Framework)</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('demo.vanilla') }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition"
                >
                    Reload This Page (SPA)
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.vanilla') }}?time={{ time() }}"
                    class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition"
                >
                    Navigate with Query (SPA)
                </x-accelade::link>
            </div>
            <p class="text-xs text-slate-400 text-center mt-2">Watch the counter state reset without a full page reload!</p>
        </div>

        <!-- Preserve Scroll & State -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">Preserve Scroll & State</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('demo.vanilla') }}?t={{ time() }}"
                    :preserveScroll="true"
                    class="px-6 py-3 bg-teal-600 text-white rounded-xl font-medium hover:bg-teal-700 transition"
                >
                    Preserve Scroll Position
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.vanilla') }}?t={{ time() }}"
                    :preserveState="true"
                    class="px-6 py-3 bg-amber-600 text-white rounded-xl font-medium hover:bg-amber-700 transition"
                >
                    Preserve Component State
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.vanilla') }}?t={{ time() }}"
                    :preserveScroll="true"
                    :preserveState="true"
                    class="px-6 py-3 bg-rose-600 text-white rounded-xl font-medium hover:bg-rose-700 transition"
                >
                    Preserve Both
                </x-accelade::link>
            </div>
            <p class="text-xs text-slate-400 text-center mt-2">Scroll down, update the counter, then click to test preservation!</p>
        </div>

        <!-- Framework Switch (Full Page Reload) -->
        <div>
            <h3 class="text-sm font-semibold text-slate-600 mb-3 text-center">Switch Framework (Full Reload)</h3>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-accelade::link
                    href="{{ route('demo.vue') }}"
                    class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition"
                >
                    Vue Demo
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.react') }}"
                    class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition"
                >
                    React Demo
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.svelte') }}"
                    class="px-6 py-3 bg-orange-600 text-white rounded-xl font-medium hover:bg-orange-700 transition"
                >
                    Svelte Demo
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('demo.angular') }}"
                    class="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition"
                >
                    Angular Demo
                </x-accelade::link>
            </div>
        </div>
    </section>

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

    <!-- Demo: Notifications -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Notifications</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Filament-style notifications from PHP backend or JavaScript frontend.</p>

        <!-- Backend (PHP) Notifications -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-slate-600 mb-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
                Backend (PHP) - Triggered from server, persists across redirects
            </h3>
            <div class="bg-violet-50 rounded-xl p-6 border border-violet-100">
                <div class="flex flex-wrap gap-3 mb-4">
                    <a href="{{ route('demo.notify', 'success') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition text-sm" data-testid="backend-notify-success">Success</a>
                    <a href="{{ route('demo.notify', 'info') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm" data-testid="backend-notify-info">Info</a>
                    <a href="{{ route('demo.notify', 'warning') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition text-sm" data-testid="backend-notify-warning">Warning</a>
                    <a href="{{ route('demo.notify', 'danger') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm" data-testid="backend-notify-danger">Danger</a>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('demo.notify', 'persistent') }}" class="px-4 py-2 bg-violet-600 text-white rounded-lg font-medium hover:bg-violet-700 transition text-sm">Persistent</a>
                    <a href="{{ route('demo.notify', 'actions') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition text-sm">With Actions</a>
                    <a href="{{ route('demo.notify', 'custom') }}" class="px-4 py-2 bg-pink-600 text-white rounded-lg font-medium hover:bg-pink-700 transition text-sm">Custom Icon</a>
                </div>
                <div class="mt-4 bg-white/60 rounded-lg p-3">
                    <pre class="text-xs font-mono text-slate-700">// PHP Controller
Notify::success('Saved!')->body('Changes saved.');
return redirect()->back();</pre>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Frontend (JS) Quick Notifications -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 bg-cyan-500 rounded-full"></span>
                    Frontend (JavaScript)
                </h3>
                <div class="flex flex-wrap gap-3">
                    <button
                        onclick="window.Accelade?.notify?.success('Success!', 'Operation completed successfully.')"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition text-sm"
                        data-testid="notify-success"
                    >
                        Success
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.info('Info', 'Here is some information.')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm"
                        data-testid="notify-info"
                    >
                        Info
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.warning('Warning', 'Please be careful!')"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition text-sm"
                        data-testid="notify-warning"
                    >
                        Warning
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.danger('Error', 'Something went wrong.')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm"
                        data-testid="notify-danger"
                    >
                        Danger
                    </button>
                </div>
            </div>

            <!-- Position Control -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Position</h3>
                <div class="flex flex-wrap gap-2">
                    <button onclick="testNotifyPosition('top-left')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Top Left</button>
                    <button onclick="testNotifyPosition('top-center')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Top Center</button>
                    <button onclick="testNotifyPosition('top-right')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Top Right</button>
                    <button onclick="testNotifyPosition('bottom-left')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Bottom Left</button>
                    <button onclick="testNotifyPosition('bottom-center')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Bottom Center</button>
                    <button onclick="testNotifyPosition('bottom-right')"
                        class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition"
                    >
                        Bottom Right
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">// JavaScript API
window.Accelade.notify.success('Title', 'Message');
window.Accelade.notify.info('Title', 'Message');
window.Accelade.notify.warning('Title', 'Message');
window.Accelade.notify.danger('Title', 'Message');

// PHP (Backend)
Notify::success('Saved!')->message('Changes saved.');</pre>
        </div>

        <script>
            function testNotifyPosition(position) {
                window.Accelade?.notify?.show({
                    id: 'pos-' + Date.now(),
                    title: position,
                    message: 'Notification at ' + position,
                    type: 'info',
                    position: position,
                    duration: 3000,
                    dismissible: true
                });
            }
        </script>
    </section>

    <!-- Shared Data Section -->
    @include('accelade::demo.partials._shared-data', ['prefix' => 'a'])

    <!-- Lazy Loading Section -->
    @include('accelade::demo.partials._lazy-loading', ['prefix' => 'a'])

    <!-- Vanilla JS Features -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-8 text-center">Vanilla JS Features</h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white/10 rounded-xl p-6 backdrop-blur">
                <div class="text-3xl mb-3">a-*</div>
                <h3 class="font-semibold text-lg mb-2">Directives</h3>
                <p class="text-indigo-100 text-sm">a-text, a-show, a-if, a-model, @click, a-bind</p>
            </div>

            <div class="bg-white/10 rounded-xl p-6 backdrop-blur">
                <div class="text-3xl mb-3">~6KB</div>
                <h3 class="font-semibold text-lg mb-2">Ultra Lightweight</h3>
                <p class="text-indigo-100 text-sm">Minimal footprint for maximum performance.</p>
            </div>

            <div class="bg-white/10 rounded-xl p-6 backdrop-blur">
                <div class="text-3xl mb-3">SPA</div>
                <h3 class="font-semibold text-lg mb-2">Built-in Router</h3>
                <p class="text-indigo-100 text-sm">SPA navigation with &lt;accelade:link&gt; component.</p>
            </div>

            <div class="bg-white/10 rounded-xl p-6 backdrop-blur">
                <div class="text-3xl mb-3">JS</div>
                <h3 class="font-semibold text-lg mb-2">Custom Scripts</h3>
                <p class="text-indigo-100 text-sm">Define custom functions with &lt;accelade:script&gt;.</p>
            </div>
        </div>
    </section>
</x-accelade::layouts.demo>
