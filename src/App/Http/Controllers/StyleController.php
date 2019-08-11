<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
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
        $path = Arr::get(Admin::allStyles(), $request->style);

        abort_if(is_null($path), 404);

        return response(
            file_get_contents($path),
            200, ['Content-Type' => 'text/css']
        );
    }
}
