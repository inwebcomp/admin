<?php

namespace InWeb\Admin\App\Fields;

class TreeField extends Field
{
    public $component = 'tree-field';
    public $fullCell = true;

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
    }
}
