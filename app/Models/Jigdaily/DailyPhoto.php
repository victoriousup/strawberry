<?php

namespace App\Models\Jigdaily;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Jigdaily\Photo;

class DailyPhoto extends Model
{
	protected $table = 'jigdaily_daily_photos';

	protected $primaryKey = 'date';

	public $timestamps = false;

	public $incrementing = false;

	protected $guarded = [];

	protected $dates = ['date'];

	protected $dateFormat = 'Y-m-d';

	public function photo()
	{
		return $this->belongsTo('App\Models\Jigdaily\Photo', 'photo_id');
	}
}
