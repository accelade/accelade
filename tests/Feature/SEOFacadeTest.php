<?php

declare(strict_types=1);

use Accelade\Facades\SEO;
use Accelade\SEO\SEO as SEOClass;

test('seo facade is registered', function () {
    $seo = app('accelade.seo');

    expect($seo)->toBeInstanceOf(SEOClass::class);
});

test('seo singleton returns same instance', function () {
    $first = app('accelade.seo');
    $second = app('accelade.seo');

    expect($first)->toBe($second);
});

test('seo facade sets title', function () {
    SEO::title('Facade Title');

    expect(SEO::getTitle())->toBe('Facade Title');
});

test('seo facade sets description', function () {
    SEO::description('Facade Description');

    expect(SEO::getDescription())->toBe('Facade Description');
});

test('seo facade sets keywords', function () {
    SEO::keywords('php, laravel');

    expect(SEO::getKeywords())->toBe(['php', 'laravel']);
});

test('seo facade sets canonical', function () {
    SEO::canonical('https://example.com');

    expect(SEO::getCanonical())->toBe('https://example.com');
});

test('seo facade sets robots', function () {
    SEO::robots('noindex, nofollow');

    expect(SEO::getRobots())->toBe('noindex, nofollow');
});

test('seo facade sets author', function () {
    SEO::author('John Doe');

    expect(SEO::getAuthor())->toBe('John Doe');
});

test('seo facade sets opengraph properties', function () {
    SEO::openGraphType('article');
    SEO::openGraphSiteName('My Site');
    SEO::openGraphTitle('OG Title');

    $og = SEO::getOpenGraph();

    expect($og['type'])->toBe('article');
    expect($og['site_name'])->toBe('My Site');
    expect($og['title'])->toBe('OG Title');
});

test('seo facade sets twitter card properties', function () {
    SEO::twitterCard('summary_large_image');
    SEO::twitterSite('@example');
    SEO::twitterCreator('@johndoe');

    $twitter = SEO::getTwitter();

    expect($twitter['card'])->toBe('summary_large_image');
    expect($twitter['site'])->toBe('@example');
    expect($twitter['creator'])->toBe('@johndoe');
});

test('seo facade adds custom meta', function () {
    SEO::metaByName('theme-color', '#ffffff');
    SEO::metaByProperty('article:section', 'Technology');

    $meta = SEO::getMeta();

    expect($meta)->toHaveCount(2);
});

test('seo facade supports fluent chaining', function () {
    $result = SEO::title('Title')
        ->description('Description')
        ->keywords('php, laravel')
        ->canonical('https://example.com');

    expect($result)->toBeInstanceOf(SEOClass::class);
});

test('seo facade renders html', function () {
    SEO::reset();
    SEO::title('Test Title');
    SEO::description('Test Description');

    $html = SEO::toHtml();

    expect($html)->toContain('<title>Test Title</title>');
    expect($html)->toContain('<meta name="description" content="Test Description">');
});

test('seo facade reset clears all values', function () {
    SEO::title('Title');
    SEO::description('Description');

    SEO::reset();

    expect(SEO::getTitle())->toBeNull();
    expect(SEO::getDescription())->toBeNull();
});

test('seo facade converts to array', function () {
    SEO::reset();
    SEO::title('Title');

    $array = SEO::toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKey('title');
});
