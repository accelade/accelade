{{-- Tooltip Directive Section - Filament-like Tooltips --}}
@props(['prefix' => 'a'])

<!-- Demo: Tooltip Directive (a-tooltip) -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Tooltip Directive</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Display contextual information using the <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">a-tooltip</code> attribute directive.
        Supports light/dark themes, RTL, and configurable width. Filament-inspired API.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Tooltip -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Basic</span>
                Simple Text Tooltip
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Just add <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">a-tooltip="Your text"</code> to any element.
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip="This is a helpful tooltip!"
                    class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors"
                >
                    Hover me
                </button>

                <span
                    a-tooltip="Additional information here"
                    class="inline-flex items-center gap-1 text-purple-500 cursor-help"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    Help icon
                </span>
            </div>
        </div>

        <!-- Theme Variants -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Themes</span>
                Light, Dark & Auto Themes
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use JSON config to set theme: <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"light"</code>, <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"dark"</code>, or <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"auto"</code> (follows system).
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip='{"content": "Light theme tooltip", "theme": "light"}'
                    class="px-4 py-2 bg-white text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    Light Theme
                </button>

                <button
                    a-tooltip='{"content": "Dark theme tooltip", "theme": "dark"}'
                    class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
                >
                    Dark Theme
                </button>

                <button
                    a-tooltip='{"content": "Auto theme - follows your system preference", "theme": "auto"}'
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                >
                    Auto Theme
                </button>
            </div>
        </div>

        <!-- Position Variants -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Positions</span>
                12 Position Variants
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Set position in JSON config. RTL support automatically mirrors left/right positions.
            </p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button
                    a-tooltip='{"content": "Top position", "position": "top"}'
                    class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600"
                >Top</button>

                <button
                    a-tooltip='{"content": "Bottom position", "position": "bottom"}'
                    class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600"
                >Bottom</button>

                <button
                    a-tooltip='{"content": "Left position", "position": "left"}'
                    class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600"
                >Left</button>

                <button
                    a-tooltip='{"content": "Right position", "position": "right"}'
                    class="w-full px-3 py-2 bg-emerald-500 text-white text-sm rounded hover:bg-emerald-600"
                >Right</button>
            </div>

            <div class="grid grid-cols-3 gap-2 mt-4">
                <button
                    a-tooltip='{"content": "Top start", "position": "top-start"}'
                    class="w-full px-2 py-1.5 text-xs rounded border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >top-start</button>

                <button
                    a-tooltip='{"content": "Top center", "position": "top"}'
                    class="w-full px-2 py-1.5 text-xs rounded border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >top</button>

                <button
                    a-tooltip='{"content": "Top end", "position": "top-end"}'
                    class="w-full px-2 py-1.5 text-xs rounded border border-[var(--docs-border)]"
                    style="background: var(--docs-bg); color: var(--docs-text);"
                >top-end</button>
            </div>
        </div>

        <!-- Click Trigger -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Click</span>
                Click Trigger
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"trigger": "click"</code> for click-activated tooltips.
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip='{"content": "Click me again to close, or click outside", "trigger": "click"}'
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors"
                >
                    Click me
                </button>

                <button
                    a-tooltip='{"content": "This tooltip stays until you click elsewhere", "trigger": "click", "position": "bottom"}'
                    class="px-4 py-2 bg-amber-500/20 text-amber-500 rounded-lg hover:bg-amber-500/30 transition-colors"
                >
                    Click tooltip
                </button>
            </div>
        </div>

        <!-- Focus Trigger for Forms -->
        <div class="rounded-xl p-4 border border-rose-500/30" style="background: rgba(244, 63, 94, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-rose-500/20 text-rose-500 rounded">Focus</span>
                Focus Trigger for Forms
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"trigger": "focus"</code> for form field hints.
            </p>

            <div class="max-w-md space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Username</label>
                    <input
                        type="text"
                        placeholder="Enter username"
                        a-tooltip='{"content": "Choose a unique username, 3-20 characters", "trigger": "focus", "position": "right"}'
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-rose-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Password</label>
                    <input
                        type="password"
                        placeholder="Enter password"
                        a-tooltip='{"content": "Minimum 8 characters with at least one number", "trigger": "focus", "position": "right"}'
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-rose-500 border border-[var(--docs-border)]"
                        style="background: var(--docs-bg); color: var(--docs-text);"
                    >
                </div>
            </div>
        </div>

        <!-- Width Control -->
        <div class="rounded-xl p-4 border border-cyan-500/30" style="background: rgba(6, 182, 212, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Width</span>
                Width Control
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Control tooltip width with <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"maxWidth"</code>. Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"none"</code> for full width.
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip='{"content": "This is a very long tooltip text that demonstrates how the default max-width of 320px constrains the content and makes it wrap nicely.", "maxWidth": "320px"}'
                    class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors"
                >
                    Default (320px)
                </button>

                <button
                    a-tooltip='{"content": "Narrow tooltip with limited width.", "maxWidth": "150px"}'
                    class="px-4 py-2 bg-cyan-500/20 text-cyan-500 rounded-lg hover:bg-cyan-500/30 transition-colors"
                >
                    Narrow (150px)
                </button>

                <button
                    a-tooltip='{"content": "This tooltip has no max-width constraint so it can be as wide as needed for the content.", "maxWidth": "none"}'
                    class="px-4 py-2 bg-cyan-700 text-white rounded-lg hover:bg-cyan-800 transition-colors"
                >
                    Full Width
                </button>
            </div>
        </div>

        <!-- Interactive & Delays -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Advanced</span>
                Interactive & Delays
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"interactive": true</code> to allow hovering the tooltip.
                Add <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"delay"</code> and <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"hideDelay"</code> for timing control.
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip='{"content": "You can hover over me! I won'\''t disappear.", "interactive": true}'
                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors"
                >
                    Interactive
                </button>

                <button
                    a-tooltip='{"content": "Appeared after 500ms!", "delay": 500}'
                    class="px-4 py-2 bg-indigo-500/20 text-indigo-500 rounded-lg hover:bg-indigo-500/30 transition-colors"
                >
                    500ms delay
                </button>

                <button
                    a-tooltip='{"content": "I'\''ll stay visible for 1 second after you leave", "hideDelay": 1000}'
                    class="px-4 py-2 bg-indigo-700 text-white rounded-lg hover:bg-indigo-800 transition-colors"
                >
                    1s hide delay
                </button>
            </div>
        </div>

        <!-- RTL Support -->
        <div class="rounded-xl p-4 border border-pink-500/30" style="background: rgba(236, 72, 153, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-pink-500/20 text-pink-500 rounded">RTL</span>
                Right-to-Left Support
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                RTL is auto-detected from the document. Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"rtl": true</code> to force RTL mode.
                Positions are automatically mirrored.
            </p>

            <div class="flex flex-wrap gap-4 items-center">
                <button
                    a-tooltip='{"content": "مرحبا بالعالم! هذا تلميح أداة من اليمين إلى اليسار.", "rtl": true, "position": "top"}'
                    class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors"
                >
                    RTL Tooltip (Arabic)
                </button>

                <button
                    a-tooltip='{"content": "שלום עולם! זה טיפ מימין לשמאל.", "rtl": true, "position": "bottom"}'
                    class="px-4 py-2 bg-pink-500/20 text-pink-500 rounded-lg hover:bg-pink-500/30 transition-colors"
                >
                    RTL Tooltip (Hebrew)
                </button>
            </div>
        </div>

        <!-- No Arrow -->
        <div class="rounded-xl p-4 border border-gray-500/30" style="background: rgba(107, 114, 128, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-gray-500/20 text-gray-500 rounded">Style</span>
                Without Arrow
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Use <code class="px-1 rounded border border-[var(--docs-border)]" style="background: var(--docs-bg);">"arrow": false</code> for a cleaner look.
            </p>

            <button
                a-tooltip='{"content": "No arrow on this tooltip", "arrow": false}'
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
            >
                No arrow
            </button>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Configuration Options</h4>
        <div class="overflow-x-auto mb-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Option</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">content</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">''</td>
                        <td class="py-2 px-3">Tooltip text content</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">theme</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'auto'</td>
                        <td class="py-2 px-3">Theme: 'light', 'dark', or 'auto' (follows system)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">position</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'top'</td>
                        <td class="py-2 px-3">Position: top, bottom, left, right (+ -start, -end variants)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">trigger</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'hover'</td>
                        <td class="py-2 px-3">Trigger: 'hover', 'click', 'focus', 'manual'</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">maxWidth</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">'320px'</td>
                        <td class="py-2 px-3">Max width (e.g., '200px', '20rem', 'none' for full)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">delay</code></td>
                        <td class="py-2 px-3">number</td>
                        <td class="py-2 px-3">0</td>
                        <td class="py-2 px-3">Delay before showing (ms)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">hideDelay</code></td>
                        <td class="py-2 px-3">number</td>
                        <td class="py-2 px-3">0</td>
                        <td class="py-2 px-3">Delay before hiding (ms)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">arrow</code></td>
                        <td class="py-2 px-3">boolean</td>
                        <td class="py-2 px-3">true</td>
                        <td class="py-2 px-3">Show arrow pointer</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">interactive</code></td>
                        <td class="py-2 px-3">boolean</td>
                        <td class="py-2 px-3">false</td>
                        <td class="py-2 px-3">Allow hovering tooltip content</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">rtl</code></td>
                        <td class="py-2 px-3">boolean</td>
                        <td class="py-2 px-3">auto</td>
                        <td class="py-2 px-3">Enable RTL mode (auto-detected from document)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-purple-500">storageKey</code></td>
                        <td class="py-2 px-3">string</td>
                        <td class="py-2 px-3">-</td>
                        <td class="py-2 px-3">Key for persisting settings to localStorage</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 class="font-medium mb-2" style="color: var(--docs-text);">JavaScript API</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Method</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">Accelade.tooltip.initAll()</code></td>
                        <td class="py-2 px-3">Initialize all [a-tooltip] elements on page</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">Accelade.tooltip.get(id)</code></td>
                        <td class="py-2 px-3">Get tooltip instance by element ID</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-purple-500">Accelade.tooltip.getSettings()</code></td>
                        <td class="py-2 px-3">Get global tooltip settings from storage</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-purple-500">Accelade.tooltip.setSettings(opts)</code></td>
                        <td class="py-2 px-3">Set global tooltip settings (persisted to localStorage)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="html" filename="tooltip-examples.html">
{{-- Simple text tooltip --}}
&lt;button a-tooltip="Helpful information"&gt;Hover me&lt;/button&gt;

{{-- JSON config for more options --}}
&lt;button a-tooltip='{"content": "Welcome!", "theme": "light", "position": "bottom"}'&gt;
    Light theme
&lt;/button&gt;

{{-- Click trigger --}}
&lt;button a-tooltip='{"content": "Click to close", "trigger": "click"}'&gt;
    Click me
&lt;/button&gt;

{{-- Form field with focus trigger --}}
&lt;input
    type="email"
    placeholder="Email"
    a-tooltip='{"content": "Enter your email", "trigger": "focus", "position": "right"}'
&gt;

{{-- Custom width --}}
&lt;button a-tooltip='{"content": "Long text...", "maxWidth": "200px"}'&gt;
    Narrow
&lt;/button&gt;

{{-- RTL support --}}
&lt;button a-tooltip='{"content": "مرحبا بالعالم", "rtl": true}'&gt;
    Arabic
&lt;/button&gt;

{{-- Interactive with delay --}}
&lt;button a-tooltip='{"content": "Hover me!", "interactive": true, "delay": 200}'&gt;
    Interactive
&lt;/button&gt;

{{-- Global settings (persisted to localStorage) --}}
&lt;script&gt;
Accelade.tooltip.setSettings({
    theme: 'dark',
    position: 'bottom',
    maxWidth: '250px'
});
&lt;/script&gt;
    </x-accelade::code-block>
</section>
