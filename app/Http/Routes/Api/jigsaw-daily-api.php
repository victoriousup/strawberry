<?php

Route::group(['prefix' => 'jigsaw-daily', 'namespace' => 'Admin\Apps\JigsawDaily'], function()
{
	Route::get('v1/version', 'ApiController@version');
	Route::get('v1/featured', 'ApiController@featured');
	Route::get('v1/store', 'ApiController@store');
	Route::get('v1/store/pack/{pack}', 'ApiController@storePack');
	Route::get('v1/daily/{date}', 'ApiController@daily');
	Route::get('v1/recommended/{pack}', 'ApiController@recommended');
	Route::get('v1/daily-promos/{pack}', 'ApiController@dailyPromos');
	Route::get('v1/faq/{version}/{platform}/{device}', 'FAQController@faq');
	Route::post('v1/testing-device', 'ApiController@testDevice');
	Route::post('v1/purchase', 'ApiController@purchase');
	Route::post('v1/pack-info', 'ApiController@packInfo');
});

Route::post('jigsaw-daily/v1/contact', 'ContactController@contactJigDaily');

