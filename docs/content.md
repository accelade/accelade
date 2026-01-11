# Content Component

The Content component renders pre-rendered HTML without any interpolation or processing. This is useful for displaying static content like Markdown that has already been converted to HTML.

## Quick Start

```blade
<x-accelade::content :html="$renderedMarkdown" />
```

## Basic Usage

Pass pre-rendered HTML content to the component:

```blade
{{-- In your controller --}}
$html = Str::markdown($post->content);
return view('post', ['html' => $html]);

{{-- In your view --}}
<x-accelade::content :html="$html" />
```

## Custom Wrapper Element

By default, the content is wrapped in a `<div>`. Use the `as` attribute to change the wrapper element:

```blade
{{-- Render as article --}}
<x-accelade::content as="article" :html="$html" />

{{-- Render as section --}}
<x-accelade::content as="section" :html="$html" />

{{-- Render as blockquote --}}
<x-accelade::content as="blockquote" :html="$quote" />
```

### Supported Wrapper Elements

| Element | Description |
|---------|-------------|
| `div` | Default wrapper (block element) |
| `span` | Inline wrapper |
| `article` | Article content |
| `section` | Document section |
| `aside` | Sidebar content |
| `main` | Main content |
| `header` | Header section |
| `footer` | Footer section |
| `nav` | Navigation section |
| `p` | Paragraph |
| `blockquote` | Block quotation |
| `pre` | Preformatted text |
| `code` | Code block |
| `figure` | Figure with caption |
| `figcaption` | Figure caption |
| `details` | Disclosure widget |
| `summary` | Summary for details |

Invalid or unsafe elements (like `script`) will fall back to `div`.

## Styling with Tailwind

The component accepts all standard HTML attributes, making it easy to style with Tailwind CSS:

```blade
{{-- Prose styling for Markdown --}}
<x-accelade::content
    as="article"
    class="prose prose-lg dark:prose-invert max-w-none"
    :html="$html"
/>

{{-- Custom styling --}}
<x-accelade::content
    class="p-6 bg-white rounded-lg shadow"
    :html="$html"
/>
```

## Use Cases

### Rendering Markdown

```php
// Controller
use Illuminate\Support\Str;

public function show(Post $post)
{
    return view('posts.show', [
        'post' => $post,
        'content' => Str::markdown($post->body),
    ]);
}
```

```blade
{{-- View --}}
<article class="post">
    <h1>{{ $post->title }}</h1>

    <x-accelade::content
        as="div"
        class="prose prose-slate"
        :html="$content"
    />
</article>
```

### Displaying Rich Text from CMS

```blade
<x-accelade::content
    as="section"
    class="cms-content"
    :html="$page->body"
/>
```

### Code Snippets with Syntax Highlighting

```php
// After processing with a syntax highlighter
$highlightedCode = highlight($code, 'php');
```

```blade
<x-accelade::content
    as="div"
    class="code-block"
    :html="$highlightedCode"
/>
```

### Email Templates Preview

```blade
<x-accelade::content
    as="div"
    class="email-preview border rounded-lg p-4"
    :html="$emailHtml"
/>
```

## Component Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `html` | string | `''` | The pre-rendered HTML content to display |
| `as` | string | `'div'` | The wrapper element tag |
| `class` | string | - | CSS classes for the wrapper |
| `id` | string | - | ID for the wrapper |
| `*` | - | - | Any other HTML attributes |

## Security Warning

Only use this component with trusted content. Since the HTML is rendered directly without sanitization, user-provided content could contain malicious scripts (XSS attacks).

**Safe:**
```blade
{{-- Content from your own database/CMS --}}
<x-accelade::content :html="$trustedContent" />

{{-- Markdown you converted yourself --}}
<x-accelade::content :html="Str::markdown($trustedMarkdown)" />
```

**Dangerous - Do NOT do this:**
```blade
{{-- Never render user input directly --}}
<x-accelade::content :html="$userProvidedHtml" />

{{-- Never render request data --}}
<x-accelade::content :html="request('content')" />
```

If you need to render user-provided content, sanitize it first using a library like [HTML Purifier](http://htmlpurifier.org/).

## Comparison with Standard Blade

The Content component is equivalent to using `{!! !!}` but with a semantic wrapper:

```blade
{{-- Standard Blade (no wrapper) --}}
{!! $html !!}

{{-- Content component (with wrapper) --}}
<x-accelade::content :html="$html" />

{{-- Equivalent to --}}
<div>{!! $html !!}</div>
```

The advantage of using the component is cleaner syntax when you need a wrapper element with attributes:

```blade
{{-- Without component --}}
<article class="prose dark:prose-invert max-w-none">
    {!! $html !!}
</article>

{{-- With component --}}
<x-accelade::content as="article" class="prose dark:prose-invert max-w-none" :html="$html" />
```

## Next Steps

- [Lazy Loading](lazy-loading.md) - Defer content rendering
- [Components](components.md) - Building reactive components
- [API Reference](api-reference.md) - Complete API documentation
