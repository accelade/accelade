<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

abstract class AcceladeComponent extends Component
{
    /**
     * The component's reactive state.
     */
    protected array $state = [];

    /**
     * Properties that should sync with the server.
     */
    protected array $syncProperties = [];

    /**
     * Initialize the component state.
     */
    abstract protected function initializeState(): void;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->initializeState();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view($this->viewName(), $this->viewData());
    }

    /**
     * Get the view name for this component.
     */
    protected function viewName(): string
    {
        $name = class_basename(static::class);
        $kebab = Str::kebab($name);

        return "accelade::components.{$kebab}";
    }

    /**
     * Get the data to pass to the view.
     */
    protected function viewData(): array
    {
        return [
            'state' => $this->state,
            'componentId' => $this->getComponentId(),
            'syncProperties' => $this->syncProperties,
        ];
    }

    /**
     * Generate a unique component ID.
     */
    protected function getComponentId(): string
    {
        $name = Str::kebab(class_basename(static::class));

        return $name.'-'.Str::random(8);
    }

    /**
     * Get the component state.
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * Set a state property.
     */
    protected function setState(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }

    /**
     * Get sync properties.
     */
    public function getSyncProperties(): array
    {
        return $this->syncProperties;
    }

    /**
     * Convert the component to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getComponentId(),
            'state' => $this->state,
            'sync' => $this->syncProperties,
        ];
    }
}
