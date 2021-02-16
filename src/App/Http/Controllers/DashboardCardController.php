<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\DashboardCardRequest;

class DashboardCardController extends Controller
{
    /**
     * List the cards for the dashboard.
     *
     * @param DashboardCardRequest $request
     * @param string $dashboard
     * @return \Illuminate\Support\Collection
     */
    public function index(DashboardCardRequest $request, $dashboard = 'main')
    {
        return $request->availableCards($dashboard);
    }
}
