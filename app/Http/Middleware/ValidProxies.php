<?php
// Thanks to https://gist.github.com/cjonstrup/5f0924007357f23cabe8

namespace App\Http\Middleware;

use Closure;

class ValidProxies
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
		// Proxies
		$request->setTrustedProxies([$request->getClientIp()]);

		return $next($request);
	}
}