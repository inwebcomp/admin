<?php

namespace InWeb\Admin\App\Http\Middleware;

use Closure;
use Auth;
use InWeb\Admin\App\Admin;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (strpos(\Route::current()->getName(), 'admin::login') === false) {
            if (! Auth::user() and in_array('api', \Route::current()->computedMiddleware)) {
                return abort('403');
            } else if (! Auth::user()) {
                return redirect(Admin::path() . '/login');
            } else if (! Auth::user()->isAdmin()) {
                return abort('403');
            }
        } else if (strpos(\Route::current()->getName(), 'admin::login.login-form') !== false) {
            if (Auth::user()) {
                return redirect(Admin::path());
            }
        }

        return $next($request);
    }
}
