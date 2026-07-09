<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CoinSystemEnabled
{
    /**
     * Handle an incoming request.
     *
     * If the coin system is disabled, return 404.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!coin_system_enabled()) {
            abort(404);
        }

        return $next($request);
    }
}
