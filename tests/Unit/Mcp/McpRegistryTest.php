<?php

declare(strict_types=1);

use Accelade\Mcp\McpRegistry;
use Accelade\Mcp\Tools\SearchDocsTool;

describe('McpRegistry', function () {
    beforeEach(function () {
        $this->registry = new McpRegistry;
    });

    it('can register a tool', function () {
        $this->registry->registerTool(SearchDocsTool::class);

        expect($this->registry->getTools())->toContain(SearchDocsTool::class);
    });

    it('does not duplicate tools', function () {
        $this->registry->registerTool(SearchDocsTool::class);
        $this->registry->registerTool(SearchDocsTool::class);

        expect($this->registry->getTools())->toHaveCount(1);
    });

    it('can register multiple tools at once', function () {
        $this->registry->registerTools([
            SearchDocsTool::class,
            'FakeTool',
        ]);

        expect($this->registry->getTools())->toHaveCount(2);
    });

    it('can register a package', function () {
        $this->registry->registerPackage('test-package', '/path/to/docs', 'Test package description');

        expect($this->registry->hasPackage('test-package'))->toBeTrue();
        expect($this->registry->getDocsPath('test-package'))->toBe('/path/to/docs');
        expect($this->registry->getPackageDescription('test-package'))->toBe('Test package description');
    });

    it('trims trailing slashes from docs path', function () {
        $this->registry->registerPackage('test', '/path/to/docs/');

        expect($this->registry->getDocsPath('test'))->toBe('/path/to/docs');
    });

    it('can register a README path', function () {
        $this->registry->registerReadme('test-package', '/path/to/README.md');

        expect($this->registry->getReadmePath('test-package'))->toBe('/path/to/README.md');
    });

    it('returns null for non-existent package', function () {
        expect($this->registry->getDocsPath('non-existent'))->toBeNull();
        expect($this->registry->getPackageDescription('non-existent'))->toBeNull();
        expect($this->registry->getReadmePath('non-existent'))->toBeNull();
    });

    it('can get all packages', function () {
        $this->registry->registerPackage('package1', '/path1');
        $this->registry->registerPackage('package2', '/path2');

        expect($this->registry->getPackages())->toBe(['package1', 'package2']);
    });

    it('returns fluent interface for chaining', function () {
        $result = $this->registry
            ->registerTool(SearchDocsTool::class)
            ->registerPackage('test', '/path')
            ->registerReadme('test', '/readme');

        expect($result)->toBeInstanceOf(McpRegistry::class);
    });
});

describe('McpRegistry Documentation Files', function () {
    beforeEach(function () {
        $this->registry = new McpRegistry;
    });

    it('returns empty array when no packages registered', function () {
        $files = $this->registry->getAllDocumentationFiles();

        expect($files)->toBeArray();
        expect($files)->toBeEmpty();
    });

    it('can get documentation files from registered package', function () {
        // Register the accelade package docs path (which exists)
        $docsPath = dirname(__DIR__, 3).'/docs';

        if (! is_dir($docsPath)) {
            $this->markTestSkipped('Docs directory not found');
        }

        $this->registry->registerPackage('accelade', $docsPath);

        $files = $this->registry->getAllDocumentationFiles();

        expect($files)->toBeArray();
        expect($files)->not->toBeEmpty();

        // Check structure of first file
        $firstFile = $files[0];
        expect($firstFile)->toHaveKeys(['package', 'path', 'name', 'content']);
        expect($firstFile['package'])->toBe('accelade');
    });
});
