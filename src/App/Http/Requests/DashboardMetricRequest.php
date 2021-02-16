<?php

namespace InWeb\Admin\App\Http\Requests;


use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Metrics\Metric;

class DashboardMetricRequest extends AdminRequest
{
    /**
     * Get the metric instance for the given request.
     *
     * @return Metric
     */
    public function metric()
    {
        return $this->availableMetrics()->first(function ($metric) {
            return $this->metric === $metric->uriKey();
        }) ?: abort(404);
    }

    /**
     * Get all of the possible metrics for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableMetrics()
    {
        return Admin::allAvailableDashboardCards($this)->whereInstanceOf(Metric::class);
    }
}
