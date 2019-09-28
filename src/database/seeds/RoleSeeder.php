<?php

namespace InWeb\Admin\Database\Seeds;

use Illuminate\Database\Seeder;
use InWeb\Admin\App\Models\AdminUser;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
    }
}
