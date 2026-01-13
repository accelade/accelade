<?php

declare(strict_types=1);

namespace Accelade\Components\Demo;

use Accelade\Bridge\BridgeResponse;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Demo Bridge Counter Component.
 *
 * Demonstrates the Bridge component functionality:
 * - Public properties accessible as `props` in JavaScript
 * - Public methods callable from JavaScript via AJAX
 * - Two-way binding with a-model
 * - Toast notifications from PHP
 *
 * NOTE: No trait required! Any component works with Bridge.
 */
class BridgeCounter extends Component
{
    /**
     * The current count.
     */
    public int $count;

    /**
     * The step value for incrementing.
     */
    public int $step;

    /**
     * A name to display.
     */
    public string $name;

    /**
     * Create a new component instance.
     */
    public function __construct(
        int $count = 0,
        int $step = 1,
        string $name = 'World'
    ) {
        $this->count = $count;
        $this->step = $step;
        $this->name = $name;
    }

    /**
     * Increment the counter.
     */
    public function increment(): BridgeResponse
    {
        $this->count += $this->step;

        return BridgeResponse::success()
            ->withMessage('Counter incremented!')
            ->toastSuccess('Incremented!', "Counter is now {$this->count}");
    }

    /**
     * Decrement the counter.
     */
    public function decrement(): BridgeResponse
    {
        $this->count -= $this->step;

        return BridgeResponse::success()
            ->withMessage('Counter decremented!')
            ->toastInfo('Decremented!', "Counter is now {$this->count}");
    }

    /**
     * Reset the counter to zero.
     */
    public function reset(): BridgeResponse
    {
        $this->count = 0;

        return BridgeResponse::success()
            ->withMessage('Counter reset!')
            ->toastWarning('Reset!', 'Counter has been reset to 0');
    }

    /**
     * Double the current count.
     */
    public function double(): BridgeResponse
    {
        $this->count *= 2;

        return BridgeResponse::success()
            ->withMessage('Counter doubled!')
            ->emit('counter-doubled', ['count' => $this->count]);
    }

    /**
     * Save the counter (simulated).
     */
    public function save(): BridgeResponse
    {
        // Simulate saving to database
        // In a real app, you would save to database here

        return BridgeResponse::success()
            ->toastSuccess('Saved!', "Counter value {$this->count} has been saved.")
            ->emit('counter-saved', ['count' => $this->count, 'name' => $this->name]);
    }

    /**
     * Update the greeting name.
     */
    public function updateName(string $newName): BridgeResponse
    {
        $this->name = $newName;

        return BridgeResponse::success()
            ->toastInfo('Name Updated', "Hello, {$this->name}!");
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('accelade::components.demo.bridge-counter');
    }
}
