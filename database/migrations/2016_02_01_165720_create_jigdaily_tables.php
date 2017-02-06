<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class CreateJigdailyTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jigdaily_photos', function (Blueprint $table)
		{
			$table->integer('id')->unique();
			$table->string('name');
			$table->string('stemmed_keywords')->default('');
			$table->integer('pack_id')->nullable();
		});

		Schema::create('jigdaily_packs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->boolean('visible')->default(true);
			$table->boolean('released')->default(false);
			$table->integer('store_order')->default(0);
			$table->integer('cover_id')->default(-1);
			$table->integer('price_tier')->default(1);
			$table->integer('coupon_id')->nullable()->default(null);
			$table->integer('itunes_status')->default(0);
			$table->boolean('featured')->default(false);
			$table->string('sticker_ids')->default('[]');
			$table->string('recommended_pack_ids')->default('[]');
		});

		Schema::create('jigdaily_stickers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('file');
			$table->double('widthScale');
			$table->double('heightScale');
			$table->double('xScale');
			$table->double('yScale');
		});

		Schema::create('jigdaily_daily_photos', function(Blueprint $table)
		{
			$table->date('date')->unique();
			$table->integer('photo_id');
		});

		Schema::create('jigdaily_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('platform');
			$table->string('receipt_id');
			$table->integer('coupon_id')->nullable()->default(null);
			$table->integer('pack_id');
			$table->dateTime('date')->default(Carbon::now());
			$table->integer('downloads')->default(0);
			$table->integer('unique_devices')->default(0);
			$table->double('price');
			$table->string('currency');
			$table->double('price_usd');
			$table->boolean('sandbox')->default(false);
			$table->boolean('active')->default(true);

			$table->index(['platform', 'receipt_id']);
		});


		Schema::create('jigdaily_devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('platform');
			$table->string('device_id');

			$table->index(['platform', 'device_id']);
		});


		Schema::create('jigdaily_device_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('transaction_id', false, true);
			$table->integer('device_id', false, true);
			$table->dateTime('date')->default(Carbon::now());
			$table->string('ip');
			$table->boolean('original_purchaser')->default(true);
			$table->integer('downloads')->default(0);

			$table->index(['transaction_id', 'device_id']);
		});


		Schema::create('jigdaily_coupons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code');
			$table->integer('pack_id');
			$table->integer('redemptions')->default(0);
			$table->integer('max_redemptions')->nullable()->default(null);
			$table->text('device_ids')->nullable()->default(null);
			$table->boolean('active')->default(true);
			$table->dateTime('expiration')->nullable()->default(null);

			$table->index('code');
		});


		Schema::create('jigdaily_test_devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('device_id');
			$table->string('description');
			$table->boolean('active')->default(true);

			$table->index('device_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('jigdaily_photos');
		Schema::drop('jigdaily_packs');
		Schema::drop('jigdaily_stickers');
		Schema::drop('jigdaily_daily_photos');
		Schema::drop('jigdaily_transactions');
		Schema::drop('jigdaily_devices');
		Schema::drop('jigdaily_device_transactions');
		Schema::drop('jigdaily_coupons');
		Schema::drop('jigdaily_test_devices');
	}
}
