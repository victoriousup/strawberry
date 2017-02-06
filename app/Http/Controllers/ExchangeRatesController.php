<?php

namespace App\Http\Controllers;

use App\Models\Utils\ExchangeRate;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utils\ExchangeRates;

class ExchangeRatesController extends Controller
{
	public function getAll(Request $request)
	{
		return ExchangeRate::get();
	}

	public function getCurrency(Request $request, String $currency)
	{
		$rateObj = ExchangeRate::find($currency);
		if(!$rateObj)
		{
			return response(['success' => 'false', 'message' => 'Unsupported currency'], 400);
		}

		return $rateObj;
	}
}
