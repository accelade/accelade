<?php

declare(strict_types=1);

namespace Accelade\Docs;

/**
 * Value object representing a documentation section.
 */
readonly class DocSection
{
    public string $slug;

    public string $label;

    public ?string $markdownFile;

    public ?string $demoPartial;

    public ?string $view;

    public bool $hasDemo;

    public string $package;

    public ?string $description;

    public ?string $icon;

    /** @var array<int, string> */
    public array $keywords;

    public function __construct(string $slug, string $label, ?DocSectionConfig $config = null)
    {
        $config ??= new DocSectionConfig;

        $this->slug = $slug;
        $this->label = $label;
        $this->markdownFile = $config->markdownFile;
        $this->demoPartial = $config->demoPartial;
        $this->view = $config->view;
        $this->hasDemo = $config->hasDemo;
        $this->package = $config->package;
        $this->description = $config->description;
        $this->icon = $config->icon;
        $this->keywords = $config->keywords;
    }

    /**
     * Get the full path to the markdown documentation file.
     */
    public function getMarkdownPath(DocsRegistry $registry): ?string
    {
        if ($this->markdownFile === null) {
            return null;
        }

        $packagePath = $registry->getPackagePath($this->package);

        if ($packagePath === null) {
            return null;
        }

        return $packagePath.'/'.$this->markdownFile;
    }

    /**
     * Get the view name for this section.
     */
    public function getViewName(): string
    {
        if ($this->view !== null) {
            return $this->view;
        }

        // Default view naming convention
        return $this->package === 'accelade'
            ? "accelade::docs.sections.{$this->slug}"
            : "{$this->package}::docs.sections.{$this->slug}";
    }

    /**
     * Get the demo partial view name.
     */
    public function getDemoPartial(): ?string
    {
        if (! $this->hasDemo) {
            return null;
        }

        if ($this->demoPartial !== null) {
            return $this->demoPartial;
        }

        // Default demo partial naming convention
        $partialSlug = str_replace('-', '-', $this->slug);

        return $this->package === 'accelade'
            ? "accelade::demo.partials._{$partialSlug}-component"
            : "{$this->package}::demo.partials._{$partialSlug}";
    }

    /**
     * Convert to array for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'label' => $this->label,
            'markdownFile' => $this->markdownFile,
            'demoPartial' => $this->demoPartial,
            'view' => $this->view,
            'hasDemo' => $this->hasDemo,
            'package' => $this->package,
            'description' => $this->description,
            'icon' => $this->icon,
            'keywords' => $this->keywords,
        ];
    }
}
