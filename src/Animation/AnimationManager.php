<?php

declare(strict_types=1);

namespace Accelade\Animation;

/**
 * Manages animation presets for transitions.
 */
class AnimationManager
{
    /**
     * Registered animation presets.
     *
     * @var array<string, AnimationPreset>
     */
    protected array $presets = [];

    /**
     * Whether built-in presets have been registered.
     */
    protected bool $builtInsRegistered = false;

    /**
     * Register built-in animation presets.
     */
    protected function registerBuiltIns(): void
    {
        if ($this->builtInsRegistered) {
            return;
        }

        // Default - subtle fade and scale
        $this->new(
            name: 'default',
            enter: 'transition ease-out duration-200',
            enterFrom: 'opacity-0 scale-95',
            enterTo: 'opacity-100 scale-100',
            leave: 'transition ease-in duration-150',
            leaveFrom: 'opacity-100 scale-100',
            leaveTo: 'opacity-0 scale-95',
        );

        // Opacity - simple fade
        $this->new(
            name: 'opacity',
            enter: 'transition-opacity ease-out duration-200',
            enterFrom: 'opacity-0',
            enterTo: 'opacity-100',
            leave: 'transition-opacity ease-in duration-150',
            leaveFrom: 'opacity-100',
            leaveTo: 'opacity-0',
        );

        // Fade - same as opacity (alias)
        $this->new(
            name: 'fade',
            enter: 'transition-opacity ease-out duration-200',
            enterFrom: 'opacity-0',
            enterTo: 'opacity-100',
            leave: 'transition-opacity ease-in duration-150',
            leaveFrom: 'opacity-100',
            leaveTo: 'opacity-0',
        );

        // Slide left
        $this->new(
            name: 'slide-left',
            enter: 'transition ease-out duration-300',
            enterFrom: 'opacity-0 -translate-x-full',
            enterTo: 'opacity-100 translate-x-0',
            leave: 'transition ease-in duration-300',
            leaveFrom: 'opacity-100 translate-x-0',
            leaveTo: 'opacity-0 -translate-x-full',
        );

        // Slide right
        $this->new(
            name: 'slide-right',
            enter: 'transition ease-out duration-300',
            enterFrom: 'opacity-0 translate-x-full',
            enterTo: 'opacity-100 translate-x-0',
            leave: 'transition ease-in duration-300',
            leaveFrom: 'opacity-100 translate-x-0',
            leaveTo: 'opacity-0 translate-x-full',
        );

        // Slide up
        $this->new(
            name: 'slide-up',
            enter: 'transition ease-out duration-300',
            enterFrom: 'opacity-0 translate-y-full',
            enterTo: 'opacity-100 translate-y-0',
            leave: 'transition ease-in duration-300',
            leaveFrom: 'opacity-100 translate-y-0',
            leaveTo: 'opacity-0 translate-y-full',
        );

        // Slide down
        $this->new(
            name: 'slide-down',
            enter: 'transition ease-out duration-300',
            enterFrom: 'opacity-0 -translate-y-full',
            enterTo: 'opacity-100 translate-y-0',
            leave: 'transition ease-in duration-300',
            leaveFrom: 'opacity-100 translate-y-0',
            leaveTo: 'opacity-0 -translate-y-full',
        );

        // Scale
        $this->new(
            name: 'scale',
            enter: 'transition ease-out duration-200',
            enterFrom: 'opacity-0 scale-0',
            enterTo: 'opacity-100 scale-100',
            leave: 'transition ease-in duration-150',
            leaveFrom: 'opacity-100 scale-100',
            leaveTo: 'opacity-0 scale-0',
        );

        // Collapse - for accordions (no translate, just fade)
        $this->new(
            name: 'collapse',
            enter: 'transition-opacity ease-out duration-200',
            enterFrom: 'opacity-0',
            enterTo: 'opacity-100',
            leave: 'transition-opacity ease-in duration-150',
            leaveFrom: 'opacity-100',
            leaveTo: 'opacity-0',
        );

        $this->builtInsRegistered = true;
    }

    /**
     * Register a new animation preset.
     */
    public function new(
        string $name,
        string $enter,
        string $enterFrom,
        string $enterTo,
        string $leave,
        string $leaveFrom,
        string $leaveTo,
    ): self {
        $this->presets[$name] = new AnimationPreset(
            name: $name,
            enter: $enter,
            enterFrom: $enterFrom,
            enterTo: $enterTo,
            leave: $leave,
            leaveFrom: $leaveFrom,
            leaveTo: $leaveTo,
        );

        return $this;
    }

    /**
     * Get an animation preset by name.
     */
    public function get(string $name): ?AnimationPreset
    {
        $this->registerBuiltIns();

        return $this->presets[$name] ?? null;
    }

    /**
     * Check if a preset exists.
     */
    public function has(string $name): bool
    {
        $this->registerBuiltIns();

        return isset($this->presets[$name]);
    }

    /**
     * Get all registered presets.
     *
     * @return array<string, AnimationPreset>
     */
    public function all(): array
    {
        $this->registerBuiltIns();

        return $this->presets;
    }

    /**
     * Get all presets as array for JSON.
     *
     * @return array<string, array<string, string>>
     */
    public function toArray(): array
    {
        $this->registerBuiltIns();

        $result = [];
        foreach ($this->presets as $name => $preset) {
            $result[$name] = $preset->toArray();
        }

        return $result;
    }
}
