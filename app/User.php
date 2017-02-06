<?php

namespace App;

use App\Models\HasPermissions;
use App\Models\UsersLog;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use HasPermissions;

	private $_lastLogin = null;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	protected $dates = ['created_at', 'updated_at'];


	public function lastLogin($cached = true)
	{
		if($this->_lastLogin != null && $cached)
		{
			return $this->_lastLogin;
		}

		$this->_lastLogin =  UsersLog::where('user_id', $this->id)->orderBy('id', 'desc')->first();

		return $this->_lastLogin;
	}
}
