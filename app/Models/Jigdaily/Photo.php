<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Utils\RecentDates;
use App\Utils\CDNHelper;
use App\Utils\Apps\JigsawDaily\Thumbnails;

class Photo extends Model
{
	protected $table = 'jigdaily_photos';

	public $timestamps = false;

	public $incrementing = false;

	protected $guarded = [];


	public function pack()
	{
		return $this->belongsTo('App\Models\Jigdaily\Pack', 'pack_id');
	}


	/**
	 * Saves this photo as a daily puzzle for a specific day. This method
	 * will replace any photo that is already set for this date.
	 *
	 * @param $date Date in YYYY-MM-DD format or Carbon date object
	 */
	public function saveDaily($date)
	{
		$daily = DailyPhoto::firstOrNew(['date' => $date]);
		$daily->photo_id = $this->attributes['id'];
		$daily->save();
	}


	/**
	 * Removes this photo as a daily puzzle for a specific day
	 *
	 * @param $date Date in YYYY-MM-DD format or Carbon date object
	 */
	public function removeDaily($date)
	{
		DailyPhoto::destroy($date);
	}


	/**
	 * Returns an array of dates that this photo was used as a daily photo.
	 *
	 * @return array of Carbon date objects
	 */
	public function getDailyDates()
	{
		$dates = DailyPhoto::where('photo_id', $this->attributes['id'])
					->orderBy('date', 'asc')
					->lists('date')->toArray();

		for($i = 0; $i < sizeof($dates); $i++)
		{
			$dates[$i] = new Carbon($dates[$i]);
		}

		return $dates;
	}


	/**
	 * Returns an object containing the daily photo dates that fall directly
	 * before and after a specific date.
	 *
	 * @param $targetDate Carbon date object, defaults to now
	 * @return object
	 */
	public function getRecentDailyDates(Carbon $targetDate = null)
	{
		if($targetDate == null)
		{
			$targetDate = Carbon::now();
		}

		return new RecentDates($this->getDailyDates(), $targetDate, true);
	}


	/**
	 * Returns the CDN url where the original image can be viewed
	 */
	public function getFileUrl($width = 500)
	{
		return CDNHelper::getUrl($this->getLocalFileUrl($width));
	}


	public function getFullFileUrl($expireMinutes)
	{
		return CDNHelper::getSecureUrl(Thumbnails::getFullPhotoPath() . $this->id . '.jpg', $expireMinutes);
	}


	public function getLocalFileUrl($width = 500)
	{
		return Thumbnails::getPhotoPath() . $width . '/' . $this->id . '-' . Thumbnails::getHash($this->id) . '.jpg';
	}

}
