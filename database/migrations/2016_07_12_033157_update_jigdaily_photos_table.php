<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJigdailyPhotosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\DB::update('UPDATE jigdaily_photos SET stemmed_keywords = (SELECT stemmed_keywords FROM stock_photos WHERE stock_photos.id = jigdaily_photos.id)');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
