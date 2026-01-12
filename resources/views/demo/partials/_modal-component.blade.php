{{-- Modal Component Section - Modals and Slideovers --}}
@props(['prefix' => 'a'])

<!-- Demo: Modal Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Modal Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Modal and slideover dialogs with async content loading using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::modal&gt;</code> and <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::link :modal="true"&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Modal -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Basic</span>
                Pre-loaded Modal
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Click to open a modal with pre-loaded content using a hash link.
            </p>

            <div class="space-x-2">
                <x-accelade::link
                    href="#basic-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Open Modal
                </x-accelade::link>
            </div>

            <!-- Pre-loaded Modal (hidden until triggered) -->
            <x-accelade::modal name="basic-modal">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Welcome!</h2>
                <p class="text-slate-600 mb-4">
                    This is a pre-loaded modal. The content was already on the page and is displayed without any network request.
                </p>
                <p class="text-slate-600 mb-6">
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

        <!-- Async Modal -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">Async</span>
                Async Content Loading
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Load modal content from a URL asynchronously. Great for forms and dynamic content.
            </p>

            <div class="space-x-2">
                <x-accelade::link
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
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
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Slideover -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Slideover</span>
                Side Panel
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Slideovers appear from the side. Great for detail panels, settings, and navigation.
            </p>

            <div class="space-x-2">
                <x-accelade::link
                    href="#slideover-demo"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Open Slideover (Right)
                </x-accelade::link>

                <x-accelade::link
                    href="#slideover-left"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Open Slideover (Left)
                </x-accelade::link>
            </div>

            <!-- Right Slideover -->
            <x-accelade::modal name="slideover-demo" :slideover="true">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Slideover Panel</h2>
                <p class="text-slate-600 mb-4">
                    This is a slideover panel. It slides in from the right side of the screen.
                </p>
                <p class="text-slate-600 mb-4">
                    Slideovers are great for:
                </p>
                <ul class="list-disc list-inside text-slate-600 mb-6 space-y-2">
                    <li>Detail views</li>
                    <li>Settings panels</li>
                    <li>Filters and search options</li>
                    <li>Navigation menus</li>
                </ul>
                <button
                    data-modal-close
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                >
                    Close Panel
                </button>
            </x-accelade::modal>

            <!-- Left Slideover -->
            <x-accelade::modal name="slideover-left" :slideover="true" slideover-position="left">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Left Slideover</h2>
                <p class="text-slate-600 mb-4">
                    This slideover appears from the left side of the screen.
                </p>
                <nav class="space-y-2 mb-6">
                    <a href="#" class="block px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Dashboard</a>
                    <a href="#" class="block px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Users</a>
                    <a href="#" class="block px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Settings</a>
                    <a href="#" class="block px-4 py-2 text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition">Help</a>
                </nav>
                <button
                    data-modal-close
                    class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition w-full"
                >
                    Close
                </button>
            </x-accelade::modal>
        </div>

        <!-- Modal Sizes -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">Sizes</span>
                Max Width Options
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Control modal width with the <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">max-width</code> prop.
            </p>

            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="#modal-sm"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition text-sm"
                >
                    sm
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-md"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition text-sm"
                >
                    md
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-lg"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition text-sm"
                >
                    lg
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-xl"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition text-sm"
                >
                    xl
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-2xl"
                    class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition text-sm"
                >
                    2xl (default)
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-4xl"
                    class="px-3 py-1.5 bg-slate-200 text-slate-700 rounded hover:bg-slate-300 transition text-sm"
                >
                    4xl
                </x-accelade::link>
            </div>

            <x-accelade::modal name="modal-sm" max-width="sm">
                <h3 class="text-lg font-semibold mb-2">Small Modal (sm)</h3>
                <p class="text-slate-600 mb-4">Max width: 24rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-md" max-width="md">
                <h3 class="text-lg font-semibold mb-2">Medium Modal (md)</h3>
                <p class="text-slate-600 mb-4">Max width: 28rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-lg" max-width="lg">
                <h3 class="text-lg font-semibold mb-2">Large Modal (lg)</h3>
                <p class="text-slate-600 mb-4">Max width: 32rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-xl" max-width="xl">
                <h3 class="text-lg font-semibold mb-2">Extra Large Modal (xl)</h3>
                <p class="text-slate-600 mb-4">Max width: 36rem</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-2xl" max-width="2xl">
                <h3 class="text-lg font-semibold mb-2">2XL Modal (2xl) - Default</h3>
                <p class="text-slate-600 mb-4">Max width: 42rem. This is the default size for modals.</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-4xl" max-width="4xl">
                <h3 class="text-lg font-semibold mb-2">4XL Modal (4xl)</h3>
                <p class="text-slate-600 mb-4">Max width: 56rem. Great for tables and wide content.</p>
                <button data-modal-close class="px-3 py-1.5 bg-slate-500 text-white rounded hover:bg-slate-600 transition">Close</button>
            </x-accelade::modal>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Bottom Sheet -->
        <div class="bg-gradient-to-r from-cyan-50 to-teal-50 rounded-xl p-6 border border-cyan-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-cyan-100 text-cyan-700 rounded">Bottom Sheet</span>
                Mobile-Friendly Panel
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Bottom sheets slide up from the bottom of the screen. Perfect for mobile interfaces, action sheets, and quick selections.
            </p>

            <div class="space-x-2">
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
                    href="{{ route('demo.section', ['framework' => $framework ?? 'vanilla', 'section' => 'counter']) }}"
                    :bottom-sheet="true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Async Bottom Sheet
                </x-accelade::link>
            </div>

            <!-- Pre-loaded Bottom Sheet -->
            <x-accelade::modal name="bottom-sheet-demo" :bottom-sheet="true">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Action Sheet</h2>
                <p class="text-slate-600 mb-4">
                    Bottom sheets are great for mobile-friendly interfaces. They slide up from the bottom and include a drag handle.
                </p>
                <div class="space-y-2 mb-6">
                    <button class="w-full px-4 py-3 text-left bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Choose from Gallery
                    </button>
                    <button class="w-full px-4 py-3 text-left bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Take a Photo
                    </button>
                    <button class="w-full px-4 py-3 text-left bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Browse Files
                    </button>
                </div>
                <button
                    data-modal-close
                    class="w-full px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition"
                >
                    Cancel
                </button>
            </x-accelade::modal>
        </div>

        <!-- Modal Positions -->
        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded">Position</span>
                Vertical Alignment
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Position modals at the top, center, or bottom of the screen.
            </p>

            <div class="flex flex-wrap gap-2">
                <x-accelade::link
                    href="#modal-top"
                    class="px-3 py-1.5 bg-orange-200 text-orange-700 rounded hover:bg-orange-300 transition text-sm"
                >
                    Top
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-center"
                    class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition text-sm"
                >
                    Center (default)
                </x-accelade::link>
                <x-accelade::link
                    href="#modal-bottom"
                    class="px-3 py-1.5 bg-orange-200 text-orange-700 rounded hover:bg-orange-300 transition text-sm"
                >
                    Bottom
                </x-accelade::link>
            </div>

            <x-accelade::modal name="modal-top" position="top">
                <h3 class="text-lg font-semibold mb-2">Top Positioned Modal</h3>
                <p class="text-slate-600 mb-4">This modal appears at the top of the viewport.</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-center" position="center">
                <h3 class="text-lg font-semibold mb-2">Center Positioned Modal</h3>
                <p class="text-slate-600 mb-4">This modal appears in the center of the viewport (default).</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-bottom" position="bottom">
                <h3 class="text-lg font-semibold mb-2">Bottom Positioned Modal</h3>
                <p class="text-slate-600 mb-4">This modal appears at the bottom of the viewport.</p>
                <button data-modal-close class="px-3 py-1.5 bg-orange-500 text-white rounded hover:bg-orange-600 transition">Close</button>
            </x-accelade::modal>
        </div>

        <!-- Close Options -->
        <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-xl p-6 border border-red-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Options</span>
                Close Behavior
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Control how the modal can be closed with <code class="bg-slate-100 px-1 py-0.5 rounded text-xs">close-explicitly</code>.
            </p>

            <div class="space-x-2">
                <x-accelade::link
                    href="#modal-explicit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                >
                    Explicit Close Only
                </x-accelade::link>

                <x-accelade::link
                    href="#modal-no-button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition"
                >
                    No Close Button
                </x-accelade::link>
            </div>

            <x-accelade::modal name="modal-explicit" :close-explicitly="true">
                <h3 class="text-lg font-semibold mb-2">Explicit Close Required</h3>
                <p class="text-slate-600 mb-4">
                    This modal can only be closed by clicking the button below. ESC key and clicking outside won't work.
                </p>
                <button data-modal-close class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    Close Modal
                </button>
            </x-accelade::modal>

            <x-accelade::modal name="modal-no-button" :close-button="false">
                <h3 class="text-lg font-semibold mb-2">No Close Button</h3>
                <p class="text-slate-600 mb-4">
                    This modal has no X button, but you can still close it by clicking outside or pressing ESC.
                </p>
                <button data-modal-close class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                    Close
                </button>
            </x-accelade::modal>
        </div>
    </div>

    <!-- All Props -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Component Props</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Prop</th>
                        <th class="text-left py-2 px-3 text-slate-600">Type</th>
                        <th class="text-left py-2 px-3 text-slate-600">Default</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">name</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Modal name for hash-based opening</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">slideover</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Render as slideover instead of modal</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">bottom-sheet</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Render as bottom sheet (mobile-friendly)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">max-width</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">2xl / md</td>
                        <td class="py-2 px-3">Max width (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">position</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">center</td>
                        <td class="py-2 px-3">Vertical position (top, center, bottom)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">slideover-position</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">right</td>
                        <td class="py-2 px-3">Slideover position (left, right)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">close-explicitly</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Disable ESC and outside click closing</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-purple-600">close-button</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">true</td>
                        <td class="py-2 px-3">Show the X close button</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-purple-600">opened</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Open immediately on page load</td>
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
&lt;x-accelade::link
    href="/users/create"
    :modal="true"
&gt;
    Create User
&lt;/x-accelade::link&gt;

{{-- Slideover --}}
&lt;x-accelade::link
    href="/settings"
    :slideover="true"
&gt;
    Settings
&lt;/x-accelade::link&gt;

{{-- Pre-loaded slideover --}}
&lt;x-accelade::modal name="settings" :slideover="true" slideover-position="left"&gt;
    &lt;nav&gt;...&lt;/nav&gt;
&lt;/x-accelade::modal&gt;

{{-- Custom size and position --}}
&lt;x-accelade::modal
    name="large-modal"
    max-width="4xl"
    position="top"
&gt;
    Large content here...
&lt;/x-accelade::modal&gt;

{{-- Explicit close required --}}
&lt;x-accelade::modal
    name="important"
    :close-explicitly="true"
&gt;
    &lt;p&gt;You must click the button to close.&lt;/p&gt;
    &lt;button data-modal-close&gt;I understand&lt;/button&gt;
&lt;/x-accelade::modal&gt;

{{-- Bottom sheet (mobile-friendly) --}}
&lt;x-accelade::link
    href="/actions"
    :bottom-sheet="true"
&gt;
    Show Actions
&lt;/x-accelade::link&gt;

{{-- Pre-loaded bottom sheet --}}
&lt;x-accelade::modal name="actions" :bottom-sheet="true"&gt;
    &lt;h2&gt;Choose an Action&lt;/h2&gt;
    &lt;button&gt;Option 1&lt;/button&gt;
    &lt;button&gt;Option 2&lt;/button&gt;
    &lt;button data-modal-close&gt;Cancel&lt;/button&gt;
&lt;/x-accelade::modal&gt;
    </x-accelade::code-block>
</section>
