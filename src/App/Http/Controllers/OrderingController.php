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

        $orderings = $request->newResource()->availableOrderings($request);

        if (empty($default) or empty($default[0])) {
            $default = $orderings->first();
        } else {
            $default['field'] = $default[0][0];
            $default['direction'] = $default[1][0];
        }

        return response()->json([
            'orderings'       => $orderings,
            'defaultOrdering' => $default,
        ]);
    }
}
