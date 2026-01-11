<?php

declare(strict_types=1);

use Accelade\SEO\SEO;

test('seo can set title', function () {
    $seo = new SEO;
    $seo->title('My Page Title');

    expect($seo->getTitle())->toBe('My Page Title');
});

test('seo can set description', function () {
    $seo = new SEO;
    $seo->description('This is a description');

    expect($seo->getDescription())->toBe('This is a description');
});

test('seo can set keywords as string', function () {
    $seo = new SEO;
    $seo->keywords('php, laravel, accelade');

    expect($seo->getKeywords())->toBe(['php', 'laravel', 'accelade']);
});

test('seo can set keywords as array', function () {
    $seo = new SEO;
    $seo->keywords(['php', 'laravel', 'accelade']);

    expect($seo->getKeywords())->toBe(['php', 'laravel', 'accelade']);
});

test('seo can set canonical url', function () {
    $seo = new SEO;
    $seo->canonical('https://example.com/page');

    expect($seo->getCanonical())->toBe('https://example.com/page');
});

test('seo can set robots meta', function () {
    $seo = new SEO;
    $seo->robots('index, follow');

    expect($seo->getRobots())->toBe('index, follow');
});

test('seo can set author meta', function () {
    $seo = new SEO;
    $seo->author('John Doe');

    expect($seo->getAuthor())->toBe('John Doe');
});

test('seo can set opengraph type', function () {
    $seo = new SEO;
    $seo->openGraphType('article');

    $og = $seo->getOpenGraph();
    expect($og['type'])->toBe('article');
});

test('seo can set opengraph site name', function () {
    $seo = new SEO;
    $seo->openGraphSiteName('My Website');

    $og = $seo->getOpenGraph();
    expect($og['site_name'])->toBe('My Website');
});

test('seo can set opengraph title', function () {
    $seo = new SEO;
    $seo->openGraphTitle('OG Title');

    $og = $seo->getOpenGraph();
    expect($og['title'])->toBe('OG Title');
});

test('seo can set opengraph description', function () {
    $seo = new SEO;
    $seo->openGraphDescription('OG Description');

    $og = $seo->getOpenGraph();
    expect($og['description'])->toBe('OG Description');
});

test('seo can set opengraph url', function () {
    $seo = new SEO;
    $seo->openGraphUrl('https://example.com');

    $og = $seo->getOpenGraph();
    expect($og['url'])->toBe('https://example.com');
});

test('seo can set opengraph image', function () {
    $seo = new SEO;
    $seo->openGraphImage('https://example.com/image.jpg', 'Image Alt');

    $og = $seo->getOpenGraph();
    expect($og['image'])->toBe('https://example.com/image.jpg');
    expect($og['image:alt'])->toBe('Image Alt');
});

test('seo can set opengraph locale', function () {
    $seo = new SEO;
    $seo->openGraphLocale('en_US');

    $og = $seo->getOpenGraph();
    expect($og['locale'])->toBe('en_US');
});

test('seo can set twitter card type', function () {
    $seo = new SEO;
    $seo->twitterCard('summary_large_image');

    $twitter = $seo->getTwitter();
    expect($twitter['card'])->toBe('summary_large_image');
});

test('seo can set twitter site', function () {
    $seo = new SEO;
    $seo->twitterSite('@mysite');

    $twitter = $seo->getTwitter();
    expect($twitter['site'])->toBe('@mysite');
});

test('seo can set twitter creator', function () {
    $seo = new SEO;
    $seo->twitterCreator('@johndoe');

    $twitter = $seo->getTwitter();
    expect($twitter['creator'])->toBe('@johndoe');
});

test('seo can set twitter title', function () {
    $seo = new SEO;
    $seo->twitterTitle('Twitter Title');

    $twitter = $seo->getTwitter();
    expect($twitter['title'])->toBe('Twitter Title');
});

test('seo can set twitter description', function () {
    $seo = new SEO;
    $seo->twitterDescription('Twitter Description');

    $twitter = $seo->getTwitter();
    expect($twitter['description'])->toBe('Twitter Description');
});

test('seo can set twitter image', function () {
    $seo = new SEO;
    $seo->twitterImage('https://example.com/twitter.jpg', 'Twitter Image');

    $twitter = $seo->getTwitter();
    expect($twitter['image'])->toBe('https://example.com/twitter.jpg');
    expect($twitter['image:alt'])->toBe('Twitter Image');
});

test('seo can add meta by name', function () {
    $seo = new SEO;
    $seo->metaByName('viewport', 'width=device-width, initial-scale=1');

    $meta = $seo->getMeta();
    expect($meta)->toHaveCount(1);
    expect($meta[0]['type'])->toBe('name');
    expect($meta[0]['key'])->toBe('viewport');
    expect($meta[0]['value'])->toBe('width=device-width, initial-scale=1');
});

test('seo can add meta by property', function () {
    $seo = new SEO;
    $seo->metaByProperty('article:author', 'John Doe');

    $meta = $seo->getMeta();
    expect($meta)->toHaveCount(1);
    expect($meta[0]['type'])->toBe('property');
    expect($meta[0]['key'])->toBe('article:author');
    expect($meta[0]['value'])->toBe('John Doe');
});

test('seo can add custom meta with attributes', function () {
    $seo = new SEO;
    $seo->meta(['http-equiv' => 'refresh', 'content' => '30']);

    $meta = $seo->getMeta();
    expect($meta)->toHaveCount(1);
    expect($meta[0]['type'])->toBe('custom');
    expect($meta[0]['attributes'])->toBe(['http-equiv' => 'refresh', 'content' => '30']);
});

test('seo supports fluent chaining', function () {
    $seo = new SEO;
    $result = $seo
        ->title('Title')
        ->description('Description')
        ->keywords('php, laravel')
        ->canonical('https://example.com')
        ->robots('index, follow')
        ->author('John')
        ->openGraphType('article')
        ->twitterCard('summary');

    expect($result)->toBeInstanceOf(SEO::class);
    expect($seo->getTitle())->toBe('Title');
    expect($seo->getDescription())->toBe('Description');
    expect($seo->getKeywords())->toBe(['php', 'laravel']);
    expect($seo->getCanonical())->toBe('https://example.com');
    expect($seo->getRobots())->toBe('index, follow');
    expect($seo->getAuthor())->toBe('John');
});

test('seo can reset all values', function () {
    $seo = new SEO;
    $seo
        ->title('Title')
        ->description('Description')
        ->keywords(['php'])
        ->canonical('https://example.com')
        ->robots('noindex')
        ->author('John')
        ->openGraphType('article')
        ->twitterCard('summary')
        ->metaByName('custom', 'value');

    $seo->reset();

    expect($seo->getTitle())->toBeNull();
    expect($seo->getDescription())->toBeNull();
    expect($seo->getKeywords())->toBe([]);
    expect($seo->getCanonical())->toBeNull();
    expect($seo->getRobots())->toBeNull();
    expect($seo->getAuthor())->toBeNull();
    expect($seo->getMeta())->toBe([]);
});

test('seo can convert to array', function () {
    $seo = new SEO;
    $seo
        ->title('Title')
        ->description('Description')
        ->robots('index');

    $array = $seo->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKeys(['title', 'description', 'keywords', 'canonical', 'robots', 'author', 'openGraph', 'twitter', 'meta']);
});

test('seo generates html with title', function () {
    $seo = new SEO;
    $seo->title('My Title');

    $html = $seo->toHtml();

    expect($html)->toContain('<title>My Title</title>');
});

test('seo generates html with description', function () {
    $seo = new SEO;
    $seo->description('My description');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="description" content="My description">');
});

test('seo generates html with keywords', function () {
    $seo = new SEO;
    $seo->keywords(['php', 'laravel', 'accelade']);

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="keywords" content="php, laravel, accelade">');
});

test('seo generates html with robots', function () {
    $seo = new SEO;
    $seo->robots('index, follow');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="robots" content="index, follow">');
});

test('seo generates html with author', function () {
    $seo = new SEO;
    $seo->author('John Doe');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="author" content="John Doe">');
});

test('seo generates html with canonical link', function () {
    $seo = new SEO;
    $seo->canonical('https://example.com/page');

    $html = $seo->toHtml();

    expect($html)->toContain('<link rel="canonical" href="https://example.com/page">');
});

test('seo generates html with opengraph tags', function () {
    $seo = new SEO;
    $seo
        ->openGraphType('article')
        ->openGraphSiteName('My Site')
        ->openGraphTitle('OG Title')
        ->openGraphDescription('OG Description')
        ->openGraphUrl('https://example.com')
        ->openGraphImage('https://example.com/image.jpg', 'Alt Text')
        ->openGraphLocale('en_US');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta property="og:type" content="article">');
    expect($html)->toContain('<meta property="og:site:name" content="My Site">');
    expect($html)->toContain('<meta property="og:title" content="OG Title">');
    expect($html)->toContain('<meta property="og:description" content="OG Description">');
    expect($html)->toContain('<meta property="og:url" content="https://example.com">');
    expect($html)->toContain('<meta property="og:image" content="https://example.com/image.jpg">');
    expect($html)->toContain('<meta property="og:image:alt" content="Alt Text">');
    expect($html)->toContain('<meta property="og:locale" content="en_US">');
});

test('seo generates html with twitter tags', function () {
    $seo = new SEO;
    $seo
        ->twitterCard('summary_large_image')
        ->twitterSite('@mysite')
        ->twitterCreator('@johndoe')
        ->twitterTitle('Twitter Title')
        ->twitterDescription('Twitter Description')
        ->twitterImage('https://example.com/twitter.jpg', 'Twitter Alt');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="twitter:card" content="summary_large_image">');
    expect($html)->toContain('<meta name="twitter:site" content="@mysite">');
    expect($html)->toContain('<meta name="twitter:creator" content="@johndoe">');
    expect($html)->toContain('<meta name="twitter:title" content="Twitter Title">');
    expect($html)->toContain('<meta name="twitter:description" content="Twitter Description">');
    expect($html)->toContain('<meta name="twitter:image" content="https://example.com/twitter.jpg">');
    expect($html)->toContain('<meta name="twitter:image:alt" content="Twitter Alt">');
});

test('seo generates html with custom meta by name', function () {
    $seo = new SEO;
    $seo->metaByName('viewport', 'width=device-width');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta name="viewport" content="width=device-width">');
});

test('seo generates html with custom meta by property', function () {
    $seo = new SEO;
    $seo->metaByProperty('article:author', 'John Doe');

    $html = $seo->toHtml();

    expect($html)->toContain('<meta property="article:author" content="John Doe">');
});

test('seo generates html with custom meta attributes', function () {
    $seo = new SEO;
    $seo->meta(['http-equiv' => 'refresh', 'content' => '30']);

    $html = $seo->toHtml();

    expect($html)->toContain('<meta http-equiv="refresh" content="30">');
});

test('seo escapes html entities in output', function () {
    $seo = new SEO;
    $seo->title('<script>alert("xss")</script>');

    $html = $seo->toHtml();

    expect($html)->not->toContain('<script>');
    expect($html)->toContain('&lt;script&gt;');
});

test('seo render is alias for toHtml', function () {
    $seo = new SEO;
    $seo->title('Test');

    expect($seo->render())->toBe($seo->toHtml());
});

test('seo can be cast to string', function () {
    $seo = new SEO;
    $seo->title('Test');

    expect((string) $seo)->toBe($seo->toHtml());
});

test('seo implements htmlable interface', function () {
    $seo = new SEO;

    expect($seo)->toBeInstanceOf(\Illuminate\Contracts\Support\Htmlable::class);
});

test('seo implements arrayable interface', function () {
    $seo = new SEO;

    expect($seo)->toBeInstanceOf(\Illuminate\Contracts\Support\Arrayable::class);
});
