<x-accelade::layouts.demo :framework="'vanilla'">
    <!-- Demo: Shared Data Overview -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Shared Data</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">
            Share data from PHP backend to JavaScript frontend. Data is available globally via <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">window.Accelade.shared</code>.
        </p>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Current Shared Data -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Current Shared Data (from PHP)</h3>
                <pre id="shared-data-display" class="text-sm font-mono text-slate-700 bg-white p-4 rounded-lg border overflow-auto max-h-64"></pre>
            </div>

            <!-- JavaScript API -->
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">JavaScript API</h3>
                <div class="space-y-4">
                    <div>
                        <button
                            onclick="showSharedValue('appName')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition text-sm"
                            data-testid="get-app-name"
                        >
                            Get App Name
                        </button>
                    </div>
                    <div>
                        <button
                            onclick="showSharedValue('user.name')"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm"
                            data-testid="get-user-name"
                        >
                            Get User Name (dot notation)
                        </button>
                    </div>
                    <div>
                        <button
                            onclick="showSharedValue('settings.theme')"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition text-sm"
                            data-testid="get-theme"
                        >
                            Get Theme Setting
                        </button>
                    </div>
                    <div id="shared-value-result" class="text-sm text-slate-600 p-3 bg-white rounded-lg border min-h-[50px]"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Demo: Using Mustache Interpolation --}}
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Text Interpolation with @{{ }}</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Use <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">@{{ expression }}</code> in Blade to display JavaScript variables and shared data.</p>

        @accelade(['greeting' => 'Hello', 'count' => 0, 'name' => 'World'])
            <div class="text-center p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl mb-6">
                <p class="text-2xl text-slate-800 mb-4">
                    @{{ greeting }}, <span class="font-bold text-indigo-600">@{{ name }}</span>!
                </p>
                <p class="text-slate-600 mb-4">
                    You clicked <span class="font-bold text-purple-600">@{{ count }}</span> times.
                </p>
                <p class="text-sm text-slate-500 mb-4">
                    App: <span class="font-semibold">@{{ shared.appName }}</span> |
                    User: <span class="font-semibold">@{{ shared.user.name }}</span> |
                    Theme: <span class="font-mono bg-slate-200 px-2 py-1 rounded">@{{ shared.settings.theme }}</span>
                </p>

                <div class="flex gap-2 justify-center">
                    <button
                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
                        @click="$set('count', count + 1)"
                        data-testid="increment-btn"
                    >
                        Click Me
                    </button>
                    <button
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition shadow-lg shadow-purple-200"
                        @click="$set('greeting', greeting === 'Hello' ? 'Welcome' : 'Hello')"
                    >
                        Toggle Greeting
                    </button>
                    <button
                        class="px-6 py-3 bg-teal-600 text-white rounded-xl font-medium hover:bg-teal-700 transition shadow-lg shadow-teal-200"
                        @click="$set('name', name === 'World' ? 'Accelade' : 'World')"
                    >
                        Toggle Name
                    </button>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                <p class="text-amber-800 text-sm">
                    <strong>Note:</strong> Use <code>@{{ }}</code> in Blade templates (the @ escapes Blade's own syntax).
                    This outputs <code>{{ }}</code> in HTML, which Accelade then evaluates.
                </p>
            </div>
        @endaccelade

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">{{-- Blade Template --}}
@@accelade(['greeting' => 'Hello', 'count' => 0])
    &lt;p&gt;@@{{ greeting }}, @@{{ name }}!&lt;/p&gt;
    &lt;p&gt;Count: @@{{ count }}&lt;/p&gt;

    {{-- Access shared data --}}
    &lt;p&gt;User: @@{{ shared.user.name }}&lt;/p&gt;
    &lt;p&gt;Theme: @@{{ shared.settings.theme }}&lt;/p&gt;

    &lt;button @click="$set('count', count + 1)"&gt;Click&lt;/button&gt;
@@endaccelade</pre>
        </div>
    </section>

    <!-- Demo: Using Shared Data with JavaScript API -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Using Shared Data with JavaScript API</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Access shared data directly in your reactive components via JavaScript.</p>

        @accelade(['greeting' => 'Hello'])
            <div class="text-center p-6 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl mb-6">
                <p class="text-2xl text-slate-800 mb-4">
                    <span a-text="greeting">Hello</span>,
                    <span id="user-name-display" class="font-bold text-indigo-600">Loading...</span>!
                </p>
                <p class="text-slate-600 mb-4">
                    Welcome to <span id="app-name-display" class="font-semibold">Loading...</span>
                </p>
                <p class="text-sm text-slate-500">
                    Your theme is: <span id="theme-display" class="font-mono bg-slate-200 px-2 py-1 rounded">Loading...</span>
                </p>
            </div>

            <div class="flex gap-2 justify-center">
                <button
                    class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
                    @click="$set('greeting', 'Welcome back')"
                >
                    Change Greeting
                </button>
                <button
                    onclick="updateTheme()"
                    class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition shadow-lg shadow-purple-200"
                    data-testid="toggle-theme"
                >
                    Toggle Theme
                </button>
            </div>
        @endaccelade

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mt-6">
            <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">// PHP (in controller or middleware)
Accelade::share('user', auth()->user()->only('id', 'name'));
Accelade::share('settings', ['theme' => 'dark']);

// JavaScript (in frontend)
const userName = window.Accelade.shared.get('user.name');
const theme = window.Accelade.shared.get('settings.theme');</pre>
        </div>
    </section>

    <!-- Demo: Reactive Subscriptions -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-teal-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Reactive Subscriptions</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Subscribe to changes in shared data.</p>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Modify Shared Data</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Update App Name:</label>
                        <input
                            type="text"
                            id="new-app-name"
                            placeholder="Enter new app name"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm"
                            data-testid="new-app-name-input"
                        />
                    </div>
                    <button
                        onclick="updateAppName()"
                        class="px-4 py-2 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition text-sm"
                        data-testid="update-app-name"
                    >
                        Update App Name
                    </button>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Change Log</h3>
                <div id="change-log" class="text-sm font-mono text-slate-700 bg-white p-4 rounded-lg border h-40 overflow-auto"></div>
            </div>
        </div>

        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mt-6">
            <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">// Subscribe to specific key changes
const unsubscribe = window.Accelade.shared.subscribe('appName', (key, newVal, oldVal) => {
    console.log(`${key} changed from ${oldVal} to ${newVal}`);
});

// Subscribe to all changes
window.Accelade.shared.subscribeAll((key, newVal, oldVal) => {
    console.log(`Shared data changed: ${key}`);
});</pre>
        </div>
    </section>

    <!-- Demo: PHP Backend Examples -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">PHP Backend Usage</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Examples of sharing data from your Laravel application.</p>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">Basic Usage</h3>
                <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap bg-white p-4 rounded-lg border">// Single value
Accelade::share('appName', 'My App');

// Multiple values
Accelade::share([
    'user' => $user,
    'settings' => $settings,
]);

// Lazy loading (closure)
Accelade::share('stats', fn() => [
    'users' => User::count(),
    'orders' => Order::count(),
]);</pre>
            </div>

            <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                <h3 class="font-medium text-slate-700 mb-4">In Middleware</h3>
                <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap bg-white p-4 rounded-lg border">// app/Http/Middleware/ShareData.php
public function handle($request, $next)
{
    Accelade::share([
        'auth' => [
            'user' => $request->user(),
            'guest' => !$request->user(),
        ],
        'flash' => [
            'success' => session('success'),
            'error' => session('error'),
        ],
    ]);

    return $next($request);
}</pre>
            </div>
        </div>
    </section>

    <!-- Navigation -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Navigate to Other Demos</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Shared data persists across SPA navigation.</p>

        <div class="flex gap-4 justify-center flex-wrap">
            <x-accelade::link
                href="{{ route('demo.vanilla') }}"
                class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition"
            >
                Vanilla Demo
            </x-accelade::link>
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
        </div>
    </section>

    <script>
        // Initialize displays when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateDisplays();
            setupSubscriptions();
        });

        // Also run immediately in case DOM is already ready
        if (document.readyState !== 'loading') {
            setTimeout(() => {
                updateDisplays();
                setupSubscriptions();
            }, 100);
        }

        function updateDisplays() {
            // Display all shared data
            const allData = window.Accelade?.shared?.all() || {};
            document.getElementById('shared-data-display').textContent = JSON.stringify(allData, null, 2);

            // Display individual values
            document.getElementById('user-name-display').textContent = window.Accelade?.shared?.get('user.name') || 'Unknown';
            document.getElementById('app-name-display').textContent = window.Accelade?.shared?.get('appName') || 'Unknown';
            document.getElementById('theme-display').textContent = window.Accelade?.shared?.get('settings.theme') || 'light';
        }

        function showSharedValue(key) {
            const value = window.Accelade?.shared?.get(key);
            const result = document.getElementById('shared-value-result');
            result.innerHTML = `<strong>${key}:</strong> ${JSON.stringify(value)}`;
        }

        function updateTheme() {
            const currentTheme = window.Accelade?.shared?.get('settings.theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            window.Accelade?.shared?.set('settings.theme', newTheme);
            updateDisplays();
        }

        function updateAppName() {
            const input = document.getElementById('new-app-name');
            if (input.value) {
                window.Accelade?.shared?.set('appName', input.value);
                input.value = '';
                updateDisplays();
            }
        }

        function setupSubscriptions() {
            const log = document.getElementById('change-log');

            // Subscribe to all changes
            window.Accelade?.shared?.subscribeAll((key, newValue, oldValue) => {
                const entry = document.createElement('div');
                entry.className = 'mb-2 pb-2 border-b border-slate-100';
                entry.innerHTML = `<span class="text-indigo-600">${key}</span>: ${JSON.stringify(oldValue)} → ${JSON.stringify(newValue)}`;
                log.insertBefore(entry, log.firstChild);
            });
        }
    </script>
</x-accelade::layouts.demo>
