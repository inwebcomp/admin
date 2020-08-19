<?php

namespace InWeb\Admin\App;

use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use Closure;
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
     * @param Closure $callback
     */
    public static function api(string $namespace, Closure $callback)
    {
        Route::namespace($namespace)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                'throttle:5000,1',
                // AuthenticateSession::class,
                VerifyCsrfToken::class,
                DispatchServingAdminEvent::class,
                SubstituteBindings::class,
                'admin-auth',
            ])
            ->as('admin.api.')
            ->prefix(Admin::path() . '/api')
            ->group($callback);
    }

    /**
     * Group admin routers for web requests
     *
     * @param Closure $callback
     */
    public static function web(Closure $callback)
    {
        Route::namespace('\InWeb\Admin\App\Http\Controllers')
            ->middleware(config('admin.middleware', []))
            ->as('admin.')
            ->prefix(Admin::path())
            ->group($callback);
    }
}
