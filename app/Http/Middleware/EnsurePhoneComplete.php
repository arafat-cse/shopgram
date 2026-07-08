<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePhoneComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && str_starts_with((string) $user->phone, 'gtmp_') && !$request->routeIs('customer.profile.*')) {
            return redirect()->route('customer.profile.edit')
                ->with('warning', 'Please add your phone number to continue.');
        }

        return $next($request);
    }
}
