{{-- Defer Component Section - Framework Agnostic --}}
@props(['prefix' => 'a'])

@php
    // Determine framework-specific attributes
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
@endphp

<!-- Demo: Defer Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Defer Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Asynchronous data loading with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::defer&gt;</code>.
        Exposes <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">processing</code>, <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">response</code>, and <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">reload()</code>.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Basic Defer -->
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Basic</span>
                Auto-fetch on Load
            </h4>
            <x-accelade::defer url="https://dummyjson.com/quotes/random">
                <div class="space-y-3">
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2 text-indigo-500">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading quote...</span>
                    </div>
                    <div {{ $showAttr }}="response">
                        <blockquote class="italic border-l-4 border-indigo-500/50 pl-4" style="color: var(--docs-text-muted);">
                            "<span {{ $textAttr }}="response.quote"></span>"
                        </blockquote>
                        <p class="text-sm mt-2" style="color: var(--docs-text-muted);">— <span {{ $textAttr }}="response.author"></span></p>
                    </div>
                    <button
                        @click="reload()"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm"
                    >
                        Get New Quote
                    </button>
                </div>
            </x-accelade::defer>
        </div>

        <!-- Manual Mode -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Manual</span>
                Load on Demand
            </h4>
            <x-accelade::defer url="https://dummyjson.com/users/1" :manual="true">
                <div class="space-y-3">
                    <div {{ $showAttr }}="!response && !processing">
                        <p class="text-sm mb-3" style="color: var(--docs-text-muted);">Data not loaded yet. Click to fetch.</p>
                    </div>
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2 text-amber-500">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Fetching user...</span>
                    </div>
                    <div {{ $showAttr }}="response" class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="font-medium" style="color: var(--docs-text);"><span {{ $textAttr }}="response.firstName"></span> <span {{ $textAttr }}="response.lastName"></span></p>
                        <p class="text-sm" style="color: var(--docs-text-muted);"><span {{ $textAttr }}="response.email"></span></p>
                        <p class="text-sm" style="color: var(--docs-text-muted);"><span {{ $textAttr }}="response.phone"></span></p>
                    </div>
                    <button
                        @click="reload()"
                        class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors text-sm"
                    >
                        Load / Refresh
                    </button>
                </div>
            </x-accelade::defer>
        </div>

        <!-- Polling -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Polling</span>
                Auto-refresh Every 5s
            </h4>
            <x-accelade::defer url="https://worldtimeapi.org/api/timezone/UTC" poll="5000">
                <div class="space-y-3">
                    <div {{ $showAttr }}="processing && !response" class="flex items-center gap-2 text-emerald-500">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading time...</span>
                    </div>
                    <div {{ $showAttr }}="response" class="text-center">
                        <p class="text-xs uppercase tracking-wide" style="color: var(--docs-text-muted);">UTC Time</p>
                        <p class="text-2xl font-mono font-bold text-emerald-500" {{ $textAttr }}="response.datetime"></p>
                        <p class="text-xs mt-2" style="color: var(--docs-text-muted);">
                            <span {{ $showAttr }}="processing" class="inline-flex items-center gap-1">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Refreshing...
                            </span>
                            <span {{ $showAttr }}="!processing">Auto-updates every 5 seconds</span>
                        </p>
                    </div>
                </div>
            </x-accelade::defer>
        </div>

        <!-- Error Handling -->
        <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">Error</span>
                Error Handling
            </h4>
            <x-accelade::defer url="https://httpstat.us/500" :manual="true">
                <div class="space-y-3">
                    <div {{ $showAttr }}="!error && !response && !processing">
                        <p class="text-sm" style="color: var(--docs-text-muted);">Click to simulate an API error (500).</p>
                    </div>
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2" style="color: var(--docs-text-muted);">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading...</span>
                    </div>
                    <div {{ $showAttr }}="error" class="p-3 bg-red-500/20 rounded-lg border border-red-500/30">
                        <p class="text-red-500 font-medium">Error occurred!</p>
                        <p class="text-sm text-red-400" {{ $textAttr }}="error"></p>
                    </div>
                    <button
                        @click="reload()"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm"
                    >
                        Trigger Error
                    </button>
                </div>
            </x-accelade::defer>
        </div>
    </div>

    <!-- Product List Example -->
    <div class="rounded-xl p-4 border border-purple-500/30 mb-4" style="background: rgba(168, 85, 247, 0.1);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Product List from API</h4>
        <x-accelade::defer url="https://dummyjson.com/products?limit=4">
            <div {{ $showAttr }}="processing && !response" class="flex justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div {{ $showAttr }}="response">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <template a-for="product in response.products">
                        <div class="rounded-lg p-3 border border-[var(--docs-border)] shadow-sm" style="background: var(--docs-bg);">
                            <p class="font-medium text-sm truncate" style="color: var(--docs-text);" {{ $textAttr }}="product.title"></p>
                            <p class="text-purple-500 font-bold">$<span {{ $textAttr }}="product.price"></span></p>
                            <p class="text-xs" style="color: var(--docs-text-muted);" {{ $textAttr }}="product.category"></p>
                        </div>
                    </template>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm" style="color: var(--docs-text-muted);">Showing <span {{ $textAttr }}="response.products.length"></span> of <span {{ $textAttr }}="response.total"></span> products</p>
                    <button
                        @click="reload()"
                        class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors text-sm"
                    >
                        Refresh
                    </button>
                </div>
            </div>
        </x-accelade::defer>
    </div>

    <x-accelade::code-block language="blade" filename="defer.blade.php">
&lt;!-- Basic defer - auto-fetches on load --&gt;
&lt;x-accelade::defer url="/api/quote"&gt;
    &lt;p {{ $showAttr }}="processing"&gt;Loading...&lt;/p&gt;
    &lt;p {{ $textAttr }}="response.quote"&gt;&lt;/p&gt;
    &lt;button @click="reload()"&gt;Refresh&lt;/button&gt;
&lt;/x-accelade::defer&gt;

&lt;!-- Manual mode - load on demand --&gt;
&lt;x-accelade::defer url="/api/data" :manual="true"&gt;
    &lt;button @click="reload()"&gt;Load Data&lt;/button&gt;
&lt;/x-accelade::defer&gt;

&lt;!-- Polling - auto-refresh every 5 seconds --&gt;
&lt;x-accelade::defer url="/api/stats" poll="5000"&gt;
    &lt;span {{ $textAttr }}="response.count"&gt;&lt;/span&gt;
&lt;/x-accelade::defer&gt;

&lt;!-- POST with request data --&gt;
&lt;x-accelade::defer url="/api/search" method="POST" :request="['q' =&gt; '']"&gt;
    &lt;div {{ $textAttr }}="response.results"&gt;&lt;/div&gt;
&lt;/x-accelade::defer&gt;
    </x-accelade::code-block>
</section>
