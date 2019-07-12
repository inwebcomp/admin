<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Status extends Field
{
    use Macroable;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'status-field';

    public $options = [];

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->displayUsing(function($value) {
            return array_first($this->options, function($option) use ($value) { return $option['value'] == $value; }) ?? null;
        });
    }

    public function options($options)
    {
        return $this->withMeta(['options' => $this->options = $options]);
    }
}