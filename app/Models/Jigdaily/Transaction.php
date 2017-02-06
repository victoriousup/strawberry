<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected $table = 'jigdaily_transactions';

	public $timestamps = false;

	protected $guarded = ['id'];

	protected $dates = ['date'];

	public function pack()
	{
		return $this->hasOne(Pack::class, 'id', 'pack_id');
	}
}