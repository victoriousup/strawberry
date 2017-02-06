<?php

namespace App\Utils;

class Prices
{
	public static function getPrices()
	{
		$prices = [];

		$prices['AUD'] = [  1.49,   2.99,   4.49];
		$prices['CAD'] = [  1.39,   2.79,   3.99];
		$prices['EUR'] = [  0.99,   1.99,   2.99];
		$prices['GBP'] = [  0.79,   1.49,   2.29];
		$prices['NZD'] = [  1.49,   2.99,   4.49];
		$prices['USD'] = [  0.99,   1.99,   2.99];

		return $prices;
	}


	// Returns an array of country codes that use a specific currency
	public static function getCountryCodes()
	{
		$codes = [];
		$codes['AUD'] = ['AU'];
		$codes['CAD'] = ['CA'];
		$codes['EUR'] = ['AT', 'BE', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LT', 'LU', 'NL', 'PT', 'SK', 'SI', 'ES'];
		$codes['GBP'] = ['GB'];
		$codes['NZD'] = ['NZ'];
		$codes['USD'] = ['KW', 'US'];

		return $codes;
	}


	public static function getCountryCodesForCurrency($currency)
	{
		return self::getCountryCodes()[$currency];
	}


	public static function getPricesByTier($tier = 1)
	{
		$prices = [];

		foreach(self::getPrices() as $key => $value)
		{
			$prices[$key] = $value[$tier - 1];
		}

		return $prices;
	}


	public static function getCurrencies()
	{
		$currencies = [];

		foreach(self::getPricesByTier(1) as $key => $value)
		{
			$currencies[$key] = $key;
		}

		return $currencies;
	}

}