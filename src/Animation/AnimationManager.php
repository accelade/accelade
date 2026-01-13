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
        $this->register(
            name: 'default',
            enter: new AnimationPhase('transition ease-out duration-200', 'opacity-0 scale-95', 'opacity-100 scale-100'),
            leave: new AnimationPhase('transition ease-in duration-150', 'opacity-100 scale-100', 'opacity-0 scale-95'),
        );

        // Opacity - simple fade
        $this->register(
            name: 'opacity',
            enter: new AnimationPhase('transition-opacity ease-out duration-200', 'opacity-0', 'opacity-100'),
            leave: new AnimationPhase('transition-opacity ease-in duration-150', 'opacity-100', 'opacity-0'),
        );

        // Fade - same as opacity (alias)
        $this->register(
            name: 'fade',
            enter: new AnimationPhase('transition-opacity ease-out duration-200', 'opacity-0', 'opacity-100'),
            leave: new AnimationPhase('transition-opacity ease-in duration-150', 'opacity-100', 'opacity-0'),
        );

        // Slide left
        $this->register(
            name: 'slide-left',
            enter: new AnimationPhase('transition ease-out duration-300', 'opacity-0 -translate-x-full', 'opacity-100 translate-x-0'),
            leave: new AnimationPhase('transition ease-in duration-300', 'opacity-100 translate-x-0', 'opacity-0 -translate-x-full'),
        );

        // Slide right
        $this->register(
            name: 'slide-right',
            enter: new AnimationPhase('transition ease-out duration-300', 'opacity-0 translate-x-full', 'opacity-100 translate-x-0'),
            leave: new AnimationPhase('transition ease-in duration-300', 'opacity-100 translate-x-0', 'opacity-0 translate-x-full'),
        );

        // Slide up
        $this->register(
            name: 'slide-up',
            enter: new AnimationPhase('transition ease-out duration-300', 'opacity-0 translate-y-full', 'opacity-100 translate-y-0'),
            leave: new AnimationPhase('transition ease-in duration-300', 'opacity-100 translate-y-0', 'opacity-0 translate-y-full'),
        );

        // Slide down
        $this->register(
            name: 'slide-down',
            enter: new AnimationPhase('transition ease-out duration-300', 'opacity-0 -translate-y-full', 'opacity-100 translate-y-0'),
            leave: new AnimationPhase('transition ease-in duration-300', 'opacity-100 translate-y-0', 'opacity-0 -translate-y-full'),
        );

        // Scale
        $this->register(
            name: 'scale',
            enter: new AnimationPhase('transition ease-out duration-200', 'opacity-0 scale-0', 'opacity-100 scale-100'),
            leave: new AnimationPhase('transition ease-in duration-150', 'opacity-100 scale-100', 'opacity-0 scale-0'),
        );

        // Collapse - for accordions (no translate, just fade)
        $this->register(
            name: 'collapse',
            enter: new AnimationPhase('transition-opacity ease-out duration-200', 'opacity-0', 'opacity-100'),
            leave: new AnimationPhase('transition-opacity ease-in duration-150', 'opacity-100', 'opacity-0'),
        );

        $this->builtInsRegistered = true;
    }

    /**
     * Register a new animation preset with phase objects.
     */
    public function register(string $name, AnimationPhase $enter, AnimationPhase $leave): self
    {
        $this->presets[$name] = new AnimationPreset(
            name: $name,
            enter: $enter,
            leave: $leave,
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
