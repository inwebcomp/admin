<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;

class FilterController extends Controller
{
    /**
     * List the filters for the given resource.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AdminRequest $request)
    {
        return response()->json($request->newResource()->availableFilters($request));
    }

    public function searchCallback(AdminRequest $request)
    {
        $resource = $request->findRelatedModel();

        return [];
    }
}
