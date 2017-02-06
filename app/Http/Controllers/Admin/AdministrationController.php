<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\UsersLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdministrationController extends Controller
{
    public function getUsers()
    {
        $users = User::all();

	    return view('admin.administration.users', compact('users'));
    }


    public function getAddUser()
    {
	    $user = new User();
	    $user->user_type = 1;

	    $title = 'Add New User';
        return view('admin.administration.user', compact('title', 'user'));
    }


	public function postAddUser(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users,email',
			'user_type' => 'required|integer'
		]);

		$user = new User();
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->user_type = $request->input('user_type');

		if($request->input('password') == '')
		{
			$user->password = bcrypt(str_random(20));
		}
		else
		{
			$user->password = bcrypt($request->input('password'));
		}

		$user->save();

		$this->syncUserPermissions($request, $user);

		return redirect('admin/users/');
	}


	public function getEditUser($id)
	{
		$user = User::findOrFail($id);
		$title = 'Edit User';
		return view('admin.administration.user', compact('title', 'user'));
	}


	public function postEditUser(Request $request, $id)
	{
		$user = User::findOrFail($id);

		$this->validate($request, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
			'user_type' => 'required|integer'
		]);

		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->user_type = $request->input('user_type');
		$user->save();

		$this->syncUserPermissions($request, $user);

		return redirect('admin/users/');
	}


	protected function syncUserPermissions(Request $request, User $user)
	{
		$permissions = [];

		$input = $request->all();
		foreach($input as $key => $value)
		{
			if(stristr($key, 'permission_') !== false)
			{
				$permissions[] = str_replace('permission_', '', $key);
			}
		}

		$user->syncPermissions($permissions);
	}




	public function getLogs()
	{
		$logs = UsersLog::where('date', '>=', Carbon::now()->subWeek())->orderBy('id', 'desc')->get();

		return view('admin.administration.logs', compact('logs'));
	}




}
