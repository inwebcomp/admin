<?php

namespace InWeb\Admin\Http\Middleware;

use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Events\AdminServiceProviderRegistered;
use InWeb\Admin\App\Providers\AdminServiceProvider;

class ServeAdmin
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if ($this->isAdminRequest($request)) {
            app()->register(AdminServiceProvider::class);

            AdminServiceProviderRegistered::dispatch();
        }

        return $next($request);
    }

    /**
     * Determine if the given request is intended for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isAdminRequest($request)
    {
        $path = trim(Admin::path(), '/') ?: '/';

        return $request->is($path) ||
               $request->is(trim($path.'/*', '/')) ||
               $request->is('nova-api/*');
    }
}
