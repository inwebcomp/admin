<?php

namespace InWeb\Admin\App\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InWeb\Admin\App\Actions\LogoutOnOtherDevices;
use InWeb\Admin\App\Fields\Date;
use InWeb\Admin\App\Fields\Number;
use InWeb\Admin\App\Fields\Password;
use InWeb\Admin\App\Fields\PasswordConfirmation;
use InWeb\Admin\App\Fields\Text;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Resources\Resource;
use InWeb\Admin\App\Section;

class AdminUser extends Resource
{
    public static    $model    = \InWeb\Admin\App\Models\AdminUser::class;
    protected static $position = 1;
    public static    $group    = 'users';

    public static $globallySearchable = false;

    public static $search = [
        'email', 'login'
    ];

    public function title()
    {
        return $this->login;
    }

    public static function label()
    {
        return __('Пользователи');
    }

    public static function singularLabel()
    {
        return __('Пользователь');
    }

    public static function uriKey()
    {
        return 'admin-user';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function fields(AdminRequest $request)
    {
        return [
            Text::make(__('Логин'), 'login')->link($this->editPath()),
            Text::make(__('Имя'), 'name')->original(),
            Text::make(__('Email'), 'email'),
            Date::make(__('Дата регистрации'), 'created_at'),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function detailFields(AdminRequest $request)
    {
        $id = $request->findModelOrFail()->id;

        $fields = [
            Text::make(__('Логин'), 'login')
                ->rules(['required', Rule::unique('admin_users')->ignore($id)]),
            Text::make(__('Имя'), 'name')
                ->original(),
            Text::make(__('Email'), 'email')
                ->rules(['required', Rule::unique('admin_users')->ignore($id)]),

            Section::make(__('')),

            Password::make(__('Пароль'), 'password')->rules(['nullable']),
            PasswordConfirmation::make(__('Повторите пароль'), 'password_confirm')->rules(['same:password']),
        ];

        if (class_exists(\Inweb\Tools\PermissionsTool\RolesResourceTool::class))
            $fields[] = new \Inweb\Tools\PermissionsTool\RolesResourceTool();

        return $fields;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param AdminRequest $request
     * @return array
     */
    public function creationFields(AdminRequest $request)
    {
        return [
            Text::make(__('Логин'), 'login')
                ->rules(['required', 'unique:admin_users']),
            Text::make(__('Имя'), 'name')
                ->original(),
            Text::make(__('Email'), 'email')
                ->rules(['required', 'unique:admin_users']),
            Number::make(__('CRM ID'), 'crm_id'),

            Password::make(__('Пароль'), 'password')->rules(['required']),
            PasswordConfirmation::make(__('Повторите пароль'), 'password_confirm')->rules(['required', 'same:password']),
        ];
    }

    public function actions(Request $request)
    {
        return [
            new LogoutOnOtherDevices(),
        ];
    }
}
