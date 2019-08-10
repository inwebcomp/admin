<?php

namespace InWeb\Admin\App\Tools;

use Illuminate\Http\Request;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Tool;

class ResourceManager extends Tool
{
    public function __construct($component = null)
    {
        parent::__construct($component);
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return false;
    }

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
