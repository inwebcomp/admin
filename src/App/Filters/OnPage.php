<?php

namespace InWeb\Admin\App\Filters;

use Illuminate\Http\Request;
use InWeb\Base\Entity;

class OnPage extends Filter
{
    public function __construct($default = null)
    {
        $this->default = $default;
    }

    public function name()
    {
        return __('На странице');
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
        return $query->take($value);
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
            '10' => 10,
            '20' => 20,
            '50' => 50,
            '100' => 100,
        ];
    }
}