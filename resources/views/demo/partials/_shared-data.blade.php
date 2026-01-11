{{-- Shared Data Section - Framework Specific --}}
@php
    $prefix = $prefix ?? 'a';
    $textAttr = $prefix . '-text';
    $showAttr = $prefix . '-show';

    // Determine click attribute based on framework
    $clickAttr = match($prefix) {
        'v' => 'v-on:click',
        'data-state' => 'data-on-click',
        's' => 's-on-click',
        'ng' => 'ng-on-click',
        default => '@click',
    };
@endphp

<!-- Demo: Shared Data -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-violet-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Shared Data</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Share data from PHP backend to JavaScript frontend. Access via <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">window.Accelade.shared</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Current Shared Data -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4">Current Shared Data</h3>
            <pre id="shared-data-display" class="text-sm font-mono text-slate-700 bg-white p-4 rounded-lg border overflow-auto max-h-48"></pre>
        </div>

        <!-- JavaScript API -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4">JavaScript API</h3>
            <div class="space-y-3">
                <button onclick="showSharedValue('appName')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition text-sm" data-testid="get-app-name">Get App Name</button>
                <button onclick="showSharedValue('user.name')" class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm" data-testid="get-user-name">Get User Name</button>
                <button onclick="toggleTheme()" class="px-4 py-2 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition text-sm" data-testid="toggle-theme">Toggle Theme</button>
                <div id="shared-value-result" class="text-sm text-slate-600 p-3 bg-white rounded-lg border min-h-[40px]"></div>
            </div>
        </div>
    </div>

    <!-- Text Interpolation Demo -->
    <div class="bg-gradient-to-r from-violet-50 to-purple-50 rounded-xl p-6 border border-violet-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Reactive State with {{ $prefix }}-* Directives</h3>
        @accelade(['greeting' => 'Hello', 'count' => 0])
            <div class="text-center">
                <p class="text-xl text-slate-800 mb-2">
                    <span {{ $textAttr }}="greeting">Hello</span>, <span class="font-bold text-violet-600" {{ $textAttr }}="shared.user.name">John Doe</span>!
                </p>
                <p class="text-slate-600 mb-2">
                    App: <span class="font-semibold" {{ $textAttr }}="shared.appName">Accelade Demo</span> |
                    Theme: <span class="font-mono bg-slate-200 px-2 py-0.5 rounded text-sm" {{ $textAttr }}="shared.settings.theme">dark</span>
                </p>
                <p class="text-slate-600 mb-4">
                    Clicked: <span class="font-bold text-purple-600" {{ $textAttr }}="count">0</span> times
                </p>
                <div class="flex gap-2 justify-center">
                    <button class="px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition" {{ $clickAttr }}="$set('count', count + 1)" data-testid="shared-increment">Increment</button>
                    <button class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition" {{ $clickAttr }}="$set('greeting', greeting === 'Hello' ? 'Welcome' : 'Hello')">Toggle Greeting</button>
                </div>
            </div>
        @endaccelade
    </div>

    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
        <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">// PHP
Accelade::share('user', ['name' => 'John']);
Accelade::share('settings', ['theme' => 'dark']);

// JavaScript
window.Accelade.shared.get('user.name');
window.Accelade.shared.set('settings.theme', 'light');

// Blade Template ({{ $prefix }}-* syntax)
&lt;span {{ $textAttr }}="shared.user.name"&gt;&lt;/span&gt;</pre>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateSharedDisplay();
    });
    if (document.readyState !== 'loading') {
        setTimeout(updateSharedDisplay, 100);
    }

    function updateSharedDisplay() {
        const display = document.getElementById('shared-data-display');
        if (display) {
            display.textContent = JSON.stringify(window.Accelade?.shared?.all() || {}, null, 2);
        }
    }

    function showSharedValue(key) {
        const value = window.Accelade?.shared?.get(key);
        document.getElementById('shared-value-result').innerHTML = `<strong>${key}:</strong> ${JSON.stringify(value)}`;
    }

    function toggleTheme() {
        const current = window.Accelade?.shared?.get('settings.theme') || 'light';
        const newTheme = current === 'dark' ? 'light' : 'dark';
        window.Accelade?.shared?.set('settings.theme', newTheme);
        updateSharedDisplay();
        document.getElementById('shared-value-result').innerHTML = `<strong>Theme changed to:</strong> ${newTheme}`;
    }
</script>
