<?php

namespace InWeb\Admin\App;

trait ResolvesValues
{
    /**
     * Resolve the index fields.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexValues()
    {
        return $this->indexFields()->each(function ($field) {
            $field->resolveForDisplay($this->resource);
        });
    }

    /**
     * Resolve the given fields to their values.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function resolveValues()
    {
        return $this->resource->model()->all();
    }
}
