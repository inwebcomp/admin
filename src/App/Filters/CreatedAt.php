<?php

namespace InWeb\Admin\App\Filters;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CreatedAt extends DateFilter
{
    public function name()
    {
        return __('Дата создания');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if (! $value)
            return $query;

        if ($this->range) {
            $value = explode(' - ', $value);

            if (($value[0] ?? false) && ($value[1] ?? false)) {
                $query->whereBetween('created_at', [Carbon::make($value[0]), Carbon::make($value[1])]);
            } else if (($value[0] ?? false) && ! ($value[1] ?? false)) {
                return $query->whereDate('created_at', '>=', $value[0]);
            } else if ($value[1] ?? false) {
                return $query->whereDate('created_at', '<=', $value[1]);
            }

            return $query;
        }

        return $query->whereDate('created_at', '=', $value);
    }
}