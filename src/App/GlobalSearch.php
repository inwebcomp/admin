<?php

namespace InWeb\Admin\App;

use InWeb\Base\Traits\WithStatus;
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
            $group = [];

            foreach ($models as $model) {
                $instance = new $resource($model);

                $group[] = [
                    'resourceName'  => $resource::uriKey(),
                    'resourceTitle' => $resource::label(),
                    'title'         => $instance->title(),
                    'subTitle'      => $instance->subtitle(),
                    'image'         => $instance->preview(),
                    'resourceId'    => $model->getKey(),
                    'url'           => $instance->editPathRelative(),
                    'visibility'    => in_array(WithStatus::class, class_uses($model)) ? $model->isPublished() : true,
                ];
            }

            $formatted[] = [
                'title' => $instance::singularLabel(),
                'items' => $group,
            ];
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
