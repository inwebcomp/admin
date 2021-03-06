<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Http\Requests\ResourceCreateRequest;
use InWeb\Admin\App\Panel;

class ResourceCreateController extends Controller
{
    public function handle(ResourceCreateRequest $request)
    {
        $resource = $request->newResource();

        return response()->json([
            'info' => $resource::info(),
            'panels' => $resource->availablePanels($request),
            'resource' => $this->assignFieldsToPanels(
                $request, $resource->serializeForCreate($request)
            ),
        ]);
    }

    /**
     * Assign any un-assigned fields to the default panel.
     *
     * @param ResourceCreateRequest $request
     * @param  array                $resource
     * @return array
     */
    protected function assignFieldsToPanels(ResourceCreateRequest $request, array $resource)
    {
        foreach ($resource['fields'] as $field) {
            $field->panel = $field->panel ?? Panel::defaultNameFor($request->newResource());
        }

        return $resource;
    }
}
