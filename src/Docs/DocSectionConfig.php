<?php

declare(strict_types=1);

namespace Accelade\Docs;

/**
 * Configuration object for DocSection optional parameters.
 */
readonly class DocSectionConfig
{
    public ?string $markdownFile;

    public ?string $demoPartial;

    public ?string $view;

    public bool $hasDemo;

    public string $package;

    public ?string $description;

    public ?string $icon;

    /** @var array<int, string> */
    public array $keywords;

    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(array $options = [])
    {
        $this->markdownFile = $options['markdownFile'] ?? null;
        $this->demoPartial = $options['demoPartial'] ?? null;
        $this->view = $options['view'] ?? null;
        $this->hasDemo = $options['hasDemo'] ?? false;
        $this->package = $options['package'] ?? 'accelade';
        $this->description = $options['description'] ?? null;
        $this->icon = $options['icon'] ?? null;
        $this->keywords = $options['keywords'] ?? [];
    }
}
