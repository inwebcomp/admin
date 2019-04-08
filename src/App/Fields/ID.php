<?php

namespace InWeb\Admin\App\Fields;

use Illuminate\Support\Traits\Macroable;

class ID extends Field
{
    use Macroable;

    public $component = 'id-field';
    public $classes = ['w-1'];

    /**
     * Create a new field.
     *
     * @param  string|null  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name = null, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name ?? 'ID', $attribute, $resolveCallback);
    }

    /**
     * Create a new, resolved ID field for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return static
     */
    public static function forModel($model)
    {
        return tap(static::make('ID', $model->getKeyName()))->resolve($model);
    }
}
