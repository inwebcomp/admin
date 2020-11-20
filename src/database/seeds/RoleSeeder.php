<?php

namespace InWeb\Admin\Database\Seeds;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::findOrCreate('Super Admin', 'admin');
    }
}
