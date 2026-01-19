<?php

declare(strict_types=1);

namespace Accelade\Mcp;

use Laravel\Mcp\Server\Tool;

/**
 * Registry for MCP tools and resources from Accelade ecosystem packages.
 *
 * This allows packages to register their own MCP tools, documentation paths,
 * and resources to be exposed through the unified Accelade MCP server.
 */
class McpRegistry
{
    /**
     * Registered MCP tools from packages.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [];

    /**
     * Registered package documentation paths.
     *
     * @var array<string, string>
     */
    protected array $docsPaths = [];

    /**
     * Registered package descriptions.
     *
     * @var array<string, string>
     */
    protected array $packageDescriptions = [];

    /**
     * Registered package README paths.
     *
     * @var array<string, string>
     */
    protected array $readmePaths = [];

    /**
     * Register a tool with the MCP server.
     *
     * @param  class-string<Tool>  $toolClass
     */
    public function registerTool(string $toolClass): self
    {
        if (! in_array($toolClass, $this->tools, true)) {
            $this->tools[] = $toolClass;
        }

        return $this;
    }

    /**
     * Register multiple tools at once.
     *
     * @param  array<int, class-string<Tool>>  $tools
     */
    public function registerTools(array $tools): self
    {
        foreach ($tools as $tool) {
            $this->registerTool($tool);
        }

        return $this;
    }

    /**
     * Register a package with its documentation path.
     */
    public function registerPackage(string $name, string $docsPath, string $description = ''): self
    {
        $this->docsPaths[$name] = rtrim($docsPath, '/');

        if (! empty($description)) {
            $this->packageDescriptions[$name] = $description;
        }

        return $this;
    }

    /**
     * Register a package's README path for documentation search.
     */
    public function registerReadme(string $package, string $readmePath): self
    {
        $this->readmePaths[$package] = $readmePath;

        return $this;
    }

    /**
     * Get all registered tools.
     *
     * @return array<int, class-string<Tool>>
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * Get all registered documentation paths.
     *
     * @return array<string, string>
     */
    public function getDocsPaths(): array
    {
        return $this->docsPaths;
    }

    /**
     * Get documentation path for a specific package.
     */
    public function getDocsPath(string $package): ?string
    {
        return $this->docsPaths[$package] ?? null;
    }

    /**
     * Get all registered package descriptions.
     *
     * @return array<string, string>
     */
    public function getPackageDescriptions(): array
    {
        return $this->packageDescriptions;
    }

    /**
     * Get description for a specific package.
     */
    public function getPackageDescription(string $package): ?string
    {
        return $this->packageDescriptions[$package] ?? null;
    }

    /**
     * Get all README paths.
     *
     * @return array<string, string>
     */
    public function getReadmePaths(): array
    {
        return $this->readmePaths;
    }

    /**
     * Get README path for a specific package.
     */
    public function getReadmePath(string $package): ?string
    {
        return $this->readmePaths[$package] ?? null;
    }

    /**
     * Get all registered packages.
     *
     * @return array<int, string>
     */
    public function getPackages(): array
    {
        return array_keys($this->docsPaths);
    }

    /**
     * Check if a package is registered.
     */
    public function hasPackage(string $package): bool
    {
        return isset($this->docsPaths[$package]);
    }

    /**
     * Get all documentation files across all packages.
     *
     * @return array<int, array{package: string, path: string, name: string, content: string}>
     */
    public function getAllDocumentationFiles(): array
    {
        $files = [];

        foreach ($this->docsPaths as $package => $docsPath) {
            // Add README if exists
            $readmePath = $this->readmePaths[$package] ?? dirname($docsPath).'/README.md';
            if (file_exists($readmePath)) {
                $files[] = [
                    'package' => $package,
                    'path' => $readmePath,
                    'name' => 'README.md',
                    'content' => file_get_contents($readmePath),
                ];
            }

            // Add all docs files
            if (is_dir($docsPath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($docsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'md') {
                        $relativePath = str_replace($docsPath.'/', '', $file->getPathname());
                        $files[] = [
                            'package' => $package,
                            'path' => $file->getPathname(),
                            'name' => $relativePath,
                            'content' => file_get_contents($file->getPathname()),
                        ];
                    }
                }
            }
        }

        return $files;
    }
}
