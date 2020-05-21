<?php

namespace InWeb\Admin\App;

use InWeb\Admin\App\Fields\Field;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Admin\App\Resources\Resource;

trait FastEditable
{
    /**
     * @param ResourceIndexRequest $request
     * @param Resource              $resource
     * @return mixed
     */
    public function fastEditValue(ResourceIndexRequest $request, Resource $resource)
    {
        $attribute = $request->field;

        return $resource->resource->{$attribute};
    }

    public function fastEditUpdate(ResourceIndexRequest $request, Resource $resource)
    {
        $attribute = $request->field;
        $value = $request->input('value');
        $model = $resource->model();

        $model->{$attribute} = $value;
        $model->save();
    }

    public function fastEditComponent(ResourceDetailRequest $request)
    {
        return 'text-input';
    }

    public function fastEditProps(ResourceDetailRequest $request)
    {
        return [
            'small' => true,
        ];
    }

    public function fastEditUpdatedValue(ResourceIndexRequest $request)
    {
        /** @var Field $field */
        $field = $this->availableFields($request)->first(function($field) use ($request) {
            if (! $field instanceof Field)
                return false;

            return $field->attribute == $request->field;
        });

        $field->resolveForDisplay($request->findResourceOrFail()->model());

        return $field->value;
    }
}