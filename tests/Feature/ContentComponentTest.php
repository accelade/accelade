<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    // Set up the view path for testing
    $this->app['config']->set('view.paths', [
        __DIR__.'/../../resources/views',
    ]);
});

it('renders basic html content', function () {
    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => '<p>Hello World</p>',
    ]);

    expect($html)->toContain('<p>Hello World</p>');
    expect($html)->toContain('<div');
    expect($html)->toContain('</div>');
});

it('uses div as default wrapper element', function () {
    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => '<span>Content</span>',
    ]);

    expect($html)->toContain('<div');
    expect($html)->toContain('</div>');
});

it('can use custom wrapper element with as attribute', function () {
    $html = Blade::render('<x-accelade::content as="article" :html="$html" />', [
        'html' => '<p>Article content</p>',
    ]);

    expect($html)->toContain('<article');
    expect($html)->toContain('</article>');
    expect($html)->not->toContain('<div');
});

it('can use section as wrapper element', function () {
    $html = Blade::render('<x-accelade::content as="section" :html="$html" />', [
        'html' => '<h2>Section Title</h2>',
    ]);

    expect($html)->toContain('<section');
    expect($html)->toContain('</section>');
});

it('can use span as wrapper element', function () {
    $html = Blade::render('<x-accelade::content as="span" :html="$html" />', [
        'html' => 'Inline content',
    ]);

    expect($html)->toContain('<span');
    expect($html)->toContain('</span>');
});

it('falls back to div for invalid tag names', function () {
    $html = Blade::render('<x-accelade::content as="script" :html="$html" />', [
        'html' => '<p>Safe content</p>',
    ]);

    expect($html)->toContain('<div');
    expect($html)->not->toContain('<script');
});

it('passes additional attributes to wrapper element', function () {
    $html = Blade::render('<x-accelade::content class="prose dark:prose-invert" id="content" :html="$html" />', [
        'html' => '<p>Styled content</p>',
    ]);

    expect($html)->toContain('class="prose dark:prose-invert"');
    expect($html)->toContain('id="content"');
});

it('renders markdown-converted html', function () {
    // Simulating pre-rendered Markdown
    $markdownHtml = '<h1>Title</h1><p>This is a <strong>bold</strong> paragraph.</p><ul><li>Item 1</li><li>Item 2</li></ul>';

    $html = Blade::render('<x-accelade::content as="article" class="prose" :html="$html" />', [
        'html' => $markdownHtml,
    ]);

    expect($html)->toContain('<article');
    expect($html)->toContain('class="prose"');
    expect($html)->toContain('<h1>Title</h1>');
    expect($html)->toContain('<strong>bold</strong>');
    expect($html)->toContain('<ul>');
    expect($html)->toContain('<li>Item 1</li>');
});

it('renders empty content when html is empty', function () {
    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => '',
    ]);

    expect($html)->toContain('<div');
    expect($html)->toContain('</div>');
    // Should be essentially empty between tags
    expect(trim(strip_tags($html)))->toBe('');
});

it('preserves html entities', function () {
    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => '<p>&copy; 2024 &mdash; All rights reserved</p>',
    ]);

    expect($html)->toContain('&copy;');
    expect($html)->toContain('&mdash;');
});

it('renders code blocks correctly', function () {
    $codeHtml = '<pre><code class="language-php">&lt;?php echo "Hello"; ?&gt;</code></pre>';

    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => $codeHtml,
    ]);

    expect($html)->toContain('<pre>');
    expect($html)->toContain('<code class="language-php">');
    expect($html)->toContain('&lt;?php');
});

it('can use blockquote as wrapper', function () {
    $html = Blade::render('<x-accelade::content as="blockquote" class="border-l-4 pl-4" :html="$html" />', [
        'html' => 'A wise quote',
    ]);

    expect($html)->toContain('<blockquote');
    expect($html)->toContain('class="border-l-4 pl-4"');
    expect($html)->toContain('A wise quote');
});

it('can use figure as wrapper', function () {
    $html = Blade::render('<x-accelade::content as="figure" :html="$html" />', [
        'html' => '<img src="/image.jpg" alt="Image"><figcaption>Caption</figcaption>',
    ]);

    expect($html)->toContain('<figure');
    expect($html)->toContain('</figure>');
    expect($html)->toContain('<img src="/image.jpg"');
});

it('supports data attributes', function () {
    $html = Blade::render('<x-accelade::content data-testid="content-block" data-section="main" :html="$html" />', [
        'html' => '<p>Test content</p>',
    ]);

    expect($html)->toContain('data-testid="content-block"');
    expect($html)->toContain('data-section="main"');
});

it('renders complex nested html', function () {
    $complexHtml = '
        <div class="card">
            <div class="card-header">
                <h3>Card Title</h3>
            </div>
            <div class="card-body">
                <p>Card content with <a href="#">link</a></p>
            </div>
        </div>
    ';

    $html = Blade::render('<x-accelade::content :html="$html" />', [
        'html' => $complexHtml,
    ]);

    expect($html)->toContain('class="card"');
    expect($html)->toContain('class="card-header"');
    expect($html)->toContain('<h3>Card Title</h3>');
    expect($html)->toContain('<a href="#">link</a>');
});
