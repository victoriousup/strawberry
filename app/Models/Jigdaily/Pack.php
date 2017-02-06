<?php

namespace App\Models\Jigdaily;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use App\Utils\RecentDates;
use App\Utils\Prices;
use App\Jobs\SaveAndroidIAP;
use DB;

class Pack extends Model
{
	use DispatchesJobs;

	protected $table = 'jigdaily_packs';

	public $timestamps = false;

	protected $guarded = ['id'];

	public static $iTunesStatusTypes = [0 => 'None', 1 => 'Submitted', 2 => 'Approved', 3 => 'Rejected'];

	public function photos()
	{
		return $this->hasMany('App\Models\Jigdaily\Photo', 'pack_id');
	}


	public function coupon()
	{
		return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
	}


	public function getStickers()
	{
		if($this->sticker_ids == null || $this->sticker_ids == '')
		{
			return [];
		}

		$stickers = [];
		$stickerIds = json_decode($this->sticker_ids);

		foreach($stickerIds as $id)
		{
			$stickers[] = Sticker::find($id);
		}

		return $stickers;
	}


	public function getRecommendedPacks()
	{
		if($this->recommended_pack_ids == null || $this->recommended_pack_ids == '')
		{
			$this->recommended_pack_ids = '[]';
		}

		$packIds = json_decode($this->recommended_pack_ids);

		return Pack::whereIn('id', $packIds);
	}


	public function addPhoto($id)
	{
		Photo::find($id)->update(['pack_id' => $this->id]);

		if($this->cover_id == -1)
		{
			$this->cover_id = $id;
			$this->save();
		}
	}


	public function removePhoto($id)
	{
		Photo::find($id)->update(['pack_id' => null]);

		if($this->cover_id == $id)
		{
			$photo = $this->photos()->first();
			if($photo)
			{
				$this->cover_id = $photo->id;
			}
			else
			{
				$this->cover_id = -1;
			}

			$this->save();
		}
	}


	/**
	 * Returns the Photo object for the cover or null if not set
	 *
	 * @return Photo
	 */
	public function getCover()
	{
		if($this->cover_id == -1)
		{
			return null;
		}

		return Photo::find($this->cover_id);
	}


	/**
	 * Returns an array of dates that this pack's photos were used as a daily photo.
	 *
	 * @return array of Carbon date objects
	 */
	public function getDailyDates()
	{
		$photoIds = $this->photos()->lists('id')->toArray();
		$dates = DailyPhoto::whereIn('photo_id', $photoIds)
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
	 * Saves this pack data to Google Play.
	 */
	public function saveToGooglePlay()
	{
		$packId = $this->id;

		if(!isset($packId))
		{
			$result = DB::table('jigdaily_packs')->select('id')->orderBy('id', 'DESC')->limit(1)->first();

			if($result == null)
			{
				$packId = 1;
			}
			else
			{
				$packId = $result->id + 1;
			}
		}

		$package = config('google.jigsaw_daily.id');
		$sku = $package . '.pack.' . $packId;
		$description = 'Pack of ' . strtolower($this->name) . ' jigsaw puzzles';
		$prices = Prices::getPricesByTier($this->price_tier);

		$iap = SaveAndroidIAP::createSimpleIAP($package, $sku, $this->name, $description, ($this->price_tier - 1) + 0.99, $prices);
		$this->dispatch(new SaveAndroidIAP($iap));
	}
}
