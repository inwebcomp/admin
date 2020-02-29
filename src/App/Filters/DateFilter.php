<?php

namespace InWeb\Admin\App\Filters;

use Illuminate\Http\Request;

abstract class DateFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'date-filter';

    public $range = false;

    /**
     * Set the first day of the week.
     *
     * @param  int  $day
     * @return $this
     */
    public function firstDayOfWeek($day)
    {
        return $this->withMeta([__FUNCTION__ => $day]);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [];
    }

    /**
     * Filter with range (since/till)
     *
     * @param bool $value
     * @return DateFilter
     */
    public function range($value = true)
    {
        $this->range = $value;

        return $this->withMeta(['range' => $value]);
    }

    /**
     * Set the default options for the filter.
     *
     * @return mixed
     */
    public function default()
    {
        if ($this->range)
            return [];

        return parent::default();
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'currentValue' => $this->default(),
            'valueSince' => '',
        ]);
    }
}
