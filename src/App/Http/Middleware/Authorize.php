<?php

namespace InWeb\Admin\App\Http\Middleware;

use InWeb\Admin\App\Admin;

class Authorize
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
        return Admin::check($request) ? $next($request) : abort(403);
    }
}
