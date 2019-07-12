<?php

namespace InWeb\Admin\App\Fields;

class Date extends Text
{
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta(['type' => 'date']);

        $this->displayUsing(function ($value) {
            return (string) $value;
        });
    }
}
