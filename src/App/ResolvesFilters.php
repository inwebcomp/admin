<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait ResolvesFilters
{
    /**
     * Get the filters that are available for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function availableFilters(AdminRequest $request)
    {
        return $this->resolveFilters($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the filters for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveFilters(AdminRequest $request)
    {
        return collect(array_values($this->filter($this->filters($request))));
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }
}
