<?php

Route::get('exchange-rates', 'ExchangeRatesController@getAll');
Route::get('exchange-rate/{currency}', 'ExchangeRatesController@getCurrency');