{{-- Demo Bridge Counter Component --}}
<div class="bg-white rounded-xl shadow-lg p-6 max-w-md">
    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-2">Bridge Counter</h3>
        <p class="text-sm text-slate-500">Two-way PHP/JS binding demo</p>
    </div>

    {{-- Counter Display --}}
    <div class="text-center mb-6">
        <div class="text-6xl font-bold text-indigo-600" a-text="props.count">{{ $count }}</div>
        <div class="text-sm text-slate-400 mt-2">
            Hello, <span a-text="props.name">{{ $name }}</span>!
        </div>
    </div>

    {{-- Name Input (two-way binding) --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Your Name</label>
        <input
            type="text"
            a-model="props.name"
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Enter your name..."
        >
    </div>

    {{-- Step Input --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Step Value</label>
        <input
            type="number"
            a-model="props.step"
            min="1"
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
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
            class="flex-1 px-4 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 transition-colors"
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
    <div class="mt-4 pt-4 border-t border-slate-200">
        <button
            @click="updateName('Guest')"
            class="text-sm text-indigo-600 hover:text-indigo-800"
        >
            Set name to "Guest"
        </button>
    </div>
</div>
