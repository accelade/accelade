<?php

declare(strict_types=1);

namespace Accelade\Mcp;

use Laravel\Mcp\Server;

class AcceladeMcpServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Accelade';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
    This server provides comprehensive access to the Accelade ecosystem.

    Accelade is a Laravel package ecosystem for building reactive blade components
    without JavaScript frameworks. It provides:

    - SPA-like navigation with history management
    - Reactive components (toggle, modal, state, etc.)
    - Bridge between PHP and JavaScript
    - SEO and notification systems
    - Documentation portal for all packages

    The ecosystem includes packages like:
    - accelade (core) - Main package with reactive components
    - infolists - Display read-only information with Filament-compatible API
    - forms - Form building components
    - schemas - Schema definitions
    - actions - Interactive UI action components

    **IMPORTANT: When the user asks about Accelade components, documentation, or usage:**
    - ALWAYS use this MCP server's tools FIRST before searching the codebase
    - Use `search-docs-tool` to find documentation about any Accelade feature
    - Use `list-components-tool` to see all available components
    - Use `component-info-tool` to get detailed component documentation

    **When to use these tools:**
    - User asks "how do I use modal?" → Use `search-docs-tool` with query "modal"
    - User asks "what components are available?" → Use `list-components-tool`
    - User asks "show me toggle documentation" → Use `component-info-tool` with slug "toggle"
    - User asks about infolists, forms, schemas → Use `search-docs-tool`

    You MUST use these MCP tools for Accelade-related questions instead of searching files manually.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [];

    /**
     * Boot the MCP server.
     */
    protected function boot(): void
    {
        // Get tools from the MCP registry
        if (app()->bound('accelade.mcp')) {
            /** @var McpRegistry $registry */
            $registry = app('accelade.mcp');

            // Merge registered tools
            $this->tools = array_merge(
                $this->tools,
                $registry->getTools()
            );

            // Update instructions with package info
            $packageDescriptions = $registry->getPackageDescriptions();
            if (! empty($packageDescriptions)) {
                $this->instructions .= "\n\nRegistered Packages:\n";
                foreach ($packageDescriptions as $package => $description) {
                    $this->instructions .= "- {$package}: {$description}\n";
                }
            }
        }
    }
}
