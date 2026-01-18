<?php

declare(strict_types=1);

namespace Accelade;

use Accelade\Animation\AnimationManager;
use Accelade\Bridge\BridgeManager;
use Accelade\Compilers\AcceladeTagCompiler;
use Accelade\Console\InstallCommand;
use Accelade\Docs\DocsRegistry;
use Accelade\Icons\BladeIconsRegistry;
use Accelade\Notification\NotificationManager;
use Accelade\SEO\SEO;
use BladeUI\Icons\Factory as BladeIconsFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AcceladeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/accelade.php', 'accelade');

        $this->app->singleton('accelade', function ($app) {
            return new Accelade($app);
        });

        $this->app->singleton('accelade.notify', function ($app) {
            $manager = new NotificationManager;
            if ($app->bound('session.store')) {
                $manager->setSession($app['session.store']);
            }

            return $manager;
        });

        $this->app->singleton('accelade.seo', function () {
            return new SEO;
        });

        $this->app->singleton('accelade.animation', function () {
            return new AnimationManager;
        });

        $this->app->singleton('accelade.bridge', function () {
            return new BridgeManager;
        });

        $this->app->singleton('accelade.docs', function () {
            return new DocsRegistry;
        });

        // Bind the class to the singleton alias for dependency injection
        $this->app->alias('accelade.docs', DocsRegistry::class);

        // Register Blade Icons Registry
        $this->app->singleton(BladeIconsRegistry::class, function ($app) {
            return new BladeIconsRegistry(
                $app->make(BladeIconsFactory::class),
                $app->make(Filesystem::class)
            );
        });

        $this->app->alias(BladeIconsRegistry::class, 'accelade.icons');
    }

    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerBladePrecompiler();
        $this->registerComponents();
        $this->registerRoutes();
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerDefaultDocs();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accelade');
    }

    protected function registerBladeDirectives(): void
    {
        // @acceladeScripts - Include the JavaScript
        Blade::directive('acceladeScripts', function () {
            return "<?php echo app('accelade')->scripts(); ?>";
        });

        // @acceladeStyles - Include any CSS
        Blade::directive('acceladeStyles', function () {
            return "<?php echo app('accelade')->styles(); ?>";
        });

        // @accelade - Start an inline reactive block
        Blade::directive('accelade', function ($expression) {
            if (empty($expression)) {
                return "<?php app('accelade')->startComponent(); ?>";
            }

            return "<?php app('accelade')->startComponent({$expression}); ?>";
        });

        // @endaccelade - End inline reactive block
        Blade::directive('endaccelade', function () {
            return "<?php echo app('accelade')->endComponent(); ?>";
        });

        // @acceladeComponent - Internal directive for compiled components
        Blade::directive('acceladeComponent', function ($expression) {
            return "<?php app('accelade')->startComponentFromTag({$expression}); ?>";
        });

        // @endacceladeComponent - End compiled component
        Blade::directive('endacceladeComponent', function () {
            return "<?php echo app('accelade')->endComponentFromTag(); ?>";
        });

        // @acceladeNotifications - Render notifications container
        Blade::directive('acceladeNotifications', function () {
            return "<?php echo view('accelade::components.notifications')->render(); ?>";
        });

        // SEO Directives
        $this->registerSeoDirectives();
    }

    protected function registerSeoDirectives(): void
    {
        // @seoTitle('Title') - Set the page title
        Blade::directive('seoTitle', function ($expression) {
            return "<?php app('accelade.seo')->title({$expression}); ?>";
        });

        // @seoDescription('Description') - Set the page description
        Blade::directive('seoDescription', function ($expression) {
            return "<?php app('accelade.seo')->description({$expression}); ?>";
        });

        // @seoKeywords('keyword1, keyword2') - Set the page keywords
        Blade::directive('seoKeywords', function ($expression) {
            return "<?php app('accelade.seo')->keywords({$expression}); ?>";
        });

        // @seoCanonical('url') - Set the canonical URL
        Blade::directive('seoCanonical', function ($expression) {
            return "<?php app('accelade.seo')->canonical({$expression}); ?>";
        });

        // @seoRobots('index, follow') - Set the robots meta tag
        Blade::directive('seoRobots', function ($expression) {
            return "<?php app('accelade.seo')->robots({$expression}); ?>";
        });

        // @seoAuthor('Author Name') - Set the author meta tag
        Blade::directive('seoAuthor', function ($expression) {
            return "<?php app('accelade.seo')->author({$expression}); ?>";
        });

        // @seoOpenGraph(['type' => 'article', ...]) - Set OpenGraph data
        Blade::directive('seoOpenGraph', function ($expression) {
            return "<?php
                \$__seoOgData = {$expression};
                \$__seo = app('accelade.seo');
                if (isset(\$__seoOgData['type'])) \$__seo->openGraphType(\$__seoOgData['type']);
                if (isset(\$__seoOgData['site_name'])) \$__seo->openGraphSiteName(\$__seoOgData['site_name']);
                if (isset(\$__seoOgData['title'])) \$__seo->openGraphTitle(\$__seoOgData['title']);
                if (isset(\$__seoOgData['description'])) \$__seo->openGraphDescription(\$__seoOgData['description']);
                if (isset(\$__seoOgData['url'])) \$__seo->openGraphUrl(\$__seoOgData['url']);
                if (isset(\$__seoOgData['image'])) \$__seo->openGraphImage(\$__seoOgData['image'], \$__seoOgData['image_alt'] ?? null);
                if (isset(\$__seoOgData['locale'])) \$__seo->openGraphLocale(\$__seoOgData['locale']);
            ?>";
        });

        // @seoTwitter(['card' => 'summary_large_image', ...]) - Set Twitter Card data
        Blade::directive('seoTwitter', function ($expression) {
            return "<?php
                \$__seoTwitterData = {$expression};
                \$__seo = app('accelade.seo');
                if (isset(\$__seoTwitterData['card'])) \$__seo->twitterCard(\$__seoTwitterData['card']);
                if (isset(\$__seoTwitterData['site'])) \$__seo->twitterSite(\$__seoTwitterData['site']);
                if (isset(\$__seoTwitterData['creator'])) \$__seo->twitterCreator(\$__seoTwitterData['creator']);
                if (isset(\$__seoTwitterData['title'])) \$__seo->twitterTitle(\$__seoTwitterData['title']);
                if (isset(\$__seoTwitterData['description'])) \$__seo->twitterDescription(\$__seoTwitterData['description']);
                if (isset(\$__seoTwitterData['image'])) \$__seo->twitterImage(\$__seoTwitterData['image'], \$__seoTwitterData['image_alt'] ?? null);
            ?>";
        });

        // @seoMeta('name', 'content') - Add a custom meta tag by name
        Blade::directive('seoMeta', function ($expression) {
            return "<?php app('accelade.seo')->metaByName({$expression}); ?>";
        });

        // @seo - Output all SEO meta tags (place in <head>)
        Blade::directive('seo', function () {
            return "<?php echo app('accelade.seo')->toHtml(); ?>";
        });
    }

    protected function registerBladePrecompiler(): void
    {
        // Precompiler transforms <x-accelade:component> tags before standard Blade compilation
        Blade::precompiler(function (string $template) {
            return (new AcceladeTagCompiler($this->app))->compile($template);
        });
    }

    protected function registerComponents(): void
    {
        // Register accelade:: component namespace
        Blade::componentNamespace('Accelade\\Components', 'accelade');
    }

    protected function registerRoutes(): void
    {
        // Core Accelade routes
        Route::group([
            'prefix' => config('accelade.prefix', 'accelade'),
            'middleware' => config('accelade.middleware', ['web']),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        // Bridge routes (always enabled)
        $this->loadRoutesFrom(__DIR__.'/../routes/bridge.php');

        // Icons API routes
        $this->registerIconsRoutes();

        // Docs routes (configurable)
        $this->registerDocsRoutes();
    }

    protected function registerIconsRoutes(): void
    {
        Route::group([
            'prefix' => config('accelade.prefix', 'accelade').'/api/icons',
            'middleware' => config('accelade.middleware', ['web']),
        ], function () {
            Route::get('/sets', [\Accelade\Http\Controllers\IconsController::class, 'sets'])->name('accelade.icons.sets');
            Route::get('/search', [\Accelade\Http\Controllers\IconsController::class, 'search'])->name('accelade.icons.search');
            Route::get('/svg/{icon}', [\Accelade\Http\Controllers\IconsController::class, 'svg'])->where('icon', '.*')->name('accelade.icons.svg');
            Route::get('/{set}', [\Accelade\Http\Controllers\IconsController::class, 'icons'])->name('accelade.icons.list');
        });
    }

    protected function registerDocsRoutes(): void
    {
        if (! config('accelade.docs.enabled', false)) {
            return;
        }

        Route::group([
            'prefix' => config('accelade.docs.prefix', 'docs'),
            'middleware' => config('accelade.docs.middleware', ['web']),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/docs.php');
        });
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/accelade.php' => config_path('accelade.php'),
            ], 'accelade-config');

            // Publish assets
            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/accelade'),
            ], 'accelade-assets');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/accelade'),
            ], 'accelade-views');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    protected function registerDefaultDocs(): void
    {
        /** @var DocsRegistry $docs */
        $docs = $this->app->make('accelade.docs');

        // Register Accelade package path
        $docs->registerPackage('accelade', __DIR__.'/../docs');

        // Register navigation groups
        $docs->registerGroup('getting-started', 'Getting Started', 'book', 10);
        $docs->registerGroup('core', 'Core', 'cube', 20);
        $docs->registerGroup('resources', 'Resources', 'folder', 60);

        // Register sections by group
        $this->registerGettingStartedDocs($docs);
        $this->registerComponentsDocs($docs);
        $this->registerResourcesDocs($docs);
    }

    protected function registerGettingStartedDocs(DocsRegistry $docs): void
    {
        $docs->section('getting-started')->label('Getting Started')->markdown('getting-started.md')->inGroup('getting-started')->register();
        $docs->section('installation')->label('Installation')->markdown('installation.md')->inGroup('getting-started')->register();
        $docs->section('configuration')->label('Configuration')->markdown('configuration.md')->inGroup('getting-started')->register();
    }

    protected function registerComponentsDocs(DocsRegistry $docs): void
    {
        $docs->section('counter')->label('Counter Demo')->markdown('components.md')->demo()->inGroup('core')->register();
        $docs->section('navigation')->label('SPA Navigation')->markdown('spa-navigation.md')->demo()->inGroup('core')->register();
        $docs->section('link')->label('Link Component')->markdown('link.md')->demo()->inGroup('core')->register();
        $docs->section('progress')->label('Progress Bar')->markdown('spa-navigation.md')->demo()->inGroup('core')->register();
        $docs->section('persistent')->label('Persistent Layout')->markdown('persistent-layout.md')->demo()->inGroup('core')->register();
        $docs->section('data')->label('Data Component')->markdown('data.md')->demo()->inGroup('core')->register();
        $docs->section('state')->label('State Management')->markdown('state.md')->demo()->inGroup('core')->register();
        $docs->section('modal')->label('Modal')->markdown('modal.md')->demo()->inGroup('core')->register();
        $docs->section('toggle')->label('Toggle')->markdown('toggle.md')->demo()->inGroup('core')->register();
        $docs->section('icon')->label('Icon')->markdown('icon.md')->demo()->inGroup('core')->register();
        $docs->section('transition')->label('Transitions')->markdown('animations.md')->demo()->inGroup('core')->register();
        $docs->section('notifications')->label('Notifications')->markdown('notifications.md')->demo()->inGroup('core')->register();
        $docs->section('code-block')->label('Code Block')->markdown('code-block.md')->demo()->inGroup('core')->register();
        $docs->section('lazy')->label('Lazy Loading')->markdown('lazy-loading.md')->demo()->inGroup('core')->register();
        $docs->section('defer')->label('Defer')->markdown('content.md')->demo()->inGroup('core')->register();
        $docs->section('content')->label('Content')->markdown('content.md')->demo()->inGroup('core')->register();
        $docs->section('rehydrate')->label('Rehydrate')->markdown('rehydrate.md')->demo()->inGroup('core')->register();
        $docs->section('teleport')->label('Teleport')->markdown('teleport.md')->demo()->inGroup('core')->register();
        $docs->section('event-bus')->label('Event Bus')->markdown('event-bus.md')->demo()->inGroup('core')->register();
        $docs->section('event')->label('Event Component')->markdown('event.md')->demo()->inGroup('core')->register();
        $docs->section('bridge')->label('Bridge (PHP/JS)')->markdown('bridge.md')->demo()->inGroup('core')->register();
        $docs->section('shared-data')->label('Shared Data')->markdown('shared-data.md')->demo()->inGroup('core')->register();
        $docs->section('flash')->label('Flash Messages')->markdown('flash.md')->demo()->inGroup('core')->register();
        $docs->section('errors')->label('Error Handling')->markdown('exception-handling.md')->demo()->inGroup('core')->register();
        $docs->section('scripts')->label('Scripts')->markdown('scripts.md')->demo()->inGroup('core')->register();
    }

    protected function registerResourcesDocs(DocsRegistry $docs): void
    {
        $docs->section('api-reference')->label('API Reference')->markdown('api-reference.md')->inGroup('resources')->register();
        $docs->section('frameworks')->label('Frameworks')->markdown('frameworks.md')->inGroup('resources')->register();
        $docs->section('architecture')->label('Architecture')->markdown('architecture.md')->inGroup('resources')->register();
        $docs->section('testing')->label('Testing')->markdown('testing.md')->inGroup('resources')->register();
        $docs->section('contributing')->label('Contributing')->markdown('contributing.md')->inGroup('resources')->register();
        $docs->section('sponsor')->label('Sponsor')->markdown('sponsor.md')->inGroup('resources')->register();
        $docs->section('thanks')->label('Thanks')->markdown('thanks.md')->inGroup('resources')->register();
    }
}
