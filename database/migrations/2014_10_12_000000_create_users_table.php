<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->rememberToken();
            $table->timestamps();
            $table->integer('user_type')->default(1);
            $table->boolean('active')->default(true);
	        $table->boolean('two_factor')->default(false);
        });

	    Schema::create('users_logs', function(Blueprint $table)
	    {
		    $table->increments('id');
		    $table->dateTime('date');
		    $table->integer('event');
		    $table->integer('user_id')->nullable()->default(null);
		    $table->string('email')->nullable()->default(null);
		    $table->string('ip')->default('');
		    $table->string('country')->default('');
		    $table->string('country_code', 2)->default('');
		    $table->string('state')->default('');
		    $table->string('city')->default('');
	    });

	    Schema::create('users_permissions', function(Blueprint $table)
	    {
		    $table->increments('id');
		    $table->integer('user_id');
		    $table->string('permission');
	    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
	    Schema::drop('users_logs');
	    Schema::drop('users_permissions');
    }
}
