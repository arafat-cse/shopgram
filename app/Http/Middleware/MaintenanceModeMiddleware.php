<?php
namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Setting::get('maintenance_mode') === '1') {
            if (!$request->user()?->hasRole('Super Admin')) {
                return response()->view('maintenance', [
                    'message' => Setting::get('maintenance_message', 'Site is under maintenance. Please check back later.'),
                ], 503);
            }
        }

        return $next($request);
    }
}
