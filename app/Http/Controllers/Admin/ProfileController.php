<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function getEdit()
    {
	    $user = Auth::user();
	    return view('admin.profile.edit', compact('user'));
    }

	public function postEdit(Request $request)
	{
		$user = Auth::user();

		$rules = [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id
		];

		if($request->input('password') != '')
		{
			$rules['password'] = 'required|min:3|max:25';
			$rules['password_confirm'] = 'required|same:password';
		}

		$this->validate($request, $rules);

		$user->name = $request->input('name');
		$user->email = $request->input('email');

		if($request->input('password') != '')
		{
			$user->password = bcrypt($request->input('password'));
		}

		$user->save();

		Session::flash('alert-success', 'Your profile has been updated!');

		return back();
	}
}
