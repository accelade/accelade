<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\View\Component;

/**
 * PersistentComponent - Base class for persistent layout components
 *
 * Extend this class to create layouts where certain elements persist
 * across SPA navigation (e.g., media players, chat widgets).
 *
 * Usage:
 * 1. Create a component extending PersistentComponent
 * 2. In your view, use $slot for the page content
 * 3. Add persistent elements outside the slot with data-accelade-persistent
 *
 * Example:
 * ```php
 * class VideoLayout extends PersistentComponent
 * {
 *     public function render()
 *     {
 *         return view('components.video-layout');
 *     }
 * }
 * ```
 *
 * ```blade
 * <div>
 *     <main data-accelade-page>
 *         {{ $slot }}
 *     </main>
 *
 *     <div data-accelade-persistent class="fixed bottom-0">
 *         <video src="..."></video>
 *     </div>
 * </div>
 * ```
 */
abstract class PersistentComponent extends Component
{
    /**
     * Whether to persist this layout across navigation.
     */
    public bool $persistent = true;

    /**
     * Get the data to pass to the view.
     */
    public function data(): array
    {
        return array_merge(parent::data(), [
            'persistent' => $this->persistent,
        ]);
    }
}
