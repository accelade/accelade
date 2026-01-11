<?php

declare(strict_types=1);

namespace Accelade;

use Accelade\Compilers\AcceladeTagCompiler;
use Accelade\Console\InstallCommand;
use Accelade\Notification\NotificationManager;
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
