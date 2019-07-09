<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait ResolvesOrderings
{
    /**
     * Get the orderings that are available for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function availableOrderings(AdminRequest $request)
    {
        return $this->resolveOrderings($request)->values();
    }

    /**
     * Get the orderings for the given request.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveOrderings(AdminRequest $request)
    {
        return collect(array_values($this->filter($this->orderings($request))));
    }

    /**
     * Get the default ordering on the entity.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|null
     */
    public function defaultOrdering(Request $request)
    {
        $ordering = $this->orderings($request)[0];

        return [$ordering['field'] => $ordering['direction']];
    }

    /**
     * Get the orderings available on the entity.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function orderings(Request $request)
    {
        $model = $request->model();

        $orderings = [
            [
                'title'     => __('По идентификатору'),
                'field'     => 'id',
                'direction' => 'asc',
            ],
            [
                'title'     => __('По дате создания'),
                'field'     => 'created_at',
                'direction' => 'asc',
            ],
            [
                'title'     => __('По дате изменения'),
                'field'     => 'updated_at',
                'direction' => 'asc',
            ],
        ];

        if ($model->positionable()) {
            array_unshift($orderings, [
                'title'     => __('По позиции'),
                'field'     => 'position',
                'direction' => 'asc',
            ]);
        }

        return $orderings;
    }
}