<?php

namespace InWeb\Admin\App\Http\Controllers;

use Carbon\Carbon;
use DB;
use InWeb\Admin\App\Actions\ActionEvent;
use InWeb\Admin\App\Http\Requests\ResourceStoreRequest;
use InWeb\Admin\App\Http\Requests\ResourceUpdateRequest;

class ResourceStoreController extends Controller
{
    public function handle(ResourceStoreRequest $request)
    {
        $resource = $request->newResource();

        $resource->authorizeToCreate($request);

        $resource::validateForCreation($request);

        $model = DB::transaction(function () use ($request, $resource) {
            $model = $request->model();
            [$model, $callbacks] = $resource::fillForCreation($request, $model);

            $model->save();

            ActionEvent::forResourceCreate($request->user(), $model)->save();

            collect($callbacks)->each->__invoke();

            return $model;
        });

        return response()->json([
            'info' => $resource::info(),
            'panels' => $resource->availablePanels($request),
            'resource' => $model->attributesToArray(),
            'redirect' => $request->newResourceWith($model)->editPath(),
        ]);
    }
}
