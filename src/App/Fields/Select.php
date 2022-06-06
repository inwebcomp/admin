<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Select extends Field
{
    use Macroable;

    public $component = 'select-field';

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta(['search' => false]);
    }

    public function withSearch()
    {
        return $this->withMeta(['search' => true]);
    }

    public function options($options)
    {
        if ($options instanceof Collection)
            $options = $options->toArray();

        $current = current($options);

        if ($current)
            $this->default($current['value']);

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

    public static function prepareOptions(
        $collection,
        $titleAttribute = 'title',
        $valueAttribute = 'id',
        $image = null,
        $color = null
    ) : array
    {
        if (! ($collection instanceof Collection))
            $collection = new Collection($collection);

        $result = [];

        foreach ($collection as $item) {
            $result[] = [
                'title' => is_callable($titleAttribute) ? $titleAttribute($item) : $item->{$titleAttribute},
                'value' => is_callable($valueAttribute) ? $valueAttribute($item) : $item->{$valueAttribute},
                'image' => is_callable($image) ? $image($item) : ($image ? $item->{$image} : null),
                'color' => is_callable($color) ? $color($item) : ($color ? $item->{$color} : null),
            ];
        }

        return $result;
    }
}
