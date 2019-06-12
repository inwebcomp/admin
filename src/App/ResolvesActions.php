<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait ResolvesActions
{
    /**
     * Get the actions that are available for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableActions(AdminRequest $request)
    {
        return $this->resolveActions($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the actions for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveActions(AdminRequest $request)
    {
        return collect(array_values($this->filter($this->actions($request))));
    }

    /**
     * Get the "pivot" actions that are available for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availablePivotActions(AdminRequest $request)
    {
        return $this->resolvePivotActions($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the "pivot" actions for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolvePivotActions(AdminRequest $request)
    {
        if ($request->viaRelationship()) {
            return collect(array_values($this->filter($this->getPivotActions($request))));
        }

        return collect();
    }

    /**
     * Get the "pivot" actions for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return array
     */
    protected function getPivotActions(AdminRequest $request)
    {
        $field = $this->availableFields($request)->first(function ($field) use ($request) {
            return isset($field->resourceName) &&
                   $field->resourceName == $request->viaResource;
        });

        if ($field && isset($field->actionsCallback)) {
            return array_values(call_user_func($field->actionsCallback, $request));
        }

        return [];
    }

    /**
     * Get the actions available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
