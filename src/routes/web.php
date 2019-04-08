<?php

use InWeb\Admin\App\Admin;
use InWeb\Admin\App\Http\Middleware\DispatchServingAdminEvent;

\Route::group([
    'prefix'     => Admin::path(),
    'namespace'  => '\InWeb\Admin\App\Http\Controllers',
    'middleware' => [
        'web',
        DispatchServingAdminEvent::class
    ],
    'as'         => 'admin::',
], function () {
    // Scripts & Styles...
    Route::get('/scripts/{script}', 'ScriptController@show')->name('script');
    Route::get('/styles/{style}', 'StyleController@show')->name('style');

    \Route::group(['middleware' => ['admin-auth']], function () {
        Route::group(['as' => 'login.'], function () {
            Route::get('login', 'RouterController@module')->name('login-form');
        });

        Route::get('/', 'RouterController@module')->name('home');

        // Core route
        Route::get('/{url?}', 'RouterController@module')->where('url', '^(?!api\/).+');
    });
});