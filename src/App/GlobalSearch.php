<?php

namespace InWeb\Admin\App;

use App\Traits\WithStatus;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Http\Requests\AdminRequest;

class GlobalSearch
{
    /**
     * The request instance.
     *
     * @var AdminRequest
     */
    public $request;
    /**
     * The resource class names that should be searched.
     *
     * @var \Illuminate\Support\Collection
     */
    public $resources;

    /**
     * Create a new global search instance.
     *
     * @param AdminRequest                    $request
     * @param  \Illuminate\Support\Collection $resources
     */
    public function __construct(AdminRequest $request, Collection $resources)
    {
        $this->request = $request;
        $this->resources = $resources;
    }

    /**
     * Get the matching resources.
     *
     * @return array
     */
    public function get()
    {
        $formatted = [];

        foreach ($this->getSearchResults() as $resource => $models) {
            foreach ($models as $model) {
                $instance = new $resource($model);

                $formatted[] = [
                    'resourceName'  => $resource::uriKey(),
                    'resourceTitle' => $resource::label(),
                    'title'         => $instance->title(),
                    'subTitle'      => $instance->subtitle(),
                    'resourceId'    => $model->getKey(),
                    'url'           => $instance->editPath(),
                    'visibility'    => in_array(WithStatus::class, class_uses($model)) ? $model->isPublished() : true,
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get the search results for the resources.
     *
     * @return array
     */
    protected function getSearchResults()
    {
        $results = [];

        foreach ($this->resources as $resource) {
            $query = $resource::buildIndexQuery(
                $this->request, $resource::newModel()->newQueryWithoutScopes(),
                $this->request->search
            );

            if (count($models = $query->limit(10)->get()) > 0) {
                $results[$resource] = $models;
            }
        }

        return collect($results)->sortKeys()->all();
    }
}
