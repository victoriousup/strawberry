<?php
/**
 * This class is used to compare a list of dates with a "target date" and find the date that
 * is directly before and after the target date.
 */

namespace App\Utils;

use Carbon\Carbon;

class RecentDates
{
	public $targetDate;
	public $beforeDate = null;
	public $afterDate = null;

	function __construct($dates, Carbon $targetDate, bool $alreadySorted = false)
	{
		$this->targetDate = $targetDate;

		if(!$alreadySorted)
		{
			$this->sortDates($dates);
		}

		foreach($dates as $date)
		{
			if($date->gt($targetDate))
			{
				$this->afterDate = $date;
				break;
			}

			$this->beforeDate = $date;
		}
	}


	/**
	 * Returns the closest date (beforeDate or afterDate) to the targetDate, or null if no dates were found.
	 */
	public function closestDate()
	{
		if($this->beforeDate == null)
		{
			return $this->afterDate;
		}

		if($this->afterDate == null)
		{
			return $this->beforeDate;
		}

		$beforeDays = $this->beforeDate->diffInDays($this->targetDate);
		$afterDays = $this->afterDate->diffInDays($this->targetDate);

		if(abs($beforeDays) < abs($afterDays))
		{
			return $this->beforeDate;
		}

		return $this->afterDate;
	}


	/**
	 * Sorts an array of Carbon date objects in chronological order
	 *
	 * @param array $dates
	 */
	private function sortDates(array $dates)
	{
		usort($dates, function($date1, $date2)
		{
			if($date1->lt($date2))
			{
				return -1;
			}
			else if($date1->gt($date2))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		});
	}


}