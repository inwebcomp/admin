<?php

namespace InWeb\Admin\App\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Actions\Action;
use InWeb\Admin\App\Fields\ActionFields;
use InWeb\Admin\App\Fields\Password;

class LogoutOnOtherDevices extends Action
{
    use Queueable, SerializesModels;

    public $onlyOnDetail = true;

    public function icon(): string
    {
        return 'far fa-sign-out-alt';
    }

    public function name(): string
    {
        return __('Выйти на других устройствах');
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return array
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $password = $fields->password;

        $currentUserId = auth()->user()->id;

        foreach ($models as $model) {
            \Auth::loginUsingId($model->id);
            \Auth::logoutOtherDevices($password);
        }

        \Auth::loginUsingId($currentUserId);

        return Action::message(__("Произведён выход из аккаунта на других устройствах"));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Password::make(__('Пароль'), 'password'),
        ];
    }
}
