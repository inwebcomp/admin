<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Admin\App\Panel;

class ResourceEditController extends Controller
{
    public function handle(ResourceDetailRequest $request)
    {
        $resource = $request->newResourceWith(tap($request->findModelQuery(), function ($query) use ($request) {
            $request->newResource()->detailQuery($request, $query);
        })->firstOrFail());

        $resource->authorizeToView($request);

        return response()->json([
            'info' => array_merge($resource::info(), [
                'title' => $resource->title()
            ]),
            'panels' => $resource->availablePanels($request),
            'resource' => $this->assignFieldsToPanels(
                $request, $resource->serializeForEdit($request)
            ),
        ]);
    }

    /**
     * Assign any un-assigned fields to the default panel.
     *
     * @param ResourceDetailRequest $request
     * @param  array                $resource
     * @return array
     */
    protected function assignFieldsToPanels(ResourceDetailRequest $request, array $resource)
    {
        foreach ($resource['fields'] as $field) {
            $field->panel = $field->panel ?? Panel::defaultNameFor($request->newResource());
        }

        return $resource;
    }
}
