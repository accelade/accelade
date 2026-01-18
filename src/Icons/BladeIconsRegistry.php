<?php

declare(strict_types=1);

namespace Accelade\Icons;

use BladeUI\Icons\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Registry for discovering and managing Blade Icons.
 * Provides API for listing, searching, and rendering icons from installed Blade Icon packages.
 */
class BladeIconsRegistry
{
    protected Factory $factory;

    protected Filesystem $filesystem;

    protected ?array $cachedIconSets = null;

    protected array $iconCache = [];

    public function __construct(Factory $factory, Filesystem $filesystem)
    {
        $this->factory = $factory;
        $this->filesystem = $filesystem;
    }

    /**
     * Get all registered icon sets.
     *
     * @return array<string, array{prefix: string, paths: array, class?: string}>
     */
    public function getSets(): array
    {
        if ($this->cachedIconSets !== null) {
            return $this->cachedIconSets;
        }

        $this->cachedIconSets = $this->factory->all();

        return $this->cachedIconSets;
    }

    /**
     * Get icon set information by name or prefix.
     */
    public function getSet(string $nameOrPrefix): ?array
    {
        $sets = $this->getSets();

        // Try by name first
        if (isset($sets[$nameOrPrefix])) {
            return array_merge(['name' => $nameOrPrefix], $sets[$nameOrPrefix]);
        }

        // Try by prefix
        foreach ($sets as $name => $set) {
            if (($set['prefix'] ?? '') === $nameOrPrefix) {
                return array_merge(['name' => $name], $set);
            }
        }

        return null;
    }

    /**
     * Get all icons from a specific set with pagination.
     *
     * @return array{icons: array, total: int, hasMore: bool}
     */
    public function getIcons(string $setName, int $offset = 0, int $limit = 50, ?string $search = null): array
    {
        $set = $this->getSet($setName);

        if (! $set) {
            return ['icons' => [], 'total' => 0, 'hasMore' => false];
        }

        $allIcons = $this->getAllIconsFromSet($set);

        // Filter by search
        if ($search !== null && $search !== '') {
            $search = strtolower($search);
            $allIcons = array_filter($allIcons, function ($icon) use ($search) {
                return str_contains(strtolower($icon['name']), $search);
            });
            $allIcons = array_values($allIcons);
        }

        $total = count($allIcons);
        $icons = array_slice($allIcons, $offset, $limit);
        $hasMore = ($offset + $limit) < $total;

        return [
            'icons' => $icons,
            'total' => $total,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * Search icons across all sets or specific sets.
     *
     * @param  array|null  $sets  Specific sets to search in, or null for all
     * @return array{icons: array, total: int, hasMore: bool}
     */
    public function searchIcons(string $query, ?array $sets = null, int $offset = 0, int $limit = 50): array
    {
        $results = [];
        $searchSets = $sets ?? array_keys($this->getSets());

        foreach ($searchSets as $setName) {
            $set = $this->getSet($setName);
            if (! $set) {
                continue;
            }

            $icons = $this->getAllIconsFromSet($set);
            $query = strtolower($query);

            foreach ($icons as $icon) {
                if (str_contains(strtolower($icon['name']), $query)) {
                    $results[] = $icon;
                }
            }
        }

        $total = count($results);
        $icons = array_slice($results, $offset, $limit);
        $hasMore = ($offset + $limit) < $total;

        return [
            'icons' => $icons,
            'total' => $total,
            'hasMore' => $hasMore,
        ];
    }

    /**
     * Get SVG content for a specific icon.
     */
    public function getSvg(string $iconName, string $class = '', array $attributes = []): ?string
    {
        try {
            $svg = $this->factory->svg($iconName, $class, $attributes);

            return $svg->toHtml();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all icons from a set (cached).
     */
    protected function getAllIconsFromSet(array $set): array
    {
        $cacheKey = $set['name'] ?? 'unknown';

        if (isset($this->iconCache[$cacheKey])) {
            return $this->iconCache[$cacheKey];
        }

        $icons = [];
        $prefix = $set['prefix'] ?? '';
        $paths = $set['paths'] ?? [];

        foreach ($paths as $path) {
            if (! $this->filesystem->isDirectory($path)) {
                continue;
            }

            $files = $this->filesystem->allFiles($path);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'svg') {
                    continue;
                }

                $relativePath = Str::after($file->getPathname(), $path.DIRECTORY_SEPARATOR);
                $iconName = Str::replaceLast('.svg', '', $relativePath);
                $iconName = str_replace(DIRECTORY_SEPARATOR, '.', $iconName);

                $fullName = $prefix ? "{$prefix}-{$iconName}" : $iconName;

                $icons[] = [
                    'name' => $iconName,
                    'fullName' => $fullName,
                    'set' => $set['name'] ?? 'default',
                    'prefix' => $prefix,
                ];
            }
        }

        // Sort icons alphabetically
        usort($icons, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $this->iconCache[$cacheKey] = $icons;

        return $icons;
    }

    /**
     * Get summary of all installed icon sets.
     *
     * @return array<array{name: string, prefix: string, count: int}>
     */
    public function getSetsSummary(): array
    {
        $summary = [];

        foreach ($this->getSets() as $name => $set) {
            $icons = $this->getAllIconsFromSet(array_merge(['name' => $name], $set));
            $summary[] = [
                'name' => $name,
                'prefix' => $set['prefix'] ?? '',
                'count' => count($icons),
            ];
        }

        return $summary;
    }

    /**
     * Check if a specific icon set is installed.
     */
    public function hasSet(string $nameOrPrefix): bool
    {
        return $this->getSet($nameOrPrefix) !== null;
    }

    /**
     * Clear the icon cache.
     */
    public function clearCache(): void
    {
        $this->cachedIconSets = null;
        $this->iconCache = [];
    }
}
