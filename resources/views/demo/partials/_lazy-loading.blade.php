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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-cyan-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Lazy Loading</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Defer content rendering with beautiful shimmer placeholders using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::lazy&gt;</code>.
    </p>

    <!-- Shimmer Variants -->
    <div class="grid md:grid-cols-3 gap-4 mb-6">
        <!-- Basic Shimmer -->
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-3 text-sm">3 Lines (Default)</h3>
            <x-accelade::lazy :shimmer="true" :delay="1500">
                <div class="space-y-2" data-testid="lazy-shimmer-default">
                    <p class="text-slate-700 text-sm">First line loaded</p>
                    <p class="text-slate-700 text-sm">Second line loaded</p>
                    <p class="text-slate-500 text-xs">Third line</p>
                </div>
            </x-accelade::lazy>
        </div>

        <!-- Circle Shimmer -->
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-center">
            <h3 class="font-medium text-slate-700 mb-3 text-sm">Circle (Avatar)</h3>
            <div class="flex justify-center">
                <x-accelade::lazy :shimmer="true" :shimmer-circle="true" :delay="1800">
                    <img src="https://ui-avatars.com/api/?name=Demo+User&background=6366f1&color=fff" alt="Avatar" class="w-12 h-12 rounded-full" data-testid="lazy-avatar" />
                </x-accelade::lazy>
            </div>
        </div>

        <!-- Rounded Shimmer -->
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-3 text-sm">Rounded Card</h3>
            <x-accelade::lazy :shimmer="true" :shimmer-rounded="true" shimmer-height="80px" :delay="2000">
                <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white p-4 rounded-lg text-sm text-center" data-testid="lazy-rounded">
                    Content loaded!
                </div>
            </x-accelade::lazy>
        </div>
    </div>

    <!-- Conditional Loading -->
    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl p-6 border border-cyan-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Conditional Loading</h3>
        @accelade(['showContent' => false])
            <div class="flex items-center justify-between mb-4">
                <span class="text-slate-600 text-sm">Click to load content on demand</span>
                <button {{ $clickAttr }}="$toggle('showContent')" class="px-4 py-2 bg-cyan-600 text-white rounded-lg text-sm font-medium hover:bg-cyan-700 transition" data-testid="toggle-lazy">
                    <span {{ $showAttr }}="!showContent">Show Content</span>
                    <span {{ $showAttr }}="showContent">Hide Content</span>
                </button>
            </div>

            <x-accelade::lazy show="showContent" :shimmer="true" :shimmer-lines="4">
                <div class="space-y-2" data-testid="lazy-conditional">
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">Your order has been shipped!</div>
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">New feature: Dark mode available</div>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 text-sm">Password expires in 7 days</div>
                </div>
            </x-accelade::lazy>
        @endaccelade
    </div>

    <!-- Custom Placeholder -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Custom Placeholder</h3>
        <x-accelade::lazy :delay="2500">
            <x-slot:placeholder>
                <div class="flex items-center gap-4 animate-pulse">
                    <div class="w-12 h-12 bg-slate-200 rounded-full"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                        <div class="h-3 bg-slate-200 rounded w-1/4"></div>
                    </div>
                </div>
            </x-slot:placeholder>

            <div class="flex items-center gap-4" data-testid="lazy-custom">
                <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=8b5cf6&color=fff" alt="Jane Smith" class="w-12 h-12 rounded-full" />
                <div>
                    <h4 class="font-semibold text-slate-800">Jane Smith</h4>
                    <p class="text-slate-500 text-sm">Product Designer</p>
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
