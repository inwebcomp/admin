<?php

namespace InWeb\Admin\App\Http\Controllers;

use Carbon\Carbon;
use DB;
use InWeb\Admin\App\Actions\ActionEvent;
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
                return response($model->updated_at . ' > ' . Carbon::createFromTimestamp($request->input('_retrieved_at')), 409)->throwResponse();
            }

            /** @var \App\Models\Entity $model */
            [$model, $callbacks] = $resource::fillForUpdate($request, $model);

            ActionEvent::forResourceUpdate($request->user(), $model)->save();

            $model->save();

            collect($callbacks)->each->__invoke();

            return $model;
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
        return false;

        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
                Carbon::createFromTimestamp($request->input('_retrieved_at'))
            );
    }
}
