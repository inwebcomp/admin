<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Select extends Field
{
    use Macroable;

    public $component = 'select-field';

    public function options($options)
    {
        if ($options instanceof Collection)
            $options = $options->toArray();

        $this->default(current($options)['value']);

        return $this->withMeta(['options' => $options]);
    }

    public function withEmpty()
    {
        if ($this->meta['options'] instanceof Collection)
            $this->meta['options'] = $this->meta['options']->toArray();

        array_unshift($this->meta['options'], [
            'title' => '-- ' . __('Выберите значение'),
            'value' => null,
        ]);

        $this->default(current($this->meta['options'])['value']);

        return $this;
    }

    public static function prepare($array)
    {
        $result = [];

        foreach ($array as $value => $title) {
            $result[] = [
                'title' => $title,
                'value' => $value,
            ];
        }

        return $result;
    }
}
