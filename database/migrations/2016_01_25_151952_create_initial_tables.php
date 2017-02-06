<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitialTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_photo_sources', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
		});

		Schema::create('stock_photos', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('description')->default("");
			$table->text('keywords')->default("");
			$table->text('stemmed_keywords')->default("");
			$table->json('square_cropping')->nullable();
			$table->integer('stock_photo_source_id');
			$table->string('stock_photo_id')->nullable();
			$table->string('photographer');
			$table->string('photographer_id')->nullable();
			$table->string('org_title')->default("");
			$table->string('org_description')->default("");
			$table->string('source_file')->default("");
			$table->string('org_keywords')->default("");
			$table->integer('default_subcategory_id')->nullable();
		});

		Schema::create('stock_photo_categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('slug');
			$table->string('name');
			$table->integer('parent_category_id')->nullable();
		});

		Schema::create('stock_photos_categories', function(Blueprint $table)
		{
			$table->integer('stock_photo_id');
			$table->integer('category_id');
			$table->integer('subcategory_id');
		});

		Schema::create('exchange_rates', function(Blueprint $table)
		{
			$table->string('currency', 10)->unique();
			$table->double('rate')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stock_photo_sources');
		Schema::drop('stock_photos');
		Schema::drop('stock_photo_categories');
		Schema::drop('stock_photos_categories');
		Schema::drop('exchange_rates');
	}
}
