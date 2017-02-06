<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;

class DeviceTransaction extends Model
{
	protected $table = 'jigdaily_device_transactions';
	public $timestamps = false;
	protected $guarded = ['id'];
	protected $dates = ['date'];

	public function transaction()
	{
		return $this->hasOne(Transaction::class, 'id', 'transaction_id');
	}
}
