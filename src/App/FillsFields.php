<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Http\Requests\AdminRequest;

trait FillsFields
{
    /**
     * Fill a new model instance using the given request.
     *
     * @param AdminRequest                         $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public static function fill(AdminRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->resolveStoreFields($request)
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param AdminRequest                         $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public static function fillForUpdate(AdminRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->resolveUpdateFields($request)
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param AdminRequest                         $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    public static function fillForCreation(AdminRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->resolveStoreFields($request)
        );
    }

    /**
     * Fill the given fields for the model.
     *
     * @param AdminRequest                         $request
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  \Illuminate\Support\Collection      $fields
     * @return array
     */
    protected static function fillFields(AdminRequest $request, $model, $fields)
    {
        return [$model, $fields->map->fill($request, $model)->filter(function ($callback) {
            return is_callable($callback);
        })->values()->all()];
    }
}
