<?php

namespace InWeb\Admin\App;

use App\Http\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use InWeb\Admin\App\Http\Middleware\Authorize;
use InWeb\Base\Http\Middleware\ApiLanguage;
use Route;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use InWeb\Admin\App\Http\Middleware\DispatchServingAdminEvent;

class AdminRoute
{
    /**
     * Group admin routers for api requests
     *
     * @param string $namespace
     * @param \Closure $callback
     */
    public static function api(string $namespace, \Closure $callback)
    {
        Route::namespace($namespace)
            ->middleware([
                'throttle:5000,1',
                \Illuminate\Session\Middleware\AuthenticateSession::class,
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                DispatchServingAdminEvent::class,
                'bindings',
                'admin-auth',
            ])
            ->as('admin.api::')
            ->prefix(Admin::path() . '/api')
            ->group($callback);
    }

    /**
     * Group admin routers for web requests
     *
     * @param \Closure $callback
     */
    public static function web(\Closure $callback)
    {
        Route::namespace('\InWeb\Admin\App\Http\Controllers')
            ->middleware(config('admin.middleware', []))
            ->as('admin::')
            ->prefix(Admin::path())
            ->group($callback);
    }
}
