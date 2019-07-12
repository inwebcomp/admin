<?php

namespace InWeb\Admin\App\Http\Middleware;

use InWeb\Admin\App\Admin;

class BootTools
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
        Admin::bootTools($request);

        return $next($request);
    }
}
