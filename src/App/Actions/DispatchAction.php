<?php

namespace InWeb\Admin\App\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use InWeb\Admin\App\Fields\ActionFields;
use Illuminate\Contracts\Queue\ShouldQueue;
use InWeb\Admin\App\Http\Requests\ActionRequest;

class DispatchAction
{
    /**
     * Dispatch the given action.
     *
     * @param \InWeb\Admin\App\Http\Requests\ActionRequest $request
     * @param \InWeb\Admin\App\Actions\Action $action
     * @param string $method
     * @param \Illuminate\Support\Collection $models
     * @param \InWeb\Admin\App\Fields\ActionFields $fields
     * @return mixed
     * @throws \Throwable
     */
    public static function forModels(
        ActionRequest $request,
        Action $action,
        $method,
        Collection $models,
        ActionFields $fields
    ) {
        if ($models->isEmpty()) {
            return;
        }

        if ($action instanceof ShouldQueue) {
            return static::queueForModels($request, $action, $method, $models);
        }

        return Transaction::run(function ($batchId) use ($fields, $request, $action, $method, $models) {
            if (! $action->withoutActionEvents) {
                ActionEvent::createForModels($request, $action, $batchId, $models);
            }

            return $action->withBatchId($batchId)->{$method}($fields, $models);
        }, function ($batchId) use ($action) {
            ActionEvent::markBatchAsFinished($batchId);
        });
    }

    /**
     * Dispatch the given action in the background.
     *
     * @param \InWeb\Admin\App\Http\Requests\ActionRequest $request
     * @param \InWeb\Admin\App\Actions\Action $action
     * @param string $method
     * @param \Illuminate\Support\Collection $models
     * @return void
     * @throws \Throwable
     */
    protected static function queueForModels(ActionRequest $request, Action $action, $method, Collection $models)
    {
        return Transaction::run(function ($batchId) use ($request, $action, $method, $models) {
            if (! $action->withoutActionEvents) {
                ActionEvent::createForModels($request, $action, $batchId, $models, 'waiting');
            }

            Queue::connection(static::connection($action))->pushOn(
                static::queue($action),
                new CallQueuedAction(
                    $action, $method, $request->resolveFields(), $models, $batchId
                )
            );
        });
    }

    /**
     * Extract the queue connection for the action.
     *
     * @param  \InWeb\Admin\App\Actions\Action  $action
     * @return string|null
     */
    protected static function connection($action)
    {
        return property_exists($action, 'connection') ? $action->connection : null;
    }

    /**
     * Extract the queue name for the action.
     *
     * @param  \InWeb\Admin\App\Actions\Action  $action
     * @return string|null
     */
    protected static function queue($action)
    {
        return property_exists($action, 'queue') ? $action->queue : null;
    }
}
