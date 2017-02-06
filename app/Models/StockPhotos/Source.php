<?php

namespace App\Models\StockPhotos;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
	public $timestamps = false;

	protected $table = 'stock_photo_sources';

	protected $fillable = ['name'];



	public function stockPhotos()
	{
		return $this->hasMany('App\Models\StockPhotos\StockPhoto', 'stock_photo_source_id');
	}
}
