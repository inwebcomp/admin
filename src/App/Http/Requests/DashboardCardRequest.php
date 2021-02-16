<?php

namespace InWeb\Admin\App\Http\Requests;

use InWeb\Admin\App\Admin;

class DashboardCardRequest extends AdminRequest
{
    /**
     * Get all of the possible cards for the request.
     *
     * @param  string  $dashboard
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableCards($dashboard)
    {
        if ($dashboard === 'main') {
            return collect(Admin::$defaultDashboardCards)
                ->unique()
                ->filter
                ->authorize($this)
                ->values();
        }

        return Admin::availableDashboardCardsForDashboard($dashboard, $this);
    }
}
