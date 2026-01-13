<?php

declare(strict_types=1);

namespace Accelade\Facades;

use Accelade\Docs\DocSection;
use Accelade\Docs\DocSectionBuilder;
use Accelade\Docs\DocsRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Accelade documentation registry.
 *
 * @method static DocsRegistry registerPackage(string $package, string $docsPath)
 * @method static DocsRegistry registerSection(DocSection $section)
 * @method static DocsRegistry registerSections(array $sections)
 * @method static DocsRegistry registerGroup(string $key, string $label, string $icon = '', int $priority = 50)
 * @method static DocsRegistry addToGroup(string $groupKey, string $sectionSlug)
 * @method static DocSectionBuilder section(string $slug)
 * @method static DocSection|null getSection(string $slug)
 * @method static bool hasSection(string $slug)
 * @method static array getAllSections()
 * @method static array getSectionsWithDemo()
 * @method static array getGroups()
 * @method static array getNavigation()
 * @method static array getFrameworkPrefixes()
 * @method static string getFrameworkPrefix(string $framework)
 * @method static bool isValidFramework(string $framework)
 *
 * @see \Accelade\Docs\DocsRegistry
 */
class Docs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.docs';
    }
}
