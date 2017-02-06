<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function ()
{
	// Authentication Routes...
	Route::get('admin/login', 'Auth\AuthController@showLoginForm');
	Route::post('admin/login', 'Auth\AuthController@login');
	Route::get('admin/logout', 'Auth\AuthController@logout');

	// Password Reset Routes...
	Route::get('admin/password/reset/{token?}', 'Auth\PasswordController@showResetForm');
	Route::post('admin/password/email', 'Auth\PasswordController@sendResetLinkEmail');
	Route::post('admin/password/reset', 'Auth\PasswordController@reset');
});



/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function ()
{
	// Include all route partials in Routes/Admin
	foreach(File::allFiles(__DIR__ . '/Routes/Admin') as $partial)
	{
		require_once $partial->getPathname();
	}
});


/*
|--------------------------------------------------------------------------
| Server API routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'middleware' => ['api']], function()
{
	// Include all route partials in Routes/Api
	foreach(File::allFiles(__DIR__ . '/Routes/Api') as $partial)
	{
		require_once $partial->getPathname();
	}
});