<?php

declare(strict_types=1);

namespace Accelade\Docs;

/**
 * Registry for documentation sections from Accelade ecosystem packages.
 *
 * Allows packages to register their documentation sections, demos,
 * and navigation items to be displayed in the unified docs portal.
 */
class DocsRegistry
{
    /**
     * Registered documentation sections.
     *
     * @var array<string, DocSection>
     */
    protected array $sections = [];

    /**
     * Sidebar navigation groups.
     *
     * @var array<string, array{label: string, icon: string, priority: int, sections: array<string>}>
     */
    protected array $groups = [];

    /**
     * Package base paths for documentation files.
     *
     * @var array<string, string>
     */
    protected array $packagePaths = [];

    /**
     * Framework prefixes for demo components.
     *
     * @var array<string, string>
     */
    protected array $frameworkPrefixes = [
        'vanilla' => 'a',
        'vue' => 'v',
        'react' => 'data-state',
        'svelte' => 's',
        'angular' => 'ng',
    ];

    /**
     * Register a package's documentation base path.
     */
    public function registerPackage(string $package, string $docsPath): self
    {
        $this->packagePaths[$package] = rtrim($docsPath, '/');

        return $this;
    }

    /**
     * Register a documentation section.
     */
    public function registerSection(DocSection $section): self
    {
        $this->sections[$section->slug] = $section;

        return $this;
    }

    /**
     * Register multiple sections at once.
     *
     * @param  array<int, DocSection>  $sections
     */
    public function registerSections(array $sections): self
    {
        foreach ($sections as $section) {
            $this->registerSection($section);
        }

        return $this;
    }

    /**
     * Register a navigation group.
     */
    public function registerGroup(string $key, string $label, string $icon = '', int $priority = 50): self
    {
        $this->groups[$key] = [
            'label' => $label,
            'icon' => $icon,
            'priority' => $priority,
            'sections' => [],
        ];

        return $this;
    }

    /**
     * Add a section to a navigation group.
     */
    public function addToGroup(string $groupKey, string $sectionSlug): self
    {
        if (isset($this->groups[$groupKey])) {
            $this->groups[$groupKey]['sections'][] = $sectionSlug;
        }

        return $this;
    }

    /**
     * Fluent API to register a section with all options.
     */
    public function section(string $slug): DocSectionBuilder
    {
        return new DocSectionBuilder($this, $slug);
    }

    /**
     * Get a section by slug.
     */
    public function getSection(string $slug): ?DocSection
    {
        return $this->sections[$slug] ?? null;
    }

    /**
     * Check if a section exists.
     */
    public function hasSection(string $slug): bool
    {
        return isset($this->sections[$slug]);
    }

    /**
     * Get all registered sections.
     *
     * @return array<string, DocSection>
     */
    public function getAllSections(): array
    {
        return $this->sections;
    }

    /**
     * Get sections that have demos.
     *
     * @return array<string>
     */
    public function getSectionsWithDemo(): array
    {
        return array_keys(array_filter(
            $this->sections,
            fn (DocSection $section) => $section->hasDemo
        ));
    }

    /**
     * Get navigation groups sorted by priority.
     *
     * @return array<string, array{label: string, icon: string, priority: int, sections: array<string>}>
     */
    public function getGroups(): array
    {
        $groups = $this->groups;

        uasort($groups, fn ($a, $b) => $a['priority'] <=> $b['priority']);

        return $groups;
    }

    /**
     * Get navigation structure for sidebar.
     *
     * @return array<int, array{key: string, label: string, icon: string, items: array<int, array{slug: string, label: string, hasDemo: bool}>}>
     */
    public function getNavigation(): array
    {
        $navigation = [];

        foreach ($this->getGroups() as $key => $group) {
            $items = [];

            foreach ($group['sections'] as $slug) {
                if (isset($this->sections[$slug])) {
                    $section = $this->sections[$slug];
                    $items[] = [
                        'slug' => $section->slug,
                        'label' => $section->label,
                        'hasDemo' => $section->hasDemo,
                    ];
                }
            }

            if (count($items) > 0) {
                $navigation[] = [
                    'key' => $key,
                    'label' => $group['label'],
                    'icon' => $group['icon'],
                    'items' => $items,
                ];
            }
        }

        return $navigation;
    }

    /**
     * Get framework prefixes.
     *
     * @return array<string, string>
     */
    public function getFrameworkPrefixes(): array
    {
        return $this->frameworkPrefixes;
    }

    /**
     * Get prefix for a framework.
     */
    public function getFrameworkPrefix(string $framework): string
    {
        return $this->frameworkPrefixes[$framework] ?? 'a';
    }

    /**
     * Check if framework is valid.
     */
    public function isValidFramework(string $framework): bool
    {
        return isset($this->frameworkPrefixes[$framework]);
    }

    /**
     * Get package documentation path.
     */
    public function getPackagePath(string $package): ?string
    {
        return $this->packagePaths[$package] ?? null;
    }

    /**
     * Get all package paths.
     *
     * @return array<string, string>
     */
    public function getPackagePaths(): array
    {
        return $this->packagePaths;
    }
}
