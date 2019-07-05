<?php

namespace InWeb\Admin\App\Providers;

use Illuminate\Support\ServiceProvider;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Events\ServingAdmin;

class AdminApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes();

        Admin::serving(function (ServingAdmin $event) {
            $this->groups();
            $this->resources();
            Admin::tools($this->tools());
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Admin::routes();
    }

    /**
     * Register the application's Admin resource groups.
     *
     * @return void
     */
    protected function groups()
    {
        Admin::group('admin', [
            'label' => __('Админ-панель'),
            'icon' => null,
        ]);

        Admin::group('other', __('Другие'));
    }

    /**
     * Register the application's Admin resources.
     *
     * @return void
     */
    protected function resources()
    {
        Admin::resourcesIn(app_path('Admin/Resources'));
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

    public function tools()
    {
        return [];
    }
}

