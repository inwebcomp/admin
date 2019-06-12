<?php

namespace InWeb\Admin\App\Actions;

trait Actionable
{
    /**
     * Get all of the action events for the user.
     */
    public function actions()
    {
        return $this->morphMany(ActionEvent::class, 'actionable');
    }
}
