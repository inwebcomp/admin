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
            return \Arr::first($this->options, function($option) use ($value) { return $option['value'] === $value; }) ?? null;
        });
    }

    protected function resolveAttribute($resource, $attribute, $original = true)
    {
        if (is_callable($this->resolveCallback)) {
            $value = parent::resolveAttribute($resource, $attribute);
            return call_user_func($this->resolveCallback, $value, $resource);
        }

        return parent::resolveAttribute($resource, $attribute);
    }

    public function options($options)
    {
        return $this->withMeta(['options' => $this->options = $options]);
    }

    public function small($value = true)
    {
        return $this->withMeta(['small' => $value]);
    }
}
