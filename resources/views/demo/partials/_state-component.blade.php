{{-- State Component Section - Unified Access to Errors, Flash & Shared Data --}}
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

    // Simulate validation errors for demo
    $demoErrors = [
        'email' => ['The email field is required.', 'The email must be a valid email address.'],
        'password' => ['The password must be at least 8 characters.'],
    ];

    // Simulate flash data for demo
    $demoFlash = [
        'success' => 'Your changes have been saved successfully!',
        'info' => 'Welcome back to the dashboard.',
    ];

    // Simulate shared data for demo
    $demoShared = [
        'user' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'admin',
        ],
        'app' => [
            'name' => 'Accelade Demo',
            'version' => '1.0.0',
        ],
    ];
@endphp

<!-- Demo: State Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">State Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Unified access to errors, flash messages, and shared data with <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::state&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Validation Errors -->
        <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-xl p-6 border border-red-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded">Errors</span>
                Validation Errors
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Access validation errors from Laravel's error bag with reactive helpers.
            </p>

            <x-accelade::state :errors="$demoErrors" id="errors-demo">
                <div class="space-y-3">
                    <!-- Check if any errors exist -->
                    <div {{ $showAttr }}="state.hasErrors" class="p-3 bg-white rounded-lg border border-red-200">
                        <p class="text-red-600 font-medium text-sm">Validation failed!</p>
                    </div>

                    <!-- Email error -->
                    <div {{ $showAttr }}="hasError('email')" class="p-3 bg-white rounded-lg border border-red-100">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <div class="text-sm text-red-600" {{ $textAttr }}="getError('email')"></div>
                    </div>

                    <!-- Password error -->
                    <div {{ $showAttr }}="hasError('password')" class="p-3 bg-white rounded-lg border border-red-100">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                        <div class="text-sm text-red-600" {{ $textAttr }}="getError('password')"></div>
                    </div>

                    <!-- Access error via state object -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200 mt-4">
                        <p class="text-xs text-slate-500 mb-1">Via state.errors:</p>
                        <span class="text-sm text-slate-700" {{ $textAttr }}="state.errors.email || 'No email error'"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>

        <!-- Flash Messages -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Flash</span>
                Flash Messages
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Access session flash data for notifications and status messages.
            </p>

            <x-accelade::state :flash="$demoFlash" id="flash-demo">
                <div class="space-y-3">
                    <!-- Success flash -->
                    <div {{ $showAttr }}="hasFlash('success')" class="p-3 bg-white rounded-lg border border-green-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-green-700" {{ $textAttr }}="getFlash('success')"></span>
                        </div>
                    </div>

                    <!-- Info flash -->
                    <div {{ $showAttr }}="hasFlash('info')" class="p-3 bg-white rounded-lg border border-blue-200">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-700" {{ $textAttr }}="getFlash('info')"></span>
                        </div>
                    </div>

                    <!-- Access via state object -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200 mt-4">
                        <p class="text-xs text-slate-500 mb-1">Via state.flash:</p>
                        <span class="text-sm text-slate-700" {{ $textAttr }}="state.flash.success || 'No success message'"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Shared Data -->
        <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 border border-purple-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded">Shared</span>
                Shared Data
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Access globally shared data like user info, app settings, etc.
            </p>

            <x-accelade::state :shared="$demoShared" id="shared-demo">
                <div class="space-y-3">
                    <!-- User info -->
                    <div {{ $showAttr }}="hasShared('user')" class="p-3 bg-white rounded-lg border border-purple-100">
                        <p class="text-xs text-slate-500 mb-2">User Info:</p>
                        <div class="space-y-1">
                            <p class="text-sm">
                                <span class="text-slate-500">Name:</span>
                                <span class="text-slate-700 font-medium" {{ $textAttr }}="getShared('user.name')"></span>
                            </p>
                            <p class="text-sm">
                                <span class="text-slate-500">Email:</span>
                                <span class="text-slate-700" {{ $textAttr }}="getShared('user.email')"></span>
                            </p>
                            <p class="text-sm">
                                <span class="text-slate-500">Role:</span>
                                <span class="text-purple-600 font-medium" {{ $textAttr }}="getShared('user.role')"></span>
                            </p>
                        </div>
                    </div>

                    <!-- App info via state object -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200">
                        <p class="text-xs text-slate-500 mb-1">Via state.shared (dot notation):</p>
                        <span class="text-sm text-slate-700" {{ $textAttr }}="state.shared.app?.name + ' v' + state.shared.app?.version"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>

        <!-- Combined State -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-slate-200 text-slate-700 rounded">Combined</span>
                All Data Together
            </h3>
            <p class="text-sm text-slate-600 mb-4">
                Use all state features within a single component.
            </p>

            <x-accelade::state
                :errors="['name' => ['Name is required']]"
                :flash="['notice' => 'Form reloaded']"
                :shared="['env' => 'demo']"
                id="combined-demo"
            >
                <div class="space-y-3">
                    <!-- Show error -->
                    <div {{ $showAttr }}="hasError('name')" class="p-2 bg-red-50 rounded text-sm text-red-600">
                        <span {{ $textAttr }}="getError('name')"></span>
                    </div>

                    <!-- Show flash -->
                    <div {{ $showAttr }}="hasFlash('notice')" class="p-2 bg-blue-50 rounded text-sm text-blue-600">
                        <span {{ $textAttr }}="getFlash('notice')"></span>
                    </div>

                    <!-- Show shared -->
                    <div class="p-2 bg-purple-50 rounded text-sm text-purple-600">
                        Environment: <span {{ $textAttr }}="getShared('env')"></span>
                    </div>

                    <!-- State summary -->
                    <div class="p-3 bg-white rounded-lg border border-slate-200 text-sm">
                        <p>
                            Has errors: <span class="font-mono" {{ $textAttr }}="state.hasErrors ? 'Yes' : 'No'"></span>
                        </p>
                    </div>
                </div>
            </x-accelade::state>
        </div>
    </div>

    <!-- All Props -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Component Props & State Object</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Prop</th>
                        <th class="text-left py-2 px-3 text-slate-600">Type</th>
                        <th class="text-left py-2 px-3 text-slate-600">Default</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">errors</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">error bag</td>
                        <td class="py-2 px-3">Validation errors (from Laravel error bag by default)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">flash</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">session flash</td>
                        <td class="py-2 px-3">Flash data (from session by default)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-600">shared</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">Accelade shared</td>
                        <td class="py-2 px-3">Shared data (from Accelade by default)</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="font-medium text-slate-700 mb-3">Available State & Methods</h4>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-2 px-3 text-slate-600">Property/Method</th>
                        <th class="text-left py-2 px-3 text-slate-600">Type</th>
                        <th class="text-left py-2 px-3 text-slate-600">Description</th>
                    </tr>
                </thead>
                <tbody class="text-slate-600">
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-slate-700">state.errors</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">First error message per field</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-slate-700">state.rawErrors</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All error messages per field (arrays)</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-slate-700">state.hasErrors</code></td>
                        <td class="py-2 px-3">boolean</td>
                        <td class="py-2 px-3">Whether any errors exist</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-slate-700">state.flash</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All flash data</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-slate-700">state.shared</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All shared data</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">hasError(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if field has error</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">getError(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get first error for field</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">getErrors(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get all errors for field</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">hasFlash(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if flash key exists</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">getFlash(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get flash value</td>
                    </tr>
                    <tr class="border-b border-slate-100">
                        <td class="py-2 px-3"><code class="text-indigo-600">hasShared(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if shared key exists (dot notation)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-600">getShared(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get shared value (dot notation)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="state-examples.blade.php">
{{-- Basic validation errors --}}
&lt;x-accelade::state&gt;
    &lt;div a-show="state.hasErrors" class="alert alert-danger"&gt;
        Please fix the errors below.
    &lt;/div&gt;

    &lt;div a-show="hasError('email')"&gt;
        &lt;span a-text="getError('email')"&gt;&lt;/span&gt;
    &lt;/div&gt;
&lt;/x-accelade::state&gt;

{{-- Flash message display --}}
&lt;x-accelade::state&gt;
    &lt;div a-show="hasFlash('success')" class="alert alert-success"&gt;
        &lt;span a-text="getFlash('success')"&gt;&lt;/span&gt;
    &lt;/div&gt;
&lt;/x-accelade::state&gt;

{{-- Access shared user data --}}
&lt;x-accelade::state&gt;
    &lt;div a-show="hasShared('user')"&gt;
        Welcome, &lt;span a-text="getShared('user.name')"&gt;&lt;/span&gt;!
    &lt;/div&gt;
&lt;/x-accelade::state&gt;

{{-- Custom data override --}}
&lt;x-accelade::state
    :errors="['custom' => 'Custom error']"
    :flash="['message' => 'Custom flash']"
    :shared="['key' => 'value']"
&gt;
    ...
&lt;/x-accelade::state&gt;
    </x-accelade::code-block>
</section>
