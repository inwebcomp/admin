<?php

namespace InWeb\Admin\App\Actions;

use InWeb\Admin\App\Resource;
use InWeb\Admin\App\Fields\ID;
use Illuminate\Http\Request;
use InWeb\Admin\App\Fields\Text;
use InWeb\Admin\App\Fields\Status;
use InWeb\Admin\App\Fields\DateTime;
use InWeb\Admin\App\Fields\KeyValue;
use InWeb\Admin\App\Http\Requests\NovaRequest;
use InWeb\Admin\App\Fields\MorphToActionTarget;

class ActionResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = ActionEvent::class;

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can edit resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can delete resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),
            Text::make(__('Action Name'), 'name'),

            Text::make(__('Action Initiated By'), function () {
                return $this->user->name ?? $this->user->email ?? __('Nova User');
            }),

            MorphToActionTarget::make(__('Action Target'), 'target'),

            Status::make(__('Action Status'), 'status', function ($value) {
                return ucfirst($value);
            })->loadingWhen(['Waiting', 'Running'])->failedWhen(['Failed']),

            $this->when(isset($this->original), function () {
                return KeyValue::make(__('Original'));
            }),

            $this->when(isset($this->changes), function () {
                return KeyValue::make(__('Changes'));
            }),

            DateTime::make(__('Action Happened At'), 'created_at')->exceptOnForms(),
        ];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \InWeb\Admin\App\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->with('user');
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return false;
    }

    /**
     * Determine if this resource is searchable.
     *
     * @return bool
     */
    public static function searchable()
    {
        return false;
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Actions');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Action');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'action-events';
    }
}
