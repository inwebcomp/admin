<?php

namespace InWeb\Admin\App\Tools;

use Illuminate\Http\Request;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Tool;

class ResourceManager extends Tool
{
    public static $displayInNavigation = false;

    /**
     * Perform any tasks that need to happen on tool registration.
     *
     * @return void
     */
    public function boot()
    {
        Admin::provideToScript([
            'resources' => function (Request $request) {
                return Admin::resourceInformation($request);
            },
        ]);
    }
}
