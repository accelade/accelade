{{-- Errors Component Section - Framework Agnostic --}}
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

    $ifAttr = match($prefix) {
        'v' => 'v-if',
        'data-state' => 'data-state-if',
        's' => 's-if',
        'ng' => 'ng-if',
        default => 'a-if',
    };

    $modelAttr = match($prefix) {
        'v' => 'v-model',
        'data-state' => 'data-state-model',
        's' => 's-model',
        'ng' => 'ng-model',
        default => 'a-model',
    };

    $classAttr = match($prefix) {
        'v' => ':class',
        'data-state' => 'data-state-class',
        's' => 's-class',
        'ng' => 'ng-class',
        default => 'a-class',
    };
@endphp

<!-- Demo: Errors Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Errors Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Display Laravel validation errors with <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::errors&gt;</code>.
        Exposes <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">errors.has()</code>, <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">errors.first()</code>, and <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">errors.all</code>.
    </p>

    <div class="space-y-4 mb-4">
        <!-- Simulated Errors Display -->
        <div class="rounded-xl p-4 border border-red-500/30" style="background: rgba(239, 68, 68, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-red-500/20 text-red-500 rounded">Demo</span>
                Error Display Methods
            </h4>

            {{-- Simulate errors for demo purposes --}}
            <x-accelade::data :default="[
                'demoErrors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.', 'The email must be a valid email address.'],
                    'password' => ['The password must be at least 8 characters.', 'The password must contain a number.'],
                ]
            ]">
                <div class="space-y-4">
                    {{-- Check if field has errors --}}
                    <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="text-sm font-medium mb-2" style="color: var(--docs-text);">errors.has('name'):</p>
                        <span {{ $showAttr }}="demoErrors.name && demoErrors.name.length > 0" class="text-red-500 text-sm">true - Field has errors</span>
                        <span {{ $showAttr }}="!demoErrors.name || demoErrors.name.length === 0" class="text-green-500 text-sm">false - No errors</span>
                    </div>

                    {{-- Get first error --}}
                    <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="text-sm font-medium mb-2" style="color: var(--docs-text);">errors.first('email'):</p>
                        <p class="text-red-500 text-sm" {{ $textAttr }}="demoErrors.email[0]"></p>
                    </div>

                    {{-- Get all errors for a field --}}
                    <div class="p-3 rounded-lg border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                        <p class="text-sm font-medium mb-2" style="color: var(--docs-text);">All password errors:</p>
                        <ul class="list-disc list-inside text-red-500 text-sm">
                            <template a-for="error in demoErrors.password">
                                <li {{ $textAttr }}="error"></li>
                            </template>
                        </ul>
                    </div>
                </div>
            </x-accelade::data>
        </div>

        <!-- Interactive Form Demo -->
        <div class="rounded-xl p-4 border border-amber-500/30" style="background: rgba(245, 158, 11, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Interactive</span>
                Form with Client Validation
            </h4>

            @accelade([
                'formData' => ['name' => '', 'email' => ''],
                'formErrors' => ['name' => null, 'email' => null],
                'submitted' => false,
            ])
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Name</label>
                        <input
                            type="text"
                            {{ $modelAttr }}="formData.name"
                            class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg); color: var(--docs-text);"
                            placeholder="Enter your name"
                        >
                        <p {{ $showAttr }}="formErrors.name" class="text-red-500 text-sm mt-1" {{ $textAttr }}="formErrors.name"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--docs-text);">Email</label>
                        <input
                            type="text"
                            {{ $modelAttr }}="formData.email"
                            class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 border border-[var(--docs-border)]"
                            style="background: var(--docs-bg); color: var(--docs-text);"
                            placeholder="Enter your email"
                        >
                        <p {{ $showAttr }}="formErrors.email" class="text-red-500 text-sm mt-1" {{ $textAttr }}="formErrors.email"></p>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="validateForm()"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm"
                        >
                            Submit (Validate)
                        </button>
                        <button
                            type="button"
                            @click="clearErrors()"
                            class="px-4 py-2 rounded-lg transition-colors text-sm border border-[var(--docs-border)]"
                            style="background: var(--docs-bg); color: var(--docs-text);"
                        >
                            Clear Errors
                        </button>
                    </div>

                    <p {{ $showAttr }}="submitted && !formErrors.name && !formErrors.email" class="text-green-500 text-sm">
                        Form submitted successfully!
                    </p>
                </div>

                <accelade:script>
                    return {
                        validateForm() {
                            const data = $get('formData');
                            let nameError = null;
                            let emailError = null;

                            if (!data.name || data.name.trim() === '') {
                                nameError = 'The name field is required.';
                            }

                            if (!data.email || data.email.trim() === '') {
                                emailError = 'The email field is required.';
                            } else if (!data.email.includes('@')) {
                                emailError = 'The email must be a valid email address.';
                            }

                            $set('formErrors.name', nameError);
                            $set('formErrors.email', emailError);
                            $set('submitted', true);
                        },
                        clearErrors() {
                            $set('formErrors.name', null);
                            $set('formErrors.email', null);
                            $set('submitted', false);
                        }
                    };
                </accelade:script>
            @endaccelade
        </div>

        <!-- Error Summary Box -->
        <div class="rounded-xl p-4 border border-orange-500/30" style="background: rgba(249, 115, 22, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Error Summary Box Pattern</h4>

            <x-accelade::data :default="[
                'summaryErrors' => [
                    'username' => ['The username has already been taken.'],
                    'terms' => ['You must accept the terms and conditions.'],
                    'age' => ['You must be at least 18 years old.'],
                ]
            ]">
                <div {{ $showAttr }}="Object.keys(summaryErrors).length > 0" class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="font-medium text-red-500">There were errors with your submission</h4>
                            <ul class="mt-2 text-sm text-red-400 list-disc list-inside">
                                <template a-for="(errors, field) in summaryErrors">
                                    <template a-for="error in errors">
                                        <li {{ $textAttr }}="error"></li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button
                        @click="$set('summaryErrors', {})"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm"
                    >
                        Clear All Errors
                    </button>
                    <button
                        @click="$set('summaryErrors', { username: ['Username taken'], email: ['Invalid email'] })"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm"
                    >
                        Add Errors
                    </button>
                </div>
            </x-accelade::data>
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="errors.blade.php">
&lt;!-- Basic errors display --&gt;
&lt;x-accelade::errors&gt;
    &lt;p {{ $ifAttr }}="errors.has('name')" {{ $textAttr }}="errors.first('name')"&gt;&lt;/p&gt;
&lt;/x-accelade::errors&gt;

&lt;!-- Loop through all errors --&gt;
&lt;x-accelade::errors&gt;
    &lt;div {{ $showAttr }}="errors.any"&gt;
        &lt;template a-for="(messages, field) in errors.all"&gt;
            &lt;p {{ $textAttr }}="messages[0]"&gt;&lt;/p&gt;
        &lt;/template&gt;
    &lt;/div&gt;
&lt;/x-accelade::errors&gt;

&lt;!-- Custom error bag --&gt;
&lt;x-accelade::errors bag="login"&gt;
    &lt;p {{ $textAttr }}="errors.first('credentials')"&gt;&lt;/p&gt;
&lt;/x-accelade::errors&gt;
    </x-accelade::code-block>
</section>
