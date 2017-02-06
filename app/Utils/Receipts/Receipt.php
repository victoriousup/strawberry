<?php
namespace App\Utils\Receipts;

class Receipt
{
	const PLATFORM_APPLE = 0;
	const PLATFORM_GOOGLE = 1;
	const PLATFORM_AMAZON = 2;

	// Is this receipt valid?
	public $valid = false;

	// Was this receipt made in the sandbox?
	public $sandbox = false;

	// Transaction information
	public $productId = null;
	public $transactionId = null;
	public $originalTransactionId = null;

	// Platform
	public $platform = self::PLATFORM_APPLE;

	public $error = null;
}