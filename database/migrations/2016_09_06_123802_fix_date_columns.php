<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDateColumns extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// We accidentally hard-coded the default dates in the database. These migrations fix it.

		Schema::table('jigdaily_transactions', function ($table)
		{
			$table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
		});

		Schema::table('jigdaily_device_transactions', function(Blueprint $table)
		{
			$table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
		});

		Schema::table('helpscout_conversations', function (Blueprint $table)
		{
			$table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
		});

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
