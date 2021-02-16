<?php

namespace InWeb\Admin\App\Http\Controllers;

use Illuminate\Routing\Controller;
use InWeb\Admin\App\Http\Requests\CardRequest;

class CardController extends Controller
{
    /**
     * List the cards for the given resource.
     *
     * @param CardRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function index(CardRequest $request)
    {
        return $request->availableCards();
    }
}
