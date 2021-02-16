<?php

namespace InWeb\Admin\App\Http\Requests;

class CardRequest extends AdminRequest
{
    /**
     * Get all of the possible metrics for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableCards()
    {
        return $this->newResource()->availableCards($this);
    }
}
