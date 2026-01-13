<?php

declare(strict_types=1);

namespace Accelade\Docs;

/**
 * Fluent builder for creating and registering documentation sections.
 */
class DocSectionBuilder
{
    protected string $slug;

    protected ?string $label = null;

    protected ?string $markdownFile = null;

    protected ?string $demoPartial = null;

    protected ?string $view = null;

    protected bool $hasDemo = false;

    protected string $package = 'accelade';

    protected ?string $description = null;

    /** @var array<int, string> */
    protected array $keywords = [];

    protected ?string $group = null;

    public function __construct(
        protected DocsRegistry $registry,
        string $slug,
    ) {
        $this->slug = $slug;
        $this->label = ucwords(str_replace('-', ' ', $slug));
    }

    /**
     * Set the display label.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the markdown documentation file (relative to package docs path).
     */
    public function markdown(string $file): self
    {
        $this->markdownFile = $file;

        return $this;
    }

    /**
     * Set the demo partial view name.
     */
    public function demo(?string $partial = null): self
    {
        $this->hasDemo = true;
        $this->demoPartial = $partial;

        return $this;
    }

    /**
     * Set the view name for the section.
     */
    public function view(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set the package this section belongs to.
     */
    public function package(string $package): self
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Set the description for search.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set keywords for search.
     *
     * @param  array<int, string>  $keywords
     */
    public function keywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Add this section to a navigation group.
     */
    public function inGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Build and register the section.
     */
    public function register(): DocsRegistry
    {
        $config = new DocSectionConfig([
            'markdownFile' => $this->markdownFile,
            'demoPartial' => $this->demoPartial,
            'view' => $this->view,
            'hasDemo' => $this->hasDemo,
            'package' => $this->package,
            'description' => $this->description,
            'keywords' => $this->keywords,
        ]);

        $section = new DocSection(
            slug: $this->slug,
            label: $this->label ?? ucwords(str_replace('-', ' ', $this->slug)),
            config: $config,
        );

        $this->registry->registerSection($section);

        if ($this->group !== null) {
            $this->registry->addToGroup($this->group, $this->slug);
        }

        return $this->registry;
    }
}
