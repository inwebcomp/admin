<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MainDashboard extends Dashboard
{
    public static function label()
    {
        return __('Рабочий стол');
    }

    public static function uriKey()
    {
        return 'main';
    }
}
