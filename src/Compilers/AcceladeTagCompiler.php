<?php

declare(strict_types=1);

namespace Accelade\Compilers;

use Illuminate\Container\Container;

class AcceladeTagCompiler
{
    protected Container $container;

    /**
     * Tags that should NOT be compiled as reactive components.
     * These remain as literal HTML for JavaScript to process.
     */
    protected array $excludedTags = [
        'script',
        'link',
        'style',
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Check if a component name should be excluded from compilation.
     */
    protected function isExcludedTag(string $component): bool
    {
        return in_array($component, $this->excludedTags, true);
    }

    /**
     * Compile Accelade tags in the template.
     */
    public function compile(string $template): string
    {
        // Compile self-closing <x-accelade:component /> tags
        $template = $this->compileSelfClosingTags($template);

        // Compile opening <x-accelade:component> tags
        $template = $this->compileOpeningTags($template);

        // Compile closing </x-accelade:component> tags
        $template = $this->compileClosingTags($template);

        return $template;
    }

    /**
     * Compile self-closing tags.
     */
    protected function compileSelfClosingTags(string $template): string
    {
        // Pattern: <accelade:component-name attr="value" /> (without x- prefix)
        $pattern = '/<accelade:([\w\-:.]+)\s*([^>]*?)\s*\/>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $component = $matches[1];

            // Skip excluded tags - leave them as-is for JavaScript
            if ($this->isExcludedTag($component)) {
                return $matches[0];
            }

            $attributeString = $matches[2];
            $attributes = $this->parseAttributes($attributeString);

            return $this->buildComponentOutput($component, $attributes, true);
        }, $template);
    }

    /**
     * Compile opening tags.
     */
    protected function compileOpeningTags(string $template): string
    {
        // Pattern: <accelade:component-name attr="value"> (without x- prefix)
        $pattern = '/<accelade:([\w\-:.]+)\s*([^>]*?)(?<!\/)>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $component = $matches[1];

            // Skip excluded tags - leave them as-is for JavaScript
            if ($this->isExcludedTag($component)) {
                return $matches[0];
            }

            $attributeString = $matches[2];
            $attributes = $this->parseAttributes($attributeString);

            return $this->buildComponentOutput($component, $attributes, false);
        }, $template);
    }

    /**
     * Compile closing tags.
     */
    protected function compileClosingTags(string $template): string
    {
        return preg_replace_callback(
            '/<\/accelade:([\w\-:.]+)>/',
            function ($matches) {
                $component = $matches[1];

                // Skip excluded tags - leave them as-is for JavaScript
                if ($this->isExcludedTag($component)) {
                    return $matches[0];
                }

                return '@endacceladeComponent';
            },
            $template
        );
    }

    /**
     * Build the component output directive.
     */
    protected function buildComponentOutput(string $component, array $attributes, bool $selfClosing): string
    {
        $attrString = $this->attributesToPhpArray($attributes);

        $output = "@acceladeComponent('{$component}', [{$attrString}])";

        if ($selfClosing) {
            // For self-closing tags, render the component view with state
            $output .= "\n@include('accelade::components.{$component}', ['state' => app('accelade')->getCurrentState()])\n";
            $output .= '@endacceladeComponent';
        }

        return $output;
    }

    /**
     * Parse attributes from the attribute string.
     */
    protected function parseAttributes(string $attributeString): array
    {
        $attributes = [];

        // Parse :binding attributes for reactive data (PHP expressions) FIRST
        preg_match_all('/:(\w[\w\-]*)=(["\'])(.*?)\2/s', $attributeString, $bindMatches, PREG_SET_ORDER);

        foreach ($bindMatches as $match) {
            $attributes['bind:'.$match[1]] = [
                'value' => $match[3],
                'type' => 'expression',
            ];
        }

        // Remove :binding attributes from string before parsing standard attributes
        $cleanedString = preg_replace('/:(\w[\w\-]*)=(["\'])(.*?)\2/s', '', $attributeString);

        // Parse standard attributes: name="value"
        preg_match_all('/(\w[\w\-]*)=(["\'])(.*?)\2/s', $cleanedString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $attributes[$match[1]] = [
                'value' => $match[3],
                'type' => 'string',
            ];
        }

        // Parse @event handlers
        preg_match_all('/@(\w+)=(["\'])(.*?)\2/s', $attributeString, $eventMatches, PREG_SET_ORDER);

        foreach ($eventMatches as $match) {
            $attributes['on:'.$match[1]] = [
                'value' => $match[3],
                'type' => 'event',
            ];
        }

        // Parse boolean attributes (just the attribute name)
        preg_match_all('/\s(\w[\w\-]*)(?=\s|$|\/?>)(?!=)/s', ' '.$attributeString, $boolMatches);

        foreach ($boolMatches[1] as $attr) {
            if (! isset($attributes[$attr]) && ! isset($attributes['bind:'.$attr])) {
                $attributes[$attr] = [
                    'value' => true,
                    'type' => 'boolean',
                ];
            }
        }

        return $attributes;
    }

    /**
     * Convert parsed attributes to PHP array string.
     */
    protected function attributesToPhpArray(array $attributes): string
    {
        $pairs = [];

        foreach ($attributes as $key => $attr) {
            if ($attr['type'] === 'expression') {
                // PHP expression - don't quote
                $pairs[] = "'{$key}' => {$attr['value']}";
            } elseif ($attr['type'] === 'boolean') {
                $pairs[] = "'{$key}' => true";
            } elseif ($attr['type'] === 'event') {
                $pairs[] = "'{$key}' => '{$attr['value']}'";
            } else {
                // String value - quote it
                $escaped = addslashes($attr['value']);
                $pairs[] = "'{$key}' => '{$escaped}'";
            }
        }

        return implode(', ', $pairs);
    }
}
