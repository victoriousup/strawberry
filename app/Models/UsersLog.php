<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersLog extends Model
{
	const EVENT_LOGIN_ATTEMPT = 0;
	const EVENT_LOGIN_SUCCESS = 1;
	const EVENT_LOCKOUT = 2;

	public $timestamps = false;

	protected $dates = ['date'];

	public $table = 'users_logs';

	public function user()
	{
		return $this->hasOne('App\User', 'user_id');
	}
}
