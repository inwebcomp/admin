<?php

// Scripts & Styles...
Route::get('/scripts/{script}', 'ScriptController@show')->name('script');
Route::get('/styles/{style}', 'StyleController@show')->name('style');

Route::group(['as' => 'login.'], function () {
    Route::get('login', 'RouterController@module')->name('login-form');
});

Route::get('/', 'RouterController@module')->name('home');

// Core route
Route::get('/{url?}', 'RouterController@module')->where('url', '^(?!api\/).+');