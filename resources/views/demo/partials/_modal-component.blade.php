{{-- Modal Component Section - Modals and Slideovers --}}
@props(['prefix' => 'a'])

<!-- Demo: Modal Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Modal Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Modal and slideover dialogs with async content loading using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::modal&gt;</code> and <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::link :modal="true"&gt;</code>.
    </p>

    <!-- Basic Modal & Async Modal -->
    <div class="space-y-4 mb-4">
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Basic</span>
                Pre-loaded Modal
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Click to open a modal with pre-loaded content using a hash link.
            </p>
            <x-accelade::link
                href="#basic-modal"
                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                </svg>
                Open Modal
            </x-accelade::link>

            <!-- Pre-loaded Modal (hidden until triggered) -->
            <x-accelade::modal name="basic-modal">
                <h2 class="text-xl font-semibold mb-4" style="color: var(--docs-text);">Welcome!</h2>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    This is a pre-loaded modal. The content was already on the page and is displayed without any network request.
                </p>
                <p class="mb-6" style="color: var(--docs-text-muted);">
                    You can close this modal by clicking the X button, clicking outside, or pressing Escape.
                </p>
                <button
                    data-modal-close
                    class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition"
                >
                    Got it!
                </button>
            </x-accelade::modal>
        </div>

        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Async</span>
                Async Content Loading
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Load modal content from a URL asynchronously. Great for forms and dynamic content.
            </p>
            <x-accelade::link
                href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                :modal="true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Load Counter Demo
            </x-accelade::link>
        </div>
    </div>

    <!-- Slideover & Sizes -->
    <div class="space-y-4 mb-4">
        <div class="rounded-xl p-4 border border-green-500/30" style="background: rgba(34, 197, 94, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-green-500/20 text-green-500 rounded">Slideover</span>
                Side Panel
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Slideovers appear from the side. Great for detail panels, settings, and navigation.
            </p>
            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="#slideover-demo"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Right
                </x-accelade::link>
                <x-accelade::link
                    href="#slideover-left"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Left
                </x-accelade::link>
            </div>

            <!-- Right Slideover -->
            <x-accelade::modal name="slideover-demo" :slideover="true">
                <h2 class="text-xl font-semibold mb-4" style="color: var(--docs-text);">Slideover Panel</h2>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    This is a slideover panel. It slides in from the right side of the screen.
                </p>
                <p class="mb-4" style="color: var(--docs-text-muted);">Slideovers are great for:</p>
                <ul class="list-disc list-inside mb-6 space-y-2" style="color: var(--docs-text-muted);">
                    <li>Detail views</li>
                    <li>Settings panels</li>
                    <li>Filters and search options</li>
                    <li>Navigation menus</li>
                </ul>
                <button data-modal-close class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    Close Panel
                </button>
            </x-accelade::modal>

            <!-- Left Slideover -->
            <x-accelade::modal name="slideover-left" :slideover="true" slideover-position="left">
                <h2 class="text-xl font-semibold mb-4" style="color: var(--docs-text);">Left Slideover</h2>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    This slideover appears from the left side of the screen.
                </p>
                <nav class="space-y-2 mb-6">
                    <a href="#" class="block px-4 py-2 rounded-lg transition border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">Dashboard</a>
                    <a href="#" class="block px-4 py-2 rounded-lg transition border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">Users</a>
                    <a href="#" class="block px-4 py-2 rounded-lg transition border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">Settings</a>
                    <a href="#" class="block px-4 py-2 rounded-lg transition border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">Help</a>
                </nav>
                <button data-modal-close class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition w-full">
                    Close
                </button>
            </x-accelade::modal>
        </div>

        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-gray-500/20 rounded" style="color: var(--docs-text-muted);">Sizes</span>
                Max Width Options
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Control modal width with the <code class="px-1 py-0.5 rounded text-xs border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">max-width</code> prop.
            </p>
            <div class="flex flex-wrap gap-2">
                <x-accelade::link href="#modal-sm" class="px-3 py-1.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">sm</x-accelade::link>
                <x-accelade::link href="#modal-md" class="px-3 py-1.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">md</x-accelade::link>
                <x-accelade::link href="#modal-lg" class="px-3 py-1.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">lg</x-accelade::link>
                <x-accelade::link href="#modal-xl" class="px-3 py-1.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">xl</x-accelade::link>
                <x-accelade::link href="#modal-2xl" class="px-3 py-1.5 bg-indigo-500 text-white rounded text-sm">2xl (default)</x-accelade::link>
                <x-accelade::link href="#modal-4xl" class="px-3 py-1.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">4xl</x-accelade::link>
            </div>

            <x-accelade::modal name="modal-sm" max-width="sm">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Small Modal (sm)</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 24rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-md" max-width="md">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Medium Modal (md)</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 28rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-lg" max-width="lg">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Large Modal (lg)</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 32rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-xl" max-width="xl">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Extra Large Modal (xl)</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 36rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-2xl" max-width="2xl">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">2XL Modal (2xl) - Default</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 42rem. This is the default size for modals.</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-4xl" max-width="4xl">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">4XL Modal (4xl)</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">Max width: 56rem. Great for tables and wide content.</p>
                <button data-modal-close class="px-3 py-1.5 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition">Close</button>
            </x-accelade::modal>
        </div>
    </div>

    <!-- Bottom Sheet & Positions & Close Options -->
    <div class="space-y-4 mb-4">
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Bottom Sheet</span>
                Mobile-Friendly Panel
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Bottom sheets slide up from the bottom. Perfect for mobile interfaces and action sheets.
            </p>
            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="#bottom-sheet-demo"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                    Open Bottom Sheet
                </x-accelade::link>
                <x-accelade::link
                    href="{{ route('docs.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    :bottom-sheet="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition"
                >
                    Async Bottom Sheet
                </x-accelade::link>
            </div>

            <!-- Pre-loaded Bottom Sheet -->
            <x-accelade::modal name="bottom-sheet-demo" :bottom-sheet="true">
                <h2 class="text-xl font-semibold mb-4" style="color: var(--docs-text);">Action Sheet</h2>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    Bottom sheets are great for mobile-friendly interfaces.
                </p>
                <div class="space-y-2 mb-6">
                    <button class="w-full px-4 py-3 text-left rounded-lg transition flex items-center gap-3 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">
                        <svg class="w-5 h-5" style="color: var(--docs-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Choose from Gallery
                    </button>
                    <button class="w-full px-4 py-3 text-left rounded-lg transition flex items-center gap-3 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">
                        <svg class="w-5 h-5" style="color: var(--docs-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Take a Photo
                    </button>
                    <button class="w-full px-4 py-3 text-left rounded-lg transition flex items-center gap-3 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt); color: var(--docs-text);">
                        <svg class="w-5 h-5" style="color: var(--docs-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Browse Files
                    </button>
                </div>
                <button data-modal-close class="w-full px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition">
                    Cancel
                </button>
            </x-accelade::modal>
        </div>

        <div class="rounded-xl p-4 border border-orange-500/30" style="background: rgba(249, 115, 22, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-orange-500/20 text-orange-500 rounded">Position</span>
                Vertical Alignment
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Position modals at the top, center, or bottom of the screen.
            </p>
            <div class="flex flex-wrap gap-2">
                <x-accelade::link href="#modal-top" class="px-3 py-1.5 bg-orange-500/20 text-orange-500 rounded hover:bg-orange-500/30 transition text-sm">Top</x-accelade::link>
                <x-accelade::link href="#modal-center" class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition text-sm">Center (default)</x-accelade::link>
                <x-accelade::link href="#modal-bottom" class="px-3 py-1.5 bg-orange-500/20 text-orange-500 rounded hover:bg-orange-500/30 transition text-sm">Bottom</x-accelade::link>
            </div>

            <x-accelade::modal name="modal-top" position="top">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Top Positioned Modal</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">This modal appears at the top of the viewport.</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-center" position="center">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Center Positioned Modal</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">This modal appears in the center of the viewport (default).</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-bottom" position="bottom">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Bottom Positioned Modal</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">This modal appears at the bottom of the viewport.</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>
        </div>

        <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">Options</span>
                Close Behavior
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Control how the modal can be closed with <code class="px-1 py-0.5 rounded text-xs border border-[var(--docs-border)]" style="background: var(--docs-bg);">close-explicitly</code>.
            </p>
            <div class="flex flex-wrap gap-2">
                <x-accelade::link href="#modal-explicit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    Explicit Close Only
                </x-accelade::link>
                <x-accelade::link href="#modal-no-button" class="inline-flex items-center gap-2 px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                    No Close Button
                </x-accelade::link>
            </div>

            <x-accelade::modal name="modal-explicit" :close-explicitly="true">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Explicit Close Required</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    This modal can only be closed by clicking the button below. ESC key and clicking outside won't work.
                </p>
                <button data-modal-close class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    Close Modal
                </button>
            </x-accelade::modal>
            <x-accelade::modal name="modal-no-button" :close-button="false">
                <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">No Close Button</h3>
                <p class="mb-4" style="color: var(--docs-text-muted);">
                    This modal has no X button, but you can still close it by clicking outside or pressing ESC.
                </p>
                <button data-modal-close class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                    Close
                </button>
            </x-accelade::modal>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">name</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Modal name for hash-based opening</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">slideover</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Render as slideover instead of modal</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">bottom-sheet</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Render as bottom sheet (mobile-friendly)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">max-width</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">2xl</td>
                        <td class="py-2 px-3">Max width (sm, md, lg, xl, 2xl, 4xl, etc.)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">position</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">center</td>
                        <td class="py-2 px-3">Vertical position (top, center, bottom)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">close-explicitly</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Disable ESC and outside click closing</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-purple-500">close-button</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">true</td>
                        <td class="py-2 px-3">Show the X close button</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="modal-examples.blade.php">
{{-- Pre-loaded modal with hash link --}}
&lt;x-accelade::link href="#my-modal"&gt;Open Modal&lt;/x-accelade::link&gt;

&lt;x-accelade::modal name="my-modal"&gt;
    &lt;h2&gt;Modal Title&lt;/h2&gt;
    &lt;p&gt;Modal content here...&lt;/p&gt;
    &lt;button data-modal-close&gt;Close&lt;/button&gt;
&lt;/x-accelade::modal&gt;

{{-- Async modal (loads from URL) --}}
&lt;x-accelade::link href="/users/create" :modal="true"&gt;
    Create User
&lt;/x-accelade::link&gt;

{{-- Slideover --}}
&lt;x-accelade::modal name="settings" :slideover="true"&gt;
    &lt;nav&gt;...&lt;/nav&gt;
&lt;/x-accelade::modal&gt;

{{-- Bottom sheet --}}
&lt;x-accelade::modal name="actions" :bottom-sheet="true"&gt;
    &lt;button&gt;Option 1&lt;/button&gt;
    &lt;button data-modal-close&gt;Cancel&lt;/button&gt;
&lt;/x-accelade::modal&gt;
    </x-accelade::code-block>
</section>
