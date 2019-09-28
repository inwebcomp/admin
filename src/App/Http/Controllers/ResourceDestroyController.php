<?php

namespace InWeb\Admin\App\Http\Controllers;

use InWeb\Admin\App\Actions\ActionEvent;
use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Http\Requests\DeletionRequest;
use InWeb\Admin\App\Http\Requests\ResourceDeleteRequest;

class ResourceDestroyController extends DeletionRequest
{
    /**
     * Destroy the given resource(s).
     *
     * @param ResourceDeleteRequest $request
     * @return void
     * @throws \Throwable
     */
    public function handle(ResourceDeleteRequest $request)
    {
        $resource = $request->newResource();

        $resource->authorizeToDelete($request);

        $request->chunks(150, function ($models) use ($request) {
            $models->each(function ($model) use ($request) {
                $model->delete();

                \DB::table('action_events')->insert(
                    ActionEvent::forResourceDelete($request->user(), collect([$model]))
                                ->map->getAttributes()->all()
                );
            });
        });
    }
}
