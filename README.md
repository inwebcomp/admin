# InWeb Admin

Admin Panel for Laravel Framework

## Features
- CRUD
- Images management

## Requirements
- Laravel >=5.6

## Installation

1. Install package via *composer require*
    ```
    composer require inwebcomp/admin
    ```
    or add to your composer.json to **autoload** section and update your dependencies
    ```
    "inwebcomp/admin": "*"
    ```
2. Run migrations
    ```
    php artisan migrate
    ```
3. Run seeder

    Run seeder to create admin user with default credentials
    ```
    php artisan db:seed --class="InWeb\Admin\Database\Seeds\DatabaseSeeder"
    ```
4. Add provider in **config/auth.php**
    ```php
    'providers' => [
         ...

         'admin' => [
             'driver' => 'eloquent',
             'model' => InWeb\Admin\App\Models\AdminUser::class,
         ],
    ],
    ```
5. Publish assets
    ```
    php artisan vendor:publish --provider="InWeb\Admin\App\Providers\AdminServiceProvider" --tag=public
    ```
    
You are ready to go!
