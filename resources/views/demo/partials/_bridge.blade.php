{{-- Bridge Component Section --}}
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

    $modelAttr = match($prefix) {
        'v' => 'v-model',
        'data-state' => 'data-state-model',
        's' => 's-model',
        'ng' => 'ng-model',
        default => 'a-model',
    };

    // Create the bridge counter component - NO TRAIT REQUIRED!
    $bridgeCounter = new \Accelade\Components\Demo\BridgeCounter(
        count: 0,
        step: 1,
        name: 'World'
    );
@endphp

<!-- Demo: Bridge Components -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Bridge Components</h3>
        <span class="text-xs px-2 py-1 bg-green-500/20 text-green-500 rounded">No Trait Required</span>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Two-way binding between PHP Blade components and JavaScript.
        Any component works - no trait needed! Call PHP methods from the frontend.
    </p>

    <div class="space-y-4 mb-4">
        {{-- Feature Overview --}}
        <div class="rounded-xl p-4 border border-indigo-500/30" style="background: rgba(99, 102, 241, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-indigo-500/20 text-indigo-500 rounded">Features</span>
                What Bridge Components Do
            </h4>
            <ul class="space-y-2 text-sm" style="color: var(--docs-text-muted);">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span><strong style="color: var(--docs-text);">No trait required</strong> - works with ANY component</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Access PHP public properties as <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">props</code></span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Call PHP methods from JavaScript via AJAX</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Two-way binding with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">{{ $modelAttr }}</code></span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Return toasts, redirects, or events from PHP</span>
                </li>
            </ul>
        </div>

        {{-- PHP Component Class --}}
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">PHP</span>
                Component Class (Plain Component)
            </h4>
            <div class="bg-slate-900 rounded-lg p-4 text-sm font-mono text-slate-100 overflow-x-auto">
                <pre class="whitespace-pre-wrap">use Accelade\Bridge\BridgeResponse;

class Counter extends Component
{
    // No trait needed!
    public int $count = 0;
    public int $step = 1;

    public function increment()
    {
        $this->count += $this->step;

        return BridgeResponse::success()
            ->toastSuccess('Done!');
    }
}</pre>
            </div>
        </div>

        {{-- Live Demo --}}
        <div class="rounded-xl p-4 border border-violet-500/30" style="background: rgba(139, 92, 246, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Live Demo</h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                This counter component is powered by PHP. Click the buttons to call PHP methods!
            </p>

            {{-- Bridge Counter with dynamic prefix attributes --}}
            <x-accelade::bridge :component="$bridgeCounter">
                <div class="rounded-xl p-6 max-w-md border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold mb-2" style="color: var(--docs-text);">Bridge Counter</h3>
                        <p class="text-sm" style="color: var(--docs-text-muted);">Two-way PHP/JS binding demo</p>
                    </div>

                    {{-- Counter Display --}}
                    <div class="text-center mb-6">
                        <div class="text-6xl font-bold text-indigo-500" {{ $textAttr }}="props.count">{{ $bridgeCounter->count }}</div>
                        <div class="text-sm mt-2" style="color: var(--docs-text-muted);">
                            Hello, <span {{ $textAttr }}="props.name">{{ $bridgeCounter->name }}</span>!
                        </div>
                    </div>

                    {{-- Name Input (two-way binding) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Your Name</label>
                        <input
                            type="text"
                            {{ $modelAttr }}="props.name"
                            class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-indigo-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                            placeholder="Enter your name..."
                        >
                    </div>

                    {{-- Step Input --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Step Value</label>
                        <input
                            type="number"
                            {{ $modelAttr }}="props.step"
                            min="1"
                            class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-indigo-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                        >
                    </div>

                    {{-- Counter Buttons --}}
                    <div class="flex gap-2 mb-4">
                        <button
                            @click="decrement()"
                            class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                        >
                            - Decrement
                        </button>
                        <button
                            @click="increment()"
                            class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                        >
                            + Increment
                        </button>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2 mb-4">
                        <button
                            @click="double()"
                            class="flex-1 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors"
                        >
                            x2 Double
                        </button>
                        <button
                            @click="reset()"
                            class="flex-1 px-4 py-2 rounded-lg transition-colors border border-[var(--docs-border)]"
                            style="background: var(--docs-bg-alt); color: var(--docs-text);"
                        >
                            Reset
                        </button>
                    </div>

                    {{-- Save Button --}}
                    <button
                        @click="save()"
                        class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors"
                    >
                        Save Counter
                    </button>

                    {{-- Update Name via Method --}}
                    <div class="mt-4 pt-4 border-t border-[var(--docs-border)]">
                        <button
                            @click="updateName('Guest')"
                            class="text-sm text-indigo-500 hover:text-indigo-400"
                        >
                            Set name to "Guest"
                        </button>
                    </div>
                </div>
            </x-accelade::bridge>
        </div>

        {{-- Event Listener --}}
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Event Listener</h4>
            <x-accelade::data :default="['lastEvent' => null, 'eventData' => null]">
                <div class="text-sm" style="color: var(--docs-text-muted);">
                    <div {{ $showAttr }}="!lastEvent">
                        Waiting for events from the bridge component...
                    </div>
                    <div {{ $showAttr }}="lastEvent" class="space-y-1">
                        <div class="text-green-500">
                            Event: <span {{ $textAttr }}="lastEvent" class="font-mono"></span>
                        </div>
                        <div class="font-mono text-xs" style="color: var(--docs-text-muted);">
                            Data: <span {{ $textAttr }}="JSON.stringify(eventData)"></span>
                        </div>
                    </div>
                </div>
                <accelade:script>
                    $on('counter-doubled', (data) => {
                        $set('lastEvent', 'counter-doubled');
                        $set('eventData', data);
                    });

                    $on('counter-saved', (data) => {
                        $set('lastEvent', 'counter-saved');
                        $set('eventData', data);
                    });
                </accelade:script>
            </x-accelade::data>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="bridge-usage.blade.php">
&lt;!-- Any component works - no trait required! --&gt;
&lt;?php
use Accelade\Bridge\BridgeResponse;

class MyComponent extends Component
{
    public string $name = '';
    public string $email = '';

    public function save()
    {
        // Save to database...

        return BridgeResponse::success()
            -&gt;toastSuccess('Saved!');
    }
}
?&gt;

&lt;!-- Use it in Blade with the bridge wrapper --&gt;
@@php
$myComponent = new MyComponent(name: 'John', email: 'john@example.com');
@@endphp

&lt;x-accelade::bridge :component="$myComponent"&gt;
    &lt;input {{ $modelAttr }}="props.name" placeholder="Name" /&gt;
    &lt;input {{ $modelAttr }}="props.email" placeholder="Email" /&gt;
    &lt;button @@click="save()"&gt;Save&lt;/button&gt;
&lt;/x-accelade::bridge&gt;
    </x-accelade::code-block>

    <div class="mt-4 p-4 bg-green-500/10 rounded-lg border border-green-500/30">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h4 class="font-medium text-green-500 mb-1">Works with All Frameworks</h4>
                <p class="text-sm text-green-400">
                    Bridge components work with all Accelade framework prefixes:
                    <code class="bg-green-500/20 px-1 rounded">a-</code> (vanilla),
                    <code class="bg-green-500/20 px-1 rounded">v-</code> (Vue),
                    <code class="bg-green-500/20 px-1 rounded">data-state-</code> (React),
                    <code class="bg-green-500/20 px-1 rounded">s-</code> (Svelte),
                    <code class="bg-green-500/20 px-1 rounded">ng-</code> (Angular).
                </p>
            </div>
        </div>
    </div>

    <div class="mt-4 p-4 bg-amber-500/10 rounded-lg border border-amber-500/30">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h4 class="font-medium text-amber-500 mb-1">Security Note</h4>
                <p class="text-sm text-amber-400">
                    All public properties are exposed to the browser. Use Eloquent's <code class="bg-amber-500/20 px-1 rounded">$hidden</code>
                    property to hide sensitive model attributes. Never expose credentials or sensitive data.
                </p>
            </div>
        </div>
    </div>
</section>
