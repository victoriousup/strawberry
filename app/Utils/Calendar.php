<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Calendar
{
	private $initialDate;

	function __construct($month = null, $year = null)
	{
		$this->initialDate = Carbon::createFromDate($year, $month, 1);
	}


	/**
	 * Returns the calendar year, such as "2016"
	 *
	 * @return int
	 */
	public function getYear()
	{
		return $this->initialDate->year;
	}


	/**
	 * Returns the month number, such as "1" for "January".
	 *
	 * @return int
	 */
	public function getMonth()
	{
		return $this->initialDate->month;
	}


	/**
	 * Returns the name of the month, such as "January".
	 *
	 * @return String
	 */
	public function getMonthName()
	{
		return $this->initialDate->format('F');
	}


	/**
	 * Returns a date object representing the first day in the month.
	 *
	 * @return Carbon
	 */
	public function getMonthStartDate()
	{
		return $this->initialDate->copy();
	}


	/**
	 * Returns a date object representing the last day in the month.
	 *
	 * @return Carbon
	 */
	public function getMonthEndDate()
	{
		return $this->initialDate->copy()->addDays($this->initialDate->daysInMonth - 1);
	}


	/**
	 * Returns the calendar start date. Note that this may not be the same as the month
	 * start date as the calendar will display dates from the previous month if the
	 * 1st does not begin on a Sunday.
	 *
	 * @return Carbon
	 */
	public function getCalendarStartDate()
	{
		return $this->initialDate->copy()->subDays($this->initialDate->dayOfWeek);
	}


	/**
	 * Returns the calendar end date. Note that this may not be the same as the month
	 * end date as the calendar will display dates from the next month if the
	 * last day of the month does not fall on a Saturday.
	 *
	 * @return Carbon
	 */
	public function getCalendarEndDate()
	{
		$monthEndDate = $this->getMonthEndDate();
		return $monthEndDate->copy()->addDays(6 - $monthEndDate->dayOfWeek);
	}


	/**
	 * Returns an array of Carbon objects for each day in the month.
	 *
	 * @return array
	 */
	public function getMonthDates()
	{
		$ret = [];
		$date = $this->getMonthStartDate();
		$endDate = $this->getMonthEndDate();

		while($date->lte($endDate))
		{
			$ret[] = $date->copy();
			$date->addDay();
		}

		return $ret;
	}


	/**
	 * Returns an array of Carbon objects for each day in the calendar.
	 *
	 * @return array
	 */
	public function getCalendarDates()
	{
		$ret = [];
		$date = $this->getCalendarStartDate();
		$endDate = $this->getCalendarEndDate();

		while($date->lte($endDate))
		{
			$ret[] = $date->copy();
			$date->addDay();
		}

		return $ret;
	}


	/**
	 * Returns true if a Carbon date object is within the current calendar month.
	 *
	 * @param Carbon $date
	 * @return bool
	 */
	public function inCurrentMonth(Carbon $date)
	{
		return $date->year == $this->initialDate->year && $date->month == $this->initialDate->month;
	}


	/**
	 * Takes an array of Carbon date objects and a **SORTED** collection of objects with a "date" parameter, and
	 * combines them so each date contains the events within that day.
	 *
	 * @param $dates
	 * @param $events
	 * @return array
	 */
	public static function eventsToDates(array $dates, Collection $events)
	{
		$ret = [];
		$index = 0;

		foreach($dates as $date)
		{
			$obj = new \stdClass();
			$obj->date = $date;
			$obj->events = [];


			for($index; $index < $events->count(); $index++)
			{
				$tmpEvent = $events[$index];
				if($tmpEvent->date->format('Y-m-d') == $date->format('Y-m-d'))
				{
					$obj->events[] = $tmpEvent;
				}
				else
				{
					break;
				}
			}

			$ret[] = $obj;
		}

		return $ret;
	}

}