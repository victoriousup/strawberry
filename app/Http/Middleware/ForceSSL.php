<?php
// Thanks to http://stackoverflow.com/questions/28402726/laravel-5-redirect-to-https

namespace App\Http\Middleware;

use Closure;

class ForceSSL
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		// Don't force SSL for MaxCDN requests
		if($request->is('/') || $request->is('api/*'))
		{
			return $next($request);
		}

		if(!$request->secure() && env('APP_ENV') != 'local')
		{
			return redirect()->secure($request->getRequestUri());
		}

		return $next($request);
	}
}