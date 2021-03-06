<?php

namespace InWeb\Admin\App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Str;
use InWeb\Admin\App\Admin;

class AdminAccess
{
    static public $availableRoutes = [
        'admin.script',
        'admin.style',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->availableRoute())
            return $next($request);

        if (strpos(\Route::current()->getName(), 'admin.login') === false and strpos(\Route::current()->getName(), 'admin.api.login') === false) {
            Auth::viaRemember();

            if (! Auth::user() and Str::endsWith(\Route::current()->getPrefix(), '/api')) {
                return abort('403');
            } else if (! Auth::user()) {
                $redirect = trim($request->getRequestUri(), '/');

                if ($redirect == Admin::path())
                    $redirect = null;

                return redirect(Admin::path() . '/login' . ($redirect ? '?redirect=' . $redirect : ''));
            }
        } else if (strpos(\Route::current()->getName(), 'admin.login.login-form') !== false) {
            if (Auth::user()) {
                return redirect(Admin::path());
            }
        }

        return $next($request);
    }

    /**
     * @return boolean
     */
    public function availableRoute()
    {
        $available = false;

        foreach (self::$availableRoutes as $route) {
            if (strpos(\Route::current()->getName(), $route) !== false) {
                $available = true;
                break;
            }
        }

        return $available;
    }
}
