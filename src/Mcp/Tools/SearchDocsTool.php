<?php

declare(strict_types=1);

namespace Accelade\Mcp\Tools;

use Accelade\Mcp\McpRegistry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchDocsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Search documentation across all Accelade ecosystem packages to understand features, components, and usage';

    public function __construct(
        protected McpRegistry $registry
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $query = (string) $request->string('query');
        $package = (string) $request->string('package', '');

        // Collect all documentation files
        $docFiles = $this->getDocumentationFiles($package);

        if (empty($docFiles)) {
            return Response::text('No documentation found. Make sure packages are registered with AcceladeMCP.');
        }

        // Search through documentation
        $results = $this->searchDocumentation($docFiles, $query);

        if (empty($results)) {
            return Response::text("No documentation found matching '{$query}'.");
        }

        // Format results
        $output = "Documentation Search Results for: {$query}\n\n";
        $output .= 'Found '.count($results)." relevant section(s):\n\n";

        foreach ($results as $result) {
            $output .= "📦 Package: {$result['package']}\n";
            $output .= "📄 File: {$result['file']}\n";
            $output .= str_repeat('=', 60)."\n\n";
            $output .= $result['content']."\n\n";
            $output .= str_repeat('-', 60)."\n\n";
        }

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        $packages = $this->registry->getPackages();

        return [
            'query' => $schema->string()
                ->description('Search query (e.g., "modal component", "state management", "infolist entry", "form validation")')
                ->required(),
            'package' => $schema->string()
                ->description('Optional: Filter by package name (e.g., "accelade", "infolists", "forms"). Leave empty to search all packages.')
                ->enum(array_merge([''], $packages)),
        ];
    }

    /**
     * Get documentation files, optionally filtered by package.
     *
     * @return array<int, array{package: string, path: string, name: string, content: string}>
     */
    protected function getDocumentationFiles(string $package = ''): array
    {
        $allFiles = $this->registry->getAllDocumentationFiles();

        if (empty($package)) {
            return $allFiles;
        }

        return array_filter($allFiles, fn ($file) => $file['package'] === $package);
    }

    /**
     * Search documentation files for relevant content.
     *
     * @param  array<int, array{package: string, path: string, name: string, content: string}>  $files
     * @return array<int, array{package: string, file: string, content: string, relevance: int}>
     */
    protected function searchDocumentation(array $files, string $query): array
    {
        $results = [];
        $queryLower = strtolower($query);
        $keywords = array_filter(explode(' ', $queryLower));

        foreach ($files as $file) {
            $content = $file['content'];
            $contentLower = strtolower($content);

            // Check if any keyword matches
            $matchCount = 0;
            foreach ($keywords as $keyword) {
                if (stripos($contentLower, $keyword) !== false) {
                    $matchCount++;
                }
            }

            // If matches found, extract relevant sections
            if ($matchCount > 0) {
                $sections = $this->extractRelevantSections($content, $keywords);

                foreach ($sections as $section) {
                    $results[] = [
                        'package' => $file['package'],
                        'file' => $file['name'],
                        'content' => $section,
                        'relevance' => $matchCount,
                    ];
                }
            }
        }

        // Sort by relevance (highest first)
        usort($results, fn ($a, $b) => $b['relevance'] <=> $a['relevance']);

        // Limit to top 5 most relevant results
        return array_slice($results, 0, 5);
    }

    /**
     * Extract relevant sections from content based on keywords.
     *
     * @param  array<int, string>  $keywords
     * @return array<int, string>
     */
    protected function extractRelevantSections(string $content, array $keywords): array
    {
        $sections = [];
        $lines = explode("\n", $content);

        // Split by markdown headers to get sections
        $currentSection = '';
        $currentHeader = '';
        $inRelevantSection = false;

        foreach ($lines as $line) {
            // Check if line is a header
            if (preg_match('/^#+\s+(.+)$/', $line, $matches)) {
                // Save previous section if it was relevant
                if ($inRelevantSection && ! empty(trim($currentSection))) {
                    $sections[] = trim($currentHeader."\n\n".$currentSection);
                }

                // Start new section
                $currentHeader = $line;
                $currentSection = '';

                // Check if header is relevant
                $headerLower = strtolower($matches[1]);
                $inRelevantSection = false;
                foreach ($keywords as $keyword) {
                    if (stripos($headerLower, $keyword) !== false) {
                        $inRelevantSection = true;

                        break;
                    }
                }
            } else {
                // Add to current section
                $currentSection .= $line."\n";

                // Check if content line is relevant
                if (! $inRelevantSection) {
                    $lineLower = strtolower($line);
                    foreach ($keywords as $keyword) {
                        if (stripos($lineLower, $keyword) !== false) {
                            $inRelevantSection = true;

                            break;
                        }
                    }
                }
            }
        }

        // Save last section if relevant
        if ($inRelevantSection && ! empty(trim($currentSection))) {
            $sections[] = trim($currentHeader."\n\n".$currentSection);
        }

        // If no sections found, return matching lines with context
        if (empty($sections)) {
            $matchingLines = [];
            foreach ($lines as $index => $line) {
                $lineLower = strtolower($line);
                foreach ($keywords as $keyword) {
                    if (stripos($lineLower, $keyword) !== false) {
                        // Add context (2 lines before and after)
                        $start = max(0, $index - 2);
                        $end = min(count($lines) - 1, $index + 2);

                        for ($i = $start; $i <= $end; $i++) {
                            if (! in_array($lines[$i], $matchingLines, true)) {
                                $matchingLines[] = $lines[$i];
                            }
                        }

                        if (count($matchingLines) >= 50) {
                            break 2;
                        }

                        break;
                    }
                }
            }

            if (! empty($matchingLines)) {
                $sections[] = implode("\n", $matchingLines);
            }
        }

        return $sections;
    }
}
