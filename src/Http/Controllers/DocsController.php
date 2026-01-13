<?php

declare(strict_types=1);

namespace Accelade\Http\Controllers;

use Accelade\Docs\DocsRegistry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class DocsController extends Controller
{
    public function __construct(
        protected DocsRegistry $registry,
    ) {}

    /**
     * Show a documentation section.
     */
    public function show(Request $request, string $section): View
    {
        $framework = $request->query('framework', 'vanilla');

        // Validate framework
        if (! $this->registry->isValidFramework($framework)) {
            $framework = 'vanilla';
        }

        // Get prefix for framework
        $prefix = $this->registry->getFrameworkPrefix($framework);

        // Get section from registry
        $docSection = $this->registry->getSection($section);

        // Check if section has a demo
        $hasDemo = $docSection->hasDemo ?? false;

        // Get documentation content
        $documentation = $this->getDocumentation($section);

        // Determine which view to render
        $viewName = $docSection?->getViewName() ?? 'accelade::docs.sections.getting-started';

        return view($viewName, [
            'framework' => $framework,
            'prefix' => $prefix,
            'section' => $section,
            'documentation' => $documentation,
            'hasDemo' => $hasDemo,
            'docSection' => $docSection,
            'navigation' => $this->registry->getNavigation(),
        ]);
    }

    /**
     * Get the documentation content for a section.
     */
    protected function getDocumentation(string $section): ?string
    {
        $docSection = $this->registry->getSection($section);

        if ($docSection === null) {
            return null;
        }

        $docPath = $docSection->getMarkdownPath($this->registry);

        if ($docPath === null || ! File::exists($docPath)) {
            return null;
        }

        $markdown = File::get($docPath);

        return $this->parseMarkdown($markdown);
    }

    /**
     * Parse markdown to HTML.
     */
    protected function parseMarkdown(string $markdown): string
    {
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        $converter = new MarkdownConverter($environment);

        $html = $converter->convert($markdown)->getContent();

        // Transform code blocks to match the code-block component style
        return $this->transformCodeBlocks($html);
    }

    /**
     * Transform code blocks to match the x-accelade::code-block component style.
     */
    protected function transformCodeBlocks(string $html): string
    {
        // Match <pre><code class="language-xxx">...</code></pre> patterns
        $pattern = '/<pre><code class="language-([^"]*)">(.*?)<\/code><\/pre>/s';

        return preg_replace_callback($pattern, function ($matches) {
            $language = $matches[1] ?? 'text';
            $code = $matches[2];
            $id = 'code-'.bin2hex(random_bytes(4));

            // Map language names to display labels
            $languageLabels = [
                'php' => 'PHP',
                'blade' => 'Blade',
                'html' => 'HTML',
                'javascript' => 'JavaScript',
                'js' => 'JavaScript',
                'css' => 'CSS',
                'bash' => 'Bash',
                'shell' => 'Shell',
                'json' => 'JSON',
                'xml' => 'XML',
                'sql' => 'SQL',
                'yaml' => 'YAML',
                'vue' => 'Vue',
                'typescript' => 'TypeScript',
                'ts' => 'TypeScript',
            ];

            $label = $languageLabels[$language] ?? strtoupper($language);

            // Map to Prism language class
            $prismLanguage = match ($language) {
                'blade' => 'php',
                'html', 'xml' => 'markup',
                'js' => 'javascript',
                'ts' => 'typescript',
                'shell' => 'bash',
                default => $language,
            };

            return <<<HTML
<div class="code-block-wrapper rounded-xl overflow-hidden shadow-lg my-4" dir="ltr" style="background:#1e293b;border:1px solid #334155;" data-code-block="{$id}">
    <div class="flex items-center justify-between px-4 py-3" style="background:#1e293b;border-bottom:1px solid #334155;">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" style="background:#ef4444;"></span>
                <span class="w-3 h-3 rounded-full" style="background:#eab308;"></span>
                <span class="w-3 h-3 rounded-full" style="background:#22c55e;"></span>
            </div>
            <span class="text-xs font-medium uppercase tracking-wider" style="color:#94a3b8;">{$label}</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="code-copy-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all" style="color:#94a3b8;background:rgba(51,65,85,0.5);" data-code-target="{$id}" title="Copy code">
                <svg class="copy-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <svg class="check-icon w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="copy-text">Copy</span>
            </button>
            <button type="button" class="code-download-btn flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-all" style="color:#94a3b8;background:rgba(51,65,85,0.5);" data-code-target="{$id}" title="Download as image">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Image</span>
            </button>
        </div>
    </div>
    <div class="code-content overflow-x-auto" style="background:#1e293b;">
        <pre id="{$id}" class="language-{$prismLanguage}" style="margin:0;border-radius:0;background:#1e293b !important;"><code class="language-{$prismLanguage}" style="background:transparent !important;">{$code}</code></pre>
    </div>
</div>
HTML;
        }, $html) ?? $html;
    }

    /**
     * Search documentation.
     */
    public function search(Request $request): array
    {
        $query = strtolower($request->query('q', ''));

        if (strlen($query) < 2) {
            return ['results' => []];
        }

        $results = [];

        foreach ($this->registry->getAllSections() as $slug => $section) {
            $docPath = $section->getMarkdownPath($this->registry);

            // Check section metadata
            $matchesMetadata = str_contains(strtolower($slug), $query)
                || str_contains(strtolower($section->label), $query)
                || str_contains(strtolower($section->description ?? ''), $query)
                || $this->matchesKeywords($query, $section->keywords);

            // Check content if file exists
            $matchesContent = false;
            if ($docPath !== null && File::exists($docPath)) {
                $content = strtolower(File::get($docPath));
                $matchesContent = str_contains($content, $query);
            }

            if ($matchesMetadata || $matchesContent) {
                $results[] = [
                    'section' => $slug,
                    'label' => $section->label,
                    'hasDemo' => $section->hasDemo,
                    'package' => $section->package,
                    'description' => $section->description,
                ];
            }
        }

        return ['results' => $results];
    }

    /**
     * Check if query matches any keywords.
     *
     * @param  array<int, string>  $keywords
     */
    protected function matchesKeywords(string $query, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains(strtolower($keyword), $query)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get navigation structure for the docs sidebar.
     */
    public function navigation(): array
    {
        return [
            'navigation' => $this->registry->getNavigation(),
            'frameworks' => $this->registry->getFrameworkPrefixes(),
        ];
    }
}
