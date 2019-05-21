<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class Select extends Field
{
    use Macroable;

    public $component = 'select-field';

    public function options($options)
    {
        return $this->withMeta(['options' => $options]);
    }

    public function withEmpty()
    {
        array_unshift($this->meta['options'] , [
            'title' => '-- ' . __('Выберите значение'),
            'value' => null,
        ]);

        return $this;
    }
}
