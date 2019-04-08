<?php

namespace InWeb\Admin\App\Http\Middleware;

use InWeb\Admin\App\Events\ServingAdmin;

class DispatchServingAdminEvent
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        ServingAdmin::dispatch($request);

        return $next($request);
    }
}
