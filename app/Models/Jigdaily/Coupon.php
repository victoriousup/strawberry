<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;
use App\Utils\RandomString;
use Carbon\Carbon;

class Coupon extends Model
{
	protected $table = 'jigdaily_coupons';
	public $timestamps = false;
	protected $dates = ['expiration'];


	public function setDeviceIdsAttribute(array $devices)
	{
		$this->attributes['device_ids'] = json_encode($devices);
	}


	public function getDeviceIdsAttribute()
	{
		if($this->attributes == null)
		{
			return [];
		}

		return json_decode($this->attributes['device_ids']);
	}


	public function validate($packId, $deviceId)
	{
		$error = null;

		if(!$this->active || ($this->expiration != null && $this->expiration->le(Carbon::now())))
		{
			$error = 'Coupon is no longer active or has expired';
		}
		// Too many redeptions?
		else if($this->max_redemptions != null && $this->max_redemptions >= $this->redemptions)
		{
			$error = 'Coupon has been redeemed too many times';
		}
		// For the correct pack?
		else if($this->pack_id != $packId)
		{
			$error = 'Coupon not valid for this pack';
		}
		// For the correct device?
		else if($this->device_ids != null && !in_array($deviceId, $this->device_ids))
		{
			$error = 'Coupon not valid for this device';
		}

		return ['valid' => $error == null, 'error' => $error];
	}


	public static function generateCode()
	{
		return time() . RandomString::generate(20);
	}
}
