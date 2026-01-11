{{-- Content Component Section - Framework Agnostic --}}

<!-- Demo: Content Component -->
<section class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 p-8 mb-8 border border-slate-100">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
        <h2 class="text-2xl font-semibold text-slate-800">Content Component</h2>
    </div>
    <p class="text-slate-500 mb-6 ml-6">
        Render pre-rendered HTML (like Markdown) using <code class="bg-slate-100 px-1.5 py-0.5 rounded text-sm">&lt;x-accelade::content&gt;</code>.
    </p>

    @php
        // Simulated pre-rendered Markdown content
        $markdownHtml = '<h3>Welcome to Accelade</h3>
<p>This is <strong>pre-rendered HTML</strong> content, perfect for:</p>
<ul>
<li>Markdown converted to HTML</li>
<li>Rich text from a CMS</li>
<li>Syntax-highlighted code</li>
</ul>
<blockquote>
<p>The content is rendered without any interpolation, making it safe for static content.</p>
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

        $quoteHtml = '<p class="text-lg italic">"The best way to predict the future is to invent it."</p><footer class="text-sm text-slate-500 mt-2">— Alan Kay</footer>';
    @endphp

    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <!-- Markdown Content -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded">Markdown</span>
                Article Content
            </h3>
            <x-accelade::content
                as="article"
                class="prose prose-sm prose-slate max-w-none"
                :html="$markdownHtml"
                data-testid="content-markdown"
            />
        </div>

        <!-- Code Block -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
            <h3 class="font-medium text-slate-700 mb-4 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">Code</span>
                Syntax Highlighted
            </h3>
            <x-accelade::content
                as="div"
                class="text-sm"
                :html="$codeHtml"
                data-testid="content-code"
            />
        </div>
    </div>

    <!-- Quote Block -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-100 mb-6">
        <h3 class="font-medium text-slate-700 mb-4">Blockquote with Custom Wrapper</h3>
        <x-accelade::content
            as="blockquote"
            class="border-l-4 border-emerald-500 pl-4"
            :html="$quoteHtml"
            data-testid="content-quote"
        />
    </div>

    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
        <pre class="text-sm font-mono text-slate-700 whitespace-pre-wrap">&lt;!-- Basic usage --&gt;
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
/&gt;</pre>
    </div>
</section>
