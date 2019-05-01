<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Admin;
use InWeb\Admin\App\GlobalSearch;
use InWeb\Admin\App\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    /**
     * Get the global search results for the given query.
     *
     * @param SearchRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(SearchRequest $request)
    {
        return (new GlobalSearch(
            $request, Admin::globallySearchableResources($request)
        ))->get();
    }
}
