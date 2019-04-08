<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Http\Request;

class RouterController extends Controller
{
	public function module(Request $request)
	{
		return view('admin::base.module');
	}
}
