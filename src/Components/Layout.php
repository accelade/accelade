<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\View\Component;

/**
 * Main application layout component.
 *
 * This component provides a flexible layout wrapper that can be used
 * standalone or integrated with the panel/themes packages.
 */
class Layout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public string $framework = 'vanilla',
        public bool $sidebar = true,
        public bool $header = true,
        public bool $footer = true,
        public ?string $theme = null,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('accelade::components.layouts.app');
    }
}
