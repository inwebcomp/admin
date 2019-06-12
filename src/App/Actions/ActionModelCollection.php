<?php

namespace InWeb\Admin\App\Actions;

use InWeb\Admin\App\Http\Requests\ActionRequest;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ActionModelCollection extends EloquentCollection
{
    /**
     * Remove models the user does not have permission to execute the action against.
     *
     * @param  \InWeb\Admin\App\Http\Requests\ActionRequest  $request
     * @return static
     */
    public function filterForExecution(ActionRequest $request)
    {
        $action = $request->action();

        if (! $request->isPivotAction()) {
            $models = $this->filterByResourceAuthorization($request);
        } else {
            $models = $this;
        }

        return static::make($models->filter(function ($model) use ($request, $action) {
            return $action->authorizedToRun($request, $model);
        }));
    }

    /**
     * Remove models the user does not have permission to execute the action against.
     *
     * @param  \InWeb\Admin\App\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Support\Collection
     */
    protected function filterByResourceAuthorization(ActionRequest $request)
    {
        if ($request->action()->runCallback) {
            $models = $this->mapInto($request->resource())->map->model();
        } else {
            $models = $this->mapInto($request->resource())
                           ->filter->authorizedToUpdate($request)->map->model();
        }
        $action = $request->action();

        if ($action instanceof DestructiveAction) {
            $models = $this->mapInto($request->resource())
                           ->filter->authorizedToDelete($request)->map->model();
        }

        return $models;
    }
}
