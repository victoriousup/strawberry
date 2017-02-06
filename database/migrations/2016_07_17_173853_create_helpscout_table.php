<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateHelpscoutTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('helpscout_conversations', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('helpscout_id');
			$table->dateTime('date')->default(Carbon::now());
			$table->string('app');
			$table->string('platform')->nullable();
			$table->string('device_type')->nullable();
			$table->string('version')->nullable();
			$table->string('device')->nullable();
			$table->string('device_id')->nullable();
			$table->string('analytics_id')->nullable();
			$table->string('ip')->nullable();
			$table->string('currency')->nullable();
			$table->string('country')->nullable();
			$table->string('country_code')->nullable();
			$table->string('state')->nullable();
			$table->string('city')->nullable();
			$table->text('attributes')->nullable();

			$table->index('helpscout_id');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('helpscout_conversations');
	}
}
