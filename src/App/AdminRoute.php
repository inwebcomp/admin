<?php

namespace InWeb\Admin\App;

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
        \Route::group([
            'prefix'     => Admin::path() . '/api',
            'namespace'  => $namespace,
            'middleware' => [
                'api',
                'admin-auth',
                DispatchServingAdminEvent::class
            ],
            'as'         => 'admin::',
        ], $callback);
    }

    /**
     * Group admin routers for web requests
     *
     * @param \Closure $callback
     */
    public static function web(\Closure $callback)
    {
        \Route::group([
            'prefix'     => Admin::path(),
            'namespace'  => '\InWeb\Admin\App\Http\Controllers',
            'middleware' => [
                'web',
                'admin-auth',
                DispatchServingAdminEvent::class
            ],
            'as'         => 'admin::',
        ], $callback);
    }
}
