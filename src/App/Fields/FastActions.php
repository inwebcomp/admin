<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class FastActions extends Field
{
    use Macroable;

    public $fullCell = true;

    public $component = 'fast-actions-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->attribute = 'id';
    }

    public function onlyOnHover()
    {
        $this->classes[] = 'data-table__value--show-on-hover';
        return $this;
    }

    public function edit($url)
    {
        return $this->withMeta(['edit' => $url]);
    }
}
