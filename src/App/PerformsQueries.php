<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Base\Entity;

trait PerformsQueries
{
    /**
     * Build an "index" query for the given resource.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $search
     * @param  array                                 $filters
     * @param  array                                 $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildIndexQuery(AdminRequest $request, $query, $search = null,
                                           array $filters = [], array $orderings = [])
    {
        return static::applyOrderings(static::applyFilters(
            $request, static::initializeQuery($request,
            static::indexQuery($request, $query->with(static::$with))
            , $search), $filters
        ), $orderings);
    }

    /**
     * Initialize the given index query.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQuery(AdminRequest $request, $query, $search)
    {
        return static::usesScout()
            ? static::initializeQueryUsingScout($request, $query, $search)
            : static::applySearch($query, $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {
        if ($search == '')
            return $query;

        return $query->where(function ($query) use ($search) {
            if (is_numeric($search) && in_array($query->getModel()->getKeyType(), ['int', 'integer'])) {
                $query->orWhere($query->getModel()->getQualifiedKeyName(), $search);
            }

            $model = $query->getModel();

            $connectionType = $query->getModel()->getConnection()->getDriverName();

            $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

            foreach (static::searchableColumns() as $column) {
                if ($model->translatable() and $model->isTranslationAttribute($column))
                    $query->orWhereTranslationLike($column, '%' . $search . '%');
                else
                    $query->orWhere($model->qualifyColumn($column), $likeOperator, '%' . $search . '%');
            }
        });
    }

    /**
     * Initialize the given index query using Laravel Scout.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQueryUsingScout(AdminRequest $request, $query, $search)
    {
        $keys = tap(static::newModel()->search($search), function ($scoutBuilder) use ($request) {
            static::scoutQuery($request, $scoutBuilder);
        })->take(200)->keys();

        return $query->whereIn(static::newModel()->getQualifiedKeyName(), $keys->all());
    }

    /**
     * Apply any applicable filters to the query.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyFilters(AdminRequest $request, $query, array $filters)
    {
        collect($filters)->each->__invoke($request, $query);

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  array                                 $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyOrderings($query, array $orderings)
    {
        if (empty($orderings)) {
            return empty($query->getQuery()->orders)
                ? static::coreOrdering($query)
                : $query;
        }

        foreach ($orderings as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return string
     */
    protected static function coreOrdering($query)
    {
        /** @var Entity $model */
        $model = $query->getModel();

        if ($model->positionable())
            $query->ordered();

        return $query->latest($model->getQualifiedKeyName());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->model()->newQuery();
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(AdminRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param AdminRequest            $request
     * @param  \Laravel\Scout\Builder $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(AdminRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(AdminRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param AdminRequest                           $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(AdminRequest $request, $query)
    {
        return $query;
    }
}
