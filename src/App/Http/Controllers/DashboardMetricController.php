<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\DashboardMetricRequest;

class DashboardMetricController extends Controller
{
    /**
     * List the metrics for the dashboard.
     *
     * @param DashboardMetricRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function index(DashboardMetricRequest $request)
    {
        return $request->availableMetrics();
    }

    /**
     * Get the specified metric's value.
     *
     * @param DashboardMetricRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function show(DashboardMetricRequest $request)
    {
        return response()->json([
            'value' => $request->metric()->resolve($request),
        ]);
    }
}
