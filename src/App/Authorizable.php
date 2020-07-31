<?php

namespace InWeb\Admin\App;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use Illuminate\Auth\Access\AuthorizationException;
use InWeb\Admin\App\Models\AdminUser;

trait Authorizable
{
    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return static::authorizableByGate() or static::authorizableByPermissions();
    }

    /**
     * Determine if the given resource is authorizable by gate.
     *
     * @return bool
     */
    public static function authorizableByGate()
    {
        return ! is_null(Gate::getPolicyFor(static::newModel()));
    }

    /**
     * Determine if the given resource is authorizable by permissions.
     *
     * @return bool
     */
    public static function authorizableByPermissions()
    {
        return in_array(HasPermissions::class, class_uses_recursive(new static));
    }

    /**
     * Determine if the element should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return self::authorizedToViewAny($request);
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws \Throwable
     */
    public function authorizeToViewAny(Request $request)
    {
        if (static::authorizableByGate()) {
            if (method_exists(Gate::getPolicyFor(static::newModel()), 'viewAny')) {
                $this->authorizeTo($request, 'viewAny');
            }
        } else if (static::authorizableByPermissions()) {
            $this->authorizeTo($request, 'viewAny');
        }
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return true;
        }

        return method_exists(Gate::getPolicyFor(static::newModel()), 'viewAny')
                        ? Gate::check('viewAny', get_class(static::newModel()))
                        : (new static)->authorizedWithoutGateTo($request, 'viewAny');
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     *
     * @throws \Throwable
     */
    public function authorizeToView(Request $request)
    {
        return $this->authorizeTo($request, 'view') && $this->authorizeToViewAny($request);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToView(Request $request)
    {
        return $this->authorizedTo($request, 'view') && $this->authorizedToViewAny($request);
    }

    /**
     * Determine if the current user can create new resources or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Throwable
     */
    public static function authorizeToCreate(Request $request)
    {
        throw_unless(static::authorizedToCreate($request), AuthorizationException::class);
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param AdminRequest $request
     * @return bool
     */
    public static function authorizedToCreate(AdminRequest $request)
    {
        return $request->newResource()->authorizedTo($request, 'create');
    }

    /**
     * Determine if the current user can update the given resource or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Throwable
     */
    public function authorizeToUpdate(Request $request)
    {
        $this->authorizeTo($request, 'update');
    }

    /**
     * Determine if the current user can update the given resource or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Throwable
     */
    public function authorizeToFastUpdate(Request $request)
    {
        $this->authorizeToUpdate($request);
        $this->authorizeTo($request, 'fast-update');
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return $this->authorizedTo($request, 'update');
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToFastUpdate(Request $request)
    {
        return $this->authorizedToUpdate($request) and $this->authorizedTo($request, 'fast-update');
    }

    /**
     * Determine if the current user can delete the given resource or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Throwable
     */
    public function authorizeToDelete(Request $request)
    {
        $this->authorizeTo($request, 'delete');
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return $this->authorizedTo($request, 'delete');
    }

    /**
     * Determine if the current user can restore the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToRestore(Request $request)
    {
        return $this->authorizedTo($request, 'restore');
    }

    /**
     * Determine if the current user can force delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToForceDelete(Request $request)
    {
        return $this->authorizedTo($request, 'forceDelete');
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAdd(AdminRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'add'.class_basename($model);

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                        ? Gate::check($method, $this->model())
                        : true;
    }

    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttachAny(AdminRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attachAny'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model()])
                    : true;
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttach(AdminRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'attach'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param  \InWeb\Admin\App\Http\Requests\AdminRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $relationship
     * @return bool
     */
    public function authorizedToDetach(AdminRequest $request, $model, $relationship)
    {
        if (! static::authorizable()) {
            return true;
        }

        $method = 'detach'.Str::singular(class_basename($model));

        return method_exists(Gate::getPolicyFor($this->model()), $method)
                    ? Gate::check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ability
     * @return void
     *
     * @throws \Throwable
     */
    public function authorizeTo(Request $request, $ability)
    {
        throw_unless($this->authorizedTo($request, $ability), AuthorizationException::class);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        if (static::authorizableByGate())
            return Gate::check($ability, $this->resource);
        else if (static::authorizableByPermissions())
            return $this->authorizedWithoutGateTo($request, $ability);

        return true;
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ability
     * @return void
     *
     * @throws \Throwable
     */
    public function authorizeWithoutGateTo(Request $request, $ability)
    {
        throw_unless($this->authorizedWithoutGateTo($request, $ability), AuthorizationException::class);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedWithoutGateTo(Request $request, $ability)
    {
        return $request->user()->can($this::permissionActionName($ability));
    }
}
