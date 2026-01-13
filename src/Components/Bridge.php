<?php

declare(strict_types=1);

namespace Accelade\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Bridge Component - Wrapper for any Blade component.
 *
 * This component takes any Blade component instance and wraps it with
 * the necessary data attributes for JavaScript two-way binding.
 * No trait required on the wrapped component.
 *
 * Usage:
 * <x-accelade::bridge :component="$myComponent">
 *     <!-- Blade template with a-model, @click method calls, etc. -->
 * </x-accelade::bridge>
 */
class Bridge extends Component
{
    /**
     * The wrapped component instance.
     */
    public Component $component;

    /**
     * Unique identifier for this bridge instance.
     */
    public string $bridgeId;

    /**
     * The encrypted state payload.
     */
    public string $state;

    /**
     * Properties hidden from bridge.
     */
    protected array $hiddenProps = [];

    /**
     * Methods excluded from bridge.
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
        '__construct',
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__call',
        '__callStatic',
        '__toString',
        '__invoke',
        '__clone',
        '__sleep',
        '__wakeup',
        '__serialize',
        '__unserialize',
        '__destruct',
        '__debugInfo',
    ];

    /**
     * Create a new component instance.
     */
    public function __construct(Component $component, array $hidden = [])
    {
        $this->component = $component;
        $this->hiddenProps = $hidden;
        $this->bridgeId = 'bridge-'.Str::random(16);

        // Create encrypted state payload
        $manager = app('accelade.bridge');
        $this->state = $manager->createPayload(
            $this->bridgeId,
            get_class($component),
            $this->getProps()
        );
    }

    /**
     * Get all public properties from the component.
     */
    public function getProps(): array
    {
        $reflection = new ReflectionClass($this->component);
        $props = [];

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            // Skip hidden properties
            if (in_array($name, $this->hiddenProps, true)) {
                continue;
            }

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

            $value = $property->getValue($this->component);
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
     * Get all callable public methods from the component.
     */
    public function getMethods(): array
    {
        $reflection = new ReflectionClass($this->component);
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
     * Get the JSON configuration for the frontend.
     */
    public function getJsonConfig(): string
    {
        return json_encode([
            'id' => $this->bridgeId,
            'component' => get_class($this->component),
            'props' => $this->getProps(),
            'methods' => $this->getMethods(),
            'state' => $this->state,
            'callUrl' => route('accelade.bridge.call'),
            'syncUrl' => route('accelade.bridge.sync'),
        ]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('accelade::components.bridge');
    }
}
