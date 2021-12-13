<?php


namespace InWeb\Admin\App\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Actions\Action;
use InWeb\Admin\App\Fields\ActionFields;

class Publish extends Action
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $withoutActionEvents = true;

    public function icon()
    {
        return 'far fa-eye';
    }

    public function name()
    {
        return __('Опубликовать');
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->isNotEmpty()) {
            foreach ($models as $model) {
                if ($model->isPublished())
                    continue;

                $model->publish();
            }
        }

        return Action::message(__("Объекты опубликованы"));
    }
}
