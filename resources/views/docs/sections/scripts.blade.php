@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="scripts" :documentation="$documentation" :hasDemo="$hasDemo">
    <!-- Demo: Custom Script Functions -->
    <section class="bg-[var(--docs-bg-secondary)] rounded-xl p-6 mb-6 border border-[var(--docs-border)]">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
            <h3 class="text-lg font-semibold">Custom Script Functions</h3>
        </div>
        <p class="text-[var(--docs-text-muted)] mb-4 text-sm">Define custom functions using &lt;accelade:script&gt; that can access state.</p>

        @accelade(['count' => 0, 'message' => 'Click the buttons!'])
            <div class="text-center p-6 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 rounded-lg mb-4">
                <p class="text-lg mb-3 font-medium" {{ $prefix }}-text="message">
                    Click the buttons!
                </p>

                <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-4" {{ $prefix }}-text="count">0</p>

                <div class="flex gap-2 justify-center">
                    <button
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition"
                        @click="addFive()"
                    >
                        +5
                    </button>
                    <button
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition"
                        @click="double()"
                    >
                        Double
                    </button>
                    <button
                        class="px-4 py-2 bg-slate-600 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition"
                        @click="reset('count')"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <accelade:script>
                return {
                    addFive() {
                        $set('count', $get('count') + 5);
                        $set('message', 'Added 5!');
                    },
                    double() {
                        const current = $get('count');
                        $set('count', current * 2);
                        $set('message', 'Doubled to ' + (current * 2) + '!');
                    }
                };
            </accelade:script>
        @endaccelade
    </section>

    <x-accelade::code-block language="javascript" filename="scripts.blade.php">
&lt;accelade:script&gt;
    return {
        addFive() {
            $set('count', $get('count') + 5);
        },
        double() {
            $set('count', $get('count') * 2);
        }
    };
&lt;/accelade:script&gt;
    </x-accelade::code-block>
</x-accelade::layouts.docs>
