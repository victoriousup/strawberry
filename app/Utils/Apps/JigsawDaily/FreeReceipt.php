<?php


namespace App\Utils\Apps\JigsawDaily;

use App\Models\Jigdaily\Device;
use App\Models\Jigdaily\DeviceTransaction;
use App\Models\Jigdaily\TestDevice;
use App\Utils\RandomString;
use App\Utils\Receipts\Receipt;
use App\Models\Jigdaily\Coupon;

class FreeReceipt extends Receipt
{
	public $coupon;

	public function __construct($platform, $deviceId, $productId)
	{
		$this->platform = $platform;
		$this->productId = $productId;

		$device = TestDevice::where('device_id', $deviceId)->first();
		if($device != null && $device->active)
		{
			$this->transactionId = RandomString::generate(20);
			$this->originalTransactionId = $this->transactionId;
			$this->sandbox = true;
			$this->valid = true;
		}
		else
		{
			$this->error = 'Not a testing device';
		}
	}
}