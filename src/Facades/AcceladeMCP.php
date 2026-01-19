<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Mcp\McpRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Accelade MCP Registry.
 *
 * Allows packages to register their MCP tools and documentation
 * with the unified Accelade MCP server.
 *
 * @method static McpRegistry registerTool(string $toolClass)
 * @method static McpRegistry registerTools(array $tools)
 * @method static McpRegistry registerPackage(string $name, string $docsPath, string $description = '')
 * @method static McpRegistry registerReadme(string $package, string $readmePath)
 * @method static array getTools()
 * @method static array getDocsPaths()
 * @method static string|null getDocsPath(string $package)
 * @method static array getPackageDescriptions()
 * @method static string|null getPackageDescription(string $package)
 * @method static array getReadmePaths()
 * @method static string|null getReadmePath(string $package)
 * @method static array getPackages()
 * @method static bool hasPackage(string $package)
 * @method static array getAllDocumentationFiles()
 *
 * @see \Accelade\Mcp\McpRegistry
 */
class AcceladeMCP extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.mcp';
    }
}
