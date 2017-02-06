<?php

namespace App\Listeners;

use App\Utils\GeoIP;
use Hash;
use App\User;
use App\Models\UsersLog;
use Carbon\Carbon;
use Illuminate\Auth\Events\Attempting;
use Request;
use League\Flysystem\Exception;

class LogAuthenticationAttempt
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}


	/**
	 * Handle the event.
	 *
	 * @param  $event
	 * @return void
	 */
	public function handle(Attempting $event)
	{
		$log = new UsersLog();
		$log->date = Carbon::now();
		$log->event = UsersLog::EVENT_LOGIN_ATTEMPT;
		$log->email = $event->credentials['email'];
		$log->ip = Request::ip();

		// Look up country and city info
		$geoIP = new GeoIP();

		try
		{
			if(!$geoIP->isLocalIP($log->ip))
			{
				$location = $geoIP->locate($log->ip);

				$log->country = $location->country->name;
				$log->country_code = $location->country->isoCode;
				$log->state = $location->mostSpecificSubdivision->name;
				$log->city = $location->city->name;
			}
		}
		catch(\Exception $e)
		{

		}

		// This login was for an actual registered user
		$user = User::where('email', $event->credentials['email'])->first();
		if($user)
		{
			$log->user_id = $user->id;

			if(Hash::check($event->credentials['password'], $user->password))
			{
				$log->event = UsersLog::EVENT_LOGIN_SUCCESS;
			}
		}

		$log->save();
	}
}
