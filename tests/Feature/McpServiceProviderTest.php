<?php

declare(strict_types=1);

use Accelade\Mcp\McpRegistry;
use Accelade\Mcp\Tools\ComponentInfoTool;
use Accelade\Mcp\Tools\ListComponentsTool;
use Accelade\Mcp\Tools\SearchDocsTool;

describe('MCP Service Provider', function () {
    it('registers the mcp registry singleton', function () {
        expect(app()->bound('accelade.mcp'))->toBeTrue();
        expect(app('accelade.mcp'))->toBeInstanceOf(McpRegistry::class);
    });

    it('returns same instance for mcp singleton', function () {
        $instance1 = app('accelade.mcp');
        $instance2 = app('accelade.mcp');

        expect($instance1)->toBe($instance2);
    });

    it('can resolve McpRegistry via class binding', function () {
        $registry = app(McpRegistry::class);

        expect($registry)->toBeInstanceOf(McpRegistry::class);
    });

    it('registers default mcp tools', function () {
        /** @var McpRegistry $registry */
        $registry = app('accelade.mcp');
        $tools = $registry->getTools();

        expect($tools)->toContain(SearchDocsTool::class);
        expect($tools)->toContain(ListComponentsTool::class);
        expect($tools)->toContain(ComponentInfoTool::class);
    });

    it('registers accelade package with mcp', function () {
        /** @var McpRegistry $registry */
        $registry = app('accelade.mcp');

        expect($registry->hasPackage('accelade'))->toBeTrue();
        expect($registry->getDocsPath('accelade'))->not->toBeNull();
        expect($registry->getPackageDescription('accelade'))->not->toBeEmpty();
    });

    it('registers accelade readme with mcp', function () {
        /** @var McpRegistry $registry */
        $registry = app('accelade.mcp');

        $readmePath = $registry->getReadmePath('accelade');

        expect($readmePath)->not->toBeNull();
        expect($readmePath)->toContain('README.md');
    });
});

describe('AcceladeMCP Facade', function () {
    it('can use facade to register tools', function () {
        $facade = \Accelade\Facades\AcceladeMCP::getFacadeRoot();

        expect($facade)->toBeInstanceOf(McpRegistry::class);
    });
});
