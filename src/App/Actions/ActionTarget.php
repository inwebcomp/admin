<?php

namespace InWeb\Admin\App\Actions;

trait ActionTarget
{
    /**
     * Get all of the action events for the user.
     */
    public function performedActions()
    {
        return $this->morphMany(ActionEvent::class, 'target');
    }
}
