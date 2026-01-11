<?php

declare(strict_types=1);

namespace Accelade\Support;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class SharedData implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The shared data store.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Lazy-loaded shared data (closures).
     *
     * @var array<string, Closure>
     */
    protected array $lazy = [];

    /**
     * Share data globally across the application.
     *
     * @param  array<string, mixed>|string  $key
     */
    public function share(array|string $key, mixed $value = null): self
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->share($k, $v);
            }

            return $this;
        }

        if ($value instanceof Closure) {
            $this->lazy[$key] = $value;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get a shared value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (array_key_exists($key, $this->lazy)) {
            $this->data[$key] = ($this->lazy[$key])();
            unset($this->lazy[$key]);

            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Check if a shared key exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data) || array_key_exists($key, $this->lazy);
    }

    /**
     * Remove a shared key.
     */
    public function forget(string $key): self
    {
        unset($this->data[$key], $this->lazy[$key]);

        return $this;
    }

    /**
     * Clear all shared data.
     */
    public function flush(): self
    {
        $this->data = [];
        $this->lazy = [];

        return $this;
    }

    /**
     * Get all shared data as an array.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        // Resolve all lazy values
        foreach ($this->lazy as $key => $closure) {
            $this->data[$key] = $closure();
        }
        $this->lazy = [];

        return $this->data;
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * Convert to JSON.
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options | JSON_THROW_ON_ERROR);
    }

    /**
     * Prepare for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * Merge additional data into shared data.
     *
     * @param  array<string, mixed>  $data
     */
    public function merge(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->share($key, $value);
        }

        return $this;
    }

    /**
     * Get the count of shared items.
     */
    public function count(): int
    {
        return count($this->data) + count($this->lazy);
    }

    /**
     * Check if shared data is empty.
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Check if shared data is not empty.
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
}
