<?php

namespace App\Utils\Receipts;


class AppleReceipt extends Receipt
{
	// Verification URLs
	const SANDBOX_URL    = 'https://sandbox.itunes.apple.com/verifyReceipt';
	const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

	// Base-64 receipt encoding
	private $encodedReceipt;

	public $platform = self::PLATFORM_APPLE;

	// ---------------------------------------------------------------------
	// Validates an iTunes receipt.
	// @param	encodedReceipt		Base-64 encoded iTunes receipt
	// @param	version				Receipt version
	// ---------------------------------------------------------------------
	function __construct($encodedReceipt, $productId)
	{
		$this->encodedReceipt = $encodedReceipt;

		// Validate the receipt
		$response = $this->validateReceipt();

		if(!$this->isValidResponse($response))
		{
			throw new \Exception('Invalid response data');
		}

		// This is a sandbox account
		if($response->status == 21007)
		{
			$this->sandbox = true;
			$response = $this->validateReceipt(true);
			if(!$this->isValidResponse($response))
			{
				throw new \Exception('Invalid response data');
			}
		}

		// Is this receipt valid?
		if($response->status == 0)
		{
			// Old single-entry receipt
			if(property_exists($response->receipt, 'product_id'))
			{
				$this->productId = $response->receipt->product_id;
				$this->transactionId = $response->receipt->transaction_id;
				$this->originalTransactionId = $response->receipt->original_transaction_id;
			}
			// New app receipt format (iOS >= 7)
			else
			{
				foreach($response->receipt->in_app as $purchase)
				{
					if($purchase->product_id == $productId)
					{
						$this->productId = $purchase->product_id;
						$this->transactionId = $purchase->transaction_id;
						$this->originalTransactionId = $purchase->original_transaction_id;

						break;
					}
				}
			}

			// Does the receipt contain the actual product?
			$this->valid = $this->productId == $productId;

		}
	}


	// ---------------------------------------------------------------------
	// Returns true if this is a valid response object from Apple.
	// ---------------------------------------------------------------------
	private function isValidResponse($response)
	{
		if(!is_null($response) && isset($response->status))
		{
			return true;
		}

		return false;
	}


	// ---------------------------------------------------------------------
	// Validates receipt data with iTunes.
	// @param	useSandbox
	// ---------------------------------------------------------------------
	private function validateReceipt($useSandbox = false)
	{
		$request = json_encode(array('receipt-data' => $this->encodedReceipt));
		$url = $useSandbox ? self::SANDBOX_URL : self::PRODUCTION_URL;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);

		// We couldn't connect for some reason...
		if($errno != 0)
		{
			throw new \Exception($errmsg, $errno);
		}

		return json_decode($response);
	}
}