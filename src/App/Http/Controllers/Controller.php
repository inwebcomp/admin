<?php

namespace InWeb\Admin\App\Http\Controllers;

abstract class Controller extends \Illuminate\Routing\Controller
{
    /**
     * @var string
     */
    public $resource;
}