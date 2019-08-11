<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use InWeb\Admin\App\Admin;

class ScriptController extends Controller
{
    /**
     * Serve the requested script.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $path = Arr::get(Admin::allScripts(), $request->script);

        abort_if(is_null($path), 404);

        return response(
            file_get_contents($path),
            200, ['Content-Type' => 'application/javascript']
        );
    }
}
