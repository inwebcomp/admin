<?php

namespace InWeb\Admin\App;

use Illuminate\Support\Collection;
use InWeb\Admin\App\Contracts\Resolvable;
use InWeb\Admin\App\Fields\FieldCollection;
use InWeb\Admin\App\Fields\ID;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ResourceCreateRequest;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Admin\App\Http\Requests\ResourceStoreRequest;
use InWeb\Admin\App\Http\Requests\ResourceUpdateRequest;

trait ResolvesFields
{
    /**
     * Resolve the index fields.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveIndexFields(AdminRequest $request)
    {
        return $this->availableFields($request)->each(function ($field) {
            $field->resolveForDisplay($this->resource);
        });
    }

    /**
     * Resolve the detail fields.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveEditFields(AdminRequest $request)
    {
        return $this->resolveFields($request);
    }

    /**
     * Resolve the update fields.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveUpdateFields(AdminRequest $request)
    {
        return $this->removeNonUpdateFields($this->resolveFields($request));
    }

    /**
     * Resolve the creation fields.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveCreationFields(AdminRequest $request)
    {
        return $this->removeNonCreationFields($this->resolveFields($request));
    }

    /**
     * Resolve the store fields.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveStoreFields(AdminRequest $request)
    {
        return $this->removeNonStoreFields($this->resolveFields($request));
    }

    /**
     * Remove non-store fields from the given collection.
     *
     * @param \Illuminate\Support\Collection $fields
     * @return \Illuminate\Support\Collection
     */
    protected function removeNonStoreFields(Collection $fields)
    {
        return $this->removeNonUpdateFields($fields);
    }

    /**
     * Remove non-creation fields from the given collection.
     *
     * @param \Illuminate\Support\Collection $fields
     * @return \Illuminate\Support\Collection
     */
    protected function removeNonCreationFields(Collection $fields)
    {
        return $fields->reject(function ($field) {
            return ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                $field instanceof ResourceToolElement ||
                $field instanceof Panel ||
                (isset($field->attribute) and $field->attribute === 'ComputedField');
        });
    }

    /**
     * Remove non-update fields from the given collection.
     *
     * @param \Illuminate\Support\Collection $fields
     * @return \Illuminate\Support\Collection
     */
    protected function removeNonUpdateFields(Collection $fields)
    {
        return $fields->reject(function ($field) {
            return ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                $field instanceof ResourceToolElement ||
                $field instanceof Section ||
                (isset($field->attribute) and $field->attribute === 'ComputedField');
        });
    }

    /**
     * Resolve the given fields to their values.
     *
     * @param AdminRequest $request
     * @return \Illuminate\Support\Collection
     */
    protected function resolveFields(AdminRequest $request)
    {
        return tap($this->availableFields($request), function ($fields) {
            $fields->whereInstanceOf(Resolvable::class)->each->resolve($this->resource);
        });
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param AdminRequest $request
     * @return FieldCollection
     */
    public function availableFields(AdminRequest $request, $filter = true)
    {
        if ($request instanceof ResourceIndexRequest)
            $fields = $this->indexFields($request);
        else if ($request instanceof ResourceDetailRequest)
            $fields = $this->detailFields($request);
        else if ($request instanceof ResourceUpdateRequest)
            $fields = $this->updateFields($request);
        else if ($request instanceof ResourceStoreRequest)
            $fields = $this->creationFields($request);
        else if ($request instanceof ResourceCreateRequest)
            $fields = $this->creationFields($request);
        else
            $fields = $this->fields($request);

        if (! $filter)
            return new FieldCollection(array_values($fields));

        return new FieldCollection(array_values($this->filter(collect($fields)->filter->authorizedToSee($request)->toArray())));
    }

    /**
     * Get the panels that are available for the given request.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function availablePanels(AdminRequest $request)
    {
        $panels = $this->availableFields($request, false)
            ->whereInstanceOf(Panel::class)
            ->filter(function (Panel $panel) use ($request) {
                return $panel->authorizedToSee($request);
            })
            ->values();

        $default = Panel::defaultNameFor($request->newResource());

        return $panels->when($panels->where('name', $default)->isEmpty(), function ($panels) use ($default) {
            return $panels->push(new Panel($default));
        })->all();
    }
}
