<?php

declare(strict_types=1);

namespace Accelade\Bridge;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Trait to enable Bridge functionality on Blade Components.
 *
 * Bridge Components provide two-way binding between PHP properties
 * and JavaScript, allowing frontend templates to:
 * - Access public PHP properties as reactive `props`
 * - Call public PHP methods via AJAX
 * - Receive responses (redirects, toasts, data updates)
 */
trait WithBridge
{
    /**
     * Unique identifier for this bridge instance.
     */
    protected string $bridgeId;

    /**
     * Middleware to apply to method calls.
     */
    protected array $bridgeMiddleware = [];

    /**
     * Properties that should be hidden from the bridge.
     */
    protected array $bridgeHidden = [];

    /**
     * Boot the bridge trait.
     */
    public function bootWithBridge(): void
    {
        $this->bridgeId = $this->generateBridgeId();
    }

    /**
     * Generate a unique bridge ID.
     */
    protected function generateBridgeId(): string
    {
        return 'bridge-'.Str::random(16);
    }

    /**
     * Get the bridge ID.
     */
    public function getBridgeId(): string
    {
        if (! isset($this->bridgeId)) {
            $this->bridgeId = $this->generateBridgeId();
        }

        return $this->bridgeId;
    }

    /**
     * Add middleware to be applied during method calls.
     */
    protected function middleware(string|array $middleware): static
    {
        $this->bridgeMiddleware = array_merge(
            $this->bridgeMiddleware,
            (array) $middleware
        );

        return $this;
    }

    /**
     * Get the middleware for bridge method calls.
     */
    public function getBridgeMiddleware(): array
    {
        return $this->bridgeMiddleware;
    }

    /**
     * Hide properties from the bridge.
     */
    protected function hiddenFromBridge(string|array $properties): static
    {
        $this->bridgeHidden = array_merge(
            $this->bridgeHidden,
            (array) $properties
        );

        return $this;
    }

    /**
     * Get all public properties that should be exposed to the bridge.
     */
    public function getBridgeProps(): array
    {
        $reflection = new ReflectionClass($this);
        $props = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            // Skip hidden properties
            if (in_array($name, $this->bridgeHidden, true)) {
                continue;
            }

            // Skip internal properties
            if (str_starts_with($name, 'bridge') || str_starts_with($name, '_')) {
                continue;
            }

            // Skip properties from parent classes (like Component)
            if ($property->getDeclaringClass()->getName() !== static::class) {
                continue;
            }

            $value = $property->getValue($this);

            // Transform Eloquent models and collections
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
     * Get all public methods that can be called via the bridge.
     */
    public function getBridgeMethods(): array
    {
        $reflection = new ReflectionClass($this);
        $methods = [];

        // Methods to exclude (from Component class and this trait)
        $excluded = [
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
            // Trait methods
            'bootWithBridge',
            'generateBridgeId',
            'getBridgeId',
            'middleware',
            'getBridgeMiddleware',
            'hiddenFromBridge',
            'getBridgeProps',
            'getBridgeMethods',
            'getBridgeConfig',
            'transformValue',
            'updateBridgeProps',
            'callBridgeMethod',
        ];

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            // Skip excluded methods
            if (in_array($name, $excluded, true)) {
                continue;
            }

            // Skip magic methods
            if (str_starts_with($name, '__')) {
                continue;
            }

            // Skip methods from parent classes
            if ($method->getDeclaringClass()->getName() !== static::class) {
                continue;
            }

            // Get method parameters
            $params = [];
            foreach ($method->getParameters() as $param) {
                $params[] = [
                    'name' => $param->getName(),
                    'optional' => $param->isOptional(),
                    'default' => $param->isOptional() ? $param->getDefaultValue() : null,
                ];
            }

            $methods[$name] = [
                'name' => $name,
                'params' => $params,
            ];
        }

        return $methods;
    }

    /**
     * Get the bridge configuration for the frontend.
     */
    public function getBridgeConfig(): array
    {
        return [
            'id' => $this->getBridgeId(),
            'component' => static::class,
            'props' => $this->getBridgeProps(),
            'methods' => array_keys($this->getBridgeMethods()),
        ];
    }

    /**
     * Update bridge properties from frontend.
     */
    public function updateBridgeProps(array $props): array
    {
        $reflection = new ReflectionClass($this);
        $updated = [];

        foreach ($props as $name => $value) {
            // Skip hidden properties
            if (in_array($name, $this->bridgeHidden, true)) {
                continue;
            }

            // Check if property exists and is public
            if (! $reflection->hasProperty($name)) {
                continue;
            }

            $property = $reflection->getProperty($name);
            if (! $property->isPublic()) {
                continue;
            }

            // Update the property
            $property->setValue($this, $value);
            $updated[$name] = $value;
        }

        return $updated;
    }

    /**
     * Call a bridge method.
     */
    public function callBridgeMethod(string $method, array $args = []): BridgeResponse
    {
        $methods = $this->getBridgeMethods();

        if (! isset($methods[$method])) {
            return BridgeResponse::error("Method '{$method}' is not callable via bridge.");
        }

        try {
            $result = $this->{$method}(...$args);

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

            // Return updated props along with any result
            return BridgeResponse::success([
                'result' => $result,
                'props' => $this->getBridgeProps(),
            ]);
        } catch (\Throwable $e) {
            return BridgeResponse::error($e->getMessage());
        }
    }
}
