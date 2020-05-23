<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Fields\Field;
use InWeb\Admin\App\Http\Requests\ResourceIndexRequest;
use InWeb\Admin\App\Resources\Resource;

class ResourceFastEditFieldController extends Controller
{
    public function edit(ResourceIndexRequest $request)
    {
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        $resource->authorizeToFastUpdate($request);

        $field = $this->getField($request, $resource);

        $props = $field->fastEditProps;

        if (is_callable($field->fastEditCalculatedProps)) {
            $props = array_merge($props, call_user_func($field->fastEditCalculatedProps, $resource));
        }

        return response()->json([
            'value' => $resource->fastEditValue($request, $resource),
            'props' => $props,
        ]);
    }

    public function update(ResourceIndexRequest $request)
    {
        /** @var Resource $resource */
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        $resource->authorizeToFastUpdate($request);

        $resource->fastEditUpdate($request, $resource);

        return response()->json([
            'value' => $resource->fastEditUpdatedValue($request),
        ]);
    }

    /**
     * @param ResourceIndexRequest $request
     * @param Resource             $resource
     * @return Field
     */
    public function getField(ResourceIndexRequest $request, Resource $resource)
    {
        /** @var Field $field */
        $field = $resource->availableFields($request)->first(function ($field) use ($request) {
            if (! $field instanceof Field)
                return false;

            return $field->attribute == $request->field;
        });

        return $field;
    }
}
