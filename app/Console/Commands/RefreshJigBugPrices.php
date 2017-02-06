<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jigdaily\Pack;

class RefreshJigBugPrices extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh-jigbug-prices';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates JigBug prices on Google Play';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$packs = Pack::get();
		foreach($packs as $pack)
		{
			$pack->saveToGooglePlay();
		}
	}
}
