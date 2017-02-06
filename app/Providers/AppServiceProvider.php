<?php

namespace App\Providers;

use Mail;
use Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Logging\Log;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Queue::failing(function (JobFailed $event)
		{
			Mail::queue('emails.failed-job', ['job' => json_encode($event->data)], function($message)
			{
				$message->subject('Failed Job');
				$message->to('kevin@dsberry.com');
			});
		});
	}


	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		// Track exceptions via Bugsnag
		$this->app->alias('bugsnag.multi', Log::class);
		$this->app->alias('bugsnag.multi', LoggerInterface::class);
	}
}
