<?php

namespace InWeb\Admin\App\Http\Controllers\ResourceTools;

use App\Models\Param;
use DB;
use App\Models\Category;
use App\Models\Filter;
use Illuminate\Http\Request;
use InWeb\Admin\App\Actions\ActionEvent;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;

class ActionsOnModelController extends Controller
{
    public function index(ResourceDetailRequest $request)
    {
        $object = $request->findResourceOrFail();

        return $object->performedActions()->with('user')->take(100)->latest()->get();
    }
}
