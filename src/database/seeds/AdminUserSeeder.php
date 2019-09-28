<?php

namespace InWeb\Admin\Database\Seeds;

use Illuminate\Database\Seeder;
use InWeb\Admin\App\Models\AdminUser;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        /** @var AdminUser $user */
        $user = AdminUser::create([
            'login' => config('admin.auth.login'),
            'email' => config('admin.auth.email'),
            'password' => \Hash::make(config('admin.auth.password')),
            'admin' => true
        ]);

        $user->assignRole('Super Admin');
    }
}
