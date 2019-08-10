<?php

namespace InWeb\Admin\App\Fields;

use InWeb\Admin\App\Http\Requests\AdminRequest;

class PasswordConfirmation extends Password
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

        $this->onlyOnForms();
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param AdminRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return void
     */
    protected function fillAttribute(AdminRequest $request, $requestAttribute, $model, $attribute)
    {
        //
    }
}
