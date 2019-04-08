<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use InWeb\Admin\App\Admin;

class AdminMenuController extends Controller
{
    public function menu(Request $request)
    {
        $menu = [];

        foreach (Admin::groupedResources($request) as $groupKey => $resources) {
            $group = Admin::groupInfo($groupKey);

            foreach ($resources as $resource) {
                if (! $resource::$displayInNavigation)
                    continue;

                $group['resources'][] = [
                    'route'    => $resource::route(),
                    'uriKey'   => $resource::uriKey(),
                    'label'    => $resource::label(),
                    'position' => $resource::position(),
                ];
            }

            usort($group['resources'], function ($a, $b) {
                return $a['position'] > $b['position'];
            });

            if (count($group['resources']))
                $menu[] = $group;
        };

        return $menu;
    }
}
