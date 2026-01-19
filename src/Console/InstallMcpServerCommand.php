<?php

declare(strict_types=1);

namespace Accelade\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallMcpServerCommand extends Command
{
    protected $signature = 'accelade:mcp';

    protected $description = 'Install the Accelade MCP server configuration';

    public function handle(): int
    {
        $this->info('Installing Accelade MCP server...');
        $this->newLine();

        // Step 1: Publish routes/ai.php if it doesn't exist
        $this->publishAiRoutes();

        // Step 2: Register MCP server in routes/ai.php
        $this->registerMcpServer();

        // Step 3: Create/Update .mcp.json for MCP clients
        $this->createMcpConfig();

        $this->newLine();
        $this->components->info('Accelade MCP server installed successfully!');
        $this->newLine();

        $this->components->bulletList([
            'Server name: accelade',
            'Server type: Local (Artisan command)',
            'Tools available: search_docs, list_components, component_info',
        ]);

        $this->newLine();
        $this->info('Available tools:');
        $this->components->bulletList([
            'search_docs - Search documentation across all Accelade ecosystem packages',
            'list_components - List all available components and documentation sections',
            'component_info - Get detailed information about a specific component',
        ]);

        $this->newLine();
        $this->info('Registered packages will have their documentation automatically searchable.');
        $this->newLine();

        $this->components->warn('Next steps:');
        $this->components->bulletList([
            'Restart your AI agent application (Claude Code, Claude Desktop, etc.)',
            'The accelade server should now be available',
            'Other Accelade packages (infolists, forms, etc.) automatically register with MCP',
        ]);

        return self::SUCCESS;
    }

    protected function publishAiRoutes(): void
    {
        $aiRoutesPath = base_path('routes/ai.php');

        if (! File::exists($aiRoutesPath)) {
            $this->info('Publishing routes/ai.php...');
            $this->call('vendor:publish', ['--tag' => 'ai-routes']);
            $this->info('Published routes/ai.php');
        } else {
            $this->info('routes/ai.php already exists');
        }
    }

    protected function registerMcpServer(): void
    {
        $aiRoutesPath = base_path('routes/ai.php');

        if (! File::exists($aiRoutesPath)) {
            $this->error('routes/ai.php not found. Cannot register MCP server.');

            return;
        }

        $content = File::get($aiRoutesPath);

        // Check if already registered
        if (str_contains($content, "'accelade'") && str_contains($content, 'AcceladeMcpServer')) {
            $this->info('MCP server already registered in routes/ai.php');

            return;
        }

        // Add the necessary use statements
        $useStatements = "use Accelade\Mcp\AcceladeMcpServer;\n";

        // Check if Laravel\Mcp\Facades\Mcp is already imported
        if (! str_contains($content, 'use Laravel\Mcp\Facades\Mcp;')) {
            $useStatements = "use Laravel\Mcp\Facades\Mcp;\n".$useStatements;
        }

        // Find the position to insert use statements (after <?php and namespace if present)
        $lines = explode("\n", $content);
        $insertPosition = 0;

        foreach ($lines as $index => $line) {
            if (str_starts_with(trim($line), 'use ')) {
                $insertPosition = $index;

                break;
            }
            if (str_starts_with(trim($line), '<?php')) {
                $insertPosition = $index + 1;
            }
        }

        // Insert use statements
        if ($insertPosition > 0) {
            array_splice($lines, $insertPosition, 0, rtrim($useStatements));
            $content = implode("\n", $lines);
        } else {
            // If no use statements found, add after <?php
            $content = str_replace("<?php\n", "<?php\n\n".$useStatements, $content);
        }

        // Add the MCP server registration at the end of the file
        $serverRegistration = <<<'PHP'

// Accelade MCP Server
// Provides documentation search and component information for the Accelade ecosystem
// Tools: search_docs, list_components, component_info
Mcp::local('accelade', AcceladeMcpServer::class);

PHP;

        $content = rtrim($content).$serverRegistration;

        File::put($aiRoutesPath, $content);

        $this->info('Registered MCP server in routes/ai.php');
    }

    protected function createMcpConfig(): void
    {
        $mcpConfigPath = base_path('.mcp.json');

        // Read existing config or create new one
        $config = [];
        if (File::exists($mcpConfigPath)) {
            $config = json_decode(File::get($mcpConfigPath), true) ?? [];
        }

        // Add accelade server
        if (! isset($config['mcpServers'])) {
            $config['mcpServers'] = [];
        }

        $config['mcpServers']['accelade'] = [
            'command' => 'php',
            'args' => [
                'artisan',
                'mcp:start',
                'accelade',
            ],
        ];

        // Write config
        File::put(
            $mcpConfigPath,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('Updated .mcp.json');
    }
}
