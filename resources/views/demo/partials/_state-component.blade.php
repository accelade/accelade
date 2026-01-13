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
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">State Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Unified access to errors, flash messages, and shared data with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::state&gt;</code>.
    </p>

    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Validation Errors -->
        <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">Errors</span>
                Validation Errors
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Access validation errors from Laravel's error bag with reactive helpers.
            </p>

            <x-accelade::state :errors="$demoErrors" id="errors-demo">
                <div class="space-y-3">
                    <!-- Check if any errors exist -->
                    <div {{ $showAttr }}="state.hasErrors" class="p-3 rounded-lg border border-red-500/30" style="background: var(--docs-bg);">
                        <p class="text-red-500 font-medium text-sm">Validation failed!</p>
                    </div>

                    <!-- Email error -->
                    <div {{ $showAttr }}="hasError('email')" class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Email</label>
                        <div class="text-sm text-red-500" {{ $textAttr }}="getError('email')"></div>
                    </div>

                    <!-- Password error -->
                    <div {{ $showAttr }}="hasError('password')" class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Password</label>
                        <div class="text-sm text-red-500" {{ $textAttr }}="getError('password')"></div>
                    </div>

                    <!-- Access error via state object -->
                    <div class="p-3 rounded-lg border border-[var(--docs-border)] mt-4" style="background: var(--docs-bg);">
                        <p class="text-xs mb-1" style="color: var(--docs-text-muted);">Via state.errors:</p>
                        <span class="text-sm" style="color: var(--docs-text);" {{ $textAttr }}="state.errors.email || 'No email error'"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>

        <!-- Flash Messages -->
        <div class="rounded-xl p-4 border border-green-500/30" style="background: rgba(34, 197, 94, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-green-500/20 text-green-500 rounded">Flash</span>
                Flash Messages
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Access session flash data for notifications and status messages.
            </p>

            <x-accelade::state :flash="$demoFlash" id="flash-demo">
                <div class="space-y-3">
                    <!-- Success flash -->
                    <div {{ $showAttr }}="hasFlash('success')" class="p-3 rounded-lg border border-green-500/30" style="background: var(--docs-bg);">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-green-500" {{ $textAttr }}="getFlash('success')"></span>
                        </div>
                    </div>

                    <!-- Info flash -->
                    <div {{ $showAttr }}="hasFlash('info')" class="p-3 rounded-lg border border-blue-500/30" style="background: var(--docs-bg);">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-blue-500" {{ $textAttr }}="getFlash('info')"></span>
                        </div>
                    </div>

                    <!-- Access via state object -->
                    <div class="p-3 rounded-lg border border-[var(--docs-border)] mt-4" style="background: var(--docs-bg);">
                        <p class="text-xs mb-1" style="color: var(--docs-text-muted);">Via state.flash:</p>
                        <span class="text-sm" style="color: var(--docs-text);" {{ $textAttr }}="state.flash.success || 'No success message'"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Shared Data -->
        <div class="rounded-xl p-4 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-purple-500/20 text-purple-500 rounded">Shared</span>
                Shared Data
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
                Access globally shared data like user info, app settings, etc.
            </p>

            <x-accelade::state :shared="$demoShared" id="shared-demo">
                <div class="space-y-3">
                    <!-- User info -->
                    <div {{ $showAttr }}="hasShared('user')" class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="text-xs mb-2" style="color: var(--docs-text-muted);">User Info:</p>
                        <div class="space-y-1">
                            <p class="text-sm">
                                <span style="color: var(--docs-text-muted);">Name:</span>
                                <span class="font-medium" style="color: var(--docs-text);" {{ $textAttr }}="getShared('user.name')"></span>
                            </p>
                            <p class="text-sm">
                                <span style="color: var(--docs-text-muted);">Email:</span>
                                <span style="color: var(--docs-text);" {{ $textAttr }}="getShared('user.email')"></span>
                            </p>
                            <p class="text-sm">
                                <span style="color: var(--docs-text-muted);">Role:</span>
                                <span class="text-purple-500 font-medium" {{ $textAttr }}="getShared('user.role')"></span>
                            </p>
                        </div>
                    </div>

                    <!-- App info via state object -->
                    <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="text-xs mb-1" style="color: var(--docs-text-muted);">Via state.shared (dot notation):</p>
                        <span class="text-sm" style="color: var(--docs-text);" {{ $textAttr }}="state.shared.app?.name + ' v' + state.shared.app?.version"></span>
                    </div>
                </div>
            </x-accelade::state>
        </div>

        <!-- Combined State -->
        <div class="rounded-xl p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
            <h4 class="font-medium mb-3 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-gray-500/20 rounded" style="color: var(--docs-text-muted);">Combined</span>
                All Data Together
            </h4>
            <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
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
                    <div {{ $showAttr }}="hasError('name')" class="p-2 rounded text-sm text-red-500 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
                        <span {{ $textAttr }}="getError('name')"></span>
                    </div>

                    <!-- Show flash -->
                    <div {{ $showAttr }}="hasFlash('notice')" class="p-2 rounded text-sm text-blue-500 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
                        <span {{ $textAttr }}="getFlash('notice')"></span>
                    </div>

                    <!-- Show shared -->
                    <div class="p-2 rounded text-sm text-purple-500 border border-purple-500/30" style="background: rgba(168, 85, 247, 0.1);">
                        Environment: <span {{ $textAttr }}="getShared('env')"></span>
                    </div>

                    <!-- State summary -->
                    <div class="p-3 rounded-lg border border-[var(--docs-border)] text-sm" style="background: var(--docs-bg-alt);">
                        <p style="color: var(--docs-text);">
                            Has errors: <span class="font-mono" {{ $textAttr }}="state.hasErrors ? 'Yes' : 'No'"></span>
                        </p>
                    </div>
                </div>
            </x-accelade::state>
        </div>
    </div>

    <!-- All Props -->
    <div class="rounded-xl p-4 border border-[var(--docs-border)] mb-4" style="background: var(--docs-bg);">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Component Props & State Object</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Prop</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Default</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">errors</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">error bag</td>
                        <td class="py-2 px-3">Validation errors (from Laravel error bag by default)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">flash</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">session flash</td>
                        <td class="py-2 px-3">Flash data (from session by default)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-500">shared</code></td>
                        <td class="py-2 px-3">array</td>
                        <td class="py-2 px-3">Accelade shared</td>
                        <td class="py-2 px-3">Shared data (from Accelade by default)</td>
                    </tr>
                </tbody>
            </table>

            <h5 class="font-medium mb-3" style="color: var(--docs-text);">Available State & Methods</h5>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--docs-border)]">
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Property/Method</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Type</th>
                        <th class="text-left py-2 px-3" style="color: var(--docs-text-muted);">Description</th>
                    </tr>
                </thead>
                <tbody style="color: var(--docs-text-muted);">
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code style="color: var(--docs-text);">state.errors</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">First error message per field</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code style="color: var(--docs-text);">state.rawErrors</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All error messages per field (arrays)</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code style="color: var(--docs-text);">state.hasErrors</code></td>
                        <td class="py-2 px-3">boolean</td>
                        <td class="py-2 px-3">Whether any errors exist</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code style="color: var(--docs-text);">state.flash</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All flash data</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code style="color: var(--docs-text);">state.shared</code></td>
                        <td class="py-2 px-3">object</td>
                        <td class="py-2 px-3">All shared data</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">hasError(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if field has error</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">getError(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get first error for field</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">getErrors(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get all errors for field</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">hasFlash(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if flash key exists</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">getFlash(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Get flash value</td>
                    </tr>
                    <tr class="border-b border-[var(--docs-border)]">
                        <td class="py-2 px-3"><code class="text-indigo-500">hasShared(key)</code></td>
                        <td class="py-2 px-3">function</td>
                        <td class="py-2 px-3">Check if shared key exists (dot notation)</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3"><code class="text-indigo-500">getShared(key)</code></td>
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
