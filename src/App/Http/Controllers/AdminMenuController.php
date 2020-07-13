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

        if (Admin::$groupedMenu) {
            $groups = Admin::groupedResourcesAndTools($request);
        } else {
            $groups = [];
            $groups['other'] = Admin::availableResources($request);
            $groups['tools'] = Admin::availableToolsForNavigation($request);
        }

        foreach ($groups as $groupKey => $resources) {
            $group = Admin::groupInfo($groupKey);

            foreach ($resources as $resource) {
                $group['resources'][] = [
                    'route'        => $resource::route(),
                    'uriKey'       => $resource::uriKey(),
                    'label'        => $resource::label(),
                    'position'     => $resource::position(),
                    'notification' => $resource::notification(),
                    'color'        => $resource::color(),
                ];
            }

            if (isset($group['resources']) and $group['resources'] and count($group['resources'])) {
                usort($group['resources'], function ($a, $b) {
                    return $a['position'] > $b['position'];
                });

                $menu[] = $group;
            }
        };

        return [
            'menu' => $menu,
        ];
    }
}
