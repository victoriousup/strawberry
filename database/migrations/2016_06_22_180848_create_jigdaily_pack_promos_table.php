<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJigdailyPackPromosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jigdaily_pack_promos', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pack_id');
			$table->tinyInteger('type');
			$table->string('name');
			$table->string('currency', 3)->nullable();
			$table->double('price')->nullable();
			$table->tinyInteger('status');
			$table->string('file');

			$table->index('pack_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jigdaily_pack_promos');
	}
}
