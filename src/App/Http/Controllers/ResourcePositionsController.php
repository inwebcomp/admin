<?php

namespace InWeb\Admin\App\Http\Controllers;

use App\Traits\Positionable;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;

class ResourcePositionsController extends Controller
{
    /**
     * Update params positions
     *
     * @param ResourceIndexRequest $request
     * @return void
     */
    public function handle(ResourceIndexRequest $request)
    {
        /** @var Positionable $model */
        $model = $request->model();

        $model::updatePositions($request->input('items'), $request->input('positions'));
    }
}
