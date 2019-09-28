<?php

namespace InWeb\Admin\App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use InWeb\Admin\App\Actions\Actionable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property bool admin
 */
class AdminUser extends Authenticatable
{
    use Actionable, Notifiable, HasRoles;

    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'email', 'password', 'admin'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Determine if entity is sortable
     *
     * @return bool
     */
    public function positionable()
    {
        return false;
    }

    /**
     * Determine if entity is sortable
     *
     * @return bool
     */
    public function translatable()
    {
        return false;
    }
}
