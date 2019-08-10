<?php

namespace InWeb\Admin\App\Fields;

use InWeb\Admin\App\Http\Requests\AdminRequest;

class Password extends Text
{
    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  mixed|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta(['type' => 'password']);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(AdminRequest $request, $requestAttribute, $model, $attribute)
    {
        if (! empty($request[$requestAttribute])) {
            $model->{$attribute} = \Hash::make($request[$requestAttribute]);
        }
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(
            parent::jsonSerialize(),
            ['value' => '']
        );
    }
}
