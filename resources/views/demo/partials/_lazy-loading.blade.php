{{-- Lazy Loading Section - Framework Specific --}}
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

<!-- Demo: Lazy Loading -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Lazy Loading</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Defer content rendering with beautiful shimmer placeholders using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::lazy&gt;</code>.
    </p>

    <!-- Shimmer Variants -->
    <div class="grid md:grid-cols-3 gap-4 mb-4">
        <!-- Basic Shimmer -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 text-sm" style="color: var(--docs-text);">3 Lines (Default)</h4>
            <x-accelade::lazy :shimmer="true" :delay="1500">
                <div class="space-y-2" data-testid="lazy-shimmer-default">
                    <p class="text-sm" style="color: var(--docs-text);">First line loaded</p>
                    <p class="text-sm" style="color: var(--docs-text);">Second line loaded</p>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Third line</p>
                </div>
            </x-accelade::lazy>
        </div>

        <!-- Circle Shimmer -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)] text-center" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 text-sm" style="color: var(--docs-text);">Circle (Avatar)</h4>
            <div class="flex justify-center">
                <x-accelade::lazy :shimmer="true" :shimmer-circle="true" :delay="1800">
                    <img src="https://ui-avatars.com/api/?name=Demo+User&background=6366f1&color=fff" alt="Avatar" class="w-12 h-12 rounded-full" data-testid="lazy-avatar" />
                </x-accelade::lazy>
            </div>
        </div>

        <!-- Rounded Shimmer -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 text-sm" style="color: var(--docs-text);">Rounded Card</h4>
            <x-accelade::lazy :shimmer="true" :shimmer-rounded="true" shimmer-height="80px" :delay="2000">
                <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-4 rounded-lg text-sm text-center" data-testid="lazy-rounded">
                    Content loaded!
                </div>
            </x-accelade::lazy>
        </div>
    </div>

    <!-- Conditional Loading -->
    <div class="rounded-xl p-4 border border-cyan-500/30 mb-4" style="background: rgba(6, 182, 212, 0.1);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Conditional Loading</h4>
        @accelade(['showContent' => false])
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm" style="color: var(--docs-text-muted);">Click to load content on demand</span>
                <button {{ $clickAttr }}="$toggle('showContent')" class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm font-medium hover:bg-cyan-700 transition" data-testid="toggle-lazy">
                    <span {{ $showAttr }}="!showContent">Show Content</span>
                    <span {{ $showAttr }}="showContent">Hide Content</span>
                </button>
            </div>

            <x-accelade::lazy show="showContent" :shimmer="true" :shimmer-lines="4">
                <div class="space-y-2" data-testid="lazy-conditional">
                    <div class="p-3 bg-green-500/20 border border-green-500/30 rounded-lg text-green-500 text-sm">Your order has been shipped!</div>
                    <div class="p-3 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-500 text-sm">New feature: Dark mode available</div>
                    <div class="p-3 bg-amber-500/20 border border-amber-500/30 rounded-lg text-amber-500 text-sm">Password expires in 7 days</div>
                </div>
            </x-accelade::lazy>
        @endaccelade
    </div>

    <!-- Custom Placeholder -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Custom Placeholder</h4>
        <x-accelade::lazy :delay="2500">
            <x-slot:placeholder>
                <div class="flex items-center gap-4 animate-pulse">
                    <div class="w-12 h-12 rounded-full" style="background: var(--docs-border);"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 rounded w-1/3" style="background: var(--docs-border);"></div>
                        <div class="h-3 rounded w-1/4" style="background: var(--docs-border);"></div>
                    </div>
                </div>
            </x-slot:placeholder>

            <div class="flex items-center gap-4" data-testid="lazy-custom">
                <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=8b5cf6&color=fff" alt="Jane Smith" class="w-12 h-12 rounded-full" />
                <div>
                    <h4 class="font-semibold" style="color: var(--docs-text);">Jane Smith</h4>
                    <p class="text-sm" style="color: var(--docs-text-muted);">Product Designer</p>
                </div>
            </div>
        </x-accelade::lazy>
    </div>

    <x-accelade::code-block language="blade" filename="lazy.blade.php">
&lt;!-- Basic shimmer --&gt;
&lt;x-accelade::lazy :shimmer="true"&gt;
    Content here
&lt;/x-accelade::lazy&gt;

&lt;!-- Circle shimmer --&gt;
&lt;x-accelade::lazy :shimmer="true" :shimmer-circle="true"&gt;
    &lt;img src="..." class="rounded-full" /&gt;
&lt;/x-accelade::lazy&gt;

&lt;!-- Conditional loading --&gt;
&lt;x-accelade::lazy show="showContent" :shimmer="true"&gt;
    Content loads when showContent is true
&lt;/x-accelade::lazy&gt;
    </x-accelade::code-block>
</section>
