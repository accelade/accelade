<?php

declare(strict_types=1);

namespace Accelade;

use Accelade\Compilers\AcceladeTagCompiler;
use Accelade\Console\InstallCommand;
use Accelade\Notification\NotificationManager;
use Accelade\SEO\SEO;
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
    }

    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerBladePrecompiler();
        $this->registerComponents();
        $this->registerRoutes();
        $this->registerPublishing();
        $this->registerCommands();

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

        // Demo routes (configurable)
        $this->registerDemoRoutes();
    }

    protected function registerDemoRoutes(): void
    {
        if (! config('accelade.demo.enabled', false)) {
            return;
        }

        Route::group([
            'prefix' => config('accelade.demo.prefix', 'demo'),
            'middleware' => config('accelade.demo.middleware', ['web']),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/demo.php');
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
}
