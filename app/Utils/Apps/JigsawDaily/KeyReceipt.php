<?php


namespace App\Utils\Apps\JigsawDaily;

use App\Models\Jigdaily\Device;
use App\Models\Jigdaily\DeviceTransaction;
use App\Models\Jigdaily\Transaction;
use App\Utils\Receipts\Receipt;
use Hash;

class KeyReceipt extends Receipt
{
	const SECRET_KEY = 'GjG8d7dKxcKhgFaq';

	// Database transaction object
	public $transaction;

	public function __construct($platform, $packId, $key)
	{
		$this->platform = $platform;

		$this->transaction = Transaction::where(['id' => self::getTransactionIdFromKey($key)])->first();

		// Invalid transaction
		if($this->transaction == null)
		{
			return;
		}

		// Invalid key
		if(!self::isKeyValid($this->transaction->id, $key))
		{
			return;
		}

		// Invalid pack id
		if($this->transaction->pack_id != $packId)
		{
			return;
		}

		// Everything is valid
		$this->valid = true;
		$this->sandbox = $this->transaction->sandbox;
		$this->transactionId = $this->originalTransactionId = $this->transaction->receipt_id;
	}


	public static function getKeyFromTransactionId($id)
	{
		return $id . '$' . Hash::make(self::SECRET_KEY . $id);
	}


	public static function getTransactionIdFromKey($key)
	{
		return (int) explode('$', $key)[0];
	}


	public static function isKeyValid($id, $key)
	{
		$len = strlen((string) $id);
		$key = substr($key, $len + 1);

		return Hash::check(self::SECRET_KEY . $id, $key);
	}

}