<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Providers\AdminServiceProvider;

class PendingRouteRegistration
{
    /**
     * Indicates if the routes have been registered.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * Register the Admin routes.
     *
     * @return void
     */
    public function register()
    {
        $this->registered = true;

        if (! app()->routesAreCached()) {
            AdminRoute::web(function () {
                require AdminServiceProvider::getPackagePath() . 'routes/web.php';
            });
        }
    }

    /**
     * Handle the object's destruction and register the router route.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
