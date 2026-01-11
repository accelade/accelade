<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * SEO Facade for managing page metadata.
 *
 * @method static \Accelade\SEO\SEO title(?string $title) Set the page title
 * @method static string|null getTitle() Get the page title
 * @method static \Accelade\SEO\SEO description(?string $description) Set the page description
 * @method static string|null getDescription() Get the page description
 * @method static \Accelade\SEO\SEO keywords(string|array $keywords) Set page keywords
 * @method static array getKeywords() Get page keywords
 * @method static \Accelade\SEO\SEO canonical(?string $url) Set the canonical URL
 * @method static string|null getCanonical() Get the canonical URL
 * @method static \Accelade\SEO\SEO robots(?string $robots) Set robots meta tag
 * @method static string|null getRobots() Get robots meta tag
 * @method static \Accelade\SEO\SEO author(?string $author) Set author meta tag
 * @method static string|null getAuthor() Get author meta tag
 * @method static \Accelade\SEO\SEO openGraphType(?string $type) Set OpenGraph type
 * @method static \Accelade\SEO\SEO openGraphSiteName(?string $siteName) Set OpenGraph site name
 * @method static \Accelade\SEO\SEO openGraphTitle(?string $title) Set OpenGraph title
 * @method static \Accelade\SEO\SEO openGraphDescription(?string $description) Set OpenGraph description
 * @method static \Accelade\SEO\SEO openGraphUrl(?string $url) Set OpenGraph URL
 * @method static \Accelade\SEO\SEO openGraphImage(?string $image, ?string $alt = null) Set OpenGraph image
 * @method static \Accelade\SEO\SEO openGraphLocale(?string $locale) Set OpenGraph locale
 * @method static array getOpenGraph() Get all OpenGraph values
 * @method static \Accelade\SEO\SEO twitterCard(?string $card) Set Twitter card type
 * @method static \Accelade\SEO\SEO twitterSite(?string $site) Set Twitter site handle
 * @method static \Accelade\SEO\SEO twitterCreator(?string $creator) Set Twitter creator handle
 * @method static \Accelade\SEO\SEO twitterTitle(?string $title) Set Twitter title
 * @method static \Accelade\SEO\SEO twitterDescription(?string $description) Set Twitter description
 * @method static \Accelade\SEO\SEO twitterImage(?string $image, ?string $alt = null) Set Twitter image
 * @method static array getTwitter() Get all Twitter card values
 * @method static \Accelade\SEO\SEO metaByName(string $name, string $content) Add a meta tag by name
 * @method static \Accelade\SEO\SEO metaByProperty(string $property, string $content) Add a meta tag by property
 * @method static \Accelade\SEO\SEO meta(array $attributes) Add a custom meta tag
 * @method static array getMeta() Get all custom meta tags
 * @method static \Accelade\SEO\SEO reset() Reset all SEO values to defaults
 * @method static string|null buildTitle() Build the full title with separator and suffix
 * @method static string|null buildDescription() Get effective description
 * @method static array buildKeywords() Get effective keywords
 * @method static string|null buildCanonical() Get effective canonical URL
 * @method static array toArray() Convert SEO data to array
 * @method static string toHtml() Generate HTML meta tags
 * @method static string render() Render SEO tags as HTML string
 *
 * @see \Accelade\SEO\SEO
 */
class SEO extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.seo';
    }
}
