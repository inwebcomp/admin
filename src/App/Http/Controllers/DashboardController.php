<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Http\Requests\DashboardRequest;
use InWeb\Admin\App\MainDashboard;

class DashboardController extends Controller
{
    /**
     * Return the details for the Dashboard.
     *
     * @param DashboardRequest $request
     * @param string $dashboard
     * @return \Illuminate\Http\Response
     */
    public function index(DashboardRequest $request, $dashboard = 'main')
    {
        $instance = Admin::dashboardForKey($dashboard, $request);

        abort_if(is_null($instance) && $dashboard !== 'main', 404);

        if ($dashboard === 'main')
            $instance = new MainDashboard();

        return response()->json([
            'label' => ! $instance ? __('Рабочий стол') : $instance->label(),
            'cards' => $request->availableCards($dashboard),
        ]);
    }
}
