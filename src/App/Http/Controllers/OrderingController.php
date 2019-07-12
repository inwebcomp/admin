<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;

class OrderingController extends Controller
{
    /**
     * List the orderings for the given resource.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AdminRequest $request)
    {
        $default = array_divide($request->newResource()->defaultOrdering($request));

        return response()->json([
            'orderings'       => $request->newResource()->availableOrderings($request),
            'defaultOrdering' => [
                'field' => $default[0][0],
                'direction' => $default[1][0],
            ],
        ]);
    }
}
