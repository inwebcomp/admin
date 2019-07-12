<?php

namespace InWeb\Admin\App\Http\Requests;

use InWeb\Admin\App\Resources\Resource;
use InWeb\Base\Entity;

trait QueriesResources
{
    use DecodesFilters;

    /**
     * Transform the request into a query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toQuery()
    {
        /** @var Resource $resource */
        $resource = $this->resource();

        return $resource::buildIndexQuery(
            $this,
            $this->newQuery(),
            $this->search,
            $this->filters()->all(),
            $this->orderings()
        );

//        return $resource::buildIndexQuery(
//            $this, $this->newQuery(), $this->search,
//            $this->filters()->all(), $this->orderings(), $this->trashed()
//        );
    }

    /**
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->model()->newQuery();
    }

    /**
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScopes()
    {
        return $this->model()->newQueryWithoutScopes();
    }

    /**
     * Get the orderings for the request.
     *
     * @return array
     */
    public function orderings()
    {
        $resource = $this->newResource();

        if (! empty($this->orderBy)) {
            return [$this->orderBy => $this->orderByDirection ?? 'asc'];
        } else if ($resource->defaultOrdering($this)) {
            return $resource->defaultOrdering($this);
        }
    }
}
