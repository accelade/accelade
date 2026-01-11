<?php

declare(strict_types=1);

namespace Accelade\Components;

class Counter extends AcceladeComponent
{
    /**
     * Properties that sync with server.
     */
    protected array $syncProperties = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $initialCount = 0,
        public ?string $sync = null,
    ) {
        if ($this->sync) {
            $this->syncProperties = explode(',', $this->sync);
        }

        parent::__construct();
    }

    /**
     * Initialize the component state.
     */
    protected function initializeState(): void
    {
        $this->state = [
            'count' => $this->initialCount,
        ];
    }
}
