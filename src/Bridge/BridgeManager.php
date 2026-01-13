<?php

declare(strict_types=1);

namespace Accelade\Bridge;

use Illuminate\Support\Facades\Crypt;
use Illuminate\View\Component;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Manages bridge component instances and state.
 *
 * Handles:
 * - Component instance storage and retrieval
 * - State serialization/deserialization
 * - Secure component fingerprinting
 * - Method calls on any component (no trait required)
 */
class BridgeManager
{
    /**
     * Registered bridge components.
     *
     * @var array<string, array{class: string, props: array}>
     */
    protected array $components = [];

    /**
     * Methods excluded from bridge calls.
     */
    protected array $excludedMethods = [
        'render',
        'resolveView',
        'data',
        'withAttributes',
        'withName',
        'shouldRender',
        'view',
        'flushCache',
        'forgetFactory',
        'forgetComponentsResolver',
        'resolveComponentsUsing',
    ];

    /**
     * Register a bridge component.
     */
    public function register(string $bridgeId, string $componentClass, array $props): void
    {
        $this->components[$bridgeId] = [
            'class' => $componentClass,
            'props' => $props,
        ];
    }

    /**
     * Get a registered component.
     */
    public function get(string $bridgeId): ?array
    {
        return $this->components[$bridgeId] ?? null;
    }

    /**
     * Check if a component is registered.
     */
    public function has(string $bridgeId): bool
    {
        return isset($this->components[$bridgeId]);
    }

    /**
     * Create a secure state payload for the frontend.
     */
    public function createPayload(string $bridgeId, string $componentClass, array $props): string
    {
        $payload = [
            'id' => $bridgeId,
            'class' => $componentClass,
            'props' => $props,
            'checksum' => $this->generateChecksum($bridgeId, $componentClass),
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Decode and verify a state payload.
     */
    public function decodePayload(string $encrypted): ?array
    {
        try {
            $payload = json_decode(Crypt::decryptString($encrypted), true);

            if (! is_array($payload)) {
                return null;
            }

            // Verify checksum
            $expectedChecksum = $this->generateChecksum(
                $payload['id'] ?? '',
                $payload['class'] ?? ''
            );

            if (($payload['checksum'] ?? '') !== $expectedChecksum) {
                return null;
            }

            return $payload;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Generate a checksum for integrity verification.
     */
    protected function generateChecksum(string $bridgeId, string $componentClass): string
    {
        return hash_hmac('sha256', $bridgeId.$componentClass, config('app.key'));
    }

    /**
     * Create a component instance from payload.
     */
    public function createInstance(array $payload): ?Component
    {
        $componentClass = $payload['class'] ?? null;
        $props = $payload['props'] ?? [];

        if (! $componentClass || ! class_exists($componentClass)) {
            return null;
        }

        try {
            // Create instance with props as constructor arguments
            $instance = app()->make($componentClass, $props);

            // Update public properties with current values
            foreach ($props as $name => $value) {
                if (property_exists($instance, $name)) {
                    $instance->{$name} = $value;
                }
            }

            return $instance;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Get all public properties from a component.
     */
    public function getProps(Component $component): array
    {
        $reflection = new ReflectionClass($component);
        $props = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            // Skip internal properties
            if (str_starts_with($name, '_') || str_starts_with($name, 'bridge')) {
                continue;
            }

            // Skip component infrastructure properties
            if (in_array($name, ['componentName', 'attributes', 'factory'], true)) {
                continue;
            }

            // Only include properties declared on the component class itself
            $declaringClass = $property->getDeclaringClass()->getName();
            if ($declaringClass === Component::class || $declaringClass === 'Illuminate\View\Component') {
                continue;
            }

            $value = $property->getValue($component);
            $props[$name] = $this->transformValue($value);
        }

        return $props;
    }

    /**
     * Transform a value for JSON serialization.
     */
    protected function transformValue(mixed $value): mixed
    {
        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            return $value->toArray();
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->map(fn ($item) => $this->transformValue($item))->toArray();
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        if (is_object($value) && method_exists($value, 'jsonSerialize')) {
            return $value->jsonSerialize();
        }

        return $value;
    }

    /**
     * Get callable methods from a component.
     */
    public function getMethods(Component $component): array
    {
        $reflection = new ReflectionClass($component);
        $methods = [];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            // Skip excluded methods
            if (in_array($name, $this->excludedMethods, true)) {
                continue;
            }

            // Skip magic methods
            if (str_starts_with($name, '__')) {
                continue;
            }

            // Only include methods declared on the component class itself
            $declaringClass = $method->getDeclaringClass()->getName();
            if ($declaringClass === Component::class || $declaringClass === 'Illuminate\View\Component') {
                continue;
            }

            $methods[] = $name;
        }

        return $methods;
    }

    /**
     * Update component properties.
     */
    public function updateProps(Component $component, array $props): array
    {
        $reflection = new ReflectionClass($component);
        $updated = [];

        foreach ($props as $name => $value) {
            // Check if property exists and is public
            if (! $reflection->hasProperty($name)) {
                continue;
            }

            $property = $reflection->getProperty($name);
            if (! $property->isPublic()) {
                continue;
            }

            // Update the property
            $property->setValue($component, $value);
            $updated[$name] = $value;
        }

        return $updated;
    }

    /**
     * Call a method on a component.
     */
    public function callMethod(Component $component, string $method, array $args = []): BridgeResponse
    {
        $methods = $this->getMethods($component);

        if (! in_array($method, $methods, true)) {
            return BridgeResponse::error("Method '{$method}' is not callable via bridge.");
        }

        try {
            $result = $component->{$method}(...$args);

            // Handle different result types
            if ($result instanceof BridgeResponse) {
                return $result;
            }

            if ($result instanceof \Illuminate\Http\RedirectResponse) {
                return BridgeResponse::redirect($result->getTargetUrl());
            }

            if ($result instanceof \Illuminate\Http\Response) {
                return BridgeResponse::data(['response' => $result->getContent()]);
            }

            // Return success with any result
            return BridgeResponse::success([
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return BridgeResponse::error($e->getMessage());
        }
    }

    /**
     * Get all registered components.
     */
    public function all(): array
    {
        return $this->components;
    }

    /**
     * Clear all registered components.
     */
    public function clear(): void
    {
        $this->components = [];
    }
}
