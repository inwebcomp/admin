<?php

namespace InWeb\Admin\App\Fields;

use InWeb\Admin\App\Http\Requests\AdminRequest;

class Boolean extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'boolean-field';
    /**
     * The value to be used when the field is "true".
     *
     * @var bool
     */
    public $trueValue = true;
    /**
     * The value to be used when the field is "false".
     *
     * @var bool
     */
    public $falseValue = false;
    /**
     * The text alignment for the field's text in tables.
     *
     * @var string
     */
    public $textAlign = 'center';

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param bool    $original
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute, $original = true)
    {
        return parent::resolveAttribute($resource, $attribute, $original) == $this->trueValue ? true : false;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param  string      $requestAttribute
     * @param  object      $model
     * @param  string      $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(AdminRequest $request, $requestAttribute, $model, $attribute)
    {
        if (isset($request[$requestAttribute])) {
            $model->{$attribute} = $request[$requestAttribute] == 'true'
                ? $this->trueValue : $this->falseValue;
        }
    }

    /**
     * Specify the values to store for the field.
     *
     * @param  mixed $trueValue
     * @param  mixed $falseValue
     * @return $this
     */
    public function values($trueValue, $falseValue)
    {
        return $this->trueValue($trueValue)->falseValue($falseValue);
    }

    /**
     * Specify the value to store when the field is "true".
     *
     * @param  mixed $value
     * @return $this
     */
    public function trueValue($value)
    {
        $this->trueValue = $value;

        return $this;
    }

    /**
     * Specify the value to store when the field is "false".
     *
     * @param  mixed $value
     * @return $this
     */
    public function falseValue($value)
    {
        $this->falseValue = $value;

        return $this;
    }
}
