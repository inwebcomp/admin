<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Http\Requests\AdminRequest;

abstract class Controller extends \Illuminate\Routing\Controller
{
    /**
     * @var string
     */
    public $resource;

    public function __construct(AdminRequest $request)
    {
//        if ($this->resource)
//            $request->route()->setParameter('resource', $this->resource);
    }
}