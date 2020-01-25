<?php

namespace InWeb\Admin\App\Fields;

use Carbon\Carbon;

class Date extends Text
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

        $this->withMeta(['type' => 'date']);

        $this->resolveUsing(function ($value) {
            return $value ? $value->format('Y-m-d') : '';
        });

        $this->displayUsing(function ($value) {
            return $value ? $value->format('Y.m.d') : null;
        });

        $this->fillUsing(function($request, $model, $attribute, $requestAttribute) {
            $value = $request->input($requestAttribute);
            try {
                $model->{$attribute} = $value ? Carbon::createFromFormat('Y-m-d', $value) : null;
            } catch (\Exception $ex) {}
        });
    }
}
