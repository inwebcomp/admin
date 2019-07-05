<?php

// Admin...
Route::get('admin-menu/menu', 'AdminMenuController@menu')->name('admin-menu.menu');

// Auth...
Route::group(['as' => 'login.'], function() {
    Route::post('signin', 'AuthController@signin')->name('signin');
    Route::post('logout', 'AuthController@logout')->name('logout');
});

// Resource...
Route::get('{resource}', 'ResourceIndexController@handle')->name('resource.index');
Route::get('{resource}/create', 'ResourceCreateController@handle')->name('resource.create');
Route::post('{resource}/store', 'ResourceStoreController@handle')->name('resource.store');
Route::get('{resource}/{resourceId}/edit', 'ResourceEditController@handle')->name('resource.edit');
Route::put('{resource}/{resourceId}/update', 'ResourceUpdateController@handle')->name('resource.update');
Route::delete('{resource}/destroy', 'ResourceDestroyController@handle')->name('resource.destroy');
Route::put('{resource}/positions', 'ResourcePositionsController@handle')->name('positions.index');

// Search...
Route::get('{resource}/search', 'SearchController@index')->name('resource.search');

// Actions...
Route::get('/{resource}/actions', 'ActionController@index');
Route::post('/{resource}/action', 'ActionController@store');

// Filters...
Route::get('/{resource}/filters', 'FilterController@index');

// Fields...
Route::group(['prefix' => 'field', 'namespace' => 'Fields'], function() {
    Route::group(['prefix' => 'tree-field'], function() {
        Route::get('tree/{resource}/{resourceId?}', 'TreeFieldController@tree');
    });
    Route::group(['prefix' => 'editor-field'], function() {
        Route::post('image/{resource}/{resourceId?}', 'EditorFieldController@image');
    });
});