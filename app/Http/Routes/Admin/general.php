<?php

// Dashboard
Route::get('/', 'Admin\AdminController@index');


// --------------------
// Administration
// --------------------
Route::group(['prefix' => 'users', 'middleware' => 'permission:admin', 'namespace' => 'Admin\\'], function()
{
	Route::get('/', 'AdministrationController@getUsers');
	Route::get('add', 'AdministrationController@getAddUser');
	Route::post('add', 'AdministrationController@postAddUser');
	Route::get('user/{id}/edit', 'AdministrationController@getEditUser');
	Route::post('user/{id}/edit', 'AdministrationController@postEditUser');
});

// Logs
Route::get('users/logs', ['middleware' => 'permission:admin', 'uses' => 'Admin\AdministrationController@getLogs']);

// Profile
Route::get('profile/edit', 'Admin\ProfileController@getEdit');
Route::post('profile/edit', 'Admin\ProfileController@postEdit');