<?php

namespace InWeb\Admin\App\Providers;

use Illuminate\Support\ServiceProvider;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Events\ServingAdmin;
use InWeb\Admin\App\Parameters;

class AdminApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function boot()
    {
        $this->routes();
        $this->groups();
        $this->resources();
        Admin::tools($this->tools());

        Admin::serving(function (ServingAdmin $event) {
            \Gate::before(function ($user, $ability) {
                return $user->hasRole('Super Admin') ? true : null;
            });

            $this->setLanguage();
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
            'icon'  => null,
        ]);

        Admin::group('other', __('Другие'));
        Admin::group('tools', __('Инструменты'), 'ellipsis-h');
    }

    /**
     * Register the application's Admin resources.
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function resources()
    {
        if (is_dir($dir = app_path('Admin/Resources')))
            Admin::resourcesIn($dir);
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

    private function setLanguage()
    {
        $value = Parameters::get('admin', 'language');

        if (in_array($value, config('inweb.languages')))
            Admin::setLocale($value);

        if (! $value and Admin::$defaultLocale)
            Admin::setLocale(Admin::$defaultLocale);
    }
}

