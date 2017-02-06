<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
	protected $table = 'jigdaily_devices';
	public $timestamps = false;
	protected $guarded = ['id'];

	public function deviceTransactions()
	{
		return $this->belongsTo(DeviceTransaction::class, 'id', 'device_id');
	}
}
