<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;

class TestDevice extends Model
{
	public $active = true;

	protected $table = 'jigdaily_test_devices';
	public $timestamps = false;
	protected $guarded = ['id'];
}
