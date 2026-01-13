{{-- Content Component Section - Framework Agnostic --}}

<!-- Demo: Content Component -->
<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Content Component</h3>
    </div>
    <p class="text-sm mb-4" style="color: var(--docs-text-muted);">
        Render pre-rendered HTML (like Markdown) using <code class="px-1.5 py-0.5 rounded text-sm border border-[var(--docs-border)]" style="background: var(--docs-bg);">&lt;x-accelade::content&gt;</code>.
    </p>

    @php
        // Simulated pre-rendered Markdown content
        $markdownHtml = '<h3 style="color: var(--docs-text); margin-bottom: 0.5rem;">Welcome to Accelade</h3>
<p style="color: var(--docs-text-muted); margin-bottom: 0.5rem;">This is <strong style="color: var(--docs-text);">pre-rendered HTML</strong> content, perfect for:</p>
<ul style="color: var(--docs-text-muted); padding-left: 1.5rem; list-style: disc;">
<li>Markdown converted to HTML</li>
<li>Rich text from a CMS</li>
<li>Syntax-highlighted code</li>
</ul>
<blockquote style="border-left: 3px solid var(--docs-border); padding-left: 1rem; margin-top: 1rem;">
<p style="color: var(--docs-text-muted); font-style: italic;">The content is rendered without any interpolation, making it safe for static content.</p>
</blockquote>';

        $codeHtml = '<pre class="bg-slate-900 text-slate-100 p-4 rounded-lg overflow-x-auto"><code class="language-php">// Example PHP code
&lt;?php

namespace App\\Http\\Controllers;

class PostController extends Controller
{
    public function show(Post $post)
    {
        return view(\'posts.show\', [
            \'content\' => Str::markdown($post->body),
        ]);
    }
}</code></pre>';

        $quoteHtml = '<p class="text-lg italic" style="color: var(--docs-text);">"The best way to predict the future is to invent it."</p><footer class="text-sm mt-2" style="color: var(--docs-text-muted);">— Alan Kay</footer>';
    @endphp

    <div class="space-y-4 mb-4">
        <!-- Markdown Content -->
        <div class="rounded-xl p-4 border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Markdown</span>
                Article Content
            </h4>
            <div class="rounded-lg p-4 border border-[var(--docs-border)]" style="background: var(--docs-bg);">
                <x-accelade::content
                    as="article"
                    class="max-w-none"
                    :html="$markdownHtml"
                    data-testid="content-markdown"
                />
            </div>
        </div>

        <!-- Code Block -->
        <div class="rounded-xl p-4 border border-blue-500/30" style="background: rgba(59, 130, 246, 0.1);">
            <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
                <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Code</span>
                Syntax Highlighted
            </h4>
            <x-accelade::content
                as="div"
                class="text-sm"
                :html="$codeHtml"
                data-testid="content-code"
            />
        </div>

        <!-- Quote Block -->
        <div class="rounded-xl p-4 border border-teal-500/30" style="background: rgba(20, 184, 166, 0.1);">
            <h4 class="font-medium mb-4" style="color: var(--docs-text);">Blockquote with Custom Wrapper</h4>
            <x-accelade::content
                as="blockquote"
                class="border-l-4 border-emerald-500 pl-4"
                :html="$quoteHtml"
                data-testid="content-quote"
            />
        </div>
    </div>

    <x-accelade::code-block language="blade" filename="content.blade.php">
&lt;!-- Basic usage --&gt;
&lt;x-accelade::content :html="$renderedMarkdown" /&gt;

&lt;!-- With custom wrapper and styling --&gt;
&lt;x-accelade::content
    as="article"
    class="prose dark:prose-invert"
    :html="$html"
/&gt;

&lt;!-- Blockquote wrapper --&gt;
&lt;x-accelade::content
    as="blockquote"
    class="border-l-4 pl-4"
    :html="$quote"
/&gt;
    </x-accelade::code-block>
</section>
