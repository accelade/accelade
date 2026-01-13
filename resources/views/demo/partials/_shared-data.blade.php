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
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-violet-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Shared Data</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Share data from PHP backend to JavaScript frontend. Access via <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">window.Accelade.shared</code>.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Current Shared Data -->
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-violet-500/20 text-violet-500 rounded">Data</span>
                Current Shared Data
            </h4>
            <pre id="shared-data-display" class="text-sm font-mono p-4 rounded-lg border border-[var(--docs-border)] overflow-auto max-h-48" style="background: var(--docs-bg); color: var(--docs-text-muted);"></pre>
        </div>

        <!-- JavaScript API -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">API</span>
                JavaScript API
            </h4>
            <div class="space-y-3">
                <div class="flex flex-wrap gap-2">
                    <button onclick="showSharedValue('appName')" class="px-4 py-2 bg-indigo-500 text-white rounded-lg font-medium hover:bg-indigo-600 transition text-sm" data-testid="get-app-name">Get App Name</button>
                    <button onclick="showSharedValue('user.name')" class="px-4 py-2 bg-purple-500 text-white rounded-lg font-medium hover:bg-purple-600 transition text-sm" data-testid="get-user-name">Get User Name</button>
                    <button onclick="toggleTheme()" class="px-4 py-2 bg-teal-500 text-white rounded-lg font-medium hover:bg-teal-600 transition text-sm" data-testid="toggle-theme">Toggle Theme</button>
                </div>
                <div id="shared-value-result" class="text-sm p-3 rounded-lg border border-[var(--docs-border)] min-h-[40px]" style="background: var(--docs-bg); color: var(--docs-text-muted);"></div>
            </div>
        </div>

        <!-- Text Interpolation Demo -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Reactive State with {{ $prefix }}-* Directives</h4>
            @accelade(['greeting' => 'Hello', 'count' => 0])
                <div class="text-center">
                    <p class="text-xl mb-2" style="color: var(--docs-text);">
                        <span {{ $textAttr }}="greeting">Hello</span>, <span class="font-bold text-violet-500" {{ $textAttr }}="shared.user.name">John Doe</span>!
                    </p>
                    <p class="mb-2" style="color: var(--docs-text-muted);">
                        App: <span class="font-semibold" {{ $textAttr }}="shared.appName">Accelade Demo</span> |
                        Theme: <span class="font-mono px-2 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);" {{ $textAttr }}="shared.settings.theme">dark</span>
                    </p>
                    <p class="mb-4" style="color: var(--docs-text-muted);">
                        Clicked: <span class="font-bold text-purple-500" {{ $textAttr }}="count">0</span> times
                    </p>
                    <div class="flex gap-2 justify-center">
                        <button class="px-4 py-2 bg-violet-500 text-white rounded-lg text-sm font-medium hover:bg-violet-600 transition" {{ $clickAttr }}="$set('count', count + 1)" data-testid="shared-increment">Increment</button>
                        <button class="px-4 py-2 bg-purple-500 text-white rounded-lg text-sm font-medium hover:bg-purple-600 transition" {{ $clickAttr }}="$set('greeting', greeting === 'Hello' ? 'Welcome' : 'Hello')">Toggle Greeting</button>
                    </div>
                </div>
            @endaccelade
        </div>
    </div>

    <x-accelade::code-block language="php" filename="shared-data.php">
// PHP
Accelade::share('user', ['name' => 'John']);
Accelade::share('settings', ['theme' => 'dark']);

// JavaScript
window.Accelade.shared.get('user.name');
window.Accelade.shared.set('settings.theme', 'light');

// Blade Template ({{ $prefix }}-* syntax)
&lt;span {{ $textAttr }}="shared.user.name"&gt;&lt;/span&gt;
    </x-accelade::code-block>
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
