<?php

namespace InWeb\Admin\App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use InWeb\Admin\App\Actions\Actionable;

/**
 * @property bool admin
 */
class AdminUser extends Authenticatable
{
    use Actionable, Notifiable;

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

    public function isAdmin()
    {
        return (bool) $this->admin;
    }

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
