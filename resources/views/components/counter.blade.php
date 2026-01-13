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
        <div class="accelade-counter p-6 rounded-xl shadow-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center"
                    style="color: var(--docs-text);"
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

            <div class="mt-4 text-center text-sm" style="color: var(--docs-text-muted);">
                <span v-show="count > 0" class="text-green-500 font-medium">Positive value</span>
                <span v-show="count < 0" class="text-red-500 font-medium">Negative value</span>
                <span v-show="count === 0" style="color: var(--docs-text-muted);">Zero</span>
            </div>
        </div>

    @elseif($framework === 'react')
        {{-- React: Uses data-state-* bindings (CSS selector safe) --}}
        <div class="accelade-counter p-6 rounded-xl shadow-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center"
                    style="color: var(--docs-text);"
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

            <div class="mt-4 text-center text-sm" style="color: var(--docs-text-muted);">
                Count value: <span data-state-text="count">{{ $initialCount }}</span>
            </div>
        </div>

    @elseif($framework === 'svelte')
        {{-- Svelte: Uses s-* bindings --}}
        <div class="accelade-counter p-6 rounded-xl shadow-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center"
                    style="color: var(--docs-text);"
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

            <div class="mt-4 text-center text-sm" style="color: var(--docs-text-muted);">
                <span s-show="count > 0" class="text-green-500 font-medium">Positive value</span>
                <span s-show="count < 0" class="text-red-500 font-medium">Negative value</span>
                <span s-show="count === 0" style="color: var(--docs-text-muted);">Zero</span>
            </div>
        </div>

    @elseif($framework === 'angular')
        {{-- Angular: Uses ng-* bindings --}}
        <div class="accelade-counter p-6 rounded-xl shadow-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center"
                    style="color: var(--docs-text);"
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

            <div class="mt-4 text-center text-sm" style="color: var(--docs-text-muted);">
                <span ng-show="count > 0" class="text-green-500 font-medium">Positive value</span>
                <span ng-show="count < 0" class="text-red-500 font-medium">Negative value</span>
                <span ng-show="count === 0" style="color: var(--docs-text-muted);">Zero</span>
            </div>
        </div>

    @else
        {{-- Vanilla JS: Uses a- prefix directives --}}
        <div class="accelade-counter p-6 rounded-xl shadow-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <div class="flex items-center justify-center gap-6">
                <button
                    type="button"
                    class="w-12 h-12 flex items-center justify-center bg-red-500 text-white text-xl font-bold rounded-full hover:bg-red-600 transition shadow-md hover:shadow-lg"
                    @click="decrement('count')"
                >
                    -
                </button>

                <span
                    class="text-4xl font-bold min-w-[80px] text-center"
                    style="color: var(--docs-text);"
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

            <div class="mt-4 text-center text-sm" style="color: var(--docs-text-muted);">
                <span a-show="count > 0" class="font-medium text-green-500">Positive value</span>
                <span a-show="count < 0" class="font-medium text-red-500">Negative value</span>
                <span a-show="count === 0" style="color: var(--docs-text-muted);">Zero</span>
            </div>
        </div>
    @endif
@endaccelade
