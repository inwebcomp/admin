<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use InWeb\Admin\App\Admin;

class StyleController extends Controller
{
    /**
     * Serve the requested style.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return response(
            file_get_contents(Admin::allStyles()[$request->style]),
            200, ['Content-Type' => 'text/css']
        );
    }
}
