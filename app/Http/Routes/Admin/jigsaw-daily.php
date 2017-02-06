<?php

// admin/jigsaw-daily/

Route::group(['prefix' => 'jigsaw-daily', 'middleware' => 'permission:jigdaily', 'namespace' => 'Admin\Apps\JigsawDaily'], function()
{
	// --------------------
	// Dashboard
	// --------------------
	Route::get('/', 'DashboardController@index');


	// --------------------
	// Manage
	// --------------------
	Route::group(['middleware' => 'permission:jigdaily:manage'], function()
	{

		// --------------------
		// Stickers
		// --------------------
		Route::get('stickers', 'StickersController@index');
		Route::get('stickers/create', 'StickersController@create');
		Route::post('stickers/create', 'StickersController@store');
		Route::get('stickers/{sticker}/edit', 'StickersController@edit');
		Route::post('stickers/{sticker}/edit', 'StickersController@update');
		Route::post('stickers/upload-image', 'StickersController@uploadImage');


		// --------------------
		// Packs
		// --------------------
		Route::get('packs', 'PacksController@index');
		Route::get('packs/create', 'PacksController@create');
		Route::post('packs/create', 'PacksController@store');
		Route::get('packs/{pack}/edit', 'PacksController@edit');
		Route::post('packs/{pack}/edit', 'PacksController@update');
		Route::get('packs/{pack}/preview', 'PacksController@preview');
		Route::get('packs/{pack}/promo-builder', 'PacksController@promoBuilder');
		Route::get('packs/promo-builder-generate', 'PacksController@promoBuilderGenerate');
		Route::get('packs/{pack}/store-preview-image', 'PacksController@storePreviewImage');
		Route::get('packs/{pack}/download', 'PacksController@download');

		Route::get('packs/{pack}/promos', 'PackPromosController@index');
		Route::get('packs/{pack}/promos/create', 'PackPromosController@create');
		Route::post('packs/{pack}/promos/create', 'PackPromosController@store');
		Route::get('packs/{pack}/promos/{promo}/edit', 'PackPromosController@edit');
		Route::post('packs/{pack}/promos/{promo}/edit', 'PackPromosController@update');
		Route::get('packs/{pack}/promos/bulk', 'PackPromosController@bulk');
		Route::post('packs/{pack}/promos/bulk', 'PackPromosController@bulkCreate');

		Route::post('packs/promos/upload', 'PackPromosController@upload');


		// --------------------
		// Organize
		// --------------------
		Route::get('organize/popup/{packId?}', 'OrganizeController@popup');
		Route::get('organize/category/{category}/subcategories', 'OrganizeController@subcategories');
		Route::post('organize/photos', 'OrganizeController@photos');
		Route::get('organize/pack/{pack}', 'OrganizeController@pack');
		Route::post('organize/add', 'OrganizeController@add');
		Route::post('organize/remove', 'OrganizeController@remove');
		Route::post('organize/set-cover', 'OrganizeController@setCover');
		Route::get('organize/{packId?}', 'OrganizeController@index');


		// --------------------
		// Store
		// --------------------
		Route::get('store', 'StoreController@index');
		Route::post('store', 'StoreController@save');


		// --------------------
		// Daily jigsaws
		// --------------------
		Route::get('daily/packs/{date}', 'DailyController@packs');
		Route::get('daily/pack/{pack}/{date}', 'DailyController@pack');
		Route::get('daily/set/{photo}/{date}', 'DailyController@set');
		Route::get('daily/{month?}/{year?}', 'DailyController@index');

	});


	// --------------------
	// Reports
	// --------------------
	Route::group(['middleware' => 'permission:jigdaily:reports'], function()
	{
		Route::get('reports/daily', 'ReportsController@daily');
	});


	// --------------------
	// Players
	// --------------------
	Route::group(['middleware' => 'permission:jigdaily:players'], function()
	{
		Route::get('players', 'PlayersController@index');
		Route::get('players/device/', 'PlayersController@device');
	});


	// --------------------
	// Settings
	// --------------------
	Route::group(['middleware' => 'permission:jigdaily:settings'], function()
	{

		// --------------------
		// Test devices
		// --------------------
		Route::get('test-devices', 'TestDevicesController@index');
		Route::get('test-devices/device/{device}/delete', 'TestDevicesController@delete');
		Route::get('test-devices/device/{device}', 'TestDevicesController@edit');
		Route::post('test-devices/device/{device}', 'TestDevicesController@update');
		Route::get('test-devices/create', 'TestDevicesController@create');
		Route::post('test-devices/create', 'TestDevicesController@store');

	});

});