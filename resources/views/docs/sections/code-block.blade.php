@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="code-block" :documentation="$documentation" :hasDemo="$hasDemo">
    <div class="space-y-8">
        {{-- Basic Example --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">Basic PHP Code</h3>
            <x-accelade::code-block language="php">
function greet(string $name): string
{
    return "Hello, {$name}!";
}

echo greet('World');
            </x-accelade::code-block>
        </section>

        {{-- With Filename --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">With Filename</h3>
            <x-accelade::code-block language="php" filename="app/Http/Controllers/UserController.php">
&lt;?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->where('active', true)
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }
}
            </x-accelade::code-block>
        </section>

        {{-- JavaScript Example --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">JavaScript</h3>
            <x-accelade::code-block language="javascript" filename="app.js">
// Accelade reactive state example
const counter = Accelade.state('counter', 0);

document.querySelector('#increment').addEventListener('click', () => {
    counter.value++;
});

counter.subscribe((value) => {
    console.log('Counter changed:', value);
});
            </x-accelade::code-block>
        </section>

        {{-- Blade Template --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">Blade Template</h3>
            <x-accelade::code-block language="blade" filename="resources/views/components/card.blade.php">
@@props(['title', 'description' => null])

&lt;div class="rounded-lg border p-6 shadow-sm"&gt;
    &lt;h3 class="text-lg font-semibold"&gt;@{{ $title }}&lt;/h3&gt;

    @@if($description)
        &lt;p class="mt-2 text-gray-600"&gt;@{{ $description }}&lt;/p&gt;
    @@endif

    &lt;div class="mt-4"&gt;
        @{{ $slot }}
    &lt;/div&gt;
&lt;/div&gt;
            </x-accelade::code-block>
        </section>

        {{-- Bash Commands --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">Shell Commands</h3>
            <x-accelade::code-block language="bash">
# Install Accelade via Composer
composer require accelade/accelade

# Publish configuration
php artisan vendor:publish --tag=accelade-config

# Build frontend assets
npm run build
            </x-accelade::code-block>
        </section>

        {{-- JSON Configuration --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">JSON Configuration</h3>
            <x-accelade::code-block language="json" filename="package.json">
{
    "name": "my-laravel-app",
    "private": true,
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "laravel-vite-plugin": "^1.0",
        "vite": "^5.0"
    }
}
            </x-accelade::code-block>
        </section>

        {{-- CSS Example --}}
        <section>
            <h3 class="text-lg font-semibold mb-4" style="color: var(--docs-text)">CSS Styles</h3>
            <x-accelade::code-block language="css" filename="resources/css/custom.css">
/* Custom button styles */
.btn-primary {
    @apply px-4 py-2 bg-indigo-600 text-white rounded-lg;
    @apply hover:bg-indigo-700 transition-colors;
    @apply focus:outline-none focus:ring-2 focus:ring-indigo-500;
}

.btn-primary:disabled {
    @apply opacity-50 cursor-not-allowed;
}
            </x-accelade::code-block>
        </section>
    </div>
</x-accelade::layouts.docs>
