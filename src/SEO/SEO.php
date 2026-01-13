<?php

declare(strict_types=1);

namespace Accelade\SEO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

/**
 * SEO Manager for managing page metadata.
 *
 * Provides a fluent interface for setting title, description, keywords,
 * canonical URLs, OpenGraph tags, Twitter Cards, and custom meta tags.
 */
class SEO implements Arrayable, Htmlable
{
    use Macroable;

    protected ?string $title = null;

    protected ?string $description = null;

    /** @var array<int, string> */
    protected array $keywords = [];

    protected ?string $canonical = null;

    /** @var array<string, string|null> */
    protected array $openGraph = [
        'type' => null,
        'site_name' => null,
        'title' => null,
        'description' => null,
        'url' => null,
        'image' => null,
        'image:alt' => null,
        'locale' => null,
    ];

    /** @var array<string, string|null> */
    protected array $twitter = [
        'card' => null,
        'site' => null,
        'creator' => null,
        'title' => null,
        'description' => null,
        'image' => null,
        'image:alt' => null,
    ];

    /** @var array<int, array{type: string, key: string, value: string}> */
    protected array $meta = [];

    protected ?string $robots = null;

    protected ?string $author = null;

    /**
     * Set the page title.
     */
    public function title(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the page title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the page description.
     */
    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the page description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set page keywords.
     *
     * @param  string|array<int, string>  $keywords
     */
    public function keywords(string|array $keywords): self
    {
        if (is_string($keywords)) {
            $keywords = array_map('trim', explode(',', $keywords));
        }

        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get page keywords.
     *
     * @return array<int, string>
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * Set the canonical URL.
     */
    public function canonical(?string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    /**
     * Get the canonical URL.
     */
    public function getCanonical(): ?string
    {
        return $this->canonical;
    }

    /**
     * Set robots meta tag.
     */
    public function robots(?string $robots): self
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Get robots meta tag.
     */
    public function getRobots(): ?string
    {
        return $this->robots;
    }

    /**
     * Set author meta tag.
     */
    public function author(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author meta tag.
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set OpenGraph type.
     */
    public function openGraphType(?string $type): self
    {
        $this->openGraph['type'] = $type;

        return $this;
    }

    /**
     * Set OpenGraph site name.
     */
    public function openGraphSiteName(?string $siteName): self
    {
        $this->openGraph['site_name'] = $siteName;

        return $this;
    }

    /**
     * Set OpenGraph title.
     */
    public function openGraphTitle(?string $title): self
    {
        $this->openGraph['title'] = $title;

        return $this;
    }

    /**
     * Set OpenGraph description.
     */
    public function openGraphDescription(?string $description): self
    {
        $this->openGraph['description'] = $description;

        return $this;
    }

    /**
     * Set OpenGraph URL.
     */
    public function openGraphUrl(?string $url): self
    {
        $this->openGraph['url'] = $url;

        return $this;
    }

    /**
     * Set OpenGraph image.
     */
    public function openGraphImage(?string $image, ?string $alt = null): self
    {
        $this->openGraph['image'] = $image;

        if ($alt !== null) {
            $this->openGraph['image:alt'] = $alt;
        }

        return $this;
    }

    /**
     * Set OpenGraph locale.
     */
    public function openGraphLocale(?string $locale): self
    {
        $this->openGraph['locale'] = $locale;

        return $this;
    }

    /**
     * Get all OpenGraph values.
     *
     * @return array<string, string|null>
     */
    public function getOpenGraph(): array
    {
        return $this->openGraph;
    }

    /**
     * Set Twitter card type.
     */
    public function twitterCard(?string $card): self
    {
        $this->twitter['card'] = $card;

        return $this;
    }

    /**
     * Set Twitter site handle.
     */
    public function twitterSite(?string $site): self
    {
        $this->twitter['site'] = $site;

        return $this;
    }

    /**
     * Set Twitter creator handle.
     */
    public function twitterCreator(?string $creator): self
    {
        $this->twitter['creator'] = $creator;

        return $this;
    }

    /**
     * Set Twitter title.
     */
    public function twitterTitle(?string $title): self
    {
        $this->twitter['title'] = $title;

        return $this;
    }

    /**
     * Set Twitter description.
     */
    public function twitterDescription(?string $description): self
    {
        $this->twitter['description'] = $description;

        return $this;
    }

    /**
     * Set Twitter image.
     */
    public function twitterImage(?string $image, ?string $alt = null): self
    {
        $this->twitter['image'] = $image;

        if ($alt !== null) {
            $this->twitter['image:alt'] = $alt;
        }

        return $this;
    }

    /**
     * Get all Twitter card values.
     *
     * @return array<string, string|null>
     */
    public function getTwitter(): array
    {
        return $this->twitter;
    }

    /**
     * Add a meta tag by name attribute.
     */
    public function metaByName(string $name, string $content): self
    {
        $this->meta[] = [
            'type' => 'name',
            'key' => $name,
            'value' => $content,
        ];

        return $this;
    }

    /**
     * Add a meta tag by property attribute.
     */
    public function metaByProperty(string $property, string $content): self
    {
        $this->meta[] = [
            'type' => 'property',
            'key' => $property,
            'value' => $content,
        ];

        return $this;
    }

    /**
     * Add a custom meta tag with arbitrary attributes.
     *
     * @param  array<string, string>  $attributes
     */
    public function meta(array $attributes): self
    {
        $this->meta[] = [
            'type' => 'custom',
            'key' => '',
            'value' => '',
            'attributes' => $attributes,
        ];

        return $this;
    }

    /**
     * Get all custom meta tags.
     *
     * @return array<int, array{type: string, key: string, value: string}>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Reset all SEO values to defaults.
     */
    public function reset(): self
    {
        $this->title = null;
        $this->description = null;
        $this->keywords = [];
        $this->canonical = null;
        $this->robots = null;
        $this->author = null;

        $this->openGraph = [
            'type' => null,
            'site_name' => null,
            'title' => null,
            'description' => null,
            'url' => null,
            'image' => null,
            'image:alt' => null,
            'locale' => null,
        ];

        $this->twitter = [
            'card' => null,
            'site' => null,
            'creator' => null,
            'title' => null,
            'description' => null,
            'image' => null,
            'image:alt' => null,
        ];

        $this->meta = [];

        return $this;
    }

    /**
     * Build the full title with separator and suffix.
     */
    public function buildTitle(): ?string
    {
        $config = config('accelade.seo', []);
        $title = $this->title ?? Arr::get($config, 'defaults.title');

        if ($title === null) {
            return null;
        }

        $separator = Arr::get($config, 'title_separator', ' | ');
        $prefix = Arr::get($config, 'title_prefix');
        $suffix = Arr::get($config, 'title_suffix');

        $parts = array_filter([$prefix, $title, $suffix]);

        return implode($separator, $parts);
    }

    /**
     * Get effective description (with fallback to default).
     */
    public function buildDescription(): ?string
    {
        return $this->description ?? Arr::get(config('accelade.seo', []), 'defaults.description');
    }

    /**
     * Get effective keywords (with fallback to default).
     *
     * @return array<int, string>
     */
    public function buildKeywords(): array
    {
        if (count($this->keywords) > 0) {
            return $this->keywords;
        }

        return Arr::get(config('accelade.seo', []), 'defaults.keywords', []);
    }

    /**
     * Get effective canonical URL (with auto-generation if enabled).
     */
    public function buildCanonical(): ?string
    {
        if ($this->canonical !== null) {
            return $this->canonical;
        }

        $autoCanonical = Arr::get(config('accelade.seo', []), 'auto_canonical_link', true);

        if ($autoCanonical && function_exists('request')) {
            return request()->url();
        }

        return null;
    }

    /**
     * Convert SEO data to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->buildTitle(),
            'description' => $this->buildDescription(),
            'keywords' => $this->buildKeywords(),
            'canonical' => $this->buildCanonical(),
            'robots' => $this->robots,
            'author' => $this->author,
            'openGraph' => $this->buildOpenGraph(),
            'twitter' => $this->buildTwitter(),
            'meta' => $this->meta,
        ];
    }

    /**
     * Build OpenGraph tags with auto-fill.
     *
     * @return array<string, string|null>
     */
    protected function buildOpenGraph(): array
    {
        $config = Arr::get(config('accelade.seo', []), 'open_graph', []);
        $autoFill = Arr::get($config, 'auto_fill', true);

        $og = $this->openGraph;

        // Apply defaults
        foreach (Arr::get($config, 'defaults', []) as $key => $value) {
            if ($og[$key] === null) {
                $og[$key] = $value;
            }
        }

        // Auto-fill from main SEO values
        if ($autoFill) {
            if ($og['title'] === null) {
                $og['title'] = $this->buildTitle();
            }
            if ($og['description'] === null) {
                $og['description'] = $this->buildDescription();
            }
            if ($og['url'] === null) {
                $og['url'] = $this->buildCanonical();
            }
        }

        return $og;
    }

    /**
     * Build Twitter card tags with auto-fill.
     *
     * @return array<string, string|null>
     */
    protected function buildTwitter(): array
    {
        $config = Arr::get(config('accelade.seo', []), 'twitter', []);
        $autoFill = Arr::get($config, 'auto_fill', true);

        $twitter = $this->twitter;

        // Apply defaults
        foreach (Arr::get($config, 'defaults', []) as $key => $value) {
            if ($twitter[$key] === null) {
                $twitter[$key] = $value;
            }
        }

        // Auto-fill from main SEO values
        if ($autoFill) {
            if ($twitter['title'] === null) {
                $twitter['title'] = $this->buildTitle();
            }
            if ($twitter['description'] === null) {
                $twitter['description'] = $this->buildDescription();
            }
        }

        return $twitter;
    }

    /**
     * Generate HTML meta tags.
     */
    public function toHtml(): string
    {
        $html = array_merge(
            $this->renderBasicMeta(),
            $this->renderOpenGraphMeta(),
            $this->renderTwitterMeta(),
            $this->renderCustomMeta(),
        );

        return implode("\n    ", $html);
    }

    /**
     * Render basic meta tags (title, description, keywords, robots, author, canonical).
     *
     * @return array<int, string>
     */
    protected function renderBasicMeta(): array
    {
        $html = [];

        $title = $this->buildTitle();
        if ($title !== null) {
            $html[] = '<title>'.e($title).'</title>';
        }

        $description = $this->buildDescription();
        if ($description !== null) {
            $html[] = '<meta name="description" content="'.e($description).'">';
        }

        $keywords = $this->buildKeywords();
        if (count($keywords) > 0) {
            $html[] = '<meta name="keywords" content="'.e(implode(', ', $keywords)).'">';
        }

        if ($this->robots !== null) {
            $html[] = '<meta name="robots" content="'.e($this->robots).'">';
        }

        if ($this->author !== null) {
            $html[] = '<meta name="author" content="'.e($this->author).'">';
        }

        $canonical = $this->buildCanonical();
        if ($canonical !== null) {
            $html[] = '<link rel="canonical" href="'.e($canonical).'">';
        }

        return $html;
    }

    /**
     * Render OpenGraph meta tags.
     *
     * @return array<int, string>
     */
    protected function renderOpenGraphMeta(): array
    {
        $html = [];
        $og = $this->buildOpenGraph();

        foreach ($og as $key => $value) {
            if ($value !== null) {
                $property = $key === 'type' ? 'og:type' : 'og:'.str_replace('_', ':', $key);
                $html[] = '<meta property="'.e($property).'" content="'.e($value).'">';
            }
        }

        return $html;
    }

    /**
     * Render Twitter Card meta tags.
     *
     * @return array<int, string>
     */
    protected function renderTwitterMeta(): array
    {
        $html = [];
        $twitter = $this->buildTwitter();

        foreach ($twitter as $key => $value) {
            if ($value !== null) {
                $name = 'twitter:'.str_replace('_', ':', $key);
                $html[] = '<meta name="'.e($name).'" content="'.e($value).'">';
            }
        }

        return $html;
    }

    /**
     * Render custom meta tags.
     *
     * @return array<int, string>
     */
    protected function renderCustomMeta(): array
    {
        $html = [];

        foreach ($this->meta as $meta) {
            if ($meta['type'] === 'name') {
                $html[] = '<meta name="'.e($meta['key']).'" content="'.e($meta['value']).'">';
            } elseif ($meta['type'] === 'property') {
                $html[] = '<meta property="'.e($meta['key']).'" content="'.e($meta['value']).'">';
            } elseif ($meta['type'] === 'custom' && isset($meta['attributes'])) {
                $attrs = [];
                foreach ($meta['attributes'] as $attrKey => $attrValue) {
                    $attrs[] = e($attrKey).'="'.e($attrValue).'"';
                }
                $html[] = '<meta '.implode(' ', $attrs).'>';
            }
        }

        return $html;
    }

    /**
     * Render SEO tags as HTML string.
     */
    public function render(): string
    {
        return $this->toHtml();
    }

    /**
     * Convert to string (renders HTML).
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
