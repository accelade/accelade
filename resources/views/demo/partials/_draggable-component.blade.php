{{-- Draggable Component Section - Drag and Drop Functionality --}}
@props(['prefix' => 'a'])

@php
    $textAttr = match($prefix) {
        'v' => 'v-text',
        'data-state' => 'data-state-text',
        's' => 's-text',
        'ng' => 'ng-text',
        default => 'a-text',
    };

    $showAttr = match($prefix) {
        'v' => 'v-show',
        'data-state' => 'data-state-show',
        's' => 's-show',
        'ng' => 'ng-show',
        default => 'a-show',
    };

    $classAttr = match($prefix) {
        'v' => ':class',
        'data-state' => 'data-state-class',
        's' => 's-class',
        'ng' => 'ng-class',
        default => 'a-class',
    };
@endphp

<!-- Demo: Draggable Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Draggable Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Enable drag and drop with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::draggable&gt;</code>.
        Supports sortable lists, multi-container drag, and dropzones.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Sortable List -->
        <div class="rounded-xl p-4 border border-orange-500/30" style="background: rgba(249, 115, 22, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-orange-500/20 text-orange-500 rounded">Basic</span>
                Sortable List
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Drag items to reorder. Add <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">data-draggable-item</code> to child elements.
            </p>

            <x-accelade::draggable class="space-y-2">
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center gap-3 hover:border-orange-500/50 transition-colors" style="background: var(--docs-bg);">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                    <span style="color: var(--docs-text);">Item 1 - Drag me!</span>
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center gap-3 hover:border-orange-500/50 transition-colors" style="background: var(--docs-bg);">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                    <span style="color: var(--docs-text);">Item 2 - Drag me!</span>
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center gap-3 hover:border-orange-500/50 transition-colors" style="background: var(--docs-bg);">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                    <span style="color: var(--docs-text);">Item 3 - Drag me!</span>
                </div>
            </x-accelade::draggable>
        </div>

        <!-- With Handle -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Handle</span>
                Drag Handle
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">handle=".drag-handle"</code> to restrict dragging to specific element.
            </p>

            <x-accelade::draggable handle=".drag-handle" class="space-y-2">
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] flex items-center gap-3" style="background: var(--docs-bg);">
                    <div class="drag-handle cursor-move p-1 hover:bg-blue-500/20 rounded">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>
                    </div>
                    <span style="color: var(--docs-text);">Drag only by the handle</span>
                    <button class="ml-auto px-2 py-1 text-sm bg-blue-500/20 text-blue-500 rounded">Edit</button>
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] flex items-center gap-3" style="background: var(--docs-bg);">
                    <div class="drag-handle cursor-move p-1 hover:bg-blue-500/20 rounded">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                        </svg>
                    </div>
                    <span style="color: var(--docs-text);">Click the button without dragging</span>
                    <button class="ml-auto px-2 py-1 text-sm bg-blue-500/20 text-blue-500 rounded">Edit</button>
                </div>
            </x-accelade::draggable>
        </div>

        <!-- Multi-Container (Kanban Style) -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Kanban</span>
                Multi-Container Drag
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">group="tasks"</code> to enable dragging between containers.
            </p>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <h5 class="text-sm font-medium mb-2 text-purple-500">To Do</h5>
                    <x-accelade::draggable group="tasks" class="space-y-2 min-h-[100px] p-2 rounded-lg border-2 border-dashed border-purple-500/30">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Design homepage
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Write documentation
                        </div>
                    </x-accelade::draggable>
                </div>

                <div>
                    <h5 class="text-sm font-medium mb-2 text-amber-500">In Progress</h5>
                    <x-accelade::draggable group="tasks" class="space-y-2 min-h-[100px] p-2 rounded-lg border-2 border-dashed border-amber-500/30">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Implement API
                        </div>
                    </x-accelade::draggable>
                </div>

                <div>
                    <h5 class="text-sm font-medium mb-2 text-emerald-500">Done</h5>
                    <x-accelade::draggable group="tasks" class="space-y-2 min-h-[100px] p-2 rounded-lg border-2 border-dashed border-emerald-500/30">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Setup project
                        </div>
                    </x-accelade::draggable>
                </div>
            </div>
        </div>

        <!-- Horizontal Sorting -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Horizontal</span>
                Horizontal Sorting
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">axis="x"</code> for horizontal-only dragging.
            </p>

            <x-accelade::draggable axis="x" class="flex gap-2 overflow-x-auto p-2">
                <div data-draggable-item class="flex-shrink-0 w-24 h-24 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center justify-center text-sm font-medium hover:border-emerald-500/50 transition-colors" style="background: var(--docs-bg); color: var(--docs-text);">
                    1
                </div>
                <div data-draggable-item class="flex-shrink-0 w-24 h-24 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center justify-center text-sm font-medium hover:border-emerald-500/50 transition-colors" style="background: var(--docs-bg); color: var(--docs-text);">
                    2
                </div>
                <div data-draggable-item class="flex-shrink-0 w-24 h-24 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center justify-center text-sm font-medium hover:border-emerald-500/50 transition-colors" style="background: var(--docs-bg); color: var(--docs-text);">
                    3
                </div>
                <div data-draggable-item class="flex-shrink-0 w-24 h-24 rounded-lg border border-[var(--docs-border)] cursor-move flex items-center justify-center text-sm font-medium hover:border-emerald-500/50 transition-colors" style="background: var(--docs-bg); color: var(--docs-text);">
                    4
                </div>
            </x-accelade::draggable>
        </div>

        <!-- Custom Ghost Class -->
        <div class="rounded-xl p-4 border border-rose-500/30" style="background: rgba(244, 63, 94, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-rose-500/20 text-rose-500 rounded">Styling</span>
                Custom Ghost & Drag Classes
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Customize appearance with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">ghostClass</code> and <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">dragClass</code>.
            </p>

            <x-accelade::draggable ghostClass="opacity-30 bg-rose-500/20" dragClass="shadow-xl scale-105" class="space-y-2">
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move transition-transform" style="background: var(--docs-bg); color: var(--docs-text);">
                    Drag me for custom effect
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move transition-transform" style="background: var(--docs-bg); color: var(--docs-text);">
                    Ghost is semi-transparent
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move transition-transform" style="background: var(--docs-bg); color: var(--docs-text);">
                    Dragged item scales up
                </div>
            </x-accelade::draggable>
        </div>

        <!-- Disabled State -->
        <div class="rounded-xl p-4 border border-gray-500/30" style="background: rgba(107, 114, 128, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-gray-500/20 text-gray-500 rounded">Disabled</span>
                Toggle Dragging
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">:disabled="true"</code> or <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">disableDrag()</code>/<code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">enableDrag()</code> methods.
            </p>

            <x-accelade::draggable :disabled="true" class="space-y-2">
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] opacity-50" style="background: var(--docs-bg); color: var(--docs-text);">
                    Dragging is disabled
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] opacity-50" style="background: var(--docs-bg); color: var(--docs-text);">
                    Cannot drag these items
                </div>
            </x-accelade::draggable>
        </div>

        <!-- Tree / Nested Dragging -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Tree</span>
                Nested / Tree Dragging
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">:tree="true"</code> to enable nesting items inside other items.
                Drag to the middle of an item or drag right to nest. Items highlight when nesting is detected.
            </p>

            <x-accelade::draggable :tree="true" :maxDepth="3" class="space-y-2">
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2" style="color: var(--docs-text);">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Parent Item 1
                    </div>
                    <div data-draggable-children class="pl-6 mt-2 space-y-2">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg-alt);">
                            <div class="flex items-center gap-2" style="color: var(--docs-text);">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Child 1.1
                            </div>
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg-alt);">
                            <div class="flex items-center gap-2" style="color: var(--docs-text);">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Child 1.2
                            </div>
                        </div>
                    </div>
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2" style="color: var(--docs-text);">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                        Parent Item 2
                    </div>
                    <div data-draggable-children class="pl-6 mt-2 space-y-2">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg-alt);">
                            <div class="flex items-center gap-2" style="color: var(--docs-text);">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Child 2.1
                            </div>
                        </div>
                    </div>
                </div>
                <div data-draggable-item class="p-3 rounded-lg border border-[var(--docs-border)] cursor-move" style="background: var(--docs-bg);">
                    <div class="flex items-center gap-2" style="color: var(--docs-text);">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Standalone Item (drag into a folder)
                    </div>
                </div>
            </x-accelade::draggable>
        </div>

        <!-- Spring Animation -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Spring</span>
                Spring Physics Animation
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Items animate with spring physics by default. Customize with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">springStiffness</code> and <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">springDamping</code>.
            </p>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h5 class="text-sm font-medium mb-2 text-indigo-400">Bouncy (Low Damping)</h5>
                    <x-accelade::draggable :springStiffness="400" :springDamping="15" class="space-y-2">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Bouncy Item 1
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Bouncy Item 2
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Bouncy Item 3
                        </div>
                    </x-accelade::draggable>
                </div>

                <div>
                    <h5 class="text-sm font-medium mb-2 text-indigo-400">Snappy (High Stiffness)</h5>
                    <x-accelade::draggable :springStiffness="600" :springDamping="35" class="space-y-2">
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Snappy Item 1
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Snappy Item 2
                        </div>
                        <div data-draggable-item class="p-2 rounded border border-[var(--docs-border)] cursor-move text-sm" style="background: var(--docs-bg); color: var(--docs-text);">
                            Snappy Item 3
                        </div>
                    </x-accelade::draggable>
                </div>
            </div>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props & Methods</h4>
        <div class="overflow-x-auto mb-4">
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
                        <td class="py-2 px-3"><code class="text-orange-500">group</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Group name for cross-container drag</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">handle</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">CSS selector for drag handle</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">animation</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">150</td>
                        <td class="py-2 px-3">Animation duration (ms)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">ghostClass</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'opacity-50'</td>
                        <td class="py-2 px-3">Classes for ghost/placeholder</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dragClass</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'shadow-lg'</td>
                        <td class="py-2 px-3">Classes for dragged element</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">disabled</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Disable dragging</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">axis</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">null</td>
                        <td class="py-2 px-3">Constraint axis: 'x' or 'y'</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dropzone</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Act as dropzone only (no sortable items)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">tree</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Enable nested/tree drag support</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">maxDepth</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">0</td>
                        <td class="py-2 px-3">Max nesting depth (0 = unlimited)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">springAnimation</code></td>
                        <td class="py-2 px-3">bool</td>
                        <td class="py-2 px-3">true</td>
                        <td class="py-2 px-3">Use spring physics for animations</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">springStiffness</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">300</td>
                        <td class="py-2 px-3">Spring stiffness (higher = snappier)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-orange-500">springDamping</code></td>
                        <td class="py-2 px-3">int</td>
                        <td class="py-2 px-3">25</td>
                        <td class="py-2 px-3">Spring damping (higher = less bounce)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">Exposed Methods</h5>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Method</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">enableDrag()</code></td>
                        <td class="py-2 px-3">Enable dragging</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">disableDrag()</code></td>
                        <td class="py-2 px-3">Disable dragging</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">isDragEnabled()</code></td>
                        <td class="py-2 px-3">Check if dragging is enabled</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">getDragItems()</code></td>
                        <td class="py-2 px-3">Get array of draggable items</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">moveDragItem(from, to)</code></td>
                        <td class="py-2 px-3">Programmatically move item</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-orange-500">refreshDrag()</code></td>
                        <td class="py-2 px-3">Re-scan DOM for items</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">Events</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Event</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dragstart</code></td>
                        <td class="py-2 px-3">When dragging begins</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dragend</code></td>
                        <td class="py-2 px-3">When dragging ends</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dragsort</code></td>
                        <td class="py-2 px-3">When items are reordered</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-orange-500">dragmove</code></td>
                        <td class="py-2 px-3">When item moves to different container</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-orange-500">dragdrop</code></td>
                        <td class="py-2 px-3">When item is dropped</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="draggable-examples.blade.php">
{{-- Basic sortable list --}}
&lt;x-accelade::draggable&gt;
    &lt;div data-draggable-item&gt;Item 1&lt;/div&gt;
    &lt;div data-draggable-item&gt;Item 2&lt;/div&gt;
    &lt;div data-draggable-item&gt;Item 3&lt;/div&gt;
&lt;/x-accelade::draggable&gt;

{{-- With drag handle --}}
&lt;x-accelade::draggable handle=".handle"&gt;
    &lt;div data-draggable-item&gt;
        &lt;span class="handle cursor-move"&gt;⋮⋮&lt;/span&gt;
        &lt;span&gt;Item content&lt;/span&gt;
    &lt;/div&gt;
&lt;/x-accelade::draggable&gt;

{{-- Cross-container drag (Kanban) --}}
&lt;div class="flex gap-4"&gt;
    &lt;x-accelade::draggable group="tasks" class="flex-1"&gt;
        &lt;div data-draggable-item&gt;Task 1&lt;/div&gt;
    &lt;/x-accelade::draggable&gt;

    &lt;x-accelade::draggable group="tasks" class="flex-1"&gt;
        &lt;div data-draggable-item&gt;Task 2&lt;/div&gt;
    &lt;/x-accelade::draggable&gt;
&lt;/div&gt;

{{-- Horizontal sorting --}}
&lt;x-accelade::draggable axis="x" class="flex gap-2"&gt;
    &lt;div data-draggable-item&gt;1&lt;/div&gt;
    &lt;div data-draggable-item&gt;2&lt;/div&gt;
&lt;/x-accelade::draggable&gt;
    </x-accelade::code-block>
</section>
