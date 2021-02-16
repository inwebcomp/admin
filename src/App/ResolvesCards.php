<?php

namespace InWeb\Admin\App;

use Illuminate\Http\Request;
use InWeb\Admin\App\Http\Requests\AdminRequest;

trait ResolvesCards
{
    /**
     * Get the cards that are available for the given request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function availableCards(AdminRequest $request)
    {
        return $this->resolveCards($request)->filter->authorize($request)->values();
    }

    /**
     * Get the cards for the given request.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveCards(AdminRequest $request)
    {
        return collect(array_values($this->filter($this->cards($request))));
    }

    /**
     * Get the cards available on the entity.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }
}
