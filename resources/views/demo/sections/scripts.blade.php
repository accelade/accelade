@props(['framework' => 'vanilla', 'prefix' => 'a'])

@php
    // Set framework before any components render
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.demo-sidebar :framework="$framework" section="scripts">
    <!-- Demo: Custom Script Functions -->
    <section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
            <h2 class="text-2xl font-semibold text-slate-800">Custom Script Functions</h2>
        </div>
        <p class="text-slate-500 mb-6 ml-6">Define custom functions using &lt;accelade:script&gt; that can access state.</p>

        @accelade(['count' => 0, 'message' => 'Click the buttons!'])
            <div class="text-center p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl mb-6">
                <p class="text-2xl text-slate-800 mb-4 font-medium" {{ $prefix }}-text="message">
                    Click the buttons!
                </p>

                <p class="text-4xl font-bold text-indigo-600 mb-4" {{ $prefix }}-text="count">0</p>

                <div class="flex gap-2 justify-center">
                    <button
                        class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition shadow-lg shadow-indigo-200"
                        @click="addFive()"
                    >
                        +5
                    </button>
                    <button
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl font-medium hover:bg-purple-700 transition shadow-lg shadow-purple-200"
                        @click="double()"
                    >
                        Double
                    </button>
                    <button
                        class="px-6 py-3 bg-slate-600 text-white rounded-xl font-medium hover:bg-slate-700 transition shadow-lg shadow-slate-200"
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
    </section>
</x-accelade::layouts.demo-sidebar>
