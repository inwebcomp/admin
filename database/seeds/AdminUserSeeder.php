<?php

namespace InWeb\Admin\Database\Seeds;

use Illuminate\Database\Seeder;
use InWeb\Admin\App\Models\AdminUser;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = AdminUser::where('login', '=', config('admin.auth.login'))
                    ->orWhere('email', '=', config('admin.auth.email'))
                    ->exists();

        if (! $user) {
            /** @var AdminUser $user */
            $user = AdminUser::create([
                'login'    => config('admin.auth.login'),
                'email'    => config('admin.auth.email'),
                'password' => \Hash::make(config('admin.auth.password')),
            ]);

            $user->assignRole('Super Admin');
        } else {
            if ($this->command) {
                $this->command->warn('User `' . config('admin.auth.login') . '` - `' . config('admin.auth.email') . '` already exists');
            }
        }
    }
}
