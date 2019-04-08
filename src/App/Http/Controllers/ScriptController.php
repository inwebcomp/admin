<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        return response(
            file_get_contents(Admin::allScripts()[$request->script]),
            200, ['Content-Type' => 'application/javascript']
        );
    }
}
