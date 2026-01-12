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
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Defer Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Asynchronous data loading with <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::defer&gt;</code>.
        Exposes <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">processing</code>, <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">response</code>, and <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">reload()</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Basic Defer -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">Basic</span>
                Auto-fetch on Load
            </h3>
            <x-accelade::defer url="https://dummyjson.com/quotes/random">
                <div class="space-y-3">
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2 text-indigo-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading quote...</span>
                    </div>
                    <div {{ $showAttr }}="response">
                        <blockquote class="italic text-slate-600 border-l-4 border-indigo-300 pl-4">
                            "<span {{ $textAttr }}="response.quote"></span>"
                        </blockquote>
                        <p class="text-sm text-slate-500 mt-2">— <span {{ $textAttr }}="response.author"></span></p>
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
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-amber-100 text-amber-700 rounded">Manual</span>
                Load on Demand
            </h3>
            <x-accelade::defer url="https://dummyjson.com/users/1" :manual="true">
                <div class="space-y-3">
                    <div {{ $showAttr }}="!response && !processing">
                        <p class="text-slate-500 text-sm mb-3">Data not loaded yet. Click to fetch.</p>
                    </div>
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2 text-amber-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Fetching user...</span>
                    </div>
                    <div {{ $showAttr }}="response" class="p-3 bg-white rounded-lg border border-amber-100">
                        <p class="font-medium text-slate-700"><span {{ $textAttr }}="response.firstName"></span> <span {{ $textAttr }}="response.lastName"></span></p>
                        <p class="text-sm text-slate-500"><span {{ $textAttr }}="response.email"></span></p>
                        <p class="text-sm text-slate-500"><span {{ $textAttr }}="response.phone"></span></p>
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
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Polling -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Polling</span>
                Auto-refresh Every 5s
            </h3>
            <x-accelade::defer url="https://worldtimeapi.org/api/timezone/UTC" poll="5000">
                <div class="space-y-3">
                    <div {{ $showAttr }}="processing && !response" class="flex items-center gap-2 text-emerald-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading time...</span>
                    </div>
                    <div {{ $showAttr }}="response" class="text-center">
                        <p class="text-xs text-slate-500 uppercase tracking-wide">UTC Time</p>
                        <p class="text-2xl font-mono font-bold text-emerald-600" {{ $textAttr }}="response.datetime"></p>
                        <p class="text-xs text-slate-400 mt-2">
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
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Error</span>
                Error Handling
            </h3>
            <x-accelade::defer url="https://httpstat.us/500" :manual="true">
                <div class="space-y-3">
                    <div {{ $showAttr }}="!error && !response && !processing">
                        <p class="text-slate-500 text-sm">Click to simulate an API error (500).</p>
                    </div>
                    <div {{ $showAttr }}="processing" class="flex items-center gap-2 text-slate-600">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading...</span>
                    </div>
                    <div {{ $showAttr }}="error" class="p-3 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-red-600 font-medium">Error occurred!</p>
                        <p class="text-sm text-red-500" {{ $textAttr }}="error"></p>
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
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Product List from API</h3>
        <x-accelade::defer url="https://dummyjson.com/products?limit=4">
            <div {{ $showAttr }}="processing && !response" class="flex justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div {{ $showAttr }}="response">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <template a-for="product in response.products">
                        <div class="bg-white rounded-lg p-3 border border-indigo-100 shadow-sm">
                            <p class="font-medium text-slate-700 text-sm truncate" {{ $textAttr }}="product.title"></p>
                            <p class="text-indigo-600 font-bold">$<span {{ $textAttr }}="product.price"></span></p>
                            <p class="text-xs text-slate-400" {{ $textAttr }}="product.category"></p>
                        </div>
                    </template>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">Showing <span {{ $textAttr }}="response.products.length"></span> of <span {{ $textAttr }}="response.total"></span> products</p>
                    <button
                        @click="reload()"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm"
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
