<?php

namespace InWeb\Admin\App\Http\Controllers\Fields;

use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;

class ModelFieldController extends Controller
{
    public function search(ResourceIndexRequest $request)
    {
        $formatted = [];

        foreach ($this->getSearchResults($request) as $resource => $models) {
            foreach ($models as $model) {
                $instance = new $resource($model);

                $formatted[] = [
                    'resourceName'  => $resource::uriKey(),
                    'resourceId'    => $model->getKey(),
                    'resourceTitle' => $resource::label(),
                    'title'         => $instance->title(),
                    'subTitle'      => $instance->subtitle(),
                    'image'         => $instance->preview(),
                    'visibility'    => in_array(WithStatus::class, class_uses($model)) ? $model->isPublished() : true,
                    'value'         => $model->getKey(),
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get the search results for the resources.
     *
     * @param AdminRequest $request
     * @return array
     */
    protected function getSearchResults(AdminRequest $request)
    {
        $results = [];

        $resource = $request->resource();

        $query = $resource::buildIndexQuery(
            $request, $resource::newModel()->newQueryWithoutScopes(),
            $request->search
        );

        if (count($models = $query->limit(10)->get()) > 0) {
            $results[$resource] = $models;
        }

        return collect($results)->sortKeys()->all();
    }
}