<?php

// Admin...
Route::get('admin-menu/menu', 'AdminMenuController@menu')->name('admin-menu.menu');
Route::post('settings', 'SettingsController@update')->name('settings.update');

// Auth...
Route::group(['as' => 'login.'], function() {
    Route::post('signin', 'AuthController@signin')->name('signin');
    Route::post('logout', 'AuthController@logout')->name('logout');
});

// Cards / Metrics...
Route::get('/metrics', 'DashboardMetricController@index');
Route::get('/metrics/{metric}', 'DashboardMetricController@show');
Route::get('/{resource}/metrics', 'MetricController@index');
Route::get('/{resource}/metrics/{metric}', 'MetricController@show');
//Route::get('/{resource}/{resourceId}/metrics/{metric}', 'DetailMetricController@show');

Route::get('/cards', 'DashboardCardController@index');
Route::get('/{resource}/cards', 'CardController@index');

// Dashboards...
Route::get('/dashboards/{dashboard}', 'DashboardController@index');
Route::get('/dashboards/cards/{dashboard}', 'DashboardCardController@index');

// Resource...
Route::get('{resource}', 'ResourceIndexController@handle')->name('resource.index');
Route::get('{resource}/create', 'ResourceCreateController@handle')->name('resource.create');
Route::post('{resource}/store', 'ResourceStoreController@handle')->name('resource.store');
Route::get('{resource}/{resourceId}/edit', 'ResourceEditController@handle')->name('resource.edit');
Route::put('{resource}/{resourceId}/update', 'ResourceUpdateController@handle')->name('resource.update');
Route::delete('{resource}/destroy', 'ResourceDestroyController@handle')->name('resource.destroy');
Route::put('{resource}/positions', 'ResourcePositionsController@handle')->name('positions.index');

// Fast Edit Field
Route::get('{resource}/{resourceId}/fast-edit/{field}', 'ResourceFastEditFieldController@edit')->name('resource.field.fast-edit');
Route::post('{resource}/{resourceId}/fast-edit/{field}', 'ResourceFastEditFieldController@update')->name('resource.field.fast-update');

// Search...
Route::get('{resource}/search', 'SearchController@index')->name('resource.search');

// Actions...
Route::get('/{resource}/actions/{resourceId?}', 'ActionController@index');
Route::post('/{resource}/action', 'ActionController@store');

// Filters...
Route::get('/{resource}/filters', 'FilterController@index');
Route::get('/{resource}/filters/search-callback', 'FilterController@searchCallback');

// Orderings...
Route::get('/{resource}/orderings', 'OrderingController@index');

// Fields...
Route::group(['prefix' => 'field', 'namespace' => 'Fields'], function() {
    Route::group(['prefix' => 'tree-field'], function() {
        Route::get('tree/{resource}/{resourceId?}', 'TreeFieldController@tree');
    });
    Route::group(['prefix' => 'editor-field'], function() {
        Route::post('image/{resource}/{resourceId?}', 'EditorFieldController@image');
        Route::post('file/{resource}/{resourceId?}', 'EditorFieldController@file');
    });
    Route::group(['prefix' => 'model-field'], function() {
        Route::get('{resource}/search', 'ModelFieldController@search');
    });
});

// ResourceTools...
Route::group(['prefix' => 'resource-tool', 'namespace' => 'ResourceTools'], function() {
    Route::group(['prefix' => 'actions-on-model-tool'], function() {
        Route::get('{resource}/{resourceId}', 'ActionsOnModelController@index');
    });
});