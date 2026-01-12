@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="notifications">
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
                    <a href="{{ route('demo.notify', 'success') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition text-sm">Success</a>
                    <a href="{{ route('demo.notify', 'info') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm">Info</a>
                    <a href="{{ route('demo.notify', 'warning') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition text-sm">Warning</a>
                    <a href="{{ route('demo.notify', 'danger') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm">Danger</a>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('demo.notify', 'persistent') }}" class="px-4 py-2 bg-violet-600 text-white rounded-lg font-medium hover:bg-violet-700 transition text-sm">Persistent</a>
                    <a href="{{ route('demo.notify', 'actions') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition text-sm">With Actions</a>
                    <a href="{{ route('demo.notify', 'custom') }}" class="px-4 py-2 bg-pink-600 text-white rounded-lg font-medium hover:bg-pink-700 transition text-sm">Custom Icon</a>
                </div>
                <div class="mt-4">
                    <x-accelade::code-block language="php" filename="Controller.php">
// PHP Controller
Notify::success('Saved!')->body('Changes saved.');
return redirect()->back();
                    </x-accelade::code-block>
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
                    >
                        Success
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.info('Info', 'Here is some information.')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm"
                    >
                        Info
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.warning('Warning', 'Please be careful!')"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition text-sm"
                    >
                        Warning
                    </button>
                    <button
                        onclick="window.Accelade?.notify?.danger('Error', 'Something went wrong.')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm"
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
                    <button onclick="testNotifyPosition('bottom-right')" class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs hover:bg-slate-700 transition">Bottom Right</button>
                </div>
            </div>
        </div>

        <x-accelade::code-block language="javascript" filename="notifications.js">
// JavaScript API
window.Accelade.notify.success('Title', 'Message');
window.Accelade.notify.info('Title', 'Message');
window.Accelade.notify.warning('Title', 'Message');
window.Accelade.notify.danger('Title', 'Message');

// PHP (Backend)
Notify::success('Saved!')->message('Changes saved.');
        </x-accelade::code-block>

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
</x-accelade::layouts.demo-sidebar>
