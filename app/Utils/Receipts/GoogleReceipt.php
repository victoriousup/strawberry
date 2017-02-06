<?php

namespace App\Utils\Receipts;


class GoogleReceipt extends Receipt
{
	public $platform = self::PLATFORM_GOOGLE;

	// ---------------------------------------------------------------------
	// Validates a Google Play receipt.
	// @param	pReceipt		Android response data from the transaction
	// ---------------------------------------------------------------------
	function __construct($signature, $responseData, $publicKey, $productId)
	{
		// Validate the receipt
		$response = self::validateReceipt($responseData, $signature, $publicKey);
		if(is_null($response))
		{
			throw new \Exception('Invalid receipt data');
		}

		// Is this receipt valid?
		if($response->valid)
		{
			// Test orders don't include an orderId
			if(!isset($response->orderId))
			{
				$response->orderId = uniqid('GPA.TEST.');
			}

			$this->productId = $response->productId;
			$this->transactionId = $response->orderId;
			$this->originalTransactionId = $response->orderId;
			$this->valid = $this->productId == $productId;
		}
	}


	// ---------------------------------------------------------------------
	// Validates receipt data from Google Play. Returns true if valid.
	// Thanks to https://gist.github.com/prime31/4750744
	// @param	responseData
	// @param	signature
	// @param	publicKey
	// ---------------------------------------------------------------------
	public static function validateReceipt($responseData, $signature, $publicKey)
	{
		$responseData = trim($responseData);
		$signature = trim($signature);
		$response = json_decode($responseData);
		$response->valid = false;

		// Invalid responseData JSON
		if(is_null($response))
		{
			return null;
		}

		//Create an RSA key compatible with openssl_verify from our Google Play sig
		$key = "-----BEGIN PUBLIC KEY-----\n".
				chunk_split($publicKey, 64,"\n").
				'-----END PUBLIC KEY-----';
		$key = openssl_get_publickey($key);

		//Signature should be in binary format, but it comes as BASE64.
		$signature = base64_decode($signature);

		//Verify the signature
		$result = openssl_verify($responseData, $signature, $key, OPENSSL_ALGO_SHA1);

		if($result == 1)
		{
			$response->valid = true;
		}

		return $response;
	}
}