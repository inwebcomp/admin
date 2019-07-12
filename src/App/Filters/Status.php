<?php

namespace InWeb\Admin\App\Filters;

use Illuminate\Http\Request;
use InWeb\Base\Entity;

class Status extends Filter
{
    public function name()
    {
        return __('Видимость');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request              $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed                                 $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('Опубликован') => Entity::STATUS_PUBLISHED,
            __('Скрыт')       => Entity::STATUS_HIDDEN,
        ];
    }
}