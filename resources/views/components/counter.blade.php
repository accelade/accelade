@props([
    'initialCount' => 0,
    'sync' => '',
    'step' => 1,
    'framework' => null,
])

@php
    // Use prop if provided, otherwise try service, fallback to config
    $framework = $framework ?? app('accelade')->getFramework();
@endphp

@accelade(['count' => (int) $initialCount, 'step' => (int) $step], $sync ? $sync : null)
    @if($framework === 'vue')
        {{-- Vue.js: Uses native Vue directives --}}
        <div class="accelade-counter p-6 bg-white rounded-xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center text-slate-800"
                    v-text="count"
                >
                    {{ $initialCount }}
                </span>

                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-green-500 text-white text-xl font-bold rounded-full hover:bg-green-600 transition shadow-md hover:shadow-lg"
                    @click="increment('count')"
                >
                    +
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-slate-500">
                <span v-show="count > 0" class="text-green-600 font-medium">Positive value</span>
                <span v-show="count < 0" class="text-red-600 font-medium">Negative value</span>
                <span v-show="count === 0" class="text-slate-400">Zero</span>
            </div>
        </div>

    @elseif($framework === 'react')
        {{-- React: Uses data-state-* bindings (CSS selector safe) --}}
        <div class="accelade-counter p-6 bg-white rounded-xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center text-slate-800"
                    data-state-text="count"
                >
                    {{ $initialCount }}
                </span>

                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-green-500 text-white text-xl font-bold rounded-full hover:bg-green-600 transition shadow-md hover:shadow-lg"
                    @click="increment('count')"
                >
                    +
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-slate-500">
                Count value: <span data-state-text="count">{{ $initialCount }}</span>
            </div>
        </div>

    @elseif($framework === 'svelte')
        {{-- Svelte: Uses s-* bindings --}}
        <div class="accelade-counter p-6 bg-white rounded-xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center text-slate-800"
                    s-text="count"
                >
                    {{ $initialCount }}
                </span>

                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-green-500 text-white text-xl font-bold rounded-full hover:bg-green-600 transition shadow-md hover:shadow-lg"
                    @click="increment('count')"
                >
                    +
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-slate-500">
                <span s-show="count > 0" class="text-green-600 font-medium">Positive value</span>
                <span s-show="count < 0" class="text-red-600 font-medium">Negative value</span>
                <span s-show="count === 0" class="text-slate-400">Zero</span>
            </div>
        </div>

    @elseif($framework === 'angular')
        {{-- Angular: Uses ng-* bindings --}}
        <div class="accelade-counter p-6 bg-white rounded-xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center text-slate-800"
                    ng-text="count"
                >
                    {{ $initialCount }}
                </span>

                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-green-500 text-white text-xl font-bold rounded-full hover:bg-green-600 transition shadow-md hover:shadow-lg"
                    @click="increment('count')"
                >
                    +
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-slate-500">
                <span ng-show="count > 0" class="text-green-600 font-medium">Positive value</span>
                <span ng-show="count < 0" class="text-red-600 font-medium">Negative value</span>
                <span ng-show="count === 0" class="text-slate-400">Zero</span>
            </div>
        </div>

    @else
        {{-- Vanilla JS: Uses a- prefix directives --}}
        <div class="accelade-counter p-6 bg-white rounded-xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center text-slate-800"
                    a-text="count"
                >
                    {{ $initialCount }}
                </span>

                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-green-500 text-white text-xl font-bold rounded-full hover:bg-green-600 transition shadow-md hover:shadow-lg"
                    @click="increment('count')"
                >
                    +
                </button>
            </div>

            <div class="mt-4 text-center text-sm text-slate-500">
                <span a-show="count > 0" class="text-green-600 font-medium">Positive value</span>
                <span a-show="count < 0" class="text-red-600 font-medium">Negative value</span>
                <span a-show="count === 0" class="text-slate-400">Zero</span>
            </div>
        </div>
    @endif
@endaccelade
