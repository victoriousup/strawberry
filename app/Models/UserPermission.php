<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
	public $timestamps = false;

	public $table = 'users_permissions';

	protected $guarded = [];

	public function user()
	{
		return $this->hasOne('App\User', 'user_id');
	}
}
