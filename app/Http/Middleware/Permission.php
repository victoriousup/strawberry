<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
	public function handle($request, Closure $next, $permission)
	{
		$user = $request->user();

		if($user->user_type != 0 && !$user->hasPermission($permission))
		{
			return response('Unauthorized.', 401);
		}

		return $next($request);
	}
}
