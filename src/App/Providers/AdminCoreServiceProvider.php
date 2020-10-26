<?php

namespace InWeb\Admin\App\Providers;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\ServiceProvider;
use InWeb\Admin\App\Http\Middleware\ServeAdmin;

/**
 * The primary purpose of this service provider is to push the ServeAdmin
 * middleware onto the middleware stack so we only need to register a
 * minimum number of resources for all other incoming app requests.
 */
class AdminCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->app->register(AdminServiceProvider::class);
        }

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(
                AdminServiceProvider::getPackagePath() . 'config/config.php',
                AdminServiceProvider::getPackageAlias()
            );
        }

        $this->app->make(HttpKernel::class)->pushMiddleware(ServeAdmin::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
