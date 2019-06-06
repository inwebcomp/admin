<?php

namespace InWeb\Admin\App\Http\Requests;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Model;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Resources\Resource;

/**
 * Trait InteractsWithResources
 * @package InWeb\Admin\App\Http\Requests
 * @property Resource|null resourceInstance
 * @property string|null resource
 * @property string|null resourceID
 */
trait InteractsWithResources
{
    public $resourceInstance = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function setResource(Resource $resource)
    {
        $this->resourceInstance = $resource;
        $this->resourceId = $resource->model()->getKey();

        return $this;
    }

    /**
     * Get the class name of the resource being requested.
     *
     * @return Resource
     */
    public function resource()
    {
        if ($this->resourceInstance)
            return $this->resourceInstance;

        return tap(Admin::resourceForKey($this->resource), function ($resource) {
            abort_if(is_null($resource), 404);
        });
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @return Resource
     */
    public function newResource()
    {
        $resource = $this->resource();

        return new $resource($this->model());
    }

    /**
     * Find the resource model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return Resource
     */
    public function findResourceOrFail($resourceId = null)
    {
        return $this->newResourceWith($this->findModelOrFail($resourceId));
    }

    /**
     * Find the model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findModelOrFail($resourceId = null)
    {
        if ($resourceId) {
            return $this->findModelQuery($resourceId)->firstOrFail();
        }

        return once(function () {
            return $this->findModelQuery()->firstOrFail();
        });
    }

    /**
     * Get the query to find the model instance for the request.
     *
     * @param  mixed|null $resourceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findModelQuery($resourceId = null)
    {
        return $this->newQueryWithoutScopes()->whereKey(
            $resourceId ?? $this->resourceId
        );
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return Resource
     */
    public function newResourceWith($model)
    {
        $resource = $this->resource();

        return new $resource($model);
    }

    /**
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->model()->newQuery();
    }

    /**
     * Get a new, scopeless query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScopes()
    {
        return $this->model()->newQueryWithoutScopes();
    }

    /**
     * Get a new instance of the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        $resource = $this->resource();

        return $resource::newModel();
    }
}
