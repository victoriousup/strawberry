<?php

namespace App\Utils;

use App\Models\Utils\ExchangeRate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class ExchangeRates
{
	/**
	 * Updates the current currency conversion rates in the database.
	 *
	 * @return bool True on successful update
	 */
	public static function update()
	{
		$appId = 'dbaefc7641334808a5d4f23ce820e619';
		$url = "http://openexchangerates.org/api/latest.json?app_id={$appId}";

		try
		{
			$res = (new Client())->request('GET', $url);
			$exchangeRates = json_decode($res->getBody());
		}
		catch(ClientException $e)
		{
			return false;
		}

		// Check for error
		if($exchangeRates == null || $exchangeRates->base != "USD")
		{
			return false;
		}

		// Import current rate data
		foreach($exchangeRates->rates as $currency => $rate)
		{
			$rateModel = ExchangeRate::firstOrCreate(['currency' => $currency]);
			$rateModel->rate = $rate;
			$rateModel->save();
		}

		return true;
	}


	/**
	 * Returns the conversion rate relative to USD. This method attempts to
	 * load the rate from the cache if it exists.
	 *
	 * @param $currency
	 * @return int|null
	 */
	public static function getRate($currency)
	{
		if($currency == 'USD')
		{
			return 1;
		}

		$rate = ExchangeRate::find($currency);
		if(!$rate)
		{
			return null;
		}

		return $rate->rate;
	}


	/**
	 * Converts a currency to USD. Returns null on error.
	 *
	 * @param $amount
	 * @param $currency
	 * @return float|null
	 */
	public static function convertToUSD($amount, $currency)
	{
		$rate = self::getRate($currency);
		if(!is_null($rate))
		{
			return round((1 / $rate) * $amount, 2);
		}

		return null;
	}


	/**
	 * Converts a currency from USD. Returns null on error.
	 *
	 * @param $amount
	 * @param $currency
	 * @return float|null
	 */
	public static function convertFromUSD($amount, $currency)
	{
		$rate = self::getRate($currency);
		if(!is_null($rate))
		{
			return round(($rate) * $amount, 2);
		}

		return null;
	}
}