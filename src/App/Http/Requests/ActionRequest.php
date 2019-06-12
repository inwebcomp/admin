<?php

namespace InWeb\Admin\App\Http\Requests;

use Closure;
use Illuminate\Support\Fluent;
use Illuminate\Http\UploadedFile;
use InWeb\Admin\App\Fields\ActionFields;
use InWeb\Admin\App\Actions\ActionModelCollection;

class ActionRequest extends AdminRequest
{
    use QueriesResources;

    /**
     * Get the action instance specified by the request.
     *
     * @return \InWeb\Admin\App\Actions\Action
     */
    public function action()
    {
        return once(function () {
            return $this->availableActions()->first(function ($action) {
                return $action->uriKey() == $this->query('action');
            }) ?: abort($this->actionExists() ? 403 : 404);
        });
    }

    /**
     * Get the all actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function resolveActions()
    {
        return $this->newResource()->resolveActions($this);
    }

    /**
     * Get the possible actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableActions()
    {
        return $this->resolveActions()->filter->authorizedToSee($this)->values();
    }

    /**
     * Determine if the specified action exists at all.
     *
     * @return bool
     */
    protected function actionExists()
    {
        return $this->resolveActions()->contains(function ($action) {
            return $action->uriKey() == $this->query('action');
        });
    }

    /**
     * Get the selected models for the action in chunks.
     *
     * @param int $count
     * @param \Closure $callback
     * @return mixed
     */
    public function chunks($count, Closure $callback)
    {
        $output = [];

        $this->toSelectedResourceQuery()->when(! $this->forAllMatchingResources(), function ($query) {
            $query->whereKey(explode(',', $this->resources));
        })->latest($this->model()->getKeyName())->chunk($count, function ($chunk) use ($callback, &$output) {
            $output[] = $callback($this->mapChunk($chunk));
        });

        return $output;
    }

    /**
     * Get the query for the models that were selected by the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function toSelectedResourceQuery()
    {
        if ($this->forAllMatchingResources()) {
            return $this->toQuery();
        }

        return $this->newQueryWithoutScopes();
    }

    /**
     * Map the chunk of models into an appropriate state.
     *
     * @param \Illuminate\Database\Eloquent\Collection $chunk
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function mapChunk($chunk)
    {
        return ActionModelCollection::make($chunk);
    }

    /**
     * Validqte the given fields.
     *
     * @return void
     */
    public function validateFields()
    {
        $this->validate(collect($this->action()->fields())->mapWithKeys(function ($field) {
            return $field->getCreationRules($this);
        })->all());
    }

    /**
     * Resolve the fields for database storage using the request.
     *
     * @return array
     */
    public function resolveFieldsForStorage()
    {
        return collect($this->resolveFields()->getAttributes())->map(function ($attribute) {
            return $attribute instanceof UploadedFile ? $attribute->hashName() : $attribute;
        })->all();
    }

    /**
     * Resolve the fields using the request.
     *
     * @return \InWeb\Admin\App\Fields\ActionFields
     */
    public function resolveFields()
    {
        return once(function () {
            $fields = new Fluent;

            $results = collect($this->action()->fields())->mapWithKeys(function ($field) use ($fields) {
                return [$field->attribute => $field->fillForAction($this, $fields)];
            });

            return new ActionFields(collect($fields->getAttributes()), $results->filter(function ($field) {
                return is_callable($field);
            }));
        });
    }

    /**
     * Get the key of model that lists the action on its dashboard.
     *
     * When running pivot actions, this is the key of the owning model.
     *
     * @param \Illuminate\Database\Eloquent\Model
     * @return int
     */
    public function actionableKey($model)
    {
        return $model->getKey();
    }

    /**
     * Get the model instance that lists the action on its dashboard.
     *
     * When running pivot actions, this is the owning model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function actionableModel()
    {
        return $this->model();
    }

    /**
     * Get the key of model that is the target of the action.
     *
     * When running pivot actions, this is the key of the target model.
     *
     * @param \Illuminate\Database\Eloquent\Model
     * @return int
     */
    public function targetKey($model)
    {
        return $model->getKey();
    }

    /**
     * Get an instance of the target model of the action.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function targetModel()
    {
        return $this->model();
    }

    /**
     * Determine if the request is for all matching resources.
     *
     * @return bool
     */
    public function forAllMatchingResources()
    {
        return $this->resources === 'all';
    }
}
