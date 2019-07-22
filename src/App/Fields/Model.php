<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Model extends Field
{
    use Macroable;

    public $component = 'model-field';
    public $model;
    public $immediate = true;

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        return $this->withMeta(['immediate' => $this->immediate]);
    }

    public function resource($resource)
    {
        return $this->withMeta(['resource' => $this->resource = $resource]);
    }

    public function immediate($immediate)
    {
        return $this->withMeta(['immediate' => $this->immediate = $immediate]);
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
}
