<?php


namespace App\Utils\Apps\JigsawDaily;

use App\Models\Jigdaily\Device;
use App\Models\Jigdaily\DeviceTransaction;
use App\Utils\RandomString;
use App\Utils\Receipts\Receipt;
use App\Models\Jigdaily\Coupon;

class CouponReceipt extends Receipt
{
	public $coupon;

	public function __construct($platform, $packId, $deviceId, $couponCode)
	{
		$this->platform = $platform;

		$this->coupon = Coupon::where(['code' => $couponCode])->first();
		if($this->coupon == null)
		{
			$this->error = 'Coupon code not valid';
		}
		else
		{
			// Has the user purchased anything before?
			$device = Device::where(['platform' => $platform, 'device_id' => $deviceId])->first(['id']);
			if($device != null)
			{
				$deviceTransaction = $device->deviceTransactions()->with(['transaction' => function($query) use ($packId)
				{
					$query->where(['pack' => $packId]);

				}])->first();

				if($deviceTransaction != null &&
					$deviceTransaction->transaction != null &&
					$deviceTransaction->transaction->coupon_id != null &&
					$deviceTransaction->transaction->coupon_id == $this->coupon->id)
				{
					$this->valid = true;
					$this->transactionId = $this->originalTransactionId = $deviceTransaction->transaction->receipt_id;

					return;
				}
			}

			// Validate the coupon
			$result = $this->coupon->validate($packId, $deviceId);
			if(!$result['valid'])
			{
				$this->error = $result['error'];
			}
			else
			{
				$this->valid = true;
				$this->transactionId = $this->originalTransactionId = 'c_' . time() . RandomString::generate(30);
			}
		}
	}
}