# MCP Server

Accelade includes a built-in Model Context Protocol (MCP) server that provides AI assistants (like Claude Code, Claude Desktop, Cursor, etc.) with intelligent access to documentation and component information across the entire Accelade ecosystem.

## Installation

Run the install command to set up the MCP server:

```bash
php artisan accelade:mcp
```

This command will:

1. Create or update `routes/ai.php` with the MCP server registration
2. Create or update `.mcp.json` for MCP client configuration
3. Display the available tools and next steps

After installation, restart your AI assistant application to connect to the new server.

## Available Tools

The Accelade MCP server provides three powerful tools:

### search_docs

Search documentation across all registered Accelade packages. This is the primary tool for finding information.

**Parameters:**
- `query` (required): Search query string
- `package` (optional): Filter results to a specific package (e.g., "accelade", "infolists", "forms")

**Example queries:**
- "How do I create a modal?"
- "What are the toggle component options?"
- "How to use text entry in infolists"

### list_components

List all available components and documentation sections across registered packages.

**Parameters:**
- `package` (optional): Filter to a specific package

**Returns:** A structured list of all registered documentation sections with their labels, descriptions, and keywords.

### component_info

Get detailed information about a specific component or documentation section.

**Parameters:**
- `name` (required): Component/section name (e.g., "modal", "toggle", "text-entry")
- `package` (optional): Specify which package to search in

**Returns:** Full documentation content for the specified component.

## How It Works

The MCP server uses a registry pattern where packages can register their documentation and make it searchable by AI assistants.

```
┌─────────────────────┐
│   AI Assistant      │
│ (Claude, Cursor)    │
└─────────┬───────────┘
          │ MCP Protocol
          ▼
┌─────────────────────┐
│  Accelade MCP       │
│     Server          │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│    McpRegistry      │
│  ┌───────────────┐  │
│  │   accelade    │  │
│  │   infolists   │  │
│  │     forms     │  │
│  │    schemas    │  │
│  └───────────────┘  │
└─────────────────────┘
```

## Registering Your Package

Any Accelade ecosystem package can register itself with the MCP server to make its documentation searchable.

### Basic Registration

In your package's service provider:

```php
<?php

namespace YourVendor\YourPackage;

use Accelade\Mcp\McpRegistry;
use Illuminate\Support\ServiceProvider;

class YourPackageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register with MCP if available
        if ($this->app->bound('accelade.mcp')) {
            $this->registerMcp();
        }
    }

    protected function registerMcp(): void
    {
        /** @var McpRegistry $mcp */
        $mcp = $this->app->make('accelade.mcp');

        // Register your package's documentation
        $mcp->registerPackage(
            'your-package',                    // Package identifier
            __DIR__.'/../docs',                // Path to docs directory
            'Description of your package'      // Brief description
        );

        // Optionally register your README
        $mcp->registerReadme('your-package', __DIR__.'/../README.md');
    }
}
```

### Documentation Structure

Your docs directory should contain markdown files:

```
your-package/
├── docs/
│   ├── getting-started.md
│   ├── installation.md
│   ├── configuration.md
│   ├── components/
│   │   ├── button.md
│   │   └── input.md
│   └── api-reference.md
└── README.md
```

The MCP server will:
- Index all `.md` files in the docs directory
- Parse frontmatter for metadata (title, description, keywords)
- Make content searchable via the `search_docs` tool

### Frontmatter Support

Add frontmatter to your markdown files for better search results:

```markdown
---
title: Button Component
description: A flexible button component with multiple variants
keywords:
  - button
  - click
  - submit
  - action
---

# Button Component

Content here...
```

## Using the Facade

You can also interact with the MCP registry programmatically:

```php
use Accelade\Facades\AcceladeMcp;

// Register a package
AcceladeMcp::registerPackage('my-package', '/path/to/docs', 'My package description');

// Get all registered packages
$packages = AcceladeMcp::getPackages();

// Search documentation
$results = AcceladeMcp::search('modal component');

// Search within a specific package
$results = AcceladeMcp::search('text entry', 'infolists');
```

## Configuration

The MCP server configuration is stored in `.mcp.json` at your project root:

```json
{
    "mcpServers": {
        "accelade": {
            "command": "php",
            "args": ["artisan", "mcp:start", "accelade"],
            "cwd": "/path/to/your/project"
        }
    }
}
```

## Registered Packages

When you install multiple Accelade packages, they automatically register with the MCP server:

| Package | Description |
|---------|-------------|
| `accelade` | Core components: modals, toggles, links, transitions, etc. |
| `infolists` | Read-only data display: text, badges, images, ratings, etc. |
| `forms` | Form inputs and validation |
| `schemas` | Schema builders for forms and infolists |

## Example: Complete Package Registration

Here's a complete example of how the Infolists package registers with MCP:

```php
<?php

namespace Accelade\Infolists;

use Accelade\Mcp\McpRegistry;
use Illuminate\Support\ServiceProvider;

class InfolistsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->bound('accelade.mcp')) {
            $this->registerMcp();
        }
    }

    protected function registerMcp(): void
    {
        /** @var McpRegistry $mcp */
        $mcp = $this->app->make('accelade.mcp');

        // Register package with description
        $mcp->registerPackage(
            'infolists',
            __DIR__.'/../docs',
            'Display read-only information with Filament-compatible API - text, badges, images, icons, colors, ratings, and more'
        );

        // Register README for overview searches
        $mcp->registerReadme('infolists', __DIR__.'/../README.md');
    }
}
```

## Troubleshooting

### MCP server not found

If your AI assistant can't find the MCP server:

1. Ensure you've run `php artisan accelade:mcp`
2. Check that `.mcp.json` exists in your project root
3. Restart your AI assistant application
4. Verify the paths in `.mcp.json` are correct

### Documentation not appearing in search

If your package's documentation isn't being found:

1. Verify your service provider is registered
2. Check that `$this->app->bound('accelade.mcp')` returns true
3. Ensure your docs directory path is correct
4. Verify markdown files have proper frontmatter

### Server startup errors

If the MCP server fails to start:

```bash
# Test the server manually
php artisan mcp:start accelade

# Check routes/ai.php for syntax errors
php artisan route:list --path=ai
```

## Best Practices

1. **Write searchable documentation**: Use clear headings, include code examples, and add relevant keywords in frontmatter.

2. **Keep docs up to date**: The MCP server serves what's in your docs directory - outdated docs lead to incorrect AI responses.

3. **Use descriptive package descriptions**: The description you provide in `registerPackage()` helps AI assistants understand when to search your package.

4. **Include a README**: Register your README file so AI assistants can get a quick overview of your package.

5. **Organize docs logically**: Group related documentation files in subdirectories for better navigation.
