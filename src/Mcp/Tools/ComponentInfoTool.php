<?php

declare(strict_types=1);

namespace Accelade\Mcp\Tools;

use Accelade\Docs\DocsRegistry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ComponentInfoTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Get detailed information about a specific component or documentation section, including its full markdown documentation';

    public function __construct(
        protected DocsRegistry $docsRegistry
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $slug = (string) $request->string('slug');

        $section = $this->docsRegistry->getSection($slug);

        if ($section === null) {
            // Try to find similar sections
            $allSections = $this->docsRegistry->getAllSections();
            $suggestions = [];

            foreach ($allSections as $s) {
                if (stripos($s->slug, $slug) !== false || stripos($s->label, $slug) !== false) {
                    $suggestions[] = $s->slug;
                }
            }

            $output = "Section '{$slug}' not found.\n\n";

            if (! empty($suggestions)) {
                $output .= "Did you mean one of these?\n";
                foreach ($suggestions as $suggestion) {
                    $output .= "  - {$suggestion}\n";
                }
            } else {
                $output .= 'Use the list_components tool to see all available sections.';
            }

            return Response::text($output);
        }

        $output = "Component: {$section->label}\n";
        $output .= str_repeat('=', 60)."\n\n";

        $output .= "**Slug:** {$section->slug}\n";
        $output .= "**Package:** {$section->package}\n";
        $output .= '**Has Demo:** '.($section->hasDemo ? 'Yes' : 'No')."\n";

        if ($section->icon) {
            $output .= "**Icon:** {$section->icon}\n";
        }

        if ($section->description) {
            $output .= "**Description:** {$section->description}\n";
        }

        if (! empty($section->keywords)) {
            $output .= '**Keywords:** '.implode(', ', $section->keywords)."\n";
        }

        $output .= "\n";

        // Get the markdown documentation
        $markdownPath = $section->getMarkdownPath($this->docsRegistry);

        if ($markdownPath !== null && file_exists($markdownPath)) {
            $output .= str_repeat('-', 60)."\n";
            $output .= "**Documentation:**\n\n";
            $output .= file_get_contents($markdownPath);
        } else {
            $output .= "\n*No markdown documentation file found.*\n";
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
        $allSections = $this->docsRegistry->getAllSections();
        $slugs = array_keys($allSections);

        return [
            'slug' => $schema->string()
                ->description('The slug of the component/section (e.g., "modal", "toggle", "infolists-text-entry")')
                ->enum($slugs)
                ->required(),
        ];
    }
}
