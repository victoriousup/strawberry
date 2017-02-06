<?php

use Illuminate\Database\Seeder;
use App\Models\StockPhotos\Category;
use App\Models\StockPhotos\Subcategory;
use App\Models\StockPhotos\StockPhoto;
use App\Models\StockPhotos\Source;
use App\User;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// $this->call(UserTableSeeder::class);

		// Initial user
		$user = new User();
		$user->name = "Kevin Cornbower";
		$user->email = "k@dsberry.com";
		$user->password = bcrypt("test123");
		$user->user_type = 0;
		$user->save();

		// Import some stock photos
		//Artisan::call('stock-photos:import', ['limit' => 100, '--skip' => true]);

	}
}
