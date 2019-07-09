<?php

namespace InWeb\Admin\App\Fields;

class Datetime extends Text
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

        $this->withMeta(['type' => 'datetime-local']);

        $this->resolveUsing(function ($value) {
            return $value ? $value->format('Y-m-d\TH:i') : '';
        });

        $this->displayUsing(function ($value) {
            return (string) $value;
        });
    }
}
