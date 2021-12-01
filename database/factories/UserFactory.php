<?php

use Faker\Generator as Faker;
use Revys\RevyAdmin\App\AdminUser;

$factory->define(AdminUser::class, function (Faker $faker) {
    static $password;

    return [
        'login'          => str_slug($faker->name),
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
