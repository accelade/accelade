<?php

declare(strict_types=1);

namespace Accelade\Mcp\Tools;

use Accelade\Docs\DocsRegistry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListComponentsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'List all available components and documentation sections in the Accelade ecosystem';

    public function __construct(
        protected DocsRegistry $docsRegistry
    ) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $group = (string) $request->string('group', '');
        $withDemo = $request->boolean('with_demo', false);

        $navigation = $this->docsRegistry->getNavigation();

        $output = "Accelade Ecosystem Components\n";
        $output .= str_repeat('=', 60)."\n\n";

        foreach ($navigation as $navGroup) {
            // Filter by group if specified
            if (! empty($group) && $navGroup['key'] !== $group) {
                continue;
            }

            $output .= "{$navGroup['icon']} **{$navGroup['label']}**\n";
            $output .= str_repeat('-', 40)."\n";

            // Direct items in the group
            foreach ($navGroup['items'] as $item) {
                if ($withDemo && ! $item['hasDemo']) {
                    continue;
                }

                $demoIndicator = $item['hasDemo'] ? ' [Demo]' : '';
                $icon = $item['icon'] ?? '•';
                $output .= "  {$icon} {$item['label']}{$demoIndicator}\n";
                $output .= "    Slug: {$item['slug']}\n";
            }

            // Subgroups
            foreach ($navGroup['subgroups'] as $subgroup) {
                $output .= "\n  📁 {$subgroup['label']}\n";

                foreach ($subgroup['items'] as $item) {
                    if ($withDemo && ! $item['hasDemo']) {
                        continue;
                    }

                    $demoIndicator = $item['hasDemo'] ? ' [Demo]' : '';
                    $icon = $item['icon'] ?? '•';
                    $output .= "    {$icon} {$item['label']}{$demoIndicator}\n";
                    $output .= "      Slug: {$item['slug']}\n";
                }
            }

            $output .= "\n";
        }

        // Add summary
        $allSections = $this->docsRegistry->getAllSections();
        $sectionsWithDemo = $this->docsRegistry->getSectionsWithDemo();

        $output .= str_repeat('=', 60)."\n";
        $output .= "Summary:\n";
        $output .= '  Total sections: '.count($allSections)."\n";
        $output .= '  Sections with demos: '.count($sectionsWithDemo)."\n";
        $output .= '  Navigation groups: '.count($navigation)."\n";

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        $groups = $this->docsRegistry->getGroups();
        $groupKeys = array_keys($groups);

        return [
            'group' => $schema->string()
                ->description('Optional: Filter by navigation group (e.g., "core", "getting-started", "infolists")')
                ->enum(array_merge([''], $groupKeys)),
            'with_demo' => $schema->boolean()
                ->description('Only show components that have interactive demos'),
        ];
    }
}
