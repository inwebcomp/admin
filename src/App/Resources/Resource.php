<?php

namespace InWeb\Admin\App\Resources;

use InWeb\Admin\App\Actions\ActionTarget;
use InWeb\Admin\App\FastEditable;
use InWeb\Admin\App\HasPermissions;
use InWeb\Admin\App\ResolvesFilters;
use InWeb\Admin\App\ResolvesOrderings;
use InWeb\Admin\App\WithNotification;
use InWeb\Base\Entity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Illuminate\Http\Resources\DelegatesToResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InWeb\Admin\App\Authorizable;
use InWeb\Admin\App\Fields\ID;
use InWeb\Admin\App\FillsFields;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\PerformsQueries;
use InWeb\Admin\App\PerformsValidation;
use InWeb\Admin\App\ResolvesActions;
use InWeb\Admin\App\ResolvesFields;
use Laravel\Scout\Searchable;
use Spatie\Permission\Models\Permission;

abstract class Resource
{
    use Authorizable,
        ResolvesFields,
        ResolvesFilters,
        ResolvesOrderings,
        ResolvesActions,
        FillsFields,
        PerformsValidation,
        ConditionallyLoadsAttributes,
        PerformsQueries,
        DelegatesToResource,
        WithNotification,
        ActionTarget,
        HasPermissions,
        FastEditable;
    /**
     * The underlying model resource instance.
     *
     * @var Entity
     */
    private $resource;
    /**
     * The underlying model class.
     *
     * @var string
     */
    public static $model = null;
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];
    /**
     * Indicates if the resoruce should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;
    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [];
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'other';
    /**
     * Position in menu
     *
     * @var string
     */
    protected static $position = 0;
    /**
     * Is detail view in inline mode or column
     *
     * @var boolean
     */
    public static $inline = true;
    /**
     * Classes to style all fields
     *
     * @var array
     */
    public $classes = [];

    /**
     * If specified, resources will be separated in groups by value (by collect()->groupBy($groupBy))
     *
     * @var null|array|callable|string
     */
    public static $groupBy = null;

    /**
     * Create a new resource instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource ?? new static::$model;
    }

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return static::$group;
    }

    /**
     * @param null|array|callable|string $value
     */
    public static function groupBy($value)
    {
        static::$groupBy = $value;
    }

    public function groupInfo(AdminRequest $request, $value)
    {
        return [
            'title' => $value,
            'selected' => false,
        ];
    }

    /**
     * Get color of menu item
     *
     * @return string|null
     */
    public static function color()
    {
        return null;
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return true;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param AdminRequest $request
     * @return array
     */
    abstract public function fields(AdminRequest $request);

    public function indexFields(AdminRequest $request)
    {
        return $this->fields($request);
    }

    public function detailFields(AdminRequest $request)
    {
        return $this->fields($request);
    }

    public function updateFields(AdminRequest $request)
    {
        return $this->detailFields($request);
    }

    public function creationFields(AdminRequest $request)
    {
        return $this->detailFields($request);
    }

    /**
     * Get the underlying model instance for the resource.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->resource;
    }

    /**
     * Determine if this resource is searchable.
     *
     * @return bool
     */
    public static function searchable()
    {
        return ! empty(static::$search);
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     */
    public static function searchableColumns()
    {
        return empty(static::$search)
            ? [static::newModel()->getKeyName()]
            : static::$search;
    }

    /**
     * Get the position in menu
     *
     * @return string
     */
    public static function position()
    {
        return static::$position;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return Str::plural(class_basename(get_called_class()));
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return Str::singular(static::label());
    }

    /**
     * Get the value that should be displayed to represent the resource id.
     *
     * @return string
     */
    public function id()
    {
        return $this->resource->getKey();
    }

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title()
    {
        return $this->{static::$title};
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return null;
    }

    public function preview()
    {
        return null;
    }

    /**
     * Link to be applied to resource title in edit view
     *
     * @return string|null
     */
    public function href()
    {
        return null;
    }

    /**
     * Get a fresh instance of the model represented by the resource.
     *
     * @return mixed
     */
    public static function newModel()
    {
        $model = static::$model;

        return new $model;
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::plural(Str::snake(class_basename(get_called_class()), '-'));
    }

    /**
     * Get the URI route name
     *
     * @return string
     */
    public static function route()
    {
        return 'index';
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return void
     */
    public function jsonSerialize()
    {
        $this->serializeWithId($this->resolveFields(
            resolve(Request::class)
        ));
    }

    /**
     * Prepare the resource for JSON serialization using the given fields.
     *
     * @param \Illuminate\Support\Collection $fields
     * @return array
     */
    public function serialize(Collection $fields)
    {
        return [
            'fields' => $fields->all(),
        ];
    }

    /**
     * Prepare the resource for JSON serialization using the given fields.
     *
     * @param \Illuminate\Support\Collection $fields
     * @return array
     */
    public function serializeWithId(Collection $fields)
    {
        return [
            'id'      => $fields->whereInstanceOf(ID::class)->first() ?: ID::forModel($this->resource),
            'classes' => $this->classes,
            'fields'  => $fields->all(),
        ];
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param AdminRequest $request
     * @param \Illuminate\Support\Collection $fields
     * @return array
     */
    public function serializeForIndex(AdminRequest $request, $fields = null)
    {
        return array_merge($this->serializeWithId($fields ?: $this->resolveIndexFields($request)), [
            'authorizedToView'       => $this->authorizedToView($request),
            'authorizedToUpdate'     => $this->authorizedToUpdate($request),
            'authorizedToFastUpdate' => $this->authorizedToFastUpdate($request),
            'authorizedToDelete'     => $this->authorizedToDelete($request),
        ]);
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function serializeForEdit(AdminRequest $request)
    {
        return array_merge($this->serializeWithId($this->resolveEditFields($request)), [
            'authorizedToView'   => $this->authorizedToView($request),
            'authorizedToUpdate' => $this->authorizedToUpdate($request),
            'authorizedToCreate' => static::authorizedToCreate($request),
            'authorizedToDelete' => $this->authorizedToDelete($request),
        ]);
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function serializeForCreate(AdminRequest $request)
    {
        return $this->serialize($this->resolveCreationFields($request));
    }

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     */
    public static function usesScout()
    {
        return in_array(Searchable::class, class_uses_recursive(static::newModel()));
    }

    public function editPath()
    {
        return '/resource/' . static::uriKey() . '#edit/' . static::uriKey() . '/' . $this->model()->getKey();
    }

    public static function info()
    {
        return [
            'uriKey'        => static::uriKey(),
            'label'         => static::label(),
            'singularLabel' => static::singularLabel(),
            'searchable'    => static::searchable(),
            'inline'        => static::$inline,
            'grouped'       => $grouped = (static::$groupBy !== null),
            'position'      => static::position(),
            'positionable'  => $grouped ? false : (new static)->model()->positionable(),
        ];
    }
}
