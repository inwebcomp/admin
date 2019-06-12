<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Http\Requests\Admin;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait ResolvesActions
{
    /**
     * Get the actions that are available for the given request.
     *
     * @param AdminRequest $request
     * @return Collection
     */
    public function availableActions(AdminRequest $request)
    {
        return $this->resolveActions($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the actions for the given request.
     *
     * @param AdminRequest $request
     * @return Collection
     */
    public function resolveActions(AdminRequest $request)
    {
        return collect(array_values($this->filter($this->actions($request))));
    }

    /**
     * Get the actions available on the entity.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
