<?php

namespace App\Models\Utils;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
	protected $table = 'exchange_rates';
	protected $fillable = ['currency', 'rate'];

	public $timestamps = false;
	public $incrementing = false;
	public $primaryKey = 'currency';
}
