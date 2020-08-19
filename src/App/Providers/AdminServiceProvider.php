<?php

namespace InWeb\Admin\App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\AdminRoute;
use InWeb\Admin\App\Console\InstallCommand;
use InWeb\Admin\App\Console\PublishCommand;
use InWeb\Admin\App\Console\SeedCommand;
use InWeb\Admin\App\Console\SyncResourcePermissionsCommand;
use InWeb\Admin\App\Console\ToolCommand;
use InWeb\Admin\App\Http\Middleware\AdminAccess;
use InWeb\Admin\App\Tools\ResourceManager;

class AdminServiceProvider extends ServiceProvider
{
    protected static $packagePath  = __DIR__ . '/../../';
    protected static $packageAlias = 'admin';

    public static function getPackageAlias()
    {
        return self::$packageAlias;
    }

    public static function getPackagePath()
    {
        return self::$packagePath;
    }

    /**
     * Bootstrap any package services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        $this->registerJsonVariables();

        $this->registerResources();
        $this->registerTools();

        $router->aliasMiddleware('admin-auth', AdminAccess::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register the package resources such as routes, templates, etc.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->registerRoutes();

        $this->loadViewsFrom(self::$packagePath . 'resources/views', self::$packageAlias);

        $this->loadTranslationsFrom(self::$packagePath . 'translations', self::$packageAlias);
        $this->loadJsonTranslationsFrom(self::$packagePath . 'translations');

        $this->loadMigrationsFrom(self::$packagePath . 'database/migrations');
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        AdminRoute::api('\InWeb\Admin\App\Http\Controllers', function () {
            $this->loadRoutesFrom(self::$packagePath . 'routes/api.php');
        });

        if (! $this->app->routesAreCached()) {
            \Route::getRoutes()->refreshNameLookups();
        }
    }

    private function registerPublishing()
    {
        // Provider
        $this->publishes([
            self::$packagePath . '/App/Console/stubs/AdminServiceProvider.stub' => app_path('Providers/AdminServiceProvider.php'),
        ], 'admin-provider');

        // Config
        $this->publishes([
            self::$packagePath . 'config/config.php' => config_path(self::$packageAlias . '.php'),
        ], 'admin-config');

        // Views
        $this->publishes([
            self::$packagePath . 'resources/views' => base_path('resources/views/vendor/admin'),
        ], 'admin-views');

        // Assets
        $this->publishes([
            self::$packagePath . 'public' => public_path('admin-assets'),
        ], 'admin-assets');
    }

    public function registerCommands()
    {
        $this->commands([
            InstallCommand::class,
            PublishCommand::class,
            SeedCommand::class,
            ToolCommand::class,
            SyncResourcePermissionsCommand::class,
        ]);
    }

    /**
     * Register the Nova JSON variables.
     *
     * @return void
     */
    protected function registerJsonVariables()
    {
        Admin::serving(function () {
            Admin::provideToScript([
                'timezone'     => config('app.timezone', 'UTC'),
                'translations' => $this->getTranslations(),
            ]);
        });
    }

    /**
     * Get the translation keys from file.
     *
     * @return array
     */
    private static function getTranslations()
    {
        $translationFile = self::$packagePath . 'resources/lang/' . app()->getLocale() . '.json';

        if (! is_readable($translationFile)) {
            return [];
        }

        return json_decode(file_get_contents($translationFile), true);
    }

    /**
     * Boot the standard Nova tools.
     *
     * @return void
     */
    protected function registerTools()
    {
        Admin::tools([
            new ResourceManager()
        ]);
    }
}
