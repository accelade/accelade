# SEO

Accelade provides a powerful SEO engine for managing page metadata. Set titles, descriptions, OpenGraph, Twitter Cards, and custom meta tags using a fluent PHP API or Blade directives.

## Quick Start

```blade
{{-- In your Blade template --}}
@seoTitle('My Page Title')
@seoDescription('This is my page description')
@seoKeywords('php, laravel, accelade')

{{-- In your layout's <head> section --}}
<head>
    @seo
</head>
```

## Blade Directives

### Basic Meta Tags

```blade
{{-- Set page title --}}
@seoTitle('My Page Title')

{{-- Set page description --}}
@seoDescription('A brief description of the page content')

{{-- Set keywords (comma-separated string) --}}
@seoKeywords('laravel, php, accelade')

{{-- Set canonical URL --}}
@seoCanonical('https://example.com/page')

{{-- Set robots meta --}}
@seoRobots('index, follow')

{{-- Set author --}}
@seoAuthor('John Doe')
```

### OpenGraph Tags

```blade
@seoOpenGraph([
    'type' => 'article',
    'site_name' => 'My Website',
    'title' => 'Article Title',
    'description' => 'Article description',
    'url' => 'https://example.com/article',
    'image' => 'https://example.com/image.jpg',
    'image_alt' => 'Image description',
    'locale' => 'en_US',
])
```

### Twitter Cards

```blade
@seoTwitter([
    'card' => 'summary_large_image',
    'site' => '@mysite',
    'creator' => '@johndoe',
    'title' => 'Tweet Title',
    'description' => 'Tweet description',
    'image' => 'https://example.com/twitter.jpg',
    'image_alt' => 'Image description',
])
```

### Custom Meta Tags

```blade
{{-- Add meta by name attribute --}}
@seoMeta('theme-color', '#6366f1')

{{-- Output all SEO tags --}}
@seo
```

## PHP API

Use the SEO facade for programmatic control:

```php
use Accelade\Facades\SEO;

// Basic meta
SEO::title('My Page Title')
    ->description('Page description')
    ->keywords(['php', 'laravel', 'accelade'])
    ->canonical('https://example.com/page')
    ->robots('index, follow')
    ->author('John Doe');

// OpenGraph
SEO::openGraphType('article')
    ->openGraphSiteName('My Website')
    ->openGraphTitle('OG Title')
    ->openGraphDescription('OG Description')
    ->openGraphUrl('https://example.com')
    ->openGraphImage('https://example.com/og.jpg', 'Alt text')
    ->openGraphLocale('en_US');

// Twitter Cards
SEO::twitterCard('summary_large_image')
    ->twitterSite('@mysite')
    ->twitterCreator('@johndoe')
    ->twitterTitle('Twitter Title')
    ->twitterDescription('Twitter Description')
    ->twitterImage('https://example.com/twitter.jpg', 'Alt text');

// Custom meta tags
SEO::metaByName('theme-color', '#6366f1');
SEO::metaByProperty('article:section', 'Technology');
SEO::meta(['http-equiv' => 'refresh', 'content' => '30']);

// Get values
$title = SEO::getTitle();
$description = SEO::getDescription();
$keywords = SEO::getKeywords();
$canonical = SEO::getCanonical();
$robots = SEO::getRobots();
$author = SEO::getAuthor();
$openGraph = SEO::getOpenGraph();
$twitter = SEO::getTwitter();
$meta = SEO::getMeta();

// Convert to array or HTML
$array = SEO::toArray();
$html = SEO::toHtml();
$html = SEO::render(); // Alias for toHtml()

// Reset all values
SEO::reset();
```

## Usage in Controllers

Set SEO data from your controller:

```php
use Accelade\Facades\SEO;

class PostController extends Controller
{
    public function show(Post $post)
    {
        SEO::title($post->title)
            ->description($post->excerpt)
            ->canonical(route('posts.show', $post))
            ->openGraphType('article')
            ->openGraphImage($post->featured_image);

        return view('posts.show', compact('post'));
    }
}
```

## Configuration

Configure default SEO values in `config/accelade.php`:

```php
'seo' => [
    // Default values when none are set
    'defaults' => [
        'title' => null,
        'description' => null,
        'keywords' => [],
    ],

    // Title formatting
    'title_separator' => ' | ',
    'title_prefix' => null,
    'title_suffix' => 'My Website', // Appended to all titles

    // Auto-generate canonical from current URL
    'auto_canonical_link' => true,

    // OpenGraph defaults
    'open_graph' => [
        'auto_fill' => true, // Auto-fill from main SEO values
        'defaults' => [
            'type' => 'website',
            'site_name' => null,
            'locale' => null,
        ],
    ],

    // Twitter Card defaults
    'twitter' => [
        'auto_fill' => true, // Auto-fill from main SEO values
        'defaults' => [
            'card' => 'summary_large_image',
            'site' => null,
            'creator' => null,
        ],
    ],
],
```

### Title Formatting

When `title_suffix` is set, titles are automatically formatted:

```php
// Config: 'title_suffix' => 'My Website', 'title_separator' => ' | '
SEO::title('About Us');
// Output: <title>About Us | My Website</title>
```

### Auto-Fill

When `auto_fill` is enabled for OpenGraph/Twitter:

- `og:title` and `twitter:title` inherit from main title
- `og:description` and `twitter:description` inherit from main description
- `og:url` inherits from canonical URL

## Output

Place `@seo` in your layout's `<head>` section:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @seo
    @acceladeStyles
    @acceladeScripts
</head>
<body>
    {{ $slot }}
</body>
</html>
```

### Generated Output Example

```html
<title>My Page Title | My Website</title>
<meta name="description" content="Page description">
<meta name="keywords" content="php, laravel, accelade">
<meta name="robots" content="index, follow">
<meta name="author" content="John Doe">
<link rel="canonical" href="https://example.com/page">
<meta property="og:type" content="article">
<meta property="og:site:name" content="My Website">
<meta property="og:title" content="My Page Title | My Website">
<meta property="og:description" content="Page description">
<meta property="og:url" content="https://example.com/page">
<meta property="og:image" content="https://example.com/og.jpg">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@mysite">
<meta name="twitter:title" content="My Page Title | My Website">
<meta name="twitter:description" content="Page description">
<meta name="twitter:image" content="https://example.com/twitter.jpg">
```

## Extending with Macros

The SEO class uses Laravel's `Macroable` trait:

```php
use Accelade\SEO\SEO;

SEO::macro('article', function (Post $post) {
    return $this
        ->title($post->title)
        ->description($post->excerpt)
        ->openGraphType('article')
        ->openGraphImage($post->featured_image)
        ->metaByProperty('article:published_time', $post->published_at->toIso8601String())
        ->metaByProperty('article:author', $post->author->name);
});

// Usage
SEO::article($post);
```

## Best Practices

1. **Set SEO in Controllers** - Keep SEO logic in controllers, not views
2. **Use Configuration** - Set site-wide defaults in config
3. **Escape Output** - All values are automatically HTML-escaped
4. **Unique Titles** - Each page should have a unique title
5. **Description Length** - Keep descriptions under 160 characters
6. **OpenGraph Images** - Use 1200x630px images for best sharing

## Next Steps

- [Configuration](configuration.md) - Full configuration reference
- [API Reference](api-reference.md) - Complete API documentation
- [SPA Navigation](spa-navigation.md) - SEO with client-side navigation
