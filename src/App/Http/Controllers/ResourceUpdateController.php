<?php

namespace InWeb\Admin\App\Http\Controllers;

use Carbon\Carbon;
use DB;
use InWeb\Admin\App\Http\Requests\ResourceUpdateRequest;

class ResourceUpdateController extends Controller
{
    public function handle(ResourceUpdateRequest $request)
    {
        $resourceObject = $request->findResourceOrFail();

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        $model = DB::transaction(function () use ($request, $resource) {
            $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

            if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                return response('', 409)->throwResponse();
            }

            [$model, $callbacks] = $resource::fillForUpdate($request, $model);

            return tap(tap($model)->save(), function ($model) use ($request, $callbacks) {
//                ActionEvent::forResourceUpdate($request->user(), $model)->save();

                collect($callbacks)->each->__invoke();
            });
        });

        return response()->json([
            'info' => $resource::info(),
            'panels' => $resourceObject->availablePanels($request),
            'resource' => $model->attributesToArray(),
        ]);
    }

    /**
     * Determine if the model has been updated since it was retrieved.
     *
     * @param ResourceUpdateRequest                $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    protected function modelHasBeenUpdatedSinceRetrieval(ResourceUpdateRequest $request, $model)
    {
        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
                Carbon::createFromTimestamp($request->input('_retrieved_at'))
            );
    }
}
