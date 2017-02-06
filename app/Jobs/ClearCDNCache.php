<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Utils\CDNHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MaxCDN;

class ClearCDNCache extends Job implements ShouldQueue
{
	use InteractsWithQueue, SerializesModels;

	protected $files;

	// "cdn" or "static"
	public $zone = 'cdn';


	/**
	 * Create a new job instance.
	 *
	 * @param array|null $files
	 */
	public function __construct(array $files = null)
	{
		$this->files = $files;
	}


	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		// CDN is not set up
		if(!CDNHelper::useCDN())
		{
			return;
		}

		$api = new MaxCDN(config('maxcdn.alias'), config('maxcdn.key'), config('maxcdn.secret'));
		$zoneId = config('maxcdn.' . $this->zone . '.zone');

		// Clear full cache
		if($this->files == null)
		{
			$result = $api->delete('/zones/pull.json/' . $zoneId . '/cache');
		}
		// Clear specific files
		else
		{
			$result = $api->delete('/zones/pull.json/' . $zoneId . '/cache', ['files' => $this->files]);
		}

		// Was the job successful?
		$jsonResult = json_decode($result);
		if($jsonResult == null || !$jsonResult->code || $jsonResult->code != 200)
		{
			throw new \Exception($result);
		}

	}
}
