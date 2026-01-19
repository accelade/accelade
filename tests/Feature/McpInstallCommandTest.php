<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

describe('MCP Install Command', function () {
    beforeEach(function () {
        // Set up test paths
        $this->aiRoutesPath = base_path('routes/ai.php');
        $this->mcpConfigPath = base_path('.mcp.json');

        // Ensure routes directory exists
        if (! File::isDirectory(base_path('routes'))) {
            File::makeDirectory(base_path('routes'), 0755, true);
        }

        // Backup original files if they exist
        if (File::exists($this->aiRoutesPath)) {
            $this->originalAiRoutes = File::get($this->aiRoutesPath);
        }
        if (File::exists($this->mcpConfigPath)) {
            $this->originalMcpConfig = File::get($this->mcpConfigPath);
        }

        // Delete test files to start fresh
        File::delete($this->aiRoutesPath);
        File::delete($this->mcpConfigPath);
    });

    afterEach(function () {
        // Clean up test files
        File::delete($this->aiRoutesPath);
        File::delete($this->mcpConfigPath);

        // Restore original files if they existed
        if (isset($this->originalAiRoutes)) {
            File::put($this->aiRoutesPath, $this->originalAiRoutes);
        }
        if (isset($this->originalMcpConfig)) {
            File::put($this->mcpConfigPath, $this->originalMcpConfig);
        }
    });

    it('can run accelade:mcp command', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp')
            ->assertSuccessful();
    });

    it('registers mcp server in routes/ai.php', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp');

        expect(File::exists($this->aiRoutesPath))->toBeTrue();

        $content = File::get($this->aiRoutesPath);
        expect($content)->toContain('AcceladeMcpServer');
        expect($content)->toContain("Mcp::local('accelade'");
    });

    it('creates or updates .mcp.json', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp');

        expect(File::exists($this->mcpConfigPath))->toBeTrue();

        $config = json_decode(File::get($this->mcpConfigPath), true);
        expect($config)->toHaveKey('mcpServers');
        expect($config['mcpServers'])->toHaveKey('accelade');
        expect($config['mcpServers']['accelade']['command'])->toBe('php');
        expect($config['mcpServers']['accelade']['args'])->toContain('mcp:start');
        expect($config['mcpServers']['accelade']['args'])->toContain('accelade');
    });

    it('does not duplicate mcp server registration', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        // Run twice
        $this->artisan('accelade:mcp');
        $this->artisan('accelade:mcp');

        $content = File::get($this->aiRoutesPath);

        // Count occurrences of AcceladeMcpServer
        $count = substr_count($content, 'AcceladeMcpServer');
        expect($count)->toBe(2); // One in use statement, one in Mcp::local
    });

    it('outputs success message', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp')
            ->expectsOutputToContain('Accelade MCP server installed successfully');
    });

    it('lists available tools', function () {
        // Create a minimal routes/ai.php file for testing
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp')
            ->expectsOutputToContain('search_docs');
    });

    it('adds use statements correctly', function () {
        // Create a minimal routes/ai.php file with existing use statement
        File::put($this->aiRoutesPath, "<?php\n\nuse App\\Models\\User;\n\n");

        $this->artisan('accelade:mcp');

        $content = File::get($this->aiRoutesPath);
        expect($content)->toContain('use Laravel\\Mcp\\Facades\\Mcp;');
        expect($content)->toContain('use Accelade\\Mcp\\AcceladeMcpServer;');
    });

    it('preserves existing content in routes/ai.php', function () {
        // Create routes/ai.php with existing content
        $existingContent = "<?php\n\nuse App\\Models\\User;\n\n// Existing comment\n";
        File::put($this->aiRoutesPath, $existingContent);

        $this->artisan('accelade:mcp');

        $content = File::get($this->aiRoutesPath);
        expect($content)->toContain('// Existing comment');
        expect($content)->toContain('AcceladeMcpServer');
    });

    it('merges with existing .mcp.json', function () {
        // Create existing .mcp.json with another server
        $existingConfig = [
            'mcpServers' => [
                'other-server' => [
                    'command' => 'node',
                    'args' => ['server.js'],
                ],
            ],
        ];
        File::put($this->mcpConfigPath, json_encode($existingConfig, JSON_PRETTY_PRINT));
        File::put($this->aiRoutesPath, "<?php\n\n");

        $this->artisan('accelade:mcp');

        $config = json_decode(File::get($this->mcpConfigPath), true);
        expect($config['mcpServers'])->toHaveKey('other-server');
        expect($config['mcpServers'])->toHaveKey('accelade');
    });
});
