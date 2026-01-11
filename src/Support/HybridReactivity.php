<?php

declare(strict_types=1);

namespace Accelade\Support;

use Illuminate\Support\Facades\Cache;

class HybridReactivity
{
    protected string $componentId;

    protected array $state = [];

    protected array $syncedProperties = [];

    public function __construct(string $componentId)
    {
        $this->componentId = $componentId;
        $this->loadState();
    }

    /**
     * Load state from cache.
     */
    public function loadState(): void
    {
        $this->state = Cache::get($this->getCacheKey(), []);
    }

    /**
     * Save state to cache.
     */
    public function saveState(): void
    {
        Cache::put(
            $this->getCacheKey(),
            $this->state,
            config('accelade.state_ttl', 3600)
        );
    }

    /**
     * Set a state property.
     */
    public function set(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
        $this->saveState();
    }

    /**
     * Get a state property.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * Get all state.
     */
    public function all(): array
    {
        return $this->state;
    }

    /**
     * Check if a property exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->state);
    }

    /**
     * Remove a property from state.
     */
    public function forget(string $key): void
    {
        unset($this->state[$key]);
        $this->saveState();
    }

    /**
     * Clear all state for this component.
     */
    public function clear(): void
    {
        $this->state = [];
        Cache::forget($this->getCacheKey());
    }

    /**
     * Get the cache key for this component.
     */
    protected function getCacheKey(): string
    {
        return "accelade.state.{$this->componentId}";
    }

    /**
     * Enable sync for a property.
     */
    public function enableSync(string $property): void
    {
        if (! in_array($property, $this->syncedProperties, true)) {
            $this->syncedProperties[] = $property;
        }
    }

    /**
     * Check if a property is synced.
     */
    public function isSynced(string $property): bool
    {
        return in_array($property, $this->syncedProperties, true);
    }
}
