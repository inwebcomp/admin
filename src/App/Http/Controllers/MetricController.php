<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\MetricRequest;

class MetricController extends Controller
{
    /**
     * List the metrics for the given resource.
     *
     * @param MetricRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function index(MetricRequest $request)
    {
        return $request->availableMetrics();
    }

    /**
     * Get the specified metric's value.
     *
     * @param MetricRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function show(MetricRequest $request)
    {
        return response()->json([
            'value' => $request->metric()->resolve($request),
        ]);
    }
}
