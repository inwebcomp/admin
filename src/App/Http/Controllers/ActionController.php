<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ActionRequest;

class ActionController extends Controller
{
    /**
     * List the actions for the given resource.
     *
     * @param \InWeb\Admin\App\Http\Requests\AdminRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(AdminRequest $request)
    {
        return response()->json([
            'actions' => $request->newResource()->availableActions($request),
        ]);
    }

    /**
     * Perform an action on the specified resources.
     *
     * @param \InWeb\Admin\App\Http\Requests\ActionRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function store(ActionRequest $request)
    {
        $request->validateFields();

        return $request->action()->handleRequest($request);
    }
}
