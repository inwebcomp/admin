<?php

namespace InWeb\Admin\App;

use Spatie\Permission\Models\Permission;

trait HasPermissions
{
    public static function permissionActions()
    {
        return [
            'viewAny' => __('Доступ к каталогу'),
            'view' => __('Просмотр'),
            'create' => __('Создание'),
            'update' => __('Изменение'),
            'delete' => __('Удаление'),
        ];
    }

    public static function syncPermissionActions()
    {
        foreach (static::permissionActions() as $action => $title) {
            Permission::findOrCreate(static::permissionActionName($action));
        }
    }

    public static function permissionActionName($action)
    {
        return static::uriKey() . ':' . $action;
    }
}