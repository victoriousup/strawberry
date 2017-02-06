<?php

namespace App\Http\Controllers;

use Cache;

class MonitorController extends Controller
{
	public function monitor()
	{
		$time = time();

		Cache::forever('monitor', $time);

		if(Cache::get('monitor') != $time)
		{
			return response(['success' => false, 'error' => 'Database not saving data'], 400);
		}

		return ['success' => true];
	}
}
