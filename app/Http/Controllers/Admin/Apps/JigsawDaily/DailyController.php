<?php

namespace App\Http\Controllers\Admin\Apps\JigsawDaily;


use App\Http\Controllers\Controller;
use App\Models\Jigdaily\Pack;
use App\Models\Jigdaily\Photo;
use Carbon\Carbon;
use App\Utils\Calendar;
use App\Models\Jigdaily\DailyPhoto;

class DailyController extends Controller
{
	public function index($month = null, $year = null)
	{
		$calendar = new Calendar($month, $year);

		$dailyPhotos = DailyPhoto::whereBetween('date', [$calendar->getCalendarStartDate(), $calendar->getCalendarEndDate()])
			->orderBy('date', 'asc')
			->with('photo')
			->get();

		$dates = Calendar::eventsToDates($calendar->getCalendarDates(), $dailyPhotos);

		return view('admin.apps.jigsawdaily.daily.index', compact('calendar', 'dates'));
	}


	public function packs($date)
	{
		$date = new Carbon($date);
		$packs = Pack::where('released', 1)->where('visible', true)->orderBy('name', 'asc')->get();

		$this->calcDaysAgoUsed($packs, $date);

		return view('admin.apps.jigsawdaily.daily.packs', compact('date', 'packs'));
	}


	public function pack(Pack $pack, $date)
	{
		$date = new Carbon($date);
		$photos = $pack->photos()->orderBy('name', 'asc')->get();

		$this->calcDaysAgoUsed($photos, $date);

		return view('admin.apps.jigsawdaily.daily.pack', compact('date', 'pack', 'photos'));
	}


	public function set(Photo $photo, $date)
	{
		$date = new Carbon($date);

		$daily = DailyPhoto::firstOrNew(['date' => $date->format('Y-m-d')]);
		$daily->photo_id = $photo->id;
		$daily->save();

		return redirect('admin/jigsaw-daily/daily/' . $date->format('m/Y') . '/');
	}


	private function calcDaysAgoUsed(&$collection, $date)
	{
		foreach($collection as $item)
		{
			$item->recentDailyDates = $item->getRecentDailyDates($date);
			$item->daysAgoUsed = null;

			$recentDate = $item->recentDailyDates->closestDate();
			if($recentDate != null)
			{
				$item->daysAgoUsed = abs($recentDate->diffInDays($date));
			}
		}
	}

}